<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerCommonInquiryQuestionRequest;
use App\Models\AssessmentAnswer;
use App\Models\CommonQuestion;
use App\Models\DocumentTemplate;
use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\InquiryRequirense;
use App\Models\ReassessmentRequest;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use App\Services\DocumentOcrVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            }])->get();
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
            return redirect()->route('resident.eligibility')->with('error', 'You have already taken the assessment for this program. You can request a reassessment if necessary.');
        }

        $questions = $service->eligibilityQuestions;

        return view('resident.eligibility.assess', compact('service', 'questions'));
    }

    public function processAssessForm(Request $request, GovernmentService $service)
    {
        $user = Auth::user();

        if (EligibilityAssessment::where('user_id', $user->id)->where('service_id', $service->id)->exists()) {
            return redirect()->route('resident.eligibility')->with('error', 'You have already taken the assessment for this program.');
        }

        $questions = $service->eligibilityQuestions;

        $rules = [];
        $messages = [];

        foreach ($questions as $question) {
            $inputName = 'question_'.$question->id;
            $presenceRule = $question->is_required ? 'required' : 'nullable';

            $rules[$inputName] = match ($question->type) {
                'boolean' => [$presenceRule, 'in:true,false'],
                'number' => $this->isHouseholdIncomeQuestion($question)
                    ? [$presenceRule, 'numeric', 'between:2000,15000']
                    : [$presenceRule, 'numeric'],
                default => [$presenceRule, 'string', 'max:5000'],
            };

            if ($this->isHouseholdIncomeQuestion($question)) {
                $messages[$inputName.'.between'] = 'Household income must be between ₱2,000 and ₱15,000.';
            }
        }

        $validated = $request->validate($rules, $messages);
        $answers = [];
        $isEligible = true;

        foreach ($questions as $q) {
            $inputName = 'question_'.$q->id;
            $userAnswer = $validated[$inputName] ?? null;

            $questionText = $q->question_text;
            $answers[$questionText] = $userAnswer ?? 'No Answer';

            if (! $q->is_required) {
                continue;
            }

            // Check eligibility rule
            if ($q->type === 'boolean') {
                $expected = filter_var($q->expected_value, FILTER_VALIDATE_BOOLEAN);
                $actual = filter_var($userAnswer, FILTER_VALIDATE_BOOLEAN);

                if (($q->operator === 'equals' || $q->operator === '==') && $actual !== $expected) {
                    $isEligible = false;
                }
            } elseif ($q->type === 'number') {
                $expected = floatval($q->expected_value);
                $actual = floatval($userAnswer);

                if ($this->isHouseholdIncomeQuestion($q)) {
                    $isEligible = $isEligible && $actual >= 2000 && $actual <= 15000;
                } elseif (($q->operator === 'less_than_or_equal' || $q->operator === '<=') && $actual > $expected) {
                    $isEligible = false;
                } elseif (($q->operator === 'less_than' || $q->operator === '<') && $actual >= $expected) {
                    $isEligible = false;
                } elseif (($q->operator === 'greater_than_or_equal' || $q->operator === '>=') && $actual < $expected) {
                    $isEligible = false;
                } elseif (($q->operator === 'greater_than' || $q->operator === '>') && $actual <= $expected) {
                    $isEligible = false;
                } elseif (($q->operator === 'equals' || $q->operator === '==') && $actual != $expected) {
                    $isEligible = false;
                }
            }
        }

        $assessment = EligibilityAssessment::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => $isEligible ? 'eligible' : 'ineligible',
        ]);

        foreach ($answers as $qText => $ansText) {
            AssessmentAnswer::create([
                'assessment_id' => $assessment->id,
                'question' => $qText,
                'answer' => $ansText ?? 'No Answer',
            ]);
        }

        return redirect()->route('resident.eligibility.result', ['refNo' => $assessment->id]);
    }

    private function isHouseholdIncomeQuestion(EligibilityQuestion $question): bool
    {
        return Str::of($question->question_text_en)
            ->lower()
            ->containsAll(['household', 'income']);
    }

    public function showAssessResult($refNo)
    {
        $assessment = EligibilityAssessment::with(['service', 'answers'])
            ->where('user_id', Auth::id())
            ->where('id', $refNo)
            ->firstOrFail();

        return view('resident.eligibility.result', compact('assessment'));
    }

    public function requestReassessment(Request $request, GovernmentService $service)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $existingRequest = ReassessmentRequest::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'You already have a pending reassessment request for this program.');
        }

        ReassessmentRequest::create([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your reassessment request has been submitted and is waiting for Admin approval.');
    }

    public function checklist(GovernmentService $service)
    {
        $user = Auth::user();

        if (! $user->isProfileComplete()) {
            return redirect()->route('resident.profile.edit')->with('error', 'Please complete your profile (Date of Birth, Address, Civil Status, Contact Number, and Valid ID) before availing a service.');
        }

        if (! $user->isEligibleFor($service->id)) {
            return redirect()->route('resident.eligibility')->with('error', 'You must first qualify through the Eligibility Assessment.');
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
                if (! $item || ! $item->is_submitted || ! $item->file_path) {
                    $allMandatoryUploaded = false;
                    break;
                }
            }
        }

        $alreadyApplied = $checklist && in_array($checklist->status, ['pending', 'approved', 'rejected']);

        return view('resident.eligibility.checklist', compact('service', 'requirements', 'uploadedDocs', 'allMandatoryUploaded', 'alreadyApplied', 'checklist'));
    }

    public function setApplicationType(Request $request, GovernmentService $service)
    {
        $request->validate([
            'application_type' => 'required|in:new,renewal',
        ]);

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

            return back()->with('success', 'Application type updated successfully.');
        }

        return back()->with('error', 'Cannot change application type after submission.');
    }

    public function uploadDocument(
        Request $request,
        GovernmentService $service,
        ServiceRequirement $requirement,
        DocumentOcrVerifier $documentOcrVerifier,
    ) {
        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,png,jpeg,doc,docx', 'max:5120'],
        ]);

        abort_unless($requirement->service_id === $service->id, 404);

        if ($file = $request->file('document')) {
            $disk = config('filesystems.default');
            $folderName = Str::slug($service->name);
            $path = $file->store('documents/'.$folderName, $disk);

            $template = DocumentTemplate::query()
                ->where('service_id', $service->id)
                ->where('requirement_id', $requirement->id)
                ->first();

            $status = 'pending';

            if ($template) {
                $verification = $documentOcrVerifier->verify(
                    $disk,
                    $path,
                    $template->file_path,
                    $template->name_en ?: $template->name_ceb,
                );

                if (! $verification['matched']) {
                    Storage::disk($disk)->delete($path);

                    return back()->with('error', $verification['message']);
                }

                $status = 'approved';
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
            ]);

            if ($existingChecklistItem?->file_path && $existingChecklistItem->file_path !== $path) {
                Storage::disk($disk)->delete($existingChecklistItem->file_path);
            }
        }

        return back()->with('success', $template
            ? 'Document verified against the template and uploaded successfully.'
            : 'Document uploaded successfully and is pending facilitator review.');
    }

    public function apply(GovernmentService $service)
    {
        $user = Auth::user();

        if (! $user->isProfileComplete()) {
            return redirect()->route('resident.profile.edit')->with('error', 'Please complete your profile (Date of Birth, Address, Civil Status, Contact Number, and Valid ID) before submitting an application.');
        }

        if (! $user->isEligibleFor($service->id)) {
            return back()->with('error', 'You must be eligible for this service before you can apply.');
        }

        $checklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if (! $checklist) {
            return back()->with('error', 'No checklist found. Please upload documents first.');
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
                    ->where('is_submitted', true)
                    ->first();
                if (! $item || ! $item->file_path) {
                    return back()->with('error', 'Please upload all mandatory documents.');
                }
            }
        }

        $checklist->update([
            'status' => 'pending',
        ]);

        return redirect()->route('resident.home')->with('success', 'Application submitted successfully!');
    }

    public function inquiry(Request $request)
    {
        if (Auth::check()) {
            $inquiries = UserInquiry::with(['service', 'responses.responder'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $inquiries = collect();
        }

        $services = GovernmentService::query()
            ->with(['commonQuestions' => fn ($query) => $query
                ->whereNotNull('answer_text')
                ->oldest()
                ->limit(5)])
            ->oldest()
            ->get();
        $templates = collect();

        return view('resident.inquiry.bot', compact('inquiries', 'services', 'templates'));
    }

    public function submitManualInquiry(Request $request)
    {
        $rules = [
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

        // Messenger-style behavior: Re-use single thread per user account or guest email
        if (! $inquiry) {
            if (Auth::check()) {
                $inquiry = UserInquiry::where('user_id', Auth::id())->latest('updated_at')->first();
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

            $inquiry->update(['status' => 'pending', 'updated_at' => now()]);
        } else {
            $inquiry = UserInquiry::create([
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
            $response = response()->json([
                'success' => true,
                'inquiry' => $inquiry->load(['service', 'responses.responder']),
            ]);

            return $response;
        }

        $redirect = back()->with('success', 'Your inquiry has been sent to the administrators.');

        return $redirect;
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

        $request->validate([
            'first_name' => ['nullable', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'middle_name' => ['nullable', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'last_name' => ['nullable', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'dob' => ['nullable', 'date', 'before_or_equal:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'civil_status' => ['nullable', Rule::in(['Single', 'Married', 'Widowed', 'Divorced', 'Live-in'])],
            'contact_number' => ['nullable', 'string', 'regex:/^(?:\+63|0)9\d{9}$/'],
            'valid_id' => ['nullable', 'file', 'mimes:pdf,jpg,png,jpeg', 'max:5120'],
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
        $user->address = $request->address;
        $user->civil_status = $request->civil_status;
        $user->contact_number = $request->contact_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($file = $request->file('valid_id')) {
            $disk = config('filesystems.default');
            $path = $file->store('valid_ids', $disk);

            // Automation: check if user name components match the ID
            $idPath = storage_path('app/public/'.$path);
            $scriptPath = base_path('scripts/verify_id.py');

            $firstName = $user->first_name ?: $request->first_name;
            $middleName = $user->middle_name ?: $request->middle_name;
            $lastName = $user->last_name ?: $request->last_name;

            if (! $firstName && ! $lastName && $user->name) {
                $nameParts = explode(' ', $user->name);
                $firstName = $nameParts[0] ?? '';
                $lastName = end($nameParts) ?? '';
            }

            $idMatches = true;
            if (file_exists($idPath)) {
                $command = 'python3 '.escapeshellarg($scriptPath).' '.escapeshellarg($idPath).' '.escapeshellarg($firstName ?: '').' '.escapeshellarg($middleName ?: '').' '.escapeshellarg($lastName ?: '');
                $output = shell_exec($command);

                if ($output) {
                    $result = json_decode($output, true);
                    if (is_array($result) && isset($result['match']) && $result['match'] === false) {
                        $idMatches = false;
                    }
                }
            }

            if (! $idMatches) {
                Storage::disk($disk)->delete($path);

                return back()->with('error', 'The uploaded ID does not match your registered account name.');
            }

            $user->valid_id_path = $path;
        }

        $user->save();

        return redirect()->route('resident.profile')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($file = $request->file('avatar')) {
            $path = $file->store('avatars', config('filesystems.default'));
            $user->avatar = $path;
            $user->save();
        }

        return back()->with('success', 'Avatar updated successfully.');
    }

    public function unlockChecklist(GovernmentService $service)
    {
        $checklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if ($checklist && $checklist->status === 'rejected') {
            $checklist->update(['status' => 'draft']);

            return back()->with('success', 'Application unlocked. You can now edit your documents and resubmit.');
        }

        return back()->with('error', 'Cannot edit this application.');
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

        $inquiry->update(['status' => 'pending']);

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

    private function guestCanAccessInquiry(Request $request, int $inquiryId): bool
    {
        return in_array($inquiryId, $request->session()->get('guest_inquiry_ids', []), true);
    }
}
