<?php

use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Models\UserChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/**
 * A resident who is not eligible for the service, with one requirement to upload against.
 *
 * @return array{0: User, 1: GovernmentService, 2: ServiceRequirement}
 */
function notEligibleResident(): array
{
    $resident = User::factory()->create(['role' => 'resident', 'valid_id_path' => 'valid_ids/id.png']);
    $category = ServiceCategory::create(['category_name' => 'Education']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Education Assistance',
        'description' => 'Desc',
        'procedure' => 'Proc',
        'icon' => 'academic-cap',
    ]);
    $requirement = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Barangay Certificate', 'ceb' => 'Barangay Certificate', 'fil' => 'Barangay Certificate'],
        'is_required' => true,
    ]);

    // Assessment exists but was not approved
    EligibilityAssessment::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'ineligible']);

    return [$resident, $service, $requirement];
}

/** Approved assessment with one flagged answer, so the resident passes the eligibility gate. */
function makeEligibleFor(User $resident, GovernmentService $service): void
{
    $assessment = EligibilityAssessment::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'eligible']);
    $assessment->answers()->create(['question' => 'Eligible', 'answer' => 'Yes', 'is_eligible' => true]);
}

test('an ineligible resident cannot start a checklist or upload documents', function () {
    Storage::fake('public');
    [$resident, $service, $requirement] = notEligibleResident();

    $this->actingAs($resident)
        ->post(route('resident.eligibility.set_type', $service), ['application_type' => 'new'])
        ->assertSessionHas('error');

    $this->actingAs($resident)
        ->post(route('resident.eligibility.upload', [$service, $requirement]), [
            'document' => UploadedFile::fake()->image('cert.jpg'),
        ])
        ->assertSessionHas('error');

    expect(UserChecklist::where('user_id', $resident->id)->exists())->toBeFalse();
});

test('avatar uploads reject SVG files', function () {
    Storage::fake('public');
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->post(route('resident.profile.avatar'), [
            'avatar' => UploadedFile::fake()->create('avatar.svg', 10, 'image/svg+xml'),
        ])
        ->assertSessionHasErrors('avatar');

    expect($resident->fresh()->avatar)->toBeNull();
});
