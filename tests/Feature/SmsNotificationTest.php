<?php

use App\Models\GovernmentService;
use App\Models\ReassessmentRequest;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserInquiry;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['services.sms.driver' => 'log']);
});

test('sms service formats Philippine mobile numbers correctly', function () {
    $sms = new SmsService;

    // Reflection method access for formatPhoneNumber
    $reflection = new ReflectionClass($sms);
    $method = $reflection->getMethod('formatPhoneNumber');

    expect($method->invoke($sms, '09171234567'))->toBe('+639171234567');
    expect($method->invoke($sms, '639171234567'))->toBe('+639171234567');
    expect($method->invoke($sms, '+639171234567'))->toBe('+639171234567');
});

test('sms log driver outputs message to log without error', function () {
    Log::shouldReceive('info')->atLeast()->once();

    $sms = new SmsService;
    $result = $sms->send('09171234567', 'Test SMS message');

    expect($result)->toBeTrue();
});

test('unisms driver sends http request with basic auth', function () {
    Http::fake([
        'https://unismsapi.com/api/sms' => Http::response(['status' => 'success'], 200),
    ]);

    config(['services.sms.driver' => 'unisms', 'services.sms.api_key' => 'test_sk_key']);

    $sms = new SmsService;
    $result = $sms->send('09187439096', 'Test UniSMS content');

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://unismsapi.com/api/sms' &&
               $request['recipient'] === '+639187439096' &&
               $request['content'] === 'Test UniSMS content';
    });

    config(['services.sms.driver' => 'log']);
});

test('account verification sends sms otp when user has contact number', function () {
    Log::shouldReceive('info')->atLeast()->once();

    $user = User::factory()->create([
        'contact_number' => '09171234567',
    ]);

    $user->sendEmailVerificationNotification();
});

test('facilitator updating application status dispatches sms notification', function () {
    Log::shouldReceive('info')->atLeast()->once();

    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident', 'contact_number' => '09171234567']);
    $category = ServiceCategory::create(['category_name' => 'Medical']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Medical Assistance',
        'description' => 'Desc',
        'procedure' => 'Proc',
    ]);
    $checklist = UserChecklist::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('facilitator.applications.update_status', $checklist), [
        'status' => 'approved',
        'remarks' => 'All documents verified.',
    ]);

    $response->assertRedirect();
    expect($checklist->fresh()->status)->toBe('approved');
});

test('facilitator inquiry reply dispatches sms notification', function () {
    Log::shouldReceive('info')->atLeast()->once();

    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident', 'contact_number' => '09171234567']);
    $inquiry = UserInquiry::create([
        'user_id' => $resident->id,
        'inquiry_text' => 'Where is my application?',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('facilitator.inquiries.reply', $inquiry), [
        'message' => 'Your application is currently under review.',
    ]);

    $response->assertRedirect();
});

test('facilitator reassessment status update dispatches sms notification', function () {
    Log::shouldReceive('info')->atLeast()->once();

    $admin = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident', 'contact_number' => '09171234567']);
    $category = ServiceCategory::create(['category_name' => 'Medical']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Medical Assistance',
        'description' => 'Desc',
        'procedure' => 'Proc',
    ]);
    $reassessment = ReassessmentRequest::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'reason' => 'Updated income details',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('facilitator.reassessments.update_status', $reassessment), [
        'status' => 'approved',
    ]);

    $response->assertRedirect();
    expect($reassessment->fresh()->status)->toBe('approved');
});

test('sms service enforces rate limiting per phone number to protect API credits', function () {
    $sms = new SmsService;
    $phone = '09179998888';

    $sms->clearRateLimit($phone);

    expect($sms->send($phone, 'Message 1'))->toBeTrue();
    expect($sms->send($phone, 'Message 2'))->toBeTrue();
    expect($sms->send($phone, 'Message 3'))->toBeTrue();

    // 4th attempt should be throttled
    expect($sms->send($phone, 'Message 4'))->toBeFalse();

    // Bypassing rate limit should succeed
    expect($sms->send($phone, 'Message 5 (Bypass)', true))->toBeTrue();
});
