<?php

use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/**
 * @return array{0: User, 1: GovernmentService, 2: ServiceRequirement, 3: UserChecklist}
 */
function residentWithDraftChecklist(): array
{
    $resident = User::factory()->create([
        'role' => 'resident',
        'dob' => '2000-01-01',
        'address' => 'Purok 1, Fatima',
        'civil_status' => 'Single',
        'contact_number' => '09171234567',
        'valid_id_path' => 'valid_ids/id.png',
    ]);
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
    $assessment = EligibilityAssessment::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'eligible']);
    $assessment->answers()->create(['question' => 'Are you a resident?', 'answer' => 'true', 'is_eligible' => true]);
    $checklist = UserChecklist::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'draft']);

    return [$resident, $service, $requirement, $checklist];
}

test('resident can view their own uploaded document but not anyone else\'s', function () {
    Storage::fake(config('filesystems.default'));
    [$resident, , $requirement, $checklist] = residentWithDraftChecklist();

    Storage::disk(config('filesystems.default'))->put('documents/cert.png', 'cert-bytes');
    $item = UserChecklistItem::create([
        'checklist_id' => $checklist->id,
        'requirement_id' => $requirement->id,
        'is_submitted' => true,
        'file_path' => 'documents/cert.png',
        'status' => 'pending',
    ]);

    $this->actingAs($resident)->get(route('resident.eligibility.document', $item->id))->assertOk();

    $stranger = User::factory()->create(['role' => 'resident']);
    $this->actingAs($stranger)->get(route('resident.eligibility.document', $item->id))->assertNotFound();
});

test('a rejected document must be replaced before the application can be submitted', function () {
    [$resident, $service, $requirement, $checklist] = residentWithDraftChecklist();

    UserChecklistItem::create([
        'checklist_id' => $checklist->id,
        'requirement_id' => $requirement->id,
        'is_submitted' => true,
        'file_path' => 'documents/cert.png',
        'status' => 'rejected',
    ]);

    $this->actingAs($resident)
        ->get(route('resident.eligibility.checklist', $service->id))
        ->assertOk()
        ->assertSee(__('messages.doc_rejected'));

    $this->actingAs($resident)
        ->post(route('resident.eligibility.apply', $service->id))
        ->assertSessionHas('error', __('messages.flash_upload_all'));

    expect($checklist->fresh()->status)->toBe('draft');
});

test('checklist redirects to the profile when required details are missing', function () {
    [$resident, $service] = residentWithDraftChecklist();
    $resident->update(['valid_id_path' => null]);

    $this->actingAs($resident)
        ->get(route('resident.eligibility.checklist', $service->id))
        ->assertRedirect(route('resident.profile.edit'));
});

test('inquiry page embeds ticket text safely and without leaking other user data', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    UserInquiry::create([
        'user_id' => $resident->id,
        'inquiry_text' => '<script>alert("xss")</script>',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($resident)->get(route('resident.inquiry'))->assertOk();

    $response->assertDontSee('<script>alert("xss")</script>', false);
    $response->assertDontSee($resident->email);
});
