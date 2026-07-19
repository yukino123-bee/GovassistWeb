<?php

use App\Models\DocumentTemplate;
use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\ServiceTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('root redirects guest to citizen home', function () {
    $response = $this->get('/');
    $response->assertRedirect('/citizen/home');
});

test('guest can access citizen home', function () {
    $response = $this->get('/citizen/home');
    $response->assertStatus(200);
});

test('guest cannot access citizen profile', function () {
    $response = $this->get('/citizen/profile');
    $response->assertRedirect('/login');
});

test('citizen can register, login, and access citizen home', function () {
    // 1. Register
    $registerResponse = $this->post('/register', [
        'name' => 'Mark Cagatin',
        'email' => 'mark@citizen.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $registerResponse->assertRedirect('/citizen/home');
    $this->assertDatabaseHas('users', [
        'email' => 'mark@citizen.com',
        'role' => 'citizen',
    ]);

    // Logout
    $this->post('/logout');

    // 2. Login
    $loginResponse = $this->post('/login', [
        'email' => 'mark@citizen.com',
        'password' => 'password123',
    ]);

    $loginResponse->assertRedirect('/citizen/home');
});

test('citizen cannot access facilitator dashboard', function () {
    $citizen = User::factory()->create(['role' => 'citizen']);

    $response = $this->actingAs($citizen)->get('/facilitator/dashboard');
    $response->assertStatus(403);
});

test('language toggle changes session language', function () {
    $response = $this->postJson('/language/toggle', ['language' => 'ceb']);
    $response->assertJson(['success' => true]);
    $this->assertEquals('ceb', session('locale'));
});

test('eligibility assessment logic works correctly', function () {
    $citizen = User::factory()->create(['role' => 'citizen']);

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

    // Rule 2: Are you a senior citizen? Must be true (boolean check)
    $q2 = EligibilityQuestion::create([
        'service_id' => $service->id,
        'question_text_en' => 'Are you a senior citizen?',
        'question_text_ceb' => 'Senior citizen ba ikaw?',
        'question_text_fil' => 'Ikaw ba ay senior citizen?',
        'type' => 'boolean',
        'operator' => '==',
        'expected_value' => 'true',
    ]);

    // 1. Submit passing inputs (income = 12000, senior = true)
    $passResponse = $this->actingAs($citizen)->post(route('citizen.eligibility.assess.submit', $service->id), [
        "question_{$q1->id}" => '12000',
        "question_{$q2->id}" => 'true',
    ]);

    // Assert that the assessment created is eligible
    $assessment = EligibilityAssessment::where('user_id', $citizen->id)->where('service_id', $service->id)->first();
    expect($assessment)->not->toBeNull();
    expect($assessment->status)->toBe('eligible');
    $passResponse->assertRedirect(route('citizen.eligibility.result', $assessment->id));

    // 2. Submit failing inputs (income = 16000, senior = true) -> should calculate as ineligible
    $citizen2 = User::factory()->create(['role' => 'citizen']);
    $failResponse = $this->actingAs($citizen2)->post(route('citizen.eligibility.assess.submit', $service->id), [
        "question_{$q1->id}" => '16000',
        "question_{$q2->id}" => 'true',
    ]);

    $failAssessment = EligibilityAssessment::where('user_id', $citizen2->id)->where('service_id', $service->id)->first();
    expect($failAssessment)->not->toBeNull();
    expect($failAssessment->status)->toBe('ineligible');
    $failResponse->assertRedirect(route('citizen.eligibility.result', $failAssessment->id));
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
    $citizen = User::factory()->create(['role' => 'citizen']);

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

    // Create a mock template
    $template = DocumentTemplate::create([
        'service_id' => $service->id,
        'requirement_id' => $requirement->id,
        'name_en' => 'Indigency',
        'name_ceb' => 'Indigency',
        'file_path' => 'templates/mock_template.pdf',
    ]);

    // Mock upload file
    Storage::fake('public');
    $file = UploadedFile::fake()->create('document.pdf', 10);

    $response = $this->actingAs($citizen)->post(route('citizen.eligibility.upload', [$service->id, $requirement->id]), [
        'document' => $file,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('user_checklist_items', [
        'requirement_id' => $requirement->id,
        'is_submitted' => true,
    ]);
});
