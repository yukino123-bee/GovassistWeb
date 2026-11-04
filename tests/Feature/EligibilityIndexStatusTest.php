<?php

use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('assessed service card shows its status instead of the start button', function (string $status, string $labelKey) {
    $resident = User::where('role', 'resident')->first();
    EligibilityAssessment::where('user_id', $resident->id)->delete();

    $assessed = GovernmentService::first();
    EligibilityAssessment::create([
        'user_id' => $resident->id,
        'service_id' => $assessed->id,
        'status' => $status,
    ]);

    $response = $this->actingAs($resident)->get(route('resident.eligibility'))->assertOk();

    $response->assertDontSee(route('resident.eligibility.assess', $assessed->id), false);
    $response->assertSee(__('messages.'.$labelKey));

    GovernmentService::where('id', '!=', $assessed->id)->get()->each(
        fn ($service) => $response->assertSee(route('resident.eligibility.assess', $service->id), false)
    );
})->with([
    'pending' => ['pending', 'pending_review'],
    'eligible' => ['eligible', 'eligible'],
]);
