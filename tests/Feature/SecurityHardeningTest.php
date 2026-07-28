<?php

use App\Models\CommonQuestion;
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
    $this->get('/login')
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

test('a guest cannot access an inquiry', function () {
    $inquiry = UserInquiry::create([
        'guest_name' => 'Resident',
        'guest_email' => 'resident@example.com',
        'inquiry_text' => 'Private inquiry',
        'status' => 'pending',
    ]);

    $this->getJson(route('resident.inquiry.messages', [
        'inquiry' => $inquiry,
        'guest_email' => 'resident@example.com',
    ]))->assertRedirect(route('login'));
});

test('a guest cannot create an inquiry', function () {
    $this->postJson(route('resident.inquiry.manual'), [
        'guest_name' => 'Resident',
        'guest_email' => 'resident@example.com',
        'inquiry_text' => 'How do I apply?',
    ])->assertRedirect(route('login'));

    expect(UserInquiry::query()->count())->toBe(0);
});

test('guests cannot access resident core pages', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with([
    'resident home' => 'resident.home',
    'government services and eligibility' => 'resident.eligibility',
    'resident inquiries' => 'resident.inquiry',
]);

test('a resident cannot add a common question to another resident inquiry', function () {
    $inquiryOwner = User::factory()->create(['role' => 'resident']);
    $otherResident = User::factory()->create(['role' => 'resident']);
    $inquiry = UserInquiry::create([
        'user_id' => $inquiryOwner->id,
        'inquiry_text' => 'Private inquiry',
        'status' => 'pending',
    ]);
    $commonQuestion = CommonQuestion::create([
        'question_text' => 'How do I apply?',
        'answer_text' => 'Complete the eligibility assessment first.',
    ]);

    $this->actingAs($otherResident)
        ->postJson(route('resident.inquiry.common_question'), [
            'common_question_id' => $commonQuestion->id,
            'inquiry_id' => $inquiry->id,
        ])
        ->assertForbidden();

    expect($inquiry->responses()->count())->toBe(0);
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
