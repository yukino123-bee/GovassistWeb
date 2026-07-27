<?php

use App\Models\GovernmentService;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('public responses include baseline security headers', function () {
    $this->get('/resident/home')
        ->assertSuccessful()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
});

test('migration execution is not exposed over HTTP', function () {
    $this->get('/run-migrations')->assertNotFound();
});

test('login attempts are rate limited', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post('/login', [
            'email' => 'attacker@example.com',
            'password' => 'invalid-password',
        ])->assertRedirect();
    }

    $this->post('/login', [
        'email' => 'attacker@example.com',
        'password' => 'invalid-password',
    ])->assertTooManyRequests();
});

test('a guest email address alone cannot access another guest inquiry', function () {
    $inquiry = UserInquiry::create([
        'guest_name' => 'Resident',
        'guest_email' => 'resident@example.com',
        'inquiry_text' => 'Private inquiry',
        'status' => 'pending',
    ]);

    $this->getJson(route('resident.inquiry.messages', [
        'inquiry' => $inquiry,
        'guest_email' => 'resident@example.com',
    ]))->assertForbidden();
});

test('a guest retains access to an inquiry created in the same session', function () {
    $response = $this->postJson(route('resident.inquiry.manual'), [
        'guest_name' => 'Resident',
        'guest_email' => 'resident@example.com',
        'inquiry_text' => 'How do I apply?',
    ])->assertSuccessful();

    $inquiryId = $response->json('inquiry.id');

    $this->getJson(route('resident.inquiry.messages', $inquiryId))
        ->assertSuccessful()
        ->assertJsonPath('inquiry.id', $inquiryId);
});

test('a requirement cannot be uploaded through another service', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);

    $resident = User::factory()->create(['role' => 'resident']);
    $category = ServiceCategory::create(['category_name' => 'Security Test']);
    $firstService = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'First Service',
        'description' => 'First',
        'procedure' => 'First',
    ]);
    $secondService = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Second Service',
        'description' => 'Second',
        'procedure' => 'Second',
    ]);
    $requirement = ServiceRequirement::create([
        'service_id' => $secondService->id,
        'requirement_text' => ['en' => 'Valid ID'],
        'is_required' => true,
    ]);

    $this->actingAs($resident)->post(route('resident.eligibility.upload', [
        'service' => $firstService,
        'requirement' => $requirement,
    ]), [
        'document' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ])->assertNotFound();

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});
