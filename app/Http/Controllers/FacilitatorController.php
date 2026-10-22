<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationApprovedEmail;
use App\Mail\InquiryReplyEmail;
use App\Models\DocumentTemplate;
use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\InquiryRequirense;
use App\Models\ReassessmentRequest;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\ServiceTranslation;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use App\Services\DocumentOcrVerifier;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FacilitatorController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'resident')->count();
        $totalServices = GovernmentService::count();
        $totalAssessments = EligibilityAssessment::count();
        $totalInquiries = UserInquiry::count();
        $pendingInquiries = UserInquiry::where('status', 'pending')->count();

        $totalApplications = UserChecklist::count();
        $pendingApplications = UserChecklist::where('status', 'pending')->count();
        $approvedApplications = UserChecklist::where('status', 'approved')->count();
        $rejectedApplications = UserChecklist::where('status', 'rejected')->count();

        $pendingReassessments = ReassessmentRequest::where('status', 'pending')->count();

        // Get application counts by service
        $servicesBreakdown = GovernmentService::withCount('checklists')
            ->orderBy('checklists_count', 'desc')
            ->limit(5)
            ->get();

        // Get recent applications (checklists)
        $recentApplications = UserChecklist::with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('facilitator.dashboard', compact(
            'totalUsers',
            'totalServices',
            'totalAssessments',
            'totalInquiries',
            'pendingInquiries',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'pendingReassessments',
            'servicesBreakdown',
            'recentApplications'
        ));
    }

    // --- Services CRUD ---
    public function services()
    {
        $services = GovernmentService::withCount(['requirements', 'eligibilityQuestions'])->get();

        return view('facilitator.services.index', compact('services'));
    }

    public function createService()
    {
        $categories = ServiceCategory::all();

        return view('facilitator.services.create', compact('categories'));
    }

    public function translateService(Request $request)
    {
        $request->validate([
            'text' => ['nullable', 'string', 'max:5000'],
        ]);

        $text = (string) $request->input('text', '');

        $ceb = $this->autoTranslateText($text, 'ceb');
        $fil = $this->autoTranslateText($text, 'fil');

        return response()->json([
            'success' => true,
            'ceb' => $ceb,
            'fil' => $fil,
        ]);
    }

    private function autoTranslateText(string $text, string $targetLang): string
    {
        if (empty(trim($text))) {
            return '';
        }

        $dictionary = [
            'ceb' => [
                'Educational Assistance' => 'Tabang sa Edukasyon',
                'Medical Assistance' => 'Tabang sa Medikal',
                'Burial Assistance' => 'Tabang sa Paglubong',
                'Transportation Assistance' => 'Tabang sa Transportasyon',
                'Employment Assistance' => 'Tabang sa Trabaho',
            ],
            'fil' => [
                'Educational Assistance' => 'Tulong sa Edukasyon',
                'Medical Assistance' => 'Tulong Medikal',
                'Burial Assistance' => 'Tulong sa Libing',
                'Transportation Assistance' => 'Tulong sa Transportasyon',
                'Employment Assistance' => 'Tulong sa Trabaho',
            ],
        ];

        if (isset($dictionary[$targetLang][$text])) {
            return $dictionary[$targetLang][$text];
        }

        if ($targetLang === 'ceb') {
            return str_ireplace(
                ['Educational Assistance', 'Medical Assistance', 'Burial Assistance', 'Transportation Assistance', 'Employment Assistance', 'Submit required documents', 'Complete the Eligibility Assessment', 'Wait for validation', 'Claim financial assistance'],
                ['Tabang sa Edukasyon', 'Tabang sa Medikal', 'Tabang sa Paglubong', 'Tabang sa Transportasyon', 'Tabang sa Trabaho', 'Isumite ang gikinahanglan nga mga dokumento', 'Kampletoha ang Eligibility Assessment', 'Paghulat sa pag-validate ug pag-apruba', 'Kuhaa ang pinansyal nga tabang'],
                $text
            );
        }

        if ($targetLang === 'fil') {
            return str_ireplace(
                ['Educational Assistance', 'Medical Assistance', 'Burial Assistance', 'Transportation Assistance', 'Employment Assistance', 'Submit required documents', 'Complete the Eligibility Assessment', 'Wait for validation', 'Claim financial assistance'],
                ['Tulong sa Edukasyon', 'Tulong Medikal', 'Tulong sa Libing', 'Tulong sa Transportasyon', 'Tulong sa Trabaho', 'Isumite ang mga kinakailangang dokumento', 'Kumpletuhin ang Eligibility Assessment', 'Maghintay para sa pag-apruba', 'Kuning ang tulong na pinansyal'],
                $text
            );
        }

        return $text;
    }

    public function storeService(Request $request)
    {
        $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ceb' => ['nullable', 'string', 'max:255'],
            'name_fil' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_ceb' => ['nullable', 'string'],
            'description_fil' => ['nullable', 'string'],
            'procedure_en' => ['required', 'string'],
            'procedure_ceb' => ['nullable', 'string'],
            'procedure_fil' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:service_categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $nameCeb = $request->name_ceb ?: $this->autoTranslateText($request->name_en, 'ceb');
        $nameFil = $request->name_fil ?: $this->autoTranslateText($request->name_en, 'fil');
        $descCeb = $request->description_ceb ?: $this->autoTranslateText($request->description_en, 'ceb');
        $descFil = $request->description_fil ?: $this->autoTranslateText($request->description_en, 'fil');
        $procCeb = $request->procedure_ceb ?: $this->autoTranslateText($request->procedure_en, 'ceb');
        $procFil = $request->procedure_fil ?: $this->autoTranslateText($request->procedure_en, 'fil');

        $service = GovernmentService::create([
            'category_id' => $request->category_id,
            'service_name' => $request->name_en,
            'description' => $request->description_en,
            'procedure' => $request->procedure_en,
            'icon' => $request->icon,
        ]);

        // Save Translations: English
        ServiceTranslation::create([
            'service_id' => $service->id,
            'language_code' => 'en',
            'service_name' => $request->name_en,
            'description' => $request->description_en,
            'procedure' => $request->procedure_en,
        ]);

        // Save Translations: Cebuano (Auto-translated if left empty)
        ServiceTranslation::create([
            'service_id' => $service->id,
            'language_code' => 'ceb',
            'service_name' => $nameCeb,
            'description' => $descCeb,
            'procedure' => $procCeb,
        ]);

        // Save Translations: Filipino (Auto-translated if left empty)
        ServiceTranslation::create([
            'service_id' => $service->id,
            'language_code' => 'fil',
            'service_name' => $nameFil,
            'description' => $descFil,
            'procedure' => $procFil,
        ]);

        Cache::forget('services_all');
        Cache::forget('categories_all');

        return redirect()->route('facilitator.services')->with('success', 'Service created successfully.');
    }

    public function editService(GovernmentService $service)
    {
        $categories = ServiceCategory::all();

        return view('facilitator.services.edit', compact('service', 'categories'));
    }

    public function updateService(Request $request, GovernmentService $service)
    {
        $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ceb' => ['nullable', 'string', 'max:255'],
            'name_fil' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_ceb' => ['nullable', 'string'],
            'description_fil' => ['nullable', 'string'],
            'procedure_en' => ['required', 'string'],
            'procedure_ceb' => ['nullable', 'string'],
            'procedure_fil' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:service_categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $nameCeb = $request->name_ceb ?: $this->autoTranslateText($request->name_en, 'ceb');
        $nameFil = $request->name_fil ?: $this->autoTranslateText($request->name_en, 'fil');
        $descCeb = $request->description_ceb ?: $this->autoTranslateText($request->description_en, 'ceb');
        $descFil = $request->description_fil ?: $this->autoTranslateText($request->description_en, 'fil');
        $procCeb = $request->procedure_ceb ?: $this->autoTranslateText($request->procedure_en, 'ceb');
        $procFil = $request->procedure_fil ?: $this->autoTranslateText($request->procedure_en, 'fil');

        $service->update([
            'category_id' => $request->category_id,
            'service_name' => $request->name_en,
            'description' => $request->description_en,
            'procedure' => $request->procedure_en,
            'icon' => $request->icon,
        ]);

        // Update/create English translation
        ServiceTranslation::updateOrCreate([
            'service_id' => $service->id,
            'language_code' => 'en',
        ], [
            'service_name' => $request->name_en,
            'description' => $request->description_en,
            'procedure' => $request->procedure_en,
        ]);

        // Update/create Cebuano translation
        ServiceTranslation::updateOrCreate([
            'service_id' => $service->id,
            'language_code' => 'ceb',
        ], [
            'service_name' => $nameCeb,
            'description' => $descCeb,
            'procedure' => $procCeb,
        ]);

        // Update/create Filipino translation
        ServiceTranslation::updateOrCreate([
            'service_id' => $service->id,
            'language_code' => 'fil',
        ], [
            'service_name' => $nameFil,
            'description' => $descFil,
            'procedure' => $procFil,
        ]);

        Cache::forget('services_all');
        Cache::forget('categories_all');

        return redirect()->route('facilitator.services')->with('success', 'Service updated successfully.');
    }

    public function destroyService(GovernmentService $service)
    {
        $service->delete();
        Cache::forget('services_all');
        Cache::forget('categories_all');

        return redirect()->route('facilitator.services')->with('success', 'Service deleted successfully.');
    }

    // --- Requirements CRUD ---
    public function requirements()
    {
        $requirements = ServiceRequirement::with('service')->get();
        $services = GovernmentService::all();

        return view('facilitator.requirements.index', compact('requirements', 'services'));
    }

    public function storeRequirement(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:government_services,id'],
            'requirement_text_en' => ['required', 'string', 'max:255'],
            'requirement_text_ceb' => ['required', 'string', 'max:255'],
            'requirement_text_fil' => ['required', 'string', 'max:255'],
            'is_required' => ['boolean'],
        ]);

        ServiceRequirement::create([
            'service_id' => $request->service_id,
            'requirement_text' => [
                'en' => $request->requirement_text_en,
                'ceb' => $request->requirement_text_ceb,
                'fil' => $request->requirement_text_fil,
            ],
            'is_required' => $request->has('is_required') || $request->input('is_required') == '1',
        ]);

        return back()->with('success', 'Requirement created successfully.');
    }

    public function destroyRequirement(ServiceRequirement $requirement)
    {
        $requirement->delete();

        return back()->with('success', 'Requirement deleted successfully.');
    }

    // --- Reassessment Requests ---
    public function reassessments()
    {
        $requests = ReassessmentRequest::with(['user', 'service'])->orderBy('created_at', 'desc')->get();

        return view('facilitator.reassessments.index', compact('requests'));
    }

    public function updateReassessmentStatus(Request $request, ReassessmentRequest $reassessment)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($reassessment->status !== 'pending') {
            return back()->with('error', 'This reassessment request was already '.$reassessment->status.'.');
        }

        $reassessment->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            EligibilityAssessment::where('user_id', $reassessment->user_id)
                ->where('service_id', $reassessment->service_id)
                ->delete();
        }

        if ($reassessment->user) {
            app(SmsService::class)->sendReassessmentUpdate(
                $reassessment->user,
                $reassessment->service?->service_name ?? 'Government Assistance Program',
                $request->status
            );
        }

        return back()->with('success', 'Reassessment request '.$request->status.' successfully.');
    }

    // --- Eligibility Questions CRUD ---
    public function eligibility()
    {
        $questions = EligibilityQuestion::with('service')->get();
        $services = GovernmentService::all();

        return view('facilitator.eligibility.index', compact('questions', 'services'));
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:government_services,id'],
            'question_text' => ['required', 'string'],
            'type' => ['required', 'string', 'in:boolean,number,text'],
            ...$this->eligibilityRuleRules($request),
            'is_required' => ['sometimes', 'boolean'],
        ]);

        try {
            $output = Process::timeout(30)->run([
                'python3',
                base_path('scripts/translate.py'),
                '--text',
                $request->question_text,
            ])->output();
        } catch (\Throwable $exception) {
            Log::warning('Question translation failed: '.$exception->getMessage());
            $output = null;
        }

        $translations = ['en' => $request->question_text, 'ceb' => '', 'fil' => ''];

        if ($output) {
            $result = json_decode($output, true);
            if (is_array($result) && ! isset($result['error'])) {
                $translations = array_merge($translations, $result);
            }
        }

        $translations['ceb'] = $translations['ceb'] ?: $request->question_text;
        $translations['fil'] = $translations['fil'] ?: $request->question_text;

        EligibilityQuestion::create([
            'service_id' => $request->service_id,
            'question_text_en' => $translations['en'],
            'question_text_ceb' => $translations['ceb'],
            'question_text_fil' => $translations['fil'],
            'type' => $request->type,
            ...$this->normalizedEligibilityRule($request),
            'is_required' => $request->boolean('is_required', true),
        ]);

        return back()->with('success', 'Question created and automatically translated successfully.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function eligibilityRuleRules(Request $request): array
    {
        return match ($request->input('type')) {
            'boolean' => ['expected_value' => ['required', 'in:true,false']],
            'number' => [
                'expected_value' => ['required', 'numeric'],
                'operator' => ['required', 'in:=,==,<=,>=,<,>'],
            ],
            default => ['expected_value' => ['nullable', 'string', 'max:255']],
        };
    }

    /**
     * @return array{expected_value: string, operator: string}
     */
    private function normalizedEligibilityRule(Request $request): array
    {
        $type = $request->input('type');

        return [
            'expected_value' => $type === 'text' ? trim((string) $request->input('expected_value')) : (string) $request->input('expected_value'),
            'operator' => $type === 'number' ? (string) $request->input('operator') : '=',
        ];
    }

    public function editQuestion(EligibilityQuestion $question)
    {
        $services = GovernmentService::all();

        return view('facilitator.eligibility.edit', compact('question', 'services'));
    }

    public function updateQuestion(Request $request, EligibilityQuestion $question)
    {
        $request->validate([
            'service_id' => ['required', 'exists:government_services,id'],
            'question_text_en' => ['required', 'string'],
            'question_text_ceb' => ['required', 'string'],
            'question_text_fil' => ['required', 'string'],
            'type' => ['required', 'string', 'in:boolean,number,text'],
            ...$this->eligibilityRuleRules($request),
            'is_required' => ['sometimes', 'boolean'],
        ]);

        $question->update([
            ...$request->only([
                'service_id',
                'question_text_en',
                'question_text_ceb',
                'question_text_fil',
                'type',
            ]),
            ...$this->normalizedEligibilityRule($request),
            'is_required' => $request->has('is_required')
                ? $request->boolean('is_required')
                : $question->is_required,
        ]);

        return redirect()->route('facilitator.eligibility')->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(EligibilityQuestion $question)
    {
        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    // --- Residents list ---
    public function users(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $residents = User::where('role', 'resident');

        $stats = [
            'total' => (clone $residents)->count(),
            'withId' => (clone $residents)->whereNotNull('valid_id_path')->count(),
            'withoutId' => (clone $residents)->whereNull('valid_id_path')->count(),
            'completeProfiles' => (clone $residents)->whereNotNull('contact_number')->where('contact_number', '!=', '')->whereNotNull('dob')->count(),
        ];

        $users = $residents
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('contact_number', 'like', '%'.$search.'%');
            }))
            ->orderBy('name', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('facilitator.users.index', compact('users', 'stats', 'search'));
    }

    // --- Assessments History ---
    public function assessments(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $stats = [
            'total' => EligibilityAssessment::count(),
            'eligible' => EligibilityAssessment::where('status', 'eligible')->count(),
            'ineligible' => EligibilityAssessment::where('status', 'ineligible')->count(),
        ];

        $assessments = EligibilityAssessment::with(['user', 'service'])
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'))
                    ->orWhereHas('service', fn ($s) => $s->where('service_name', 'like', '%'.$search.'%'));
            }))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('facilitator.assessments.index', compact('assessments', 'stats', 'search'));
    }

    // --- Submitted Applications ---
    public function applications(Request $request)
    {
        $status = in_array($request->query('status'), ['pending', 'approved', 'rejected'], true)
            ? $request->query('status')
            : null;
        $search = trim((string) $request->query('q', ''));

        $baseQuery = UserChecklist::query();

        if ($request->filled('service_id')) {
            $baseQuery->where('service_id', $request->service_id);
        }

        if ($search !== '') {
            $baseQuery->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Review queue: pending applications first (oldest first), then everything else newest first.
        $applications = (clone $baseQuery)
            ->with(['user', 'service'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN status = 'pending' THEN created_at END ASC")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $services = GovernmentService::all();

        return view('facilitator.applications.index', compact('applications', 'services', 'statusCounts', 'status', 'search'));
    }

    public function showApplication(UserChecklist $checklist)
    {
        $requirements = $checklist->service->requirements;
        $uploadedDocs = UserChecklistItem::where('checklist_id', $checklist->id)
            ->get()
            ->keyBy('requirement_id');

        $nextPendingApplication = UserChecklist::where('status', 'pending')
            ->where('id', '!=', $checklist->id)
            ->orderBy('created_at')
            ->first();

        return view('facilitator.applications.show', compact('checklist', 'requirements', 'uploadedDocs', 'nextPendingApplication'));
    }

    public function viewDocument(UserChecklistItem $item)
    {
        if (! $item->file_path) {
            abort(404, 'No document uploaded for this item.');
        }

        $disk = config('filesystems.default');

        if (! Storage::disk($disk)->exists($item->file_path)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk($disk)->response($item->file_path);
    }

    public function updateApplicationStatus(Request $request, UserChecklist $checklist)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
            'remarks' => ['nullable', 'string', 'required_if:status,rejected'],
        ], [
            'remarks.required_if' => 'Please give the resident a reason when rejecting an application.',
        ]);

        if ($request->status === 'approved' && $this->hasUnapprovedRequiredDocuments($checklist)) {
            return back()->withErrors([
                'status' => 'Cannot approve: all required documents must be uploaded and approved first.',
            ]);
        }

        $hasChanged = $checklist->status !== $request->status
            || (string) $checklist->remarks !== (string) $request->remarks;

        $checklist->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        // Resubmitting an unchanged decision must not notify the resident a second time.
        if (! $hasChanged) {
            return redirect()->route('facilitator.applications')->with('success', 'No changes were made to this application.');
        }

        if ($request->status === 'approved') {
            try {
                if ($checklist->user?->email) {
                    Mail::to($checklist->user->email)->send(new ApplicationApprovedEmail($checklist));
                }
            } catch (\Exception $e) {
                // Log or handle mail sending failures gracefully, don't block request in development
                Log::error('Failed to send approval email: '.$e->getMessage());
            }
        }

        if ($checklist->user) {
            app(SmsService::class)->sendApplicationUpdate(
                $checklist->user,
                $checklist->service?->service_name ?? 'Government Assistance Program',
                $request->status,
                $request->remarks
            );
        }

        return redirect()->route('facilitator.applications')->with('success', 'Application status updated and the resident has been notified.');
    }

    private function hasUnapprovedRequiredDocuments(UserChecklist $checklist): bool
    {
        $approvedItems = $checklist->items()
            ->where('is_submitted', true)
            ->whereNotNull('file_path')
            ->where('status', 'approved')
            ->pluck('requirement_id');

        return $checklist->requiredRequirements()->whereNotIn('id', $approvedItems->all())->isNotEmpty();
    }

    public function updateItemStatus(Request $request, UserChecklistItem $item)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
        ]);

        $item->update(['status' => $request->status]);

        return back()->with('success', 'Document status updated.');
    }

    public function batchUpdateChecklistItems(Request $request, UserChecklist $checklist)
    {
        $request->validate([
            'statuses' => ['required', 'array'],
            'statuses.*' => ['required', 'string', 'in:pending,approved,rejected'],
        ]);

        foreach ($request->statuses as $itemId => $status) {
            UserChecklistItem::where('id', $itemId)
                ->where('checklist_id', $checklist->id)
                ->update(['status' => $status]);
        }

        return back()->with('success', 'All document statuses updated successfully.');
    }

    public function autoVerifyItem(UserChecklistItem $item, DocumentOcrVerifier $documentOcrVerifier)
    {
        $checklist = $item->checklist;

        $template = DocumentTemplate::query()
            ->where('service_id', $checklist->service_id)
            ->where('requirement_id', $item->requirement_id)
            ->first();

        if (! $item->file_path || ! $template) {
            return back()->with('error', 'This document has no template set up, so it cannot be checked automatically. Review it by eye instead.');
        }

        $verification = $documentOcrVerifier->verify(
            config('filesystems.default'),
            $item->file_path,
            $template->file_path,
            $template->name_en ?: $template->name_ceb,
        );

        $item->update([
            'review_result' => $verification['status'],
            'review_score' => $verification['score'],
            'review_note' => $documentOcrVerifier->describe($verification),
            'status' => $verification['status'] === 'verified' ? 'approved' : $item->status,
        ]);

        return match ($verification['status']) {
            'verified' => back()->with('success', 'Document verified and approved.'),
            'mismatch' => back()->with('error', 'Possible wrong document. '.$documentOcrVerifier->describe($verification)),
            default => back()->with('success', 'Checked again. A manual review is still needed: '.$documentOcrVerifier->describe($verification)),
        };
    }

    // --- Inquiries Management ---
    public function inquiries()
    {
        $inquiries = UserInquiry::with(['user.checklists.service', 'user.inquiries', 'service', 'responses.responder'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->unique(function ($inq) {
                return $inq->user_id ? 'user_'.$inq->user_id : 'guest_'.strtolower($inq->guest_email ?? 'unknown');
            })
            ->values();

        return view('facilitator.inquiries.index', compact('inquiries'));
    }

    public function getMessages(UserInquiry $inquiry)
    {
        return response()->json([
            'success' => true,
            'inquiry' => $inquiry->load(['user.checklists.service', 'user.inquiries', 'service', 'responses.responder']),
        ]);
    }

    public function replyInquiry(Request $request, UserInquiry $inquiry)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'status' => ['nullable', 'in:in_progress,resolved'],
        ]);

        if ($inquiry->status === 'closed') {
            $message = 'This inquiry ticket is closed. Reopen it before replying.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['message' => $message]);
        }

        $reply = InquiryRequirense::create([
            'inquiry_id' => $inquiry->id,
            'requireent_text' => $request->message,
            'responded_by' => auth()->id(),
        ]);

        $inquiry->update(['status' => $request->input('status', 'resolved')]);

        // Send email reply to resident
        $email = $inquiry->user ? $inquiry->user->email : $inquiry->guest_email;
        if ($email) {
            try {
                Mail::to($email)->send(new InquiryReplyEmail($inquiry, $request->message));
            } catch (\Exception $e) {
                Log::error('Failed to send manual inquiry reply email: '.$e->getMessage());
            }
        }

        // Send SMS reply alert to resident if contact number available
        if ($inquiry->user) {
            app(SmsService::class)->sendInquiryReply($inquiry->user, $request->message);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'reply' => $reply->load('responder'),
                'inquiry' => $inquiry->fresh(['user.checklists.service', 'user.inquiries', 'service', 'responses.responder']),
            ]);
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function updateInquiryStatus(Request $request, UserInquiry $inquiry)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,resolved,closed'],
        ]);

        $inquiry->update(['status' => $request->status]);

        return back()->with('success', 'Inquiry status updated successfully.');
    }

    public function deleteInquiry(UserInquiry $inquiry)
    {
        $inquiry->responses()->delete();
        $inquiry->delete();

        return redirect()->route('facilitator.inquiries')->with('success', 'Inquiry conversation deleted successfully.');
    }

    // --- Profile Settings ---
    public function editProfile()
    {
        return view('facilitator.profile.edit', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'contact_number' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;

        if ($file = $request->file('avatar')) {
            $path = $file->store('avatars', config('filesystems.default'));
            $user->avatar = $path;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    // --- Users CRUD extensions ---
    public function createUser()
    {
        return view('facilitator.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->merge([
            'contact_number' => User::normalizeContactNumber((string) $request->input('contact_number')) ?? $request->input('contact_number'),
            'email' => $request->filled('email') ? strtolower(trim((string) $request->input('email'))) : null,
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'contact_number' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,contact_number'],
            'dob' => ['nullable', 'date'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'resident',
            'contact_number' => $request->contact_number,
            'dob' => $request->dob,
        ]);

        return redirect()->route('facilitator.users')->with('success', 'User created successfully.');
    }

    public function showUser(User $user)
    {
        $user->load(['checklists.service', 'inquiries']);

        return view('facilitator.users.show', compact('user'));
    }

    public function viewValidId(User $user)
    {
        if (! $user->valid_id_path) {
            abort(404, 'User does not have a valid ID.');
        }

        $disk = config('filesystems.default');

        if (! Storage::disk($disk)->exists($user->valid_id_path)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk($disk)->response($user->valid_id_path);
    }

    public function editUser(User $user)
    {
        return view('facilitator.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->merge([
            'contact_number' => User::normalizeContactNumber((string) $request->input('contact_number')) ?? $request->input('contact_number'),
            'email' => $request->filled('email') ? strtolower(trim((string) $request->input('email'))) : null,
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'contact_number' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,contact_number,'.$user->id],
            'dob' => ['nullable', 'date'],
        ]);

        $user->update($request->only('name', 'email', 'contact_number', 'dob'));

        return redirect()->route('facilitator.users')->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id() || ! $user->isResident()) {
            return redirect()->route('facilitator.users')->with('error', 'Only resident accounts can be deleted.');
        }

        $disk = config('filesystems.default');
        $user->load('checklists.items');

        foreach ($user->checklists as $checklist) {
            foreach ($checklist->items as $checklistItem) {
                if ($checklistItem->file_path) {
                    Storage::disk($disk)->delete($checklistItem->file_path);
                }
            }
        }

        foreach ([$user->valid_id_path, $user->avatar] as $storedPath) {
            if ($storedPath) {
                Storage::disk($disk)->delete($storedPath);
            }
        }

        $user->delete();

        return redirect()->route('facilitator.users')->with('success', 'User deleted successfully.');
    }

    // --- Assessments Detail View ---
    public function showAssessment(EligibilityAssessment $assessment)
    {
        $assessment->load(['user', 'service', 'answers']);

        return view('facilitator.assessments.show', compact('assessment'));
    }

    public function destroyAssessment(EligibilityAssessment $assessment)
    {
        $assessment->delete();

        return redirect()->route('facilitator.assessments')->with('success', 'Assessment deleted successfully.');
    }

    // --- Applications CRUD extensions ---
    public function createApplication()
    {
        $users = User::where('role', 'resident')->get();
        $services = GovernmentService::all();

        return view('facilitator.applications.create', compact('users', 'services'));
    }

    public function storeApplication(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'resident')],
            'service_id' => ['required', 'exists:government_services,id'],
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
        ]);
        UserChecklist::create($validated);

        return redirect()->route('facilitator.applications')->with('success', 'Application created successfully.');
    }

    public function editApplication(UserChecklist $checklist)
    {
        $users = User::where('role', 'resident')->get();
        $services = GovernmentService::all();

        return view('facilitator.applications.edit', compact('checklist', 'users', 'services'));
    }

    public function updateApplication(Request $request, UserChecklist $checklist)
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'resident')],
            'service_id' => ['required', 'exists:government_services,id'],
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
        ]);

        if ($validated['status'] === 'approved'
            && $checklist->status !== 'approved'
            && (int) $validated['service_id'] === $checklist->service_id
            && $this->hasUnapprovedRequiredDocuments($checklist)) {
            return back()->withInput()->withErrors([
                'status' => 'Cannot approve: all required documents must be uploaded and approved first.',
            ]);
        }

        $checklist->update($validated);

        return redirect()->route('facilitator.applications')->with('success', 'Application updated successfully.');
    }

    public function destroyApplication(UserChecklist $checklist)
    {
        $checklist->delete();

        return redirect()->route('facilitator.applications')->with('success', 'Application deleted successfully.');
    }

    // --- Document Templates Management ---
    public function templates()
    {
        $services = GovernmentService::with(['requirements.template'])->get();

        return view('facilitator.templates.index', compact('services'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:government_services,id',
            'requirement_id' => 'required|exists:service_requirements,id',
            'keywords' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:jpg,png,jpeg|max:5120',
        ]);

        abort_unless(
            ServiceRequirement::query()
                ->whereKey($request->integer('requirement_id'))
                ->where('service_id', $request->integer('service_id'))
                ->exists(),
            404,
        );

        $disk = config('filesystems.default');
        $path = $request->file('template_file')->store('templates', $disk);

        // Overwrite or create template for this requirement
        $template = DocumentTemplate::where('requirement_id', $request->requirement_id)->first();
        if ($template) {
            // Delete old file
            if ($template->file_path && \Storage::disk($disk)->exists($template->file_path)) {
                \Storage::disk($disk)->delete($template->file_path);
            }
            $template->update([
                'service_id' => $request->service_id,
                'name_en' => $request->keywords,
                'name_ceb' => $request->keywords,
                'description_en' => null,
                'description_ceb' => null,
                'file_path' => $path,
            ]);
        } else {
            DocumentTemplate::create([
                'service_id' => $request->service_id,
                'requirement_id' => $request->requirement_id,
                'name_en' => $request->keywords,
                'name_ceb' => $request->keywords,
                'description_en' => null,
                'description_ceb' => null,
                'file_path' => $path,
            ]);
        }

        return redirect()->route('facilitator.templates')->with('success', 'Document template set successfully.');
    }

    public function destroyTemplate(DocumentTemplate $template)
    {
        $disk = config('filesystems.default');
        if ($template->file_path && \Storage::disk($disk)->exists($template->file_path)) {
            \Storage::disk($disk)->delete($template->file_path);
        }
        $template->delete();

        return redirect()->route('facilitator.templates')->with('success', 'Document template deleted successfully.');
    }
}
