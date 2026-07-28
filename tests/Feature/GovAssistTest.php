<?php

use App\Mail\ApplicationApprovedEmail;
use App\Mail\InquiryReplyEmail;
use App\Models\AssessmentAnswer;
use App\Models\DocumentTemplate;
use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\ServiceTranslation;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('root redirects guest to resident home', function () {
    $response = $this->get('/');
    $response->assertRedirect('/resident/home');
});

test('guest can access resident home', function () {
    $response = $this->get('/resident/home');
    $response->assertStatus(200);
});

test('login form links back to the resident home page', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('Back to Home')
        ->assertSee('href="'.route('resident.home').'"', false);
});

test('guest cannot access resident profile', function () {
    $response = $this->get('/resident/profile');
    $response->assertRedirect('/login');
});

test('resident can register, login, and access resident home', function () {
    // 1. Register
    $registerResponse = $this->post('/register', [
        'first_name' => 'Mark',
        'middle_name' => 'Santos',
        'last_name' => 'Cagatin',
        'email' => 'mark@resident.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $registerResponse->assertRedirect('/resident/home');
    $this->assertDatabaseHas('users', [
        'email' => 'mark@resident.com',
        'name' => 'Mark Santos Cagatin',
        'first_name' => 'Mark',
        'middle_name' => 'Santos',
        'last_name' => 'Cagatin',
        'role' => 'resident',
    ]);

    // Logout
    $this->post('/logout');

    // 2. Login
    $loginResponse = $this->post('/login', [
        'email' => 'mark@resident.com',
        'password' => 'password123',
    ]);

    $loginResponse->assertRedirect('/resident/home');
});

test('registration and profile fields enforce their displayed formats', function () {
    $this->post(route('register'), [
        'first_name' => 'Juan123',
        'last_name' => 'Dela Cruz',
        'email' => 'juan@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('first_name');

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->get(route('resident.profile.edit'))
        ->assertSuccessful()
        ->assertSee('House/Unit No., Street, Barangay, Municipality/City, Province')
        ->assertSee('Use 09XXXXXXXXX or +639XXXXXXXXX.');

    $this->actingAs($resident)
        ->post(route('resident.profile.update'), [
            'email' => $resident->email,
            'first_name' => 'Juan123',
            'dob' => now()->addDay()->toDateString(),
            'address' => str_repeat('a', 501),
            'contact_number' => '12345',
        ])
        ->assertSessionHasErrors([
            'first_name',
            'dob',
            'address',
            'contact_number',
        ]);
});

test('resident cannot access facilitator dashboard', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $response = $this->actingAs($resident)->get('/facilitator/dashboard');
    $response->assertStatus(403);
});

test('resident selects civil status from the supported options', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->get(route('resident.profile.edit'))
        ->assertSuccessful()
        ->assertSee('Single')
        ->assertSee('Married')
        ->assertSee('Widowed')
        ->assertSee('Divorced')
        ->assertSee('Live-in');

    $this->actingAs($resident)
        ->post(route('resident.profile.update'), [
            'email' => $resident->email,
            'civil_status' => 'Invalid status',
        ])
        ->assertSessionHasErrors('civil_status');

    $this->actingAs($resident)
        ->post(route('resident.profile.update'), [
            'email' => $resident->email,
            'civil_status' => 'Live-in',
        ])
        ->assertRedirect();

    expect($resident->fresh()->civil_status)->toBe('Live-in');
});

test('language toggle changes session language', function () {
    $response = $this->postJson('/language/toggle', ['language' => 'ceb']);
    $response->assertJson(['success' => true]);
    $this->assertEquals('ceb', session('locale'));
});

test('supplied subanen translations appear on authentication and dashboard pages', function () {
    $category = ServiceCategory::create(['category_name' => 'Educational Assistance']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Educational Assistance Program',
        'description' => 'Provides educational financial assistance.',
        'procedure' => 'Complete the eligibility assessment.',
    ]);
    ServiceTranslation::create([
        'service_id' => $service->id,
        'language_code' => 'sub',
        'service_name' => 'Gabang ni programa ne ngaji',
        'description' => 'Me phenun gobang rin sehutliha rn megiskiela,',
        'procedure' => null,
    ]);

    $this->postJson('/language/toggle', ['language' => 'sub'])->assertSuccessful();

    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('Sulat mu para mesunan hin mipegulek mu')
        ->assertSee('Pheseled mu su ngalan mhibetangan mu')
        ->assertSee('Milingawan mu Password mu');

    $this->get(route('register'))
        ->assertSuccessful()
        ->assertSee('Ghitungan mu')
        ->assertSee('Gbagel ngalan mu')
        ->assertSee('Pheselected mu puli password');

    $this->get(route('resident.home'))
        ->assertSuccessful()
        ->assertSee('Sembuen su haphegebek rin heseled mu')
        ->assertSee('Gabang rin ne ngaji')
        ->assertSee('Gabang ni programa ne ngaji')
        ->assertSee('Me phenun gobang rin sehutliha rn megiskiela,');
});

test('eligibility assessment logic works correctly', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    // Create a category
    $category = ServiceCategory::create([
        'category_name' => 'Medical Services',
    ]);

    // Create a service
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Test Medical Assistance',
        'description' => 'Provides financial aid for healthcare.',
        'procedure' => 'Submit valid ID and certificates.',
    ]);

    // Create service translations
    ServiceTranslation::create([
        'service_id' => $service->id,
        'language_code' => 'en',
        'service_name' => 'Test Medical Assistance',
        'description' => 'Provides financial aid for healthcare.',
        'procedure' => 'Submit valid ID and certificates.',
    ]);

    ServiceTranslation::create([
        'service_id' => $service->id,
        'language_code' => 'ceb',
        'service_name' => 'Pagsulay nga Tabang Medikal',
        'description' => 'Naghatag og tabang pinansyal sa pag-atiman sa panglawas.',
        'procedure' => 'Isumite ang balido nga ID ug mga sertipiko.',
    ]);

    // Create eligibility questions
    // Rule 1: Monthly income must be less than 15000 (number check)
    $q1 = EligibilityQuestion::create([
        'service_id' => $service->id,
        'question_text_en' => 'Is your income less than 15000?',
        'question_text_ceb' => 'Ubos ba sa 15000 ang imong kita?',
        'question_text_fil' => 'Mababa ba sa 15000 ang kita?',
        'type' => 'number',
        'operator' => '<',
        'expected_value' => '15000',
    ]);

    // Rule 2: Are you a senior resident? Must be true (boolean check)
    $q2 = EligibilityQuestion::create([
        'service_id' => $service->id,
        'question_text_en' => 'Are you a senior resident?',
        'question_text_ceb' => 'Senior resident ba ikaw?',
        'question_text_fil' => 'Ikaw ba ay senior resident?',
        'type' => 'boolean',
        'operator' => '==',
        'expected_value' => 'true',
    ]);

    // 1. Submit passing inputs (income = 12000, senior = true)
    $passResponse = $this->actingAs($resident)->post(route('resident.eligibility.assess.submit', $service->id), [
        "question_{$q1->id}" => '12000',
        "question_{$q2->id}" => 'true',
    ]);

    // Assert that the assessment created is eligible
    $assessment = EligibilityAssessment::where('user_id', $resident->id)->where('service_id', $service->id)->first();
    expect($assessment)->not->toBeNull();
    expect($assessment->status)->toBe('eligible');
    $passResponse->assertRedirect(route('resident.eligibility.result', $assessment->id));

    // 2. Submit failing inputs (income = 16000, senior = true) -> should calculate as ineligible
    $resident2 = User::factory()->create(['role' => 'resident']);
    $failResponse = $this->actingAs($resident2)->post(route('resident.eligibility.assess.submit', $service->id), [
        "question_{$q1->id}" => '16000',
        "question_{$q2->id}" => 'true',
    ]);

    $failAssessment = EligibilityAssessment::where('user_id', $resident2->id)->where('service_id', $service->id)->first();
    expect($failAssessment)->not->toBeNull();
    expect($failAssessment->status)->toBe('ineligible');
    $failResponse->assertRedirect(route('resident.eligibility.result', $failAssessment->id));
});

test('household income eligibility accepts only amounts from 2000 through 15000', function () {
    $category = ServiceCategory::create(['category_name' => 'Medical Services']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Medical Assistance',
        'description' => 'Medical support',
        'procedure' => 'Complete the assessment.',
    ]);
    $question = EligibilityQuestion::create([
        'service_id' => $service->id,
        'question_text_en' => 'What is your monthly household income?',
        'question_text_ceb' => 'Unsa ang binulan nga kita sa inyong panimalay?',
        'question_text_fil' => 'Ano ang buwanang kita ng inyong sambahayan?',
        'question_text_sub' => 'Household income',
        'type' => 'number',
        'operator' => '<',
        'expected_value' => '15000',
    ]);

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->get(route('resident.eligibility.assess', $service))
        ->assertSuccessful()
        ->assertSee('min="2000"', false)
        ->assertSee('max="15000"', false)
        ->assertSee('Valid household income range: ₱2,000–₱15,000.');

    foreach ([1999, 15001] as $invalidIncome) {
        $this->actingAs($resident)
            ->post(route('resident.eligibility.assess.submit', $service), [
                "question_{$question->id}" => $invalidIncome,
            ])
            ->assertSessionHasErrors([
                "question_{$question->id}" => 'Household income must be between ₱2,000 and ₱15,000.',
            ]);
    }

    expect(EligibilityAssessment::query()->where('user_id', $resident->id)->exists())->toBeFalse();

    foreach ([2000, 15000] as $validIncome) {
        $eligibleResident = User::factory()->create(['role' => 'resident']);

        $this->actingAs($eligibleResident)
            ->post(route('resident.eligibility.assess.submit', $service), [
                "question_{$question->id}" => $validIncome,
            ])
            ->assertRedirect();

        expect(EligibilityAssessment::query()
            ->where('user_id', $eligibleResident->id)
            ->where('service_id', $service->id)
            ->value('status'))->toBe('eligible');
    }
});

test('facilitator can manage document templates by program', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);

    // Create a category and service
    $category = ServiceCategory::create(['category_name' => 'Test Service Category']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Medical Assistance Program',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    // Create a requirement for the service
    $requirement = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Indigency Certificate', 'ceb' => 'Indigency'],
        'is_required' => true,
    ]);

    // 1. Visit the templates management page as facilitator
    $response = $this->actingAs($facilitator)->get(route('facilitator.templates'));
    $response->assertStatus(200);
    $response->assertSee('Medical Assistance Program');
    $response->assertSee('Indigency Certificate');

    // 2. Upload a template for the requirement
    Storage::fake('public');
    $file = UploadedFile::fake()->image('template.png');

    $postResponse = $this->actingAs($facilitator)->post(route('facilitator.templates.store'), [
        'service_id' => $service->id,
        'requirement_id' => $requirement->id,
        'keywords' => 'English Template Name',
        'template_file' => $file,
    ]);

    $postResponse->assertRedirect(route('facilitator.templates'));
    $this->assertDatabaseHas('document_templates', [
        'requirement_id' => $requirement->id,
        'name_en' => 'English Template Name',
    ]);

    $template = DocumentTemplate::first();

    // 3. Delete the template
    $deleteResponse = $this->actingAs($facilitator)->delete(route('facilitator.templates.destroy', $template->id));
    $deleteResponse->assertRedirect(route('facilitator.templates'));
    $this->assertDatabaseMissing('document_templates', [
        'id' => $template->id,
    ]);
});

test('document templates verification matches keywords for PDF uploads', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    Process::fake([
        '*' => Process::result(output: json_encode([
            'match' => true,
            'score' => 0.91,
            'method' => 'pdf_text_template_and_keywords',
            'note' => 'Document verified successfully.',
        ])),
    ]);

    $resident = User::factory()->create(['role' => 'resident']);

    $category = ServiceCategory::create(['category_name' => 'Category']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service Program',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    $requirement = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Indigency Certificate', 'ceb' => 'Indigency'],
        'is_required' => true,
    ]);

    Storage::disk('public')->put('templates/mock_template.pdf', 'template');

    $template = DocumentTemplate::create([
        'service_id' => $service->id,
        'requirement_id' => $requirement->id,
        'name_en' => 'Indigency',
        'name_ceb' => 'Indigency',
        'file_path' => 'templates/mock_template.pdf',
    ]);

    $file = UploadedFile::fake()->create('document.pdf', 10);

    $response = $this->actingAs($resident)->post(route('resident.eligibility.upload', [$service->id, $requirement->id]), [
        'document' => $file,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('user_checklist_items', [
        'requirement_id' => $requirement->id,
        'is_submitted' => true,
        'status' => 'approved',
    ]);

    Process::assertRan(fn ($process) => str_contains(implode(' ', $process->command), 'Indigency'));
});

test('document templates verification accepts an upload identical to the configured template', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    Process::fake();

    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Category']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Medical Assistance Program',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);
    $requirement = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Medical Certificate'],
        'is_required' => true,
    ]);

    $documentContents = 'scanned medical certificate binary contents';
    Storage::disk('public')->put('templates/medical-certificate.pdf', $documentContents);
    DocumentTemplate::create([
        'service_id' => $service->id,
        'requirement_id' => $requirement->id,
        'name_en' => 'Medical Certificate,Patient Name,Physician',
        'name_ceb' => 'Medical Certificate,Patient Name,Physician',
        'file_path' => 'templates/medical-certificate.pdf',
    ]);

    $response = $this->actingAs($resident)->post(route('resident.eligibility.upload', [
        $service->id,
        $requirement->id,
    ]), [
        'document' => UploadedFile::fake()->createWithContent('medical-certificate.pdf', $documentContents),
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHas('success', 'Document verified against the template and uploaded successfully.');
    $this->assertDatabaseHas('user_checklist_items', [
        'requirement_id' => $requirement->id,
        'is_submitted' => true,
        'status' => 'approved',
    ]);
    Process::assertNothingRan();
});

test('document template verification rejects a mismatched upload', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    Process::fake([
        '*' => Process::result(output: json_encode([
            'match' => false,
            'score' => 0.08,
            'method' => 'pdf_text_template_and_keywords',
            'note' => 'The document does not match the configured template or all required keywords.',
        ])),
    ]);

    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Category']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Medical Assistance Program',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);
    $requirement = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Medical Certificate'],
        'is_required' => true,
    ]);

    Storage::disk('public')->put('templates/medical-certificate.pdf', 'template');
    DocumentTemplate::create([
        'service_id' => $service->id,
        'requirement_id' => $requirement->id,
        'name_en' => 'Medical Certificate,Patient Name,Physician',
        'name_ceb' => 'Medical Certificate,Patient Name,Physician',
        'file_path' => 'templates/medical-certificate.pdf',
    ]);

    $response = $this->actingAs($resident)->post(route('resident.eligibility.upload', [
        $service->id,
        $requirement->id,
    ]), [
        'document' => UploadedFile::fake()->create('unrelated-document.pdf', 10),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'The document does not match the configured template or all required keywords.');
    expect(UserChecklistItem::query()->where('requirement_id', $requirement->id)->exists())->toBeFalse();
    expect(Storage::disk('public')->allFiles('documents'))->toBeEmpty();
});

test('facilitator layout loads notifications correctly', function () {
    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Cat']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    // Create pending checklist
    UserChecklist::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('facilitator.dashboard'));
    $response->assertStatus(200);
    $response->assertViewHas('adminNotifications');

    $notifs = $response->viewData('adminNotifications');
    expect($notifs->count())->toBeGreaterThanOrEqual(1);
    expect($notifs->first()['type'])->toBe('application');
});

test('manual inquiries can be replied to, while chatbot inquiries hide reply form', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);

    $category = ServiceCategory::create(['category_name' => 'Cat']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    // Bot inquiry
    $botInq = UserInquiry::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'inquiry_text' => 'Hello Bot',
        'status' => 'pending',
    ]);

    // Manual inquiry
    $manualInq = UserInquiry::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'inquiry_text' => 'Hello Admin',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('facilitator.inquiries'));
    $response->assertStatus(200);

    // Test manual reply endpoint works
    $replyResponse = $this->actingAs($admin)->post(route('facilitator.inquiries.reply', $manualInq), [
        'message' => 'Hello back',
    ]);
    $replyResponse->assertRedirect();
    $this->assertDatabaseHas('inquiry_requirenses', [
        'inquiry_id' => $manualInq->id,
        'requireent_text' => 'Hello back',
    ]);

    Mail::assertSent(InquiryReplyEmail::class, function ($mail) use ($resident) {
        return $mail->hasTo($resident->email);
    });
});

test('approving application triggers email notification', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Cat']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    $checklist = UserChecklist::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('facilitator.applications.update_status', $checklist), [
        'status' => 'approved',
        'remarks' => 'Looks great!',
    ]);

    $response->assertRedirect(route('facilitator.applications'));

    Mail::assertSent(ApplicationApprovedEmail::class, function ($mail) use ($resident) {
        return $mail->hasTo($resident->email);
    });
});

test('assessment details route displays user answers', function () {
    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Cat']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    $assess = EligibilityAssessment::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'eligible',
    ]);

    AssessmentAnswer::create([
        'assessment_id' => $assess->id,
        'question' => 'Are you indigent?',
        'answer' => 'Yes',
    ]);

    $response = $this->actingAs($admin)->get(route('facilitator.assessments.show', $assess));
    $response->assertStatus(200);
    $response->assertSee('Calculation Overview');
    $response->assertSee('Are you indigent?');
    $response->assertSee('Yes');
});

test('facilitator can batch update document statuses', function () {
    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Cat']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    $checklist = UserChecklist::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $req = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Indigency Certificate'],
    ]);

    $item = UserChecklistItem::create([
        'checklist_id' => $checklist->id,
        'requirement_id' => $req->id,
        'status' => 'pending',
        'is_submitted' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('facilitator.checklist_items.batch_update', $checklist->id), [
        'statuses' => [
            $item->id => 'approved',
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('user_checklist_items', [
        'id' => $item->id,
        'status' => 'approved',
    ]);
});

test('facilitator can download all documents zipped', function () {
    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Cat']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    $checklist = UserChecklist::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $req = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Indigency Certificate'],
    ]);

    Storage::fake('public');
    $file = UploadedFile::fake()->create('document.pdf', 10);
    $path = $file->store('documents', 'public');

    $item = UserChecklistItem::create([
        'checklist_id' => $checklist->id,
        'requirement_id' => $req->id,
        'file_path' => $path,
        'status' => 'pending',
        'is_submitted' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('facilitator.applications.download_all', $checklist->id));
    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/zip');
});

test('facilitator dashboard displays "none" instead of "0 apps" when there are no applications for a service', function () {
    $admin = User::factory()->create(['role' => 'facilitator']);
    $category = ServiceCategory::create(['category_name' => 'Cat']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Service',
        'description' => 'Test',
        'procedure' => 'Test',
    ]);

    $response = $this->actingAs($admin)->get(route('facilitator.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('none');
    $response->assertDontSee('0 apps');
});
