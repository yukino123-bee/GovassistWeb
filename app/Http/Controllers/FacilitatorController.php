<?php

namespace App\Http\Controllers;

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

class FacilitatorController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'citizen')->count();
        $totalServices = GovernmentService::count();
        $totalAssessments = EligibilityAssessment::count();

        // Count pending inquiries as open
        $openInquiries = UserInquiry::where('status', 'pending')->count();

        // Get recent applications (checklists)
        $recentApplications = UserChecklist::with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('facilitator.dashboard', compact('totalUsers', 'totalServices', 'totalAssessments', 'openInquiries', 'recentApplications'));
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

    public function storeService(Request $request)
    {
        $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ceb' => ['required', 'string', 'max:255'],
            'name_fil' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_ceb' => ['required', 'string'],
            'description_fil' => ['nullable', 'string'],
            'procedure_en' => ['required', 'string'],
            'procedure_ceb' => ['required', 'string'],
            'procedure_fil' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:service_categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

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

        // Save Translations: Cebuano
        ServiceTranslation::create([
            'service_id' => $service->id,
            'language_code' => 'ceb',
            'service_name' => $request->name_ceb,
            'description' => $request->description_ceb,
            'procedure' => $request->procedure_ceb,
        ]);

        // Save Translations: Filipino
        ServiceTranslation::create([
            'service_id' => $service->id,
            'language_code' => 'fil',
            'service_name' => $request->name_fil ?: $request->name_en,
            'description' => $request->description_fil ?: $request->description_en,
            'procedure' => $request->procedure_fil ?: $request->procedure_en,
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
            'name_ceb' => ['required', 'string', 'max:255'],
            'name_fil' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_ceb' => ['required', 'string'],
            'description_fil' => ['nullable', 'string'],
            'procedure_en' => ['required', 'string'],
            'procedure_ceb' => ['required', 'string'],
            'procedure_fil' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:service_categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

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
            'service_name' => $request->name_ceb,
            'description' => $request->description_ceb,
            'procedure' => $request->procedure_ceb,
        ]);

        // Update/create Filipino translation
        ServiceTranslation::updateOrCreate([
            'service_id' => $service->id,
            'language_code' => 'fil',
        ], [
            'service_name' => $request->name_fil ?: $request->name_en,
            'description' => $request->description_fil ?: $request->description_en,
            'procedure' => $request->procedure_fil ?: $request->procedure_en,
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

    // --- Citizens list ---
    public function users()
    {
        $users = User::where('role', 'citizen')->orderBy('name', 'asc')->get();

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
    public function applications()
    {
        $applications = UserChecklist::with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('facilitator.applications.index', compact('applications'));
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
        $inquiries = UserInquiry::with(['user', 'service', 'responses.responder'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('facilitator.inquiries.index', compact('inquiries'));
    }

    public function replyInquiry(Request $request, UserInquiry $inquiry)
    {
        $request->validate([
            'message' => ['required', 'string'],
        ]);

        InquiryRequirense::create([
            'inquiry_id' => $inquiry->id,
            'requireent_text' => $request->message,
            'responded_by' => auth()->id(),
        ]);

        $inquiry->update(['status' => 'resolved']);

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
            'role' => 'citizen',
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

    // --- Assessments CRUD extensions ---
    public function createAssessment()
    {
        $users = User::where('role', 'citizen')->get();
        $services = GovernmentService::all();

        return view('facilitator.assessments.create', compact('users', 'services'));
    }

    public function storeAssessment(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:government_services,id'],
            'is_eligible' => ['required', 'boolean'],
        ]);
        EligibilityAssessment::create($request->all());

        return redirect()->route('facilitator.assessments')->with('success', 'Assessment created successfully.');
    }

    public function editAssessment(EligibilityAssessment $assessment)
    {
        $users = User::where('role', 'citizen')->get();
        $services = GovernmentService::all();

        return view('facilitator.assessments.edit', compact('assessment', 'users', 'services'));
    }

    public function updateAssessment(Request $request, EligibilityAssessment $assessment)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:government_services,id'],
            'is_eligible' => ['required', 'boolean'],
        ]);
        $assessment->update($request->all());

        return redirect()->route('facilitator.assessments')->with('success', 'Assessment updated successfully.');
    }

    public function destroyAssessment(EligibilityAssessment $assessment)
    {
        $assessment->delete();

        return redirect()->route('facilitator.assessments')->with('success', 'Assessment deleted successfully.');
    }

    // --- Applications CRUD extensions ---
    public function createApplication()
    {
        $users = User::where('role', 'citizen')->get();
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
        $users = User::where('role', 'citizen')->get();
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

    // --- Inquiries AI Extension ---
    public function generateAIDraft(Request $request, UserInquiry $inquiry)
    {
        $scriptPath = base_path('scripts/govbot_ai.py');
        // We pass the inquiry text to the script
        $command = 'python3 '.escapeshellarg($scriptPath).' --message '.escapeshellarg($inquiry->subject.' '.$inquiry->message).' --lang en';
        $output = shell_exec($command);

        $response = 'I am unable to generate a draft right now.';
        if ($output) {
            $result = json_decode($output, true);
            if (isset($result['response'])) {
                $response = $result['response'];
            }
        }

        return response()->json(['draft' => "AI Suggested Draft:\n".$response]);
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
}
