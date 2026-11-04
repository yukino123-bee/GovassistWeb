<?php

use App\Models\InquiryRequirense;
use App\Models\User;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a conversation with no activity for a day is deleted', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $inquiry = UserInquiry::create(['user_id' => $resident->id, 'inquiry_text' => 'Hello', 'status' => 'pending']);

    $this->travel(23)->hours();
    $this->artisan('inquiries:prune-inactive')->assertSuccessful();
    expect(UserInquiry::find($inquiry->id))->not->toBeNull();

    $this->travel(2)->hours();
    $this->artisan('inquiries:prune-inactive')->assertSuccessful();
    expect(UserInquiry::find($inquiry->id))->toBeNull();
});

test('a new message keeps an old conversation alive for another day', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $inquiry = UserInquiry::create(['user_id' => $resident->id, 'inquiry_text' => 'Hello', 'status' => 'pending']);

    $this->travel(20)->hours();
    InquiryRequirense::create(['inquiry_id' => $inquiry->id, 'requireent_text' => 'Still need help', 'responded_by' => $resident->id]);

    $this->travel(10)->hours();
    $this->artisan('inquiries:prune-inactive')->assertSuccessful();
    expect(UserInquiry::find($inquiry->id))->not->toBeNull();

    $this->travel(15)->hours();
    $this->artisan('inquiries:prune-inactive')->assertSuccessful();
    expect(UserInquiry::find($inquiry->id))->toBeNull();
});
