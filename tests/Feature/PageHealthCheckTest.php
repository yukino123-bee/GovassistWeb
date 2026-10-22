<?php

use App\Models\EligibilityAssessment;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('public pages render successfully without errors', function () {
    $this->get('/')->assertRedirect(route('login'));
    $this->get(route('login'))->assertOk();
    $this->get(route('register'))->assertOk();
    $this->get(route('password.request'))->assertOk();
    $this->get(route('password.reset', ['token' => 'sample-token']))->assertOk();
});

test('resident pages render successfully without errors', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);

    $resident = User::where('role', 'resident')->first();
    $serviceWithoutAssessment = GovernmentService::whereDoesntHave('assessments', function ($q) use ($resident) {
        $q->where('user_id', $resident->id);
    })->first();

    // Give resident valid ID to make profile complete
    $resident->update(['valid_id_path' => 'valid_ids/sample_id.png']);
    Storage::disk('public')->put('valid_ids/sample_id.png', 'fake-image-content');

    // Resident home, eligibility, inquiry
    $this->actingAs($resident)->get(route('resident.home'))->assertOk();
    $this->actingAs($resident)->get(route('resident.eligibility'))->assertOk();
    $this->actingAs($resident)->get(route('resident.inquiry'))->assertOk();

    // Resident legal
    $this->actingAs($resident)->get(route('resident.legal.terms'))->assertOk();
    $this->actingAs($resident)->get(route('resident.legal.privacy'))->assertOk();

    // Resident profile & valid ID
    $this->actingAs($resident)->get(route('resident.profile'))->assertOk();
    $this->actingAs($resident)->get(route('resident.profile.edit'))->assertOk();
    $this->actingAs($resident)->get(route('resident.profile.valid_id'))->assertOk();

    // Resident assessment form (for service without prior assessment)
    $this->actingAs($resident)->get(route('resident.eligibility.assess', $serviceWithoutAssessment))->assertOk();

    // Assessment result page
    $assessment = EligibilityAssessment::where('user_id', $resident->id)->first();
    expect($assessment)->not->toBeNull();
    $refNo = 'REF-'.str_pad((string) $assessment->id, 5, '0', STR_PAD_LEFT);
    $this->actingAs($resident)->get(route('resident.eligibility.result', $refNo))->assertOk();

    // Resident checklist for eligible service
    $eligibleService = $assessment->service;
    $this->actingAs($resident)->get(route('resident.eligibility.checklist', $eligibleService))->assertOk();

    // Inquiries messages
    $inquiry = UserInquiry::where('user_id', $resident->id)->first();
    expect($inquiry)->not->toBeNull();
    $this->actingAs($resident)->get(route('resident.inquiry.messages', $inquiry))->assertOk();
});

test('facilitator pages render successfully without errors', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);

    $facilitator = User::where('role', 'facilitator')->first();
    $resident = User::where('role', 'resident')->first();
    $service = GovernmentService::first();
    $question = EligibilityQuestion::first();
    $checklist = UserChecklist::first();
    $assessment = EligibilityAssessment::first();
    $inquiry = UserInquiry::first();

    $resident->update(['valid_id_path' => 'valid_ids/resident.png']);
    Storage::disk('public')->put('valid_ids/resident.png', 'resident-id');

    // Dashboard & Profile
    $this->actingAs($facilitator)->get(route('facilitator.dashboard'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.profile.edit'))->assertOk();

    // Services CRUD pages
    $this->actingAs($facilitator)->get(route('facilitator.services'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.services.create'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.services.edit', $service))->assertOk();

    // Requirements
    $this->actingAs($facilitator)->get(route('facilitator.requirements'))->assertOk();

    // Eligibility Questions
    $this->actingAs($facilitator)->get(route('facilitator.eligibility'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.eligibility.edit', $question))->assertOk();

    // Users
    $this->actingAs($facilitator)->get(route('facilitator.users'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.users.create'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.users.show', $resident))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.users.edit', $resident))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.users.valid_id', $resident))->assertOk();

    // Assessments
    $this->actingAs($facilitator)->get(route('facilitator.assessments'))->assertOk();
    if ($assessment) {
        $this->actingAs($facilitator)->get(route('facilitator.assessments.show', $assessment))->assertOk();
    }

    // Reassessments
    $this->actingAs($facilitator)->get(route('facilitator.reassessments'))->assertOk();

    // Applications / Checklists
    $this->actingAs($facilitator)->get(route('facilitator.applications'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.applications.create'))->assertOk();
    if ($checklist) {
        $this->actingAs($facilitator)->get(route('facilitator.applications.show', $checklist))->assertOk();
        $this->actingAs($facilitator)->get(route('facilitator.applications.edit', $checklist))->assertOk();

        $item = $checklist->items()->first();
        if ($item) {
            $item->update(['file_path' => 'documents/doc.png', 'is_submitted' => true]);
            Storage::disk('public')->put('documents/doc.png', 'doc-content');
            $this->actingAs($facilitator)->get(route('facilitator.checklist_items.document', $item))->assertOk();
        }
    }

    // Inquiries
    $this->actingAs($facilitator)->get(route('facilitator.inquiries'))->assertOk();
    if ($inquiry) {
        $this->actingAs($facilitator)->get(route('facilitator.inquiries.messages', $inquiry))->assertOk();
    }

    // Templates
    $this->actingAs($facilitator)->get(route('facilitator.templates'))->assertOk();

    // Reports & Exports
    $this->actingAs($facilitator)->get(route('facilitator.reports'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.reports.export.applications'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.reports.export.residents'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.reports.export.assessments'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.reports.export.inquiries'))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.reports.export.all'))->assertOk();
});
