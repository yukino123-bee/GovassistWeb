<?php

use App\Models\User;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a resident message marks the thread unread for staff until staff open or reply to it', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $inquiry = UserInquiry::create(['user_id' => $resident->id, 'subject' => 'Help', 'inquiry_text' => 'Question', 'status' => 'pending']);

    // Resident writes: staff see it as unread
    $this->actingAs($resident)->postJson(route('resident.inquiry.reply', $inquiry), ['message' => 'Follow-up'])->assertOk();
    expect(UserInquiry::unreadForStaff()->count())->toBe(1)
        ->and($inquiry->fresh()->hasUnreadResidentMessage())->toBeTrue();

    // Staff open the thread: cleared
    $this->actingAs($facilitator)->getJson(route('facilitator.inquiries.messages', $inquiry))->assertOk();
    expect(UserInquiry::unreadForStaff()->count())->toBe(0);

    // Resident writes again: unread again
    $this->actingAs($resident)->postJson(route('resident.inquiry.reply', $inquiry), ['message' => 'Still waiting'])->assertOk();
    expect(UserInquiry::unreadForStaff()->count())->toBe(1);

    // Staff reply: cleared
    $this->actingAs($facilitator)->post(route('facilitator.inquiries.reply', $inquiry), ['message' => 'Answer'])->assertRedirect();
    expect(UserInquiry::unreadForStaff()->count())->toBe(0)
        ->and($inquiry->fresh()->hasUnreadResidentMessage())->toBeFalse();
});

test('staff replies never count as unread for staff', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $inquiry = UserInquiry::create(['user_id' => $resident->id, 'subject' => 'Help', 'inquiry_text' => 'Question', 'status' => 'pending']);

    $this->actingAs($facilitator)->post(route('facilitator.inquiries.reply', $inquiry), ['message' => 'Answer'])->assertRedirect();

    expect(UserInquiry::unreadForStaff()->count())->toBe(0);
});
