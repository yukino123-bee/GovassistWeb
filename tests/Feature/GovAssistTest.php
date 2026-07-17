<?php

use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\ServiceCategory;
use App\Models\ServiceTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
