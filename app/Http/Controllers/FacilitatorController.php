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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $type = $request->input('type', 'name'); // name, description, procedure
        $text = $request->input('text', '');

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
            'name_sub' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_ceb' => ['nullable', 'string'],
            'description_fil' => ['nullable', 'string'],
            'description_sub' => ['nullable', 'string'],
            'procedure_en' => ['required', 'string'],
            'procedure_ceb' => ['nullable', 'string'],
            'procedure_fil' => ['nullable', 'string'],
            'procedure_sub' => ['nullable', 'string'],
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

        // Save Translations: Subanen (Manual Entry Only - Not Automated)
        if ($request->filled('name_sub') || $request->filled('description_sub') || $request->filled('procedure_sub')) {
            ServiceTranslation::create([
                'service_id' => $service->id,
                'language_code' => 'sub',
                'service_name' => $request->name_sub ?: '',
                'description' => $request->description_sub ?: '',
                'procedure' => $request->procedure_sub ?: '',
            ]);
        }

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
            'name_sub' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_ceb' => ['nullable', 'string'],
            'description_fil' => ['nullable', 'string'],
            'description_sub' => ['nullable', 'string'],
            'procedure_en' => ['required', 'string'],
            'procedure_ceb' => ['nullable', 'string'],
            'procedure_fil' => ['nullable', 'string'],
            'procedure_sub' => ['nullable', 'string'],
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

        // Update/create Subanen translation (Manual Entry Only - Not Automated)
        if ($request->filled('name_sub') || $request->filled('description_sub') || $request->filled('procedure_sub')) {
            ServiceTranslation::updateOrCreate([
                'service_id' => $service->id,
                'language_code' => 'sub',
            ], [
                'service_name' => $request->name_sub ?: '',
                'description' => $request->description_sub ?: '',
                'procedure' => $request->procedure_sub ?: '',
            ]);
        }

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

        $reassessment->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            EligibilityAssessment::where('user_id', $reassessment->user_id)
                ->where('service_id', $reassessment->service_id)
                ->delete();
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
            'expected_value' => ['required', 'string'],
            'operator' => ['required', 'string'],
        ]);

        $scriptPath = base_path('scripts/translate.py');
        $command = 'python3 '.escapeshellarg($scriptPath).' --text '.escapeshellarg($request->question_text);
        $output = shell_exec($command);

        $translations = ['en' => $request->question_text, 'ceb' => '', 'fil' => '', 'sub' => ''];

        if ($output) {
            $result = json_decode($output, true);
            if (! isset($result['error'])) {
                $translations = $result;
            }
        }

        EligibilityQuestion::create([
            'service_id' => $request->service_id,
            'question_text_en' => $translations['en'],
            'question_text_ceb' => $translations['ceb'],
            'question_text_fil' => $translations['fil'],
            'question_text_sub' => $translations['sub'],
            'type' => $request->type,
            'expected_value' => $request->expected_value,
            'operator' => $request->operator,
        ]);

        return back()->with('success', 'Question created and automatically translated successfully.');
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
            'question_text_sub' => ['required', 'string'],
            'type' => ['required', 'string', 'in:boolean,number,text'],
            'expected_value' => ['required', 'string'],
            'operator' => ['required', 'string'],
        ]);

        $question->update($request->all());

        return redirect()->route('facilitator.eligibility')->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(EligibilityQuestion $question)
    {
        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    // --- Residents list ---
    public function users()
    {
        $users = User::where('role', 'resident')->orderBy('name', 'asc')->get();

        return view('facilitator.users.index', compact('users'));
    }

    // --- Assessments History ---
    public function assessments()
    {
        $assessments = EligibilityAssessment::with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('facilitator.assessments.index', compact('assessments'));
    }

    // --- Submitted Applications ---
    public function applications(Request $request)
    {
        $query = UserChecklist::with(['user', 'service']);

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $applications = $query->orderBy('created_at', 'desc')->get();
        $services = GovernmentService::all();

        return view('facilitator.applications.index', compact('applications', 'services'));
    }

    public function showApplication(UserChecklist $checklist)
    {
        $requirements = $checklist->service->requirements;
        $uploadedDocs = UserChecklistItem::where('checklist_id', $checklist->id)
            ->get()
            ->keyBy('requirement_id');

        return view('facilitator.applications.show', compact('checklist', 'requirements', 'uploadedDocs'));
    }

    public function updateApplicationStatus(Request $request, UserChecklist $checklist)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
            'remarks' => ['nullable', 'string'],
        ]);

        $checklist->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        if ($request->status === 'approved') {
            try {
                Mail::to($checklist->user->email)->send(new ApplicationApprovedEmail($checklist));
            } catch (\Exception $e) {
                // Log or handle mail sending failures gracefully, don't block request in development
                Log::error('Failed to send approval email: '.$e->getMessage());
            }
        }

        return redirect()->route('facilitator.applications')->with('success', 'Application status updated.');
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

    public function downloadAllApplicationFiles(UserChecklist $checklist)
    {
        $uploadedDocs = UserChecklistItem::where('checklist_id', $checklist->id)
            ->whereNotNull('file_path')
            ->get();

        if ($uploadedDocs->isEmpty()) {
            return back()->with('error', 'No documents have been uploaded for this application.');
        }

        $zip = new \ZipArchive;
        $fileName = 'application_'.$checklist->id.'_documents.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($uploadedDocs as $doc) {
                $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($doc->file_path);
                if (file_exists($filePath)) {
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    $zipEntryName = str_replace(' ', '_', $doc->requirement->name_en).'.'.$extension;
                    $zip->addFile($filePath, $zipEntryName);
                }
            }
            $zip->close();

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to generate zip file.');
    }

    public function autoVerifyItem(UserChecklistItem $item)
    {
        $user = $item->checklist->user;

        if (! $user->valid_id_path || ! $item->file_path) {
            return back()->with('error', 'Both a valid ID and a document must be present to auto-verify.');
        }

        $idPath = storage_path('app/public/'.$user->valid_id_path);
        $docPath = storage_path('app/public/'.$item->file_path);

        $scriptPath = base_path('scripts/compare_images.py');
        // Simple command execution, for a real app we'd use Symfony Process
        $command = 'python3 '.escapeshellarg($scriptPath).' '.escapeshellarg($idPath).' '.escapeshellarg($docPath);
        $output = shell_exec($command);

        if ($output) {
            $result = json_decode($output, true);
            if (isset($result['match']) && $result['match'] === true) {
                $item->update(['status' => 'approved']);

                return back()->with('success', 'Document auto-verified successfully! (Score: '.number_format($result['score'], 2).')');
            }

            if (isset($result['error'])) {
                return back()->with('error', 'Auto-verify failed: '.$result['error']);
            }

            return back()->with('error', 'Document does not match. (Score: '.(isset($result['score']) ? number_format($result['score'], 2) : 'N/A').')');
        }

        return back()->with('error', 'Could not run verification script.');
    }

    // --- Inquiries Management ---
    public function inquiries()
    {
        $inquiries = UserInquiry::with(['user.checklists.service', 'user.inquiries', 'service', 'responses.responder'])
            ->orderBy('created_at', 'desc')
            ->get();

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
            'message' => ['required', 'string'],
        ]);

        $reply = InquiryRequirense::create([
            'inquiry_id' => $inquiry->id,
            'requireent_text' => $request->message,
            'responded_by' => auth()->id(),
        ]);

        $inquiry->update(['status' => 'resolved']);

        // Send email reply to resident
        $email = $inquiry->user ? $inquiry->user->email : $inquiry->guest_email;
        if ($email) {
            try {
                Mail::to($email)->send(new InquiryReplyEmail($inquiry, $request->message));
            } catch (\Exception $e) {
                Log::error('Failed to send manual inquiry reply email: '.$e->getMessage());
            }
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
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;

        if ($file = $request->file('avatar')) {
            $path = $file->store('avatars', env('FILESYSTEM_DISK', 'public'));
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'contact_number' => ['nullable', 'string'],
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

    public function editUser(User $user)
    {
        return view('facilitator.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'contact_number' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
        ]);

        $user->update($request->only('name', 'email', 'contact_number', 'dob'));

        return redirect()->route('facilitator.users')->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
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
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:government_services,id'],
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
        ]);
        UserChecklist::create($request->all());

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
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:government_services,id'],
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
        ]);
        $checklist->update($request->all());

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
            'template_file' => 'required|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $disk = env('FILESYSTEM_DISK', 'public');
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
        $disk = env('FILESYSTEM_DISK', 'public');
        if ($template->file_path && \Storage::disk($disk)->exists($template->file_path)) {
            \Storage::disk($disk)->delete($template->file_path);
        }
        $template->delete();

        return redirect()->route('facilitator.templates')->with('success', 'Document template deleted successfully.');
    }

    // --- Reports & Exports Management ---
    public function reports()
    {
        $totalApplications = UserChecklist::count();
        $approvedApplications = UserChecklist::where('status', 'approved')->count();
        $pendingApplications = UserChecklist::where('status', 'pending')->count();
        $rejectedApplications = UserChecklist::where('status', 'rejected')->count();

        $totalResidents = User::where('role', 'resident')->count();
        $verifiedResidents = User::where('role', 'resident')->whereNotNull('valid_id_path')->count();

        $totalAssessments = EligibilityAssessment::count();
        $eligibleAssessments = EligibilityAssessment::where('status', 'eligible')->count();

        $totalInquiries = UserInquiry::count();
        $pendingInquiries = UserInquiry::where('status', 'pending')->count();

        $services = GovernmentService::all();

        return view('facilitator.reports.index', compact(
            'totalApplications',
            'approvedApplications',
            'pendingApplications',
            'rejectedApplications',
            'totalResidents',
            'verifiedResidents',
            'totalAssessments',
            'eligibleAssessments',
            'totalInquiries',
            'pendingInquiries',
            'services'
        ));
    }

    public function exportApplications(Request $request)
    {
        $query = UserChecklist::with(['user', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $applications = $query->orderBy('created_at', 'desc')->get();
        $filename = 'govassist_applications_report_'.now()->format('Y-m-d').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Application ID', 'Resident Name', 'Email', 'Assistance Program', 'Application Type', 'Status', 'Submitted Date', 'Remarks'], "\t");

            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->user ? $app->user->name : 'N/A',
                    $app->user ? $app->user->email : 'N/A',
                    $app->service ? $app->service->name_en : 'N/A',
                    strtoupper($app->application_type ?? 'new'),
                    strtoupper($app->status),
                    $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : '',
                    $app->remarks ?? '',
                ], "\t");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportResidents(Request $request)
    {
        $query = User::where('role', 'resident');

        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->whereNotNull('valid_id_path');
            } elseif ($request->verified === 'no') {
                $query->whereNull('valid_id_path');
            }
        }

        $residents = $query->orderBy('created_at', 'desc')->get();
        $filename = 'govassist_residents_registry_'.now()->format('Y-m-d').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($residents) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['User ID', 'Resident Name', 'Email', 'Contact Number', 'Civil Status', 'Date of Birth', 'Complete Address', 'Valid ID Status', 'Registered Date'], "\t");

            foreach ($residents as $c) {
                fputcsv($file, [
                    $c->id,
                    $c->name,
                    $c->email,
                    $c->contact_number ?? 'N/A',
                    $c->civil_status ?? 'N/A',
                    $c->dob ? $c->dob->format('Y-m-d') : 'N/A',
                    $c->address ?? 'N/A',
                    $c->valid_id_path ? 'VERIFIED / UPLOADED' : 'PENDING UPLOAD',
                    $c->created_at ? $c->created_at->format('Y-m-d H:i:s') : '',
                ], "\t");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAssessments(Request $request)
    {
        $query = EligibilityAssessment::with(['user', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assessments = $query->orderBy('created_at', 'desc')->get();
        $filename = 'govassist_eligibility_assessments_'.now()->format('Y-m-d').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($assessments) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Assessment ID', 'Resident Name', 'Email', 'Assistance Program', 'Assessment Status', 'Calculated At'], "\t");

            foreach ($assessments as $a) {
                fputcsv($file, [
                    $a->id,
                    $a->user ? $a->user->name : 'N/A',
                    $a->user ? $a->user->email : 'N/A',
                    $a->service ? $a->service->name_en : 'N/A',
                    strtoupper($a->status),
                    $a->created_at ? $a->created_at->format('Y-m-d H:i:s') : '',
                ], "\t");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportInquiries(Request $request)
    {
        $query = UserInquiry::with(['user', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inquiries = $query->orderBy('created_at', 'desc')->get();
        $filename = 'govassist_inquiries_helpdesk_'.now()->format('Y-m-d').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($inquiries) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Inquiry ID', 'Sender Name', 'Email', 'Source Channel', 'Assistance Program', 'Inquiry Message', 'Status', 'Date Submitted'], "\t");

            foreach ($inquiries as $inq) {
                fputcsv($file, [
                    $inq->id,
                    $inq->user ? $inq->user->name : ($inq->guest_name ?? 'Guest Resident'),
                    $inq->user ? $inq->user->email : ($inq->guest_email ?? 'N/A'),
                    'Manual Helpdesk',
                    $inq->service ? $inq->service->name_en : 'General Inquiry',
                    $inq->inquiry_text,
                    strtoupper($inq->status),
                    $inq->created_at ? $inq->created_at->format('Y-m-d H:i:s') : '',
                ], "\t");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAllMasterReport(Request $request)
    {
        $filename = 'govassist_master_reports_summary_'.now()->format('Y-m-d').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            // Header Section
            fputcsv($file, ['GOVASSIST SSFO MASTER SYSTEM REPORT SUMMARY'], "\t");
            fputcsv($file, ['Generated At:', now()->format('Y-m-d H:i:s')], "\t");
            fputcsv($file, [], "\t");

            // Summary Stats
            fputcsv($file, ['METRIC SUMMARY', 'TOTAL RECORD COUNT'], "\t");
            fputcsv($file, ['Total Registered Residents', User::where('role', 'resident')->count()], "\t");
            fputcsv($file, ['Total Applications Submitted', UserChecklist::count()], "\t");
            fputcsv($file, ['Approved Applications', UserChecklist::where('status', 'approved')->count()], "\t");
            fputcsv($file, ['Pending Review Applications', UserChecklist::where('status', 'pending')->count()], "\t");
            fputcsv($file, ['Rejected Applications', UserChecklist::where('status', 'rejected')->count()], "\t");
            fputcsv($file, ['Total Eligibility Assessments', EligibilityAssessment::count()], "\t");
            fputcsv($file, [], "\t");

            // Applications Data
            fputcsv($file, ['RECENT APPLICATIONS LIST'], "\t");
            fputcsv($file, ['App ID', 'Resident Name', 'Email', 'Program', 'Status', 'Submitted Date'], "\t");
            $apps = UserChecklist::with(['user', 'service'])->orderBy('created_at', 'desc')->take(100)->get();
            foreach ($apps as $a) {
                fputcsv($file, [$a->id, $a->user?->name ?? 'N/A', $a->user?->email ?? 'N/A', $a->service?->name_en ?? 'N/A', strtoupper($a->status), $a->created_at?->format('Y-m-d H:i:s') ?? ''], "\t");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
