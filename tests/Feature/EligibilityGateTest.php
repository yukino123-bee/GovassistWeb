<?php

use App\Models\AssessmentAnswer;
use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an eligible status alone does not let a resident in when the live score is below the program minimum', function () {
    $category = ServiceCategory::create(['category_name' => 'Educational Assistance']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Educational Assistance',
        'description' => 'School support',
        'procedure' => 'Complete the assessment.',
        'passing_percentage' => 100,
        'minimum_percentage' => 60,
    ]);
    $resident = User::factory()->create(['role' => 'resident']);

    $assessment = EligibilityAssessment::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'eligible',
    ]);
    AssessmentAnswer::create(['assessment_id' => $assessment->id, 'question' => 'Q1', 'answer' => 'Yes', 'is_eligible' => true, 'is_required' => true]);
    $failed = AssessmentAnswer::create(['assessment_id' => $assessment->id, 'question' => 'Q2', 'answer' => 'No', 'is_eligible' => false, 'is_required' => true]);

    // 50% score, minimum 60%: the stored "eligible" status must not count
    expect($resident->isEligibleFor($service->id))->toBeFalse();

    $failed->update(['is_eligible' => true]);

    // 100% score: eligible again
    expect($resident->isEligibleFor($service->id))->toBeTrue();
});
