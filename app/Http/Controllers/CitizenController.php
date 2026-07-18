<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAnswer;
use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\InquiryRequirense;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use App\Models\ReassessmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CitizenController extends Controller
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

        return view('citizen.home', compact('categories', 'applications', 'search'));
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

        return view('citizen.eligibility.index', compact('services', 'assessments', 'reassessmentRequests'));
    }

    public function showAssessForm(GovernmentService $service)
    {
        $existingAssessment = EligibilityAssessment::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if ($existingAssessment) {
            return redirect()->route('citizen.eligibility')->with('error', 'You have already taken the assessment for this program. You can request a reassessment if necessary.');
        }

        $questions = $service->eligibilityQuestions;

        return view('citizen.eligibility.assess', compact('service', 'questions'));
    }

    public function processAssessForm(Request $request, GovernmentService $service)
    {
        $user = Auth::user();

        if (EligibilityAssessment::where('user_id', $user->id)->where('service_id', $service->id)->exists()) {
            return redirect()->route('citizen.eligibility')->with('error', 'You have already taken the assessment for this program.');
        }

        $questions = $service->eligibilityQuestions;

        $answers = [];
        $isEligible = true;

        foreach ($questions as $q) {
            $inputName = 'question_'.$q->id;
            $userAnswer = $request->input($inputName);

            $questionText = $q->question_text;
            $answers[$questionText] = $userAnswer;

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

                if (($q->operator === 'less_than_or_equal' || $q->operator === '<=') && $actual > $expected) {
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

        return redirect()->route('citizen.eligibility.result', ['refNo' => $assessment->id]);
    }

    public function showAssessResult($refNo)
    {
        $assessment = EligibilityAssessment::with(['service', 'answers'])
            ->where('user_id', Auth::id())
            ->where('id', $refNo)
            ->firstOrFail();

        return view('citizen.eligibility.result', compact('assessment'));
    }

    public function requestReassessment(Request $request, GovernmentService $service)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
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
        $eligible = EligibilityAssessment::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->where('status', 'eligible')
            ->exists();

        if (! $eligible) {
            return redirect()->route('citizen.eligibility')->with('error', 'You must first qualify through the Eligibility Assessment.');
        }

        $requirements = $service->requirements;

        $checklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

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

        $alreadyApplied = $checklist && $checklist->status !== 'pending';

        return view('citizen.eligibility.checklist', compact('service', 'requirements', 'uploadedDocs', 'allMandatoryUploaded', 'alreadyApplied', 'checklist'));
    }

    public function uploadDocument(Request $request, GovernmentService $service, ServiceRequirement $requirement)
    {
        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,png,jpeg,doc,docx', 'max:5120'],
        ]);

        if ($file = $request->file('document')) {
            $folderName = \Illuminate\Support\Str::slug($service->name);
            $path = $file->store('documents/' . $folderName, 'public');

            $checklist = UserChecklist::firstOrCreate([
                'user_id' => Auth::id(),
                'service_id' => $service->id,
            ], [
                'status' => 'pending',
            ]);

            $checklistItem = UserChecklistItem::updateOrCreate([
                'checklist_id' => $checklist->id,
                'requirement_id' => $requirement->id,
            ], [
                'is_submitted' => true,
                'file_path' => $path,
                'submitted_at' => now(),
                'status' => 'pending',
            ]);

            // Automation: Compare against Admin Template
            $template = \App\Models\DocumentTemplate::where('requirement_id', $requirement->id)->first();
            if ($template) {
                $userDocPath = storage_path('app/public/' . $path);
                $templatePath = storage_path('app/public/' . $template->file_path);
                $scriptPath = base_path('scripts/compare_images.py');

                $command = 'python3 ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($userDocPath) . ' ' . escapeshellarg($templatePath);
                $output = shell_exec($command);

                if ($output) {
                    $result = json_decode($output, true);
                    if (isset($result['match']) && $result['match'] === true) {
                        $checklistItem->update(['status' => 'approved']);
                    }
                }
            }
        }

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function apply(GovernmentService $service)
    {
        $eligible = EligibilityAssessment::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->where('status', 'eligible')
            ->exists();

        if (! $eligible) {
            return back()->with('error', 'You must be eligible to apply.');
        }

        $checklist = UserChecklist::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if (! $checklist) {
            return back()->with('error', 'No checklist found. Please upload documents first.');
        }

        $requirements = $service->requirements;
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

        return redirect()->route('citizen.home')->with('success', 'Application submitted successfully!');
    }

    public function inquiry()
    {
        $inquiries = Auth::check()
            ? UserInquiry::with(['service', 'responses.responder'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $services = GovernmentService::all();
        $templates = collect();

        return view('citizen.inquiry.bot', compact('inquiries', 'services', 'templates'));
    }

    public function inquiryChat(Request $request)
    {
        $message = $request->input('message');
        $serviceId = $request->input('service_id');
        
        // Default to english if guest
        $lang = Auth::check() && Auth::user()->language ? Auth::user()->language : 'en';

        $response = $this->getBotResponse($message, $lang);

        // Only save the inquiry to the database if the user is authenticated
        if (Auth::check()) {
            $inquiry = UserInquiry::create([
                'user_id' => Auth::id(),
                'service_id' => $serviceId ?: null,
                'inquiry_text' => $message,
                'status' => 'pending',
            ]);

            $facilitator = User::where('role', 'facilitator')->first();
            $responderId = $facilitator ? $facilitator->id : Auth::id();

            InquiryRequirense::create([
                'inquiry_id' => $inquiry->id,
                'requireent_text' => $response,
                'responded_by' => $responderId,
            ]);
        }

        return response()->json([
            'reply' => $response,
            'time' => now()->format('h:i A'),
        ]);
    }

    public function submitManualInquiry(Request $request)
    {
        $rules = [
            'inquiry_text' => ['required', 'string'],
            'service_id' => ['nullable', 'exists:government_services,id'],
        ];

        if (!Auth::check()) {
            $rules['guest_name'] = ['required', 'string', 'max:255'];
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        $request->validate($rules);

        UserInquiry::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'guest_name' => $request->input('guest_name'),
            'guest_email' => $request->input('guest_email'),
            'service_id' => $request->input('service_id'),
            'inquiry_text' => $request->input('inquiry_text'),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your inquiry has been sent to the administrators.');
    }

    public function profile()
    {
        return view('citizen.profile.show');
    }

    public function editProfile()
    {
        return view('citizen.profile.edit');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'dob' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'civil_status' => ['nullable', 'string'],
            'contact_number' => ['nullable', 'string'],
            'valid_id' => ['nullable', 'file', 'mimes:pdf,jpg,png,jpeg', 'max:5120'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->address = $request->address;
        $user->civil_status = $request->civil_status;
        $user->contact_number = $request->contact_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($file = $request->file('valid_id')) {
            $path = $file->store('valid_ids', 'public');
            $user->valid_id_path = $path;
        }

        $user->save();

        return redirect()->route('citizen.profile')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($file = $request->file('avatar')) {
            $path = $file->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return back()->with('success', 'Avatar updated successfully.');
    }

    protected function getBotResponse($msg, $lang)
    {
        $scriptPath = base_path('scripts/govbot_ai.py');
        $command = 'python3 '.escapeshellarg($scriptPath).' --message '.escapeshellarg($msg).' --lang '.escapeshellarg($lang);
        $output = shell_exec($command);

        if ($output) {
            $result = json_decode($output, true);
            if (isset($result['response'])) {
                return $result['response'];
            }
        }

        if ($lang === 'ceb') {
            return 'Pasayloa, wala ko kasabot sa imong pangutana. Mahimo ka mangutana bahin sa mga programa sa: **Edukasyon, Medikal, Pagpalubong, Transportasyon, o Trabaho**, ug ang ilang mga kinahanglanon.';
        } elseif ($lang === 'sub') {
            return 'Pasensya, daa nako nasabtan inyo pangutana. Pwede niyo pangutan-on ang mga programa sa: **Edukasyon, Medikal, Palubong, Pamasahe, o Trabaho**.';
        }
        
        return "I'm sorry, I didn't quite understand your query. You can ask about our programs: **Educational, Medical, Burial, Transportation, or Employment** assistance, and their required documents.";
    }
}
