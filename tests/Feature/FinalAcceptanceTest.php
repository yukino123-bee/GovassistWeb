<?php

use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\InquiryRequirense;
use App\Models\ReassessmentRequest;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use App\Models\UserInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
});

function residentUser(): User
{
    return User::factory()->create(['role' => 'resident']);
}

test('every parameterless page renders for its role in every language', function () {
    $facilitator = User::where('role', 'facilitator')->first();
    $resident = User::where('role', 'resident')->first();
    $resident->update(['valid_id_path' => 'valid_ids/x.png']);
    Storage::disk('public')->put('valid_ids/x.png', 'x');

    $routes = collect(Route::getRoutes()->getRoutes())->filter(fn ($r) => in_array('GET', $r->methods())
        && (str_starts_with($r->uri(), 'resident/') || str_starts_with($r->uri(), 'facilitator/'))
        && ! str_contains($r->uri(), '{') && ! str_contains($r->uri(), 'export'));

    foreach (['en', 'ceb', 'fil'] as $lang) {
        foreach ($routes as $route) {
            $user = str_starts_with($route->uri(), 'resident') ? $resident : $facilitator;
            $user->update(['language' => $lang]);
            $response = $this->actingAs($user)->withSession(['locale' => $lang])->get('/'.$route->uri());
            expect($response->getStatusCode())->toBeLessThan(500, "{$lang} /{$route->uri()}");
            if ($route->uri() !== 'resident/profile/valid-id') {
                expect($response->getStatusCode())->toBeIn([200, 302], "{$lang} /{$route->uri()}");
            }
        }
    }
});

test('csv and xlsx exports respond for a facilitator', function () {
    $facilitator = User::where('role', 'facilitator')->first();
    foreach (['applications', 'residents', 'assessments', 'inquiries', 'all'] as $type) {
        $this->actingAs($facilitator)->get(route("facilitator.reports.export.{$type}"))->assertOk();
    }
});

test('residents cannot reach facilitator routes and facilitators cannot reach resident routes', function () {
    $resident = residentUser();
    $facilitator = User::where('role', 'facilitator')->first();

    foreach (['dashboard', 'users', 'reports', 'services', 'assessments', 'applications'] as $page) {
        $this->actingAs($resident)->get(route("facilitator.{$page}"))->assertStatus(403);
    }
    $this->actingAs($resident)->get(route('facilitator.reports.export.residents'))->assertStatus(403);
    $this->actingAs($resident)->delete(route('facilitator.users.destroy', $facilitator))->assertStatus(403);

    $this->actingAs($facilitator)->get(route('resident.home'))->assertStatus(403);
});

test('unverified residents are sent to email verification', function () {
    $unverified = User::factory()->unverified()->create(['role' => 'resident']);
    $this->actingAs($unverified)->get(route('resident.home'))->assertRedirect(route('verification.notice'));
});

test('a resident cannot see another resident results, documents or inquiries', function () {
    $owner = User::where('role', 'resident')->first();
    $intruder = residentUser();

    $assessment = EligibilityAssessment::where('user_id', $owner->id)->first();
    $this->actingAs($intruder)->get(route('resident.eligibility.result', $assessment->id))->assertNotFound();

    $service = GovernmentService::first();
    $checklist = UserChecklist::create(['user_id' => $owner->id, 'service_id' => $service->id, 'status' => 'draft']);
    $item = UserChecklistItem::create([
        'checklist_id' => $checklist->id,
        'requirement_id' => $service->requirements->first()->id,
        'is_submitted' => true,
        'file_path' => 'documents/a.jpg',
        'status' => 'pending',
    ]);
    Storage::disk('public')->put('documents/a.jpg', 'x');
    $this->actingAs($intruder)->get(route('resident.eligibility.document', $item))->assertNotFound();

    $inquiry = UserInquiry::create(['user_id' => $owner->id, 'inquiry_text' => 'private', 'status' => 'pending']);
    $reply = InquiryRequirense::create(['inquiry_id' => $inquiry->id, 'requireent_text' => 'mine', 'responded_by' => $owner->id, 'is_system' => false]);

    $this->actingAs($intruder)->get(route('resident.inquiry.messages', $inquiry))->assertForbidden();
    $this->actingAs($intruder)->post(route('resident.inquiry.reply', $inquiry), ['message' => 'hi'])->assertForbidden();
    $this->actingAs($intruder)->post(route('resident.inquiry.resolve', $inquiry))->assertForbidden();
    $this->actingAs($intruder)->delete(route('resident.inquiry.delete_inquiry', $inquiry))->assertForbidden();
    $this->actingAs($intruder)->delete(route('resident.inquiry.delete_reply', $reply))->assertForbidden();

    expect(UserInquiry::find($inquiry->id))->not->toBeNull();
    expect(InquiryRequirense::find($reply->id))->not->toBeNull();
});

test('a resident cannot request reassessment for a service they never assessed', function () {
    $resident = residentUser();
    $service = GovernmentService::first();

    $this->actingAs($resident)->post(route('resident.eligibility.reassess', $service), ['reason' => 'please'])
        ->assertSessionHas('error');

    expect(ReassessmentRequest::where('user_id', $resident->id)->count())->toBe(0);
});

test('a locked checklist rejects uploads and leaves no orphan file', function () {
    $resident = residentUser();
    $service = GovernmentService::first();
    $requirement = $service->requirements->first();
    UserChecklist::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'pending']);

    $this->actingAs($resident)->post(route('resident.eligibility.upload', [$service, $requirement]), [
        'document' => UploadedFile::fake()->image('d.jpg'),
    ])->assertSessionHas('error');

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

test('uploads reject non-image files and oversize files', function () {
    $resident = residentUser();
    $service = GovernmentService::first();
    $requirement = $service->requirements->first();

    $this->actingAs($resident)->post(route('resident.eligibility.upload', [$service, $requirement]), [
        'document' => UploadedFile::fake()->create('evil.php', 10, 'application/x-php'),
    ])->assertSessionHasErrors('document');
    $this->actingAs($resident)->post(route('resident.eligibility.upload', [$service, $requirement]), [
        'document' => UploadedFile::fake()->image('big.jpg')->size(6000),
    ])->assertSessionHasErrors('document');
});

test('facilitator cannot delete another facilitator or themselves', function () {
    $facilitator = User::where('role', 'facilitator')->first();
    $this->actingAs($facilitator)->delete(route('facilitator.users.destroy', $facilitator))->assertSessionHas('error');
    expect(User::find($facilitator->id))->not->toBeNull();
});

test('the assessment cannot be submitted twice for the same service', function () {
    $resident = residentUser();
    $service = GovernmentService::has('eligibilityQuestions')->first();
    $payload = [];
    foreach ($service->eligibilityQuestions as $q) {
        $payload['question_'.$q->id] = $q->type === 'boolean' ? $q->expected_value : '5000';
    }
    $this->actingAs($resident)->post(route('resident.eligibility.assess.submit', $service), $payload);
    $this->actingAs($resident)->post(route('resident.eligibility.assess.submit', $service), $payload)->assertSessionHas('error');

    expect(EligibilityAssessment::where('user_id', $resident->id)->where('service_id', $service->id)->count())->toBe(1);
});

test('xss in profile name is escaped on facilitator user list', function () {
    $facilitator = User::where('role', 'facilitator')->first();
    residentUser()->update(['name' => '<script>alert(1)</script>']);
    $this->actingAs($facilitator)->get(route('facilitator.users'))->assertDontSee('<script>alert(1)</script>', false);
});

test('creating an eligibility question falls back to the original text when translation fails', function () {
    Process::fake(fn () => throw new RuntimeException('python missing'));
    $facilitator = User::where('role', 'facilitator')->first();
    $service = GovernmentService::first();

    $this->actingAs($facilitator)->post(route('facilitator.eligibility.store'), [
        'service_id' => $service->id,
        'question_text' => 'Are you a resident of the city?',
        'type' => 'boolean',
        'expected_value' => 'true',
    ])->assertSessionHas('success');

    $this->assertDatabaseHas('eligibility_questions', [
        'question_text_en' => 'Are you a resident of the city?',
        'question_text_ceb' => 'Are you a resident of the city?',
    ]);
});

test('written eligibility answers are scored against accepted keywords', function () {
    $facilitator = User::where('role', 'facilitator')->first();
    $service = GovernmentService::first();
    $service->eligibilityQuestions()->delete();
    Process::fake();

    $this->actingAs($facilitator)->post(route('facilitator.eligibility.store'), [
        'service_id' => $service->id,
        'question_text' => 'Which barangay do you live in?',
        'type' => 'text',
        'expected_value' => 'Lahug, Apas ',
    ])->assertSessionHas('success');

    $question = $service->eligibilityQuestions()->first();
    expect($question->expected_value)->toBe('Lahug, Apas')
        ->and($question->passing_rule_label)->toBe('Mentions lahug / apas');

    $passer = residentUser();
    $this->actingAs($passer)->post(route('resident.eligibility.assess.submit', $service), ['question_'.$question->id => 'I live in LAHUG, Cebu'])
        ->assertRedirect();
    $failer = residentUser();
    $this->actingAs($failer)->post(route('resident.eligibility.assess.submit', $service), ['question_'.$question->id => 'Mandaue'])
        ->assertRedirect();

    expect(EligibilityAssessment::where('user_id', $passer->id)->value('status'))->toBe('eligible')
        ->and(EligibilityAssessment::where('user_id', $failer->id)->value('status'))->toBe('ineligible');
});

test('written questions without keywords stay informational', function () {
    $service = GovernmentService::first();
    $service->eligibilityQuestions()->delete();
    $question = $service->eligibilityQuestions()->create([
        'question_text_en' => 'Anything else?', 'question_text_ceb' => 'x', 'question_text_fil' => 'x',
        'type' => 'text', 'expected_value' => '', 'operator' => '=', 'is_required' => true,
    ]);

    $resident = residentUser();
    $this->actingAs($resident)->post(route('resident.eligibility.assess.submit', $service), ['question_'.$question->id => 'nothing'])->assertRedirect();

    expect(EligibilityAssessment::where('user_id', $resident->id)->value('status'))->toBe('eligible');
});

test('residents, assessments and applications lists are paginated and searchable on the server', function () {
    $facilitator = User::where('role', 'facilitator')->first();
    User::factory()->count(25)->create(['role' => 'resident']);
    User::factory()->create(['role' => 'resident', 'name' => 'Zebediah Needle']);

    $this->actingAs($facilitator)->get(route('facilitator.users'))->assertOk()->assertDontSee('Zebediah Needle')->assertSee('page=2');
    $this->actingAs($facilitator)->get(route('facilitator.users', ['q' => 'Zebediah']))->assertOk()->assertSee('Zebediah Needle');
    $this->actingAs($facilitator)->get(route('facilitator.users', ['page' => 2]))->assertOk();
    $this->actingAs($facilitator)->get(route('facilitator.assessments', ['q' => 'nobody-matches']))->assertOk()->assertSee('No resident assessments');
});
