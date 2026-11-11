<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerCommonInquiryQuestionRequest;
use App\Models\AssessmentAnswer;
use App\Models\CommonQuestion;
use App\Models\DocumentTemplate;
use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\InquiryRequirense;
use App\Models\ReassessmentRequest;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use App\Services\DocumentOcrVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class ResidentController extends Controller
{
    public function home(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        if ($search) {
            $categories = ServiceCategory::with(['governmentServices' => function ($q) use ($search) {
                $q->where('service_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }, 'governmentServices.translations'])->get();
        } else {
            $categories = ServiceCategory::with('governmentServices.translations')->get();
        }

        $applications = $user
            ? UserChecklist::with('service')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('resident.home', compact('categories', 'applications', 'search'));
    }

    public function eligibility()
    {
        $services = GovernmentService::with('translations')->get();

        $assessments = Auth::check()
            ? EligibilityAssessment::with('service')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $reassessmentRequests = Auth::check()
            ? ReassessmentRequest::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->get()
                ->keyBy('service_id')
            : collect();

        return view('resident.eligibility.index', compact('services', 'assessments', 'reassessmentRequests'));
    }

    public function showAssessForm(GovernmentService $service)
    {
        $existingAssessment = EligibilityAssessment::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if ($existingAssessment) {
            return redirect()->route('resident.eligibility')->with('error', __('messages.flash_already_assessed'));
        }

        $questions = $service->eligibilityQuestions;

        return view('resident.eligibility.assess', compact('service', 'questions'));
    }

    public function processAssessForm(Request $request, GovernmentService $service)
    {
        $user = Auth::user();

        if (EligibilityAssessment::where('user_id', $user->id)->where('service_id', $service->id)->exists()) {
            return redirect()->route('resident.eligibility')->with('error', __('messages.flash_already_assessed'));
        }

        $questions = $service->eligibilityQuestions;

        $rules = [];

        foreach ($questions as $question) {
            $presenceRule = $question->is_required ? 'required' : 'nullable';

            $rules['question_'.$question->id] = match ($question->type) {
                'boolean' => [$presenceRule, 'in:true,false'],
                'number' => [$presenceRule, 'numeric'],
                default => [$presenceRule, 'string', 'max:5000'],
            };
        }

        $validated = $request->validate($rules);
        $answers = [];

        foreach ($questions as $q) {
            $answers[$q->question_text] = [
                'answer' => $validated['question_'.$q->id] ?? 'No Answer',
                'is_required' => (bool) $q->is_required,
            ];
        }

        $assessment = EligibilityAssessment::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'pending',
        ]);

        foreach ($answers as $qText => $entry) {
            AssessmentAnswer::create([
                'assessment_id' => $assessment->id,
                'question' => $qText,
                'answer' => $entry['answer'],
                'is_required' => $entry['is_required'],
            ]);
        }

        return redirect()->route('resident.eligibility.result', ['refNo' => $assessment->id]);
    }

    public function showAssessResult($refNo)
    {
        $id = is_numeric($refNo) ? $refNo : (int) preg_replace('/[^0-9]/', '', (string) $refNo);

        $assessment = EligibilityAssessment::with(['service', 'answers'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('resident.eligibility.result', compact('assessment'));
    }

    public function requestReassessment(Request $request, GovernmentService $service)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $hasAssessment = EligibilityAssessment::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->exists();

        if (! $hasAssessment) {
            return back()->with('error', __('messages.flash_must_qualify'));
        }

        $existingRequest = ReassessmentRequest::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', __('messages.flash_reassess_pending'));
        }

        ReassessmentRequest::create([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', __('messages.flash_reassess_sent'));
    }

    public function checklist(GovernmentService $service)
    {
        $user = Auth::user();

        if (! $user->isProfileComplete()) {
            return redirect()->route('resident.profile.edit')->with('error', __('messages.profile_gate_flash', ['fields' => $this->missingProfileFieldNames($user)]));
        }

        if (! $user->isEligibleFor($service->id)) {
            return redirect()->route('resident.eligibility')->with('error', __('messages.flash_must_qualify'));
        }

        $checklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        // Check if this is the Employment Assistance service
        $isEmployment = str_contains(strtolower($service->service_name), 'employment');

        // Default application type for Employment if not set
        if ($isEmployment && $checklist && ! $checklist->application_type) {
            $checklist->update(['application_type' => 'new']);
        }

        $requirements = $service->requirements;

        // Apply renewal filter if active
        if ($isEmployment && $checklist && $checklist->application_type === 'renewal') {
            $requirements = $requirements->filter(function ($req) {
                $name = strtolower($req->name_en);

                return str_contains($name, 'pds') ||
                       str_contains($name, 'recommendation') ||
                       str_contains($name, 'letter request') ||
                       str_contains($name, 'endorsement');
            });
        }

        $uploadedDocs = collect();
        if ($checklist) {
            $uploadedDocs = UserChecklistItem::where('checklist_id', $checklist->id)
                ->get()
                ->keyBy('requirement_id');
        }

        $allMandatoryUploaded = true;
        foreach ($requirements as $req) {
            if ($req->is_required) {
                $item = $uploadedDocs[$req->id] ?? null;
                if (! $this->isDocumentSatisfied($item)) {
                    $allMandatoryUploaded = false;
                    break;
                }
            }
        }

        $alreadyApplied = $checklist && in_array($checklist->status, ['pending', 'approved', 'rejected']);

        return view('resident.eligibility.checklist', compact('service', 'requirements', 'uploadedDocs', 'allMandatoryUploaded', 'alreadyApplied', 'checklist'));
    }

    /**
     * A required document counts once uploaded, unless a facilitator rejected it.
     */
    private function isDocumentSatisfied(?UserChecklistItem $item): bool
    {
        return $item !== null
            && $item->is_submitted
            && ! empty($item->file_path)
            && $item->status !== 'rejected';
    }

    private function missingProfileFieldNames(User $user): string
    {
        return collect($user->missingProfileFields())
            ->map(fn (string $labelKey): string => __('messages.'.$labelKey))
            ->implode(', ');
    }

    public function setApplicationType(Request $request, GovernmentService $service)
    {
        $request->validate([
            'application_type' => 'required|in:new,renewal',
        ]);

        if (! Auth::user()->isEligibleFor($service->id)) {
            return back()->with('error', __('messages.flash_must_be_eligible'));
        }

        $checklist = UserChecklist::firstOrCreate([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
        ], [
            'status' => 'draft',
        ]);

        if ($checklist->status === 'draft') {
            $checklist->update([
                'application_type' => $request->application_type,
            ]);

            // Clean up files that do not belong to renewal when switching
            if ($request->application_type === 'renewal') {
                $pdsReq = $service->requirements->first(function ($req) {
                    return str_contains(strtolower($req->name_en), 'pds');
                });

                if ($pdsReq) {
                    UserChecklistItem::where('checklist_id', $checklist->id)
                        ->where('requirement_id', '!=', $pdsReq->id)
                        ->delete();
                }
            }

            return back()->with('success', __('messages.flash_type_updated'));
        }

        return back()->with('error', __('messages.flash_type_locked'));
    }

    public function uploadDocument(
        Request $request,
        GovernmentService $service,
        ServiceRequirement $requirement,
        DocumentOcrVerifier $documentOcrVerifier,
    ) {
        $request->validate([
            'document' => ['required', 'file', 'mimes:jpg,png,jpeg', 'max:5120'],
        ]);

        abort_unless($requirement->service_id === $service->id, 404);

        if (! Auth::user()->isEligibleFor($service->id)) {
            return back()->with('error', __('messages.flash_must_be_eligible'));
        }

        $existingChecklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if ($existingChecklist && $existingChecklist->status !== 'draft') {
            return back()->with('error', __('messages.flash_submitted_locked'));
        }

        if ($file = $request->file('document')) {
            $disk = config('filesystems.default');
            $folderName = Str::slug($service->name);
            $path = $file->store('documents/'.$folderName, $disk);

            $template = DocumentTemplate::query()
                ->where('service_id', $service->id)
                ->where('requirement_id', $requirement->id)
                ->first();

            $status = 'pending';
            $review = ['review_result' => null, 'review_score' => null, 'review_note' => null];

            if ($template) {
                $verification = $documentOcrVerifier->verify(
                    $disk,
                    $path,
                    $template->file_path,
                    $template->name_en ?: $template->name_ceb,
                );

                if ($verification['status'] === 'mismatch') {
                    Storage::disk($disk)->delete($path);

                    return back()->with('error', $verification['message']);
                }

                $status = $verification['status'] === 'verified' ? 'approved' : 'pending';
                $review = [
                    'review_result' => $verification['status'],
                    'review_score' => $verification['score'],
                    'review_note' => $documentOcrVerifier->describe($verification),
                ];
            }

            $checklist = UserChecklist::firstOrCreate([
                'user_id' => Auth::id(),
                'service_id' => $service->id,
            ], [
                'status' => 'draft',
            ]);

            $existingChecklistItem = UserChecklistItem::query()
                ->where('checklist_id', $checklist->id)
                ->where('requirement_id', $requirement->id)
                ->first();

            UserChecklistItem::updateOrCreate([
                'checklist_id' => $checklist->id,
                'requirement_id' => $requirement->id,
            ], [
                'is_submitted' => true,
                'file_path' => $path,
                'submitted_at' => now(),
                'status' => $status,
                ...$review,
            ]);

            if ($existingChecklistItem?->file_path && $existingChecklistItem->file_path !== $path) {
                Storage::disk($disk)->delete($existingChecklistItem->file_path);
            }
        }

        if ($template && $review['review_result'] === 'verified') {
            return back()->with('success', __('messages.flash_doc_verified'));
        }

        $qualityTips = $template && $verification['issues'] ? ' '.implode(' ', $verification['issues']) : '';

        return back()->with('success', __('messages.flash_doc_pending').$qualityTips);
    }

    public function apply(GovernmentService $service)
    {
        $user = Auth::user();

        if (! $user->isProfileComplete()) {
            return redirect()->route('resident.profile.edit')->with('error', __('messages.profile_gate_flash', ['fields' => $this->missingProfileFieldNames($user)]));
        }

        if (! $user->isEligibleFor($service->id)) {
            return back()->with('error', __('messages.flash_must_be_eligible'));
        }

        $checklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if (! $checklist) {
            return back()->with('error', __('messages.flash_no_checklist'));
        }

        $requirements = $service->requirements;
        $isEmployment = str_contains(strtolower($service->service_name), 'employment');
        if ($isEmployment && $checklist->application_type === 'renewal') {
            $requirements = $requirements->filter(function ($req) {
                $name = strtolower($req->name_en);

                return str_contains($name, 'pds') ||
                       str_contains($name, 'recommendation') ||
                       str_contains($name, 'letter request') ||
                       str_contains($name, 'endorsement');
            });
        }
        foreach ($requirements as $req) {
            if ($req->is_required) {
                $item = UserChecklistItem::where('checklist_id', $checklist->id)
                    ->where('requirement_id', $req->id)
                    ->first();
                if (! $this->isDocumentSatisfied($item)) {
                    return back()->with('error', __('messages.flash_upload_all'));
                }
            }
        }

        $checklist->update([
            'status' => 'pending',
        ]);

        return redirect()->route('resident.home')->with('success', __('messages.flash_app_submitted'));
    }

    public function inquiry(Request $request)
    {
        if (Auth::check()) {
            $inquiries = UserInquiry::with(['service.translations', 'responses.responder'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $inquiries = collect();
        }

        $services = GovernmentService::query()
            ->with(['translations', 'commonQuestions' => fn ($query) => $query
                ->whereNotNull('answer_text')
                ->oldest()
                ->limit(5)])
            ->oldest()
            ->get();
        $templates = collect();

        // Opening the Inquiry page clears the nav indicator
        $inquiries->each->markSeenByResident();

        return view('resident.inquiry.bot', compact('inquiries', 'services', 'templates'));
    }

    public function submitManualInquiry(Request $request)
    {
        $rules = [
            'subject' => ['nullable', 'string', 'max:255'],
            'inquiry_text' => ['required', 'string', 'max:5000'],
            'service_id' => ['nullable', 'exists:government_services,id'],
            'inquiry_id' => ['nullable', 'exists:user_inquiries,id'],
        ];

        if (! Auth::check()) {
            $rules['guest_name'] = ['required', 'string', 'max:255'];
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        $request->validate($rules);

        $inquiry = null;

        if ($request->filled('inquiry_id')) {
            if (Auth::check()) {
                $inquiry = UserInquiry::where('id', $request->input('inquiry_id'))
                    ->where('user_id', Auth::id())
                    ->first();
            } elseif ($this->guestCanAccessInquiry($request, $request->integer('inquiry_id'))) {
                $inquiry = UserInquiry::where('id', $request->input('inquiry_id'))
                    ->where('guest_email', $request->input('guest_email'))
                    ->first();
            }
        }

        if ($inquiry) {
            if ($request->input('service_id')) {
                $inquiry->update([
                    'service_id' => $request->input('service_id'),
                ]);
            }

            InquiryRequirense::create([
                'inquiry_id' => $inquiry->id,
                'requireent_text' => $request->input('inquiry_text'),
                'responded_by' => Auth::check() ? Auth::id() : null,
            ]);

            $inquiry->update(['status' => 'pending', 'awaiting_reply_since' => null]);
            $inquiry->touch();
        } else {
            $subject = $request->input('subject') ?: Str::limit($request->input('inquiry_text'), 50);

            $inquiry = UserInquiry::create([
                'subject' => $subject,
                'user_id' => Auth::check() ? Auth::id() : null,
                'guest_name' => $request->input('guest_name'),
                'guest_email' => $request->input('guest_email'),
                'service_id' => $request->input('service_id'),
                'inquiry_text' => $request->input('inquiry_text'),
                'status' => 'pending',
            ]);

            if (! Auth::check()) {
                $request->session()->push('guest_inquiry_ids', $inquiry->id);
            }

            InquiryRequirense::create([
                'inquiry_id' => $inquiry->id,
                'requireent_text' => $request->input('inquiry_text'),
                'responded_by' => Auth::check() ? Auth::id() : null,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'inquiry' => $inquiry->load(['service', 'responses.responder']),
            ]);
        }

        return back()->with('success', 'Your message has been sent. Staff will reply here.');
    }

    public function answerCommonInquiryQuestion(AnswerCommonInquiryQuestionRequest $request)
    {
        $validated = $request->validated();
        $commonQuestion = CommonQuestion::query()->findOrFail($validated['common_question_id']);

        $inquiry = DB::transaction(function () use ($request, $validated, $commonQuestion) {
            $inquiry = null;

            if (isset($validated['inquiry_id'])) {
                $inquiry = UserInquiry::query()->findOrFail($validated['inquiry_id']);

                $canAccessInquiry = $request->user()
                    ? $inquiry->user_id === $request->user()->id
                    : $this->guestCanAccessInquiry($request, $inquiry->id)
                        && $inquiry->guest_email === $validated['guest_email'];

                abort_unless($canAccessInquiry, 403);
            }

            if (! $inquiry) {
                $inquiry = UserInquiry::create([
                    'user_id' => $request->user()?->id,
                    'guest_name' => $validated['guest_name'] ?? null,
                    'guest_email' => $validated['guest_email'] ?? null,
                    'service_id' => $commonQuestion->service_id,
                    'inquiry_text' => $commonQuestion->question_text,
                    'status' => 'resolved',
                ]);

                if (! $request->user()) {
                    $request->session()->push('guest_inquiry_ids', $inquiry->id);
                }
            }

            InquiryRequirense::create([
                'inquiry_id' => $inquiry->id,
                'requireent_text' => $commonQuestion->question_text,
                'responded_by' => $request->user()?->id,
            ]);

            InquiryRequirense::create([
                'inquiry_id' => $inquiry->id,
                'requireent_text' => $commonQuestion->answer_text,
                'responded_by' => null,
                'is_system' => true,
            ]);

            $inquiry->update([
                'status' => 'resolved',
                'awaiting_reply_since' => now(),
                'updated_at' => now(),
            ]);

            return $inquiry;
        });

        return response()->json([
            'success' => true,
            'inquiry' => $inquiry->load(['service', 'responses.responder']),
        ]);
    }

    public function getMessages(Request $request, UserInquiry $inquiry)
    {
        if (Auth::check()) {
            if ($inquiry->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            if (! $this->guestCanAccessInquiry($request, $inquiry->id)) {
                abort(403);
            }
        }

        return response()->json([
            'success' => true,
            'inquiry' => $inquiry->load(['service', 'responses.responder']),
        ]);
    }

    public function profile()
    {
        return view('resident.profile.show');
    }

    public function editProfile()
    {
        return view('resident.profile.edit');
    }

    public function viewValidId()
    {
        $user = Auth::user();

        abort_unless($user->valid_id_path, 404, 'You have not uploaded a valid ID.');

        $disk = config('filesystems.default');

        abort_unless(
            Storage::disk($disk)->exists($user->valid_id_path),
            404,
            'The uploaded ID could not be found.',
        );

        return Storage::disk($disk)->response($user->valid_id_path);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->merge([
            'contact_number' => User::normalizeContactNumber((string) $request->input('contact_number')) ?? $request->input('contact_number'),
            'email' => $request->filled('email') ? strtolower(trim((string) $request->input('email'))) : null,
        ]);

        $request->validate([
            'first_name' => ['nullable', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'middle_name' => ['nullable', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'last_name' => ['nullable', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'dob' => ['nullable', 'date', 'before_or_equal:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'civil_status' => ['nullable', Rule::in(User::CIVIL_STATUSES)],
            'sex' => ['nullable', Rule::in(User::SEXES)],
            'barangay' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/', 'unique:users,contact_number,'.$user->id],
            'valid_id' => ['nullable', 'file', 'mimes:jpg,png,jpeg', 'max:5120'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->filled('first_name') || $request->filled('last_name')) {
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->last_name = $request->last_name;
            $user->name = trim($request->first_name.' '.($request->middle_name ? $request->middle_name.' ' : '').$request->last_name);
        } elseif ($request->filled('name')) {
            $user->name = $request->name;
        }

        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->sex = $request->sex;
        $user->barangay = $request->barangay;
        $user->street_address = $request->street_address;
        $user->address = $request->filled('street_address') || $request->filled('barangay')
            ? collect([$request->street_address, $request->barangay])->filter()->implode(', ')
            : $request->address;
        $user->civil_status = $request->civil_status;
        $user->contact_number = $request->contact_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($file = $request->file('valid_id')) {
            $disk = config('filesystems.default');
            $path = $file->store('valid_ids', $disk);

            $firstName = $user->first_name ?: $request->first_name;
            $middleName = $user->middle_name ?: $request->middle_name;
            $lastName = $user->last_name ?: $request->last_name;

            if (! $firstName && ! $lastName && $user->name) {
                $nameParts = explode(' ', $user->name);
                $firstName = $nameParts[0] ?? '';
                $lastName = end($nameParts) ?? '';
            }

            if (! $this->validIdMatchesName($disk, $path, (string) $firstName, (string) $middleName, (string) $lastName)) {
                Storage::disk($disk)->delete($path);

                return back()->with('error', 'The uploaded ID does not match your registered account name.');
            }

            $previousIdPath = $user->valid_id_path;
            $user->valid_id_path = $path;

            if ($previousIdPath && $previousIdPath !== $path) {
                Storage::disk($disk)->delete($previousIdPath);
            }
        }

        $user->save();

        return redirect()->route('resident.profile')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            // No SVG: it can carry scripts and would run on this site when opened
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($file = $request->file('avatar')) {
            $path = $file->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return back()->with('success', 'Avatar updated successfully.');
    }

    public function viewDocument(UserChecklistItem $item)
    {
        abort_unless($item->checklist?->user_id === Auth::id() && $item->file_path, 404);

        $disk = config('filesystems.default');
        abort_unless(Storage::disk($disk)->exists($item->file_path), 404);

        return Storage::disk($disk)->response($item->file_path);
    }

    public function unlockChecklist(GovernmentService $service)
    {
        $checklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if ($checklist && $checklist->status === 'rejected') {
            $checklist->update(['status' => 'draft']);

            return back()->with('success', __('messages.flash_unlocked'));
        }

        return back()->with('error', __('messages.flash_cannot_edit'));
    }

    public function replyInquiry(Request $request, UserInquiry $inquiry)
    {
        if (Auth::check()) {
            if ($inquiry->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            if (! $this->guestCanAccessInquiry($request, $inquiry->id)) {
                abort(403);
            }
        }

        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $reply = InquiryRequirense::create([
            'inquiry_id' => $inquiry->id,
            'requireent_text' => $request->message,
            'responded_by' => Auth::check() ? Auth::id() : null,
        ]);

        $inquiry->update(['status' => 'pending', 'awaiting_reply_since' => null]);

        return response()->json([
            'success' => true,
            'reply' => $reply->requireent_text,
            'time' => now()->format('h:i A'),
        ]);
    }

    public function deleteInquiry(Request $request, UserInquiry $inquiry)
    {
        if (Auth::check()) {
            if ($inquiry->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            if (! $this->guestCanAccessInquiry($request, $inquiry->id)) {
                abort(403);
            }
        }

        $inquiry->responses()->delete();
        $inquiry->delete();

        return response()->json(['success' => true]);
    }

    public function deleteReply(Request $request, InquiryRequirense $response)
    {
        if (Auth::check()) {
            if ($response->responded_by !== Auth::id()) {
                abort(403);
            }
        } else {
            if (! $response->inquiry || ! $this->guestCanAccessInquiry($request, $response->inquiry->id)) {
                abort(403);
            }
        }

        $response->delete();

        return response()->json(['success' => true]);
    }

    private function validIdMatchesName(string $disk, string $path, string $firstName, string $middleName, string $lastName): bool
    {
        $source = Storage::disk($disk)->readStream($path);

        if ($source === null) {
            return true;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $temporaryPath = tempnam(sys_get_temp_dir(), 'govassist-id-');
        $localPath = $temporaryPath.($extension !== '' ? '.'.$extension : '');
        rename($temporaryPath, $localPath);

        try {
            $destination = fopen($localPath, 'wb');
            stream_copy_to_stream($source, $destination);
            fclose($destination);

            $result = Process::timeout(120)->run([
                'python3',
                base_path('scripts/verify_id.py'),
                $localPath,
                $firstName,
                $middleName,
                $lastName,
            ]);

            $decoded = json_decode($result->output(), true);

            return ! (is_array($decoded) && ($decoded['match'] ?? null) === false);
        } finally {
            if (is_resource($source)) {
                fclose($source);
            }

            if (is_file($localPath)) {
                unlink($localPath);
            }
        }
    }

    private function guestCanAccessInquiry(Request $request, int $inquiryId): bool
    {
        return in_array($inquiryId, $request->session()->get('guest_inquiry_ids', []), true);
    }
}
