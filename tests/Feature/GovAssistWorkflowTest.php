<?php

use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\InquiryRequirense;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\ServiceTranslation;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('facilitator can perform services CRUD', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $category = ServiceCategory::create(['category_name' => 'Education']);

    // 1. Service list
    $response = $this->actingAs($facilitator)->get(route('facilitator.services'));
    $response->assertStatus(200);

    // 2. Store service
    $storeResponse = $this->actingAs($facilitator)->post(route('facilitator.services.store'), [
        'category_id' => $category->id,
        'name_en' => 'New Service EN',
        'name_ceb' => 'New Service CEB',
        'name_fil' => 'New Service FIL',
        'description_en' => 'Desc EN',
        'description_ceb' => 'Desc CEB',
        'description_fil' => 'Desc FIL',
        'procedure_en' => 'Proc EN',
        'procedure_ceb' => 'Proc CEB',
        'procedure_fil' => 'Proc FIL',
        'icon' => 'academic-cap',
    ]);
    $storeResponse->assertRedirect(route('facilitator.services'));

    $service = GovernmentService::first();
    expect($service->service_name)->toBe('New Service EN');
    expect($service->icon)->toBe('academic-cap');

    // 3. Edit service page
    $editResponse = $this->actingAs($facilitator)->get(route('facilitator.services.edit', $service->id));
    $editResponse->assertStatus(200);

    // 4. Update service
    $updateResponse = $this->actingAs($facilitator)->put(route('facilitator.services.update', $service->id), [
        'category_id' => $category->id,
        'name_en' => 'Updated Service EN',
        'name_ceb' => 'Updated Service CEB',
        'name_fil' => 'Updated Service FIL',
        'description_en' => 'Updated Desc EN',
        'description_ceb' => 'Updated Desc CEB',
        'description_fil' => 'Updated Desc FIL',
        'procedure_en' => 'Updated Proc EN',
        'procedure_ceb' => 'Updated Proc CEB',
        'procedure_fil' => 'Updated Proc FIL',
        'icon' => 'heart',
    ]);
    $updateResponse->assertRedirect(route('facilitator.services'));

    $service->refresh();
    expect($service->service_name)->toBe('Updated Service EN');
    expect($service->icon)->toBe('heart');

    // Check translations are correct
    expect($service->name_en)->toBe('Updated Service EN');
    expect($service->name_ceb)->toBe('Updated Service CEB');
    expect($service->name_fil)->toBe('Updated Service FIL');

    // 5. Delete service
    $deleteResponse = $this->actingAs($facilitator)->delete(route('facilitator.services.destroy', $service->id));
    $deleteResponse->assertRedirect(route('facilitator.services'));
    expect(GovernmentService::count())->toBe(0);
});

test('facilitator can manage checklist requirements', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $category = ServiceCategory::create(['category_name' => 'Education']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Education Assistance',
        'description' => 'Desc',
        'procedure' => 'Proc',
        'icon' => 'academic-cap',
    ]);

    // 1. Requirements page
    $response = $this->actingAs($facilitator)->get(route('facilitator.requirements'));
    $response->assertStatus(200);

    // 2. Add requirement
    $storeResponse = $this->actingAs($facilitator)->post(route('facilitator.requirements.store'), [
        'service_id' => $service->id,
        'requirement_text_en' => 'Valid Student ID',
        'requirement_text_ceb' => 'Balido nga ID sa Estudyante',
        'requirement_text_fil' => 'Balidong ID ng Estudyante',
        'is_required' => '1',
    ]);
    $storeResponse->assertRedirect();

    $requirement = ServiceRequirement::first();
    expect($requirement->requirement_text['en'])->toBe('Valid Student ID');
    expect($requirement->is_required)->toBeTrue();

    // 3. Delete requirement
    $deleteResponse = $this->actingAs($facilitator)->delete(route('facilitator.requirements.destroy', $requirement->id));
    $deleteResponse->assertRedirect();
    expect(ServiceRequirement::count())->toBe(0);
});

test('facilitator can manage eligibility questions', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $category = ServiceCategory::create(['category_name' => 'Education']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Education Assistance',
        'description' => 'Desc',
        'procedure' => 'Proc',
        'icon' => 'academic-cap',
    ]);

    // 1. Eligibility page
    $response = $this->actingAs($facilitator)->get(route('facilitator.eligibility'));
    $response->assertStatus(200);

    // 2. Add question
    $storeResponse = $this->actingAs($facilitator)->post(route('facilitator.eligibility.store'), [
        'service_id' => $service->id,
        'question_text' => 'Are you a resident?',
        'type' => 'boolean',
        'operator' => '==',
        'expected_value' => 'true',
    ]);
    $storeResponse->assertRedirect();

    $question = EligibilityQuestion::first();
    expect($question->question_text_en)->toBe('Are you a resident?');

    // 3. Delete question
    $deleteResponse = $this->actingAs($facilitator)->delete(route('facilitator.eligibility.destroy', $question->id));
    $deleteResponse->assertRedirect();
    expect(EligibilityQuestion::count())->toBe(0);
});

test('resident can switch languages and complete full flow', function () {
    Storage::fake('public');

    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create([
        'role' => 'resident',
        'dob' => '2000-01-01',
        'address' => 'Test Address',
        'civil_status' => 'Single',
        'contact_number' => '09123456789',
        'valid_id_path' => 'valid_ids/test.png',
    ]);
    $category = ServiceCategory::create(['category_name' => 'Education']);

    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Education Assistance',
        'description' => 'Desc',
        'procedure' => 'Proc',
        'icon' => 'academic-cap',
    ]);

    ServiceTranslation::create([
        'service_id' => $service->id,
        'language_code' => 'en',
        'service_name' => 'Education Assistance EN',
        'description' => 'Desc EN',
        'procedure' => 'Proc EN',
    ]);

    ServiceTranslation::create([
        'service_id' => $service->id,
        'language_code' => 'ceb',
        'service_name' => 'Education Assistance CEB',
        'description' => 'Desc CEB',
        'procedure' => 'Proc CEB',
    ]);

    ServiceTranslation::create([
        'service_id' => $service->id,
        'language_code' => 'fil',
        'service_name' => 'Education Assistance FIL',
        'description' => 'Desc FIL',
        'procedure' => 'Proc FIL',
    ]);

    $requirement = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => [
            'en' => 'Student ID',
            'ceb' => 'ID sa Estudyante',
            'fil' => 'ID ng Estudyante',
        ],
        'is_required' => true,
    ]);

    $question = EligibilityQuestion::create([
        'service_id' => $service->id,
        'question_text_en' => 'Are you enrolled?',
        'question_text_ceb' => 'Enrolled ba ka?',
        'question_text_fil' => 'Enrolled ka ba?',
        'type' => 'boolean',
        'operator' => '==',
        'expected_value' => 'true',
    ]);

    // 1. Access Resident Home
    $response = $this->actingAs($resident)->get(route('resident.home'));
    $response->assertStatus(200);

    // 2. Perform Eligibility Assessment
    $assessResponse = $this->actingAs($resident)->post(route('resident.eligibility.assess.submit', $service->id), [
        "question_{$question->id}" => 'true',
    ]);

    $assessment = EligibilityAssessment::first();
    expect($assessment->status)->toBe('eligible');
    $assessResponse->assertRedirect(route('resident.eligibility.result', $assessment->id));

    // 3. View Checklist
    $checklistResponse = $this->actingAs($resident)->get(route('resident.eligibility.checklist', $service->id));
    $checklistResponse->assertStatus(200);

    // 4. Upload Checklist Requirement File
    $file = UploadedFile::fake()->create('id.pdf', 500);
    $uploadResponse = $this->actingAs($resident)->post(route('resident.eligibility.upload', [$service->id, $requirement->id]), [
        'document' => $file,
    ]);
    $uploadResponse->assertRedirect();

    $checklistItem = UserChecklistItem::first();
    expect($checklistItem->file_path)->not->toBeNull();
    expect($checklistItem->status)->toBe('pending');

    // 5. Submit Application
    $applyResponse = $this->actingAs($resident)->post(route('resident.eligibility.apply', $service->id));
    $applyResponse->assertRedirect(route('resident.home'));

    $userChecklist = UserChecklist::first();
    expect($userChecklist->status)->toBe('pending');

    // 6. Facilitator reviews application
    $appShowResponse = $this->actingAs($facilitator)->get(route('facilitator.applications.show', $userChecklist->id));
    $appShowResponse->assertStatus(200);

    // Facilitator approves the item
    $approveItemResponse = $this->actingAs($facilitator)->post(route('facilitator.checklist_items.update_status', $checklistItem->id), [
        'status' => 'approved',
    ]);
    $approveItemResponse->assertRedirect();
    $checklistItem->refresh();
    expect($checklistItem->status)->toBe('approved');

    // Facilitator approves the checklist application
    $approveAppResponse = $this->actingAs($facilitator)->post(route('facilitator.applications.update_status', $userChecklist->id), [
        'status' => 'approved',
        'remarks' => 'All documents verified.',
    ]);
    $approveAppResponse->assertRedirect();
    $userChecklist->refresh();
    expect($userChecklist->status)->toBe('approved');
    expect($userChecklist->remarks)->toBe('All documents verified.');
});

test('resident can submit inquiry and facilitator replies', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $category = ServiceCategory::create(['category_name' => 'Education']);
    $service = GovernmentService::create([
        'category_id' => $category->id,
        'service_name' => 'Education Assistance',
        'description' => 'Desc',
        'procedure' => 'Proc',
        'icon' => 'academic-cap',
    ]);

    // 1. Submit manual inquiry
    $inquiryResponse = $this->actingAs($resident)->post(route('resident.inquiry.manual'), [
        'inquiry_text' => 'Help with Education Assistance',
        'service_id' => $service->id,
    ]);
    $inquiryResponse->assertRedirect();

    $inquiry = UserInquiry::first();
    expect($inquiry->inquiry_text)->toBe('Help with Education Assistance');
    expect($inquiry->status)->toBe('pending');

    // 2. Facilitator views inquiries
    $inqsResponse = $this->actingAs($facilitator)->get(route('facilitator.inquiries'));
    $inqsResponse->assertStatus(200);

    // 3. Facilitator replies to inquiry
    $replyResponse = $this->actingAs($facilitator)->post(route('facilitator.inquiries.reply', $inquiry->id), [
        'message' => 'We can help you with this program.',
    ]);
    $replyResponse->assertRedirect();

    $inquiry->refresh();
    expect($inquiry->status)->toBe('resolved');
    expect($inquiry->responses->last()->response_text)->toBe('We can help you with this program.');

    // 4. Submit secondary manual inquiry with inquiry_id (appends to existing thread)
    $secondResponse = $this->actingAs($resident)->post(route('resident.inquiry.manual'), [
        'inquiry_id' => $inquiry->id,
        'inquiry_text' => 'What are the requirements for Education Assistance?',
        'service_id' => $service->id,
    ]);
    $secondResponse->assertRedirect();
    expect($inquiry->responses()->count())->toBe(3);

    // 5. Submit new manual inquiry without inquiry_id (creates distinct new thread)
    $thirdResponse = $this->actingAs($resident)->post(route('resident.inquiry.manual'), [
        'inquiry_text' => 'New distinct inquiry about Medical Assistance',
    ]);
    $thirdResponse->assertRedirect();
    expect(UserInquiry::where('user_id', $resident->id)->count())->toBe(2);
});

test('facilitator can reply via json ajax and poll messages', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $facilitator = User::factory()->create(['role' => 'facilitator']);

    $inquiry = UserInquiry::create([
        'user_id' => $resident->id,
        'inquiry_text' => 'Need help with housing',
        'status' => 'pending',
    ]);

    // AJAX reply from facilitator
    $response = $this->actingAs($facilitator)
        ->postJson(route('facilitator.inquiries.reply', $inquiry->id), [
            'message' => 'Housing assistance is available on Mondays.',
        ]);

    $response->assertOk();
    $response->assertJson(['success' => true]);

    // Poll messages endpoint
    $pollResponse = $this->actingAs($resident)
        ->getJson(route('resident.inquiry.messages', $inquiry->id));

    $pollResponse->assertOk();
    $pollResponse->assertJsonPath('inquiry.id', $inquiry->id);
});

test('facilitator can delete inquiry thread', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $facilitator = User::factory()->create(['role' => 'facilitator']);

    $inquiry = UserInquiry::create([
        'user_id' => $resident->id,
        'inquiry_text' => 'Some inquiry content',
        'status' => 'pending',
    ]);

    InquiryRequirense::create([
        'inquiry_id' => $inquiry->id,
        'requireent_text' => 'Some response',
        'responded_by' => $facilitator->id,
    ]);

    $deleteResponse = $this->actingAs($facilitator)->delete(route('facilitator.inquiries.delete', $inquiry->id));
    $deleteResponse->assertRedirect(route('facilitator.inquiries'));

    $this->assertDatabaseMissing('user_inquiries', ['id' => $inquiry->id]);
    $this->assertDatabaseMissing('inquiry_requirenses', ['inquiry_id' => $inquiry->id]);
});

test('resident can unsend their reply message or delete inquiry', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $inquiry = UserInquiry::create([
        'user_id' => $resident->id,
        'inquiry_text' => 'First message of inquiry',
        'status' => 'pending',
    ]);

    $reply = InquiryRequirense::create([
        'inquiry_id' => $inquiry->id,
        'requireent_text' => 'Resident response message',
        'responded_by' => $resident->id,
    ]);

    // 1. Delete reply (unsend message)
    $response1 = $this->actingAs($resident)->delete(route('resident.inquiry.delete_reply', $reply->id));
    $response1->assertJson(['success' => true]);
    $this->assertDatabaseMissing('inquiry_requirenses', ['id' => $reply->id]);

    // 2. Delete parent inquiry thread
    $response2 = $this->actingAs($resident)->delete(route('resident.inquiry.delete_inquiry', $inquiry->id));
    $response2->assertJson(['success' => true]);
    $this->assertDatabaseMissing('user_inquiries', ['id' => $inquiry->id]);
});

test('resident can edit and resubmit application if not approved', function () {
    Storage::fake('public');

    $resident = User::factory()->create([
        'role' => 'resident',
        'dob' => '2000-01-01',
        'address' => 'Test Address',
        'civil_status' => 'Single',
        'contact_number' => '09123456789',
        'valid_id_path' => 'valid_ids/test.png',
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
        'requirement_text' => [
            'en' => 'Student ID',
        ],
        'is_required' => true,
    ]);

    // Setup an initial rejected application
    $checklist = UserChecklist::create([
        'user_id' => $resident->id,
        'service_id' => $service->id,
        'status' => 'rejected',
    ]);
    UserChecklistItem::create([
        'checklist_id' => $checklist->id,
        'requirement_id' => $requirement->id,
        'file_path' => 'docs/id.pdf',
        'is_submitted' => true,
        'status' => 'pending',
    ]);

    // Try to unlock application
    $response = $this->actingAs($resident)->post(route('resident.eligibility.checklist.edit', $service->id));
    $response->assertRedirect();

    $checklist->refresh();
    expect($checklist->status)->toBe('draft');
});
