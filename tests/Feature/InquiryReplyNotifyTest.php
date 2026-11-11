<?php

use App\Models\InquiryRequirense;
use App\Models\User;
use App\Models\UserInquiry;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('only the first staff reply is texted, and later replies show as unread until the resident opens the inquiry page', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $inquiry = UserInquiry::create(['user_id' => $resident->id, 'subject' => 'Help', 'inquiry_text' => 'Question', 'status' => 'pending']);

    $this->mock(SmsService::class, fn ($mock) => $mock->shouldReceive('sendInquiryReply')->once());

    $this->actingAs($facilitator)->post(route('facilitator.inquiries.reply', $inquiry), ['message' => 'First answer'])->assertRedirect();
    expect($resident->fresh()->unreadInquiryCount())->toBe(1);

    // Second reply: the mock above expects exactly one SMS, so a second send would fail the test
    $this->actingAs($facilitator)->post(route('facilitator.inquiries.reply', $inquiry), ['message' => 'Follow-up'])->assertRedirect();
    expect($resident->fresh()->unreadInquiryCount())->toBe(1);

    // Opening the Inquiry page clears the indicator
    $this->actingAs($resident)->get(route('resident.inquiry'))->assertOk();
    expect($resident->fresh()->unreadInquiryCount())->toBe(0);

    // A reply after that shows again
    $this->actingAs($facilitator)->post(route('facilitator.inquiries.reply', $inquiry), ['message' => 'Third'])->assertRedirect();
    expect($resident->fresh()->unreadInquiryCount())->toBe(1);
    expect(InquiryRequirense::where('inquiry_id', $inquiry->id)->count())->toBe(3);
});

test('a resident reply does not count as unread for the resident', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $inquiry = UserInquiry::create(['user_id' => $resident->id, 'subject' => 'Help', 'inquiry_text' => 'Question', 'status' => 'pending']);
    InquiryRequirense::create(['inquiry_id' => $inquiry->id, 'requireent_text' => 'My follow-up', 'responded_by' => $resident->id]);

    expect($resident->unreadInquiryCount())->toBe(0);
});
