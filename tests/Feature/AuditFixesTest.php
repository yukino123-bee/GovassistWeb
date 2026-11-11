<?php

use App\Models\DocumentTemplate;
use App\Models\EligibilityQuestion;
use App\Models\GovernmentService;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserChecklistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makeServiceWithRequirement(): array
{
    $service = GovernmentService::create(['service_name' => 'Education Assistance', 'description' => 'd', 'procedure' => 'p']);
    $requirement = ServiceRequirement::create([
        'service_id' => $service->id,
        'requirement_text' => ['en' => 'Student ID', 'ceb' => 'ID', 'fil' => 'ID'],
        'is_required' => true,
    ]);

    return [$service, $requirement];
}

test('resident cannot upload documents after submitting the application', function () {
    Storage::fake('local');
    [$service, $requirement] = makeServiceWithRequirement();
    $resident = User::factory()->create(['role' => 'resident']);
    UserChecklist::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'pending']);

    $this->actingAs($resident)
        ->post(route('resident.eligibility.upload', [$service->id, $requirement->id]), [
            'document' => UploadedFile::fake()->image('id.jpg'),
        ])
        ->assertSessionHas('error');

    expect(UserChecklistItem::count())->toBe(0);
});

test('profile valid id is checked against the name through the configured disk', function () {
    Storage::fake('local');
    Process::fake(['*' => Process::result('{"match": false}')]);
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->post(route('resident.profile.update'), [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => $resident->email,
            'contact_number' => '09171234567',
            'valid_id' => UploadedFile::fake()->image('id.jpg'),
        ])
        ->assertSessionHas('error');

    Process::assertRan(fn ($process) => str_contains(implode(' ', $process->command), 'verify_id.py'));
    expect($resident->fresh()->valid_id_path)->toBeNull();
    expect(Storage::disk('local')->allFiles('valid_ids'))->toBeEmpty();
});

test('application cannot be approved while required documents are not approved', function () {
    [$service, $requirement] = makeServiceWithRequirement();
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);
    $checklist = UserChecklist::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'pending']);

    $this->actingAs($facilitator)
        ->post(route('facilitator.applications.update_status', $checklist), ['status' => 'approved'])
        ->assertSessionHasErrors('status');
    expect($checklist->fresh()->status)->toBe('pending');

    UserChecklistItem::create([
        'checklist_id' => $checklist->id,
        'requirement_id' => $requirement->id,
        'is_submitted' => true,
        'file_path' => 'documents/x.pdf',
        'status' => 'approved',
    ]);

    $this->actingAs($facilitator)
        ->post(route('facilitator.applications.update_status', $checklist), ['status' => 'approved'])
        ->assertSessionDoesntHaveErrors();
    expect($checklist->fresh()->status)->toBe('approved');
});

function readXlsx($response): array
{
    $zip = new ZipArchive;
    $zip->open($response->baseResponse->getFile()->getPathname());
    $sheets = [];

    for ($index = 1; $zip->locateName("xl/worksheets/sheet{$index}.xml") !== false; $index++) {
        $sheets[] = $zip->getFromName("xl/worksheets/sheet{$index}.xml");
    }

    $workbook = $zip->getFromName('xl/workbook.xml');
    $zip->close();

    return ['sheets' => $sheets, 'workbook' => $workbook];
}

test('exports are real xlsx files that never store user text as formulas', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    User::factory()->create(['role' => 'resident', 'name' => '=HYPERLINK("http://evil","x")']);

    $response = $this->actingAs($facilitator)->get(route('facilitator.reports.export.residents'));
    $response->assertOk()->assertDownload();

    expect($response->headers->get('content-type'))->toContain('spreadsheetml.sheet');

    $xlsx = readXlsx($response);
    expect($xlsx['sheets'][0])
        ->toContain('t="inlineStr"')
        ->toContain('=HYPERLINK(&quot;http://evil&quot;,&quot;x&quot;)')
        ->not->toContain('<f>');
});

test('applications export honours status, program and date filters', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    [$service] = makeServiceWithRequirement();
    $other = GovernmentService::create(['service_name' => 'Burial Assistance', 'description' => 'd', 'procedure' => 'p']);
    $resident = User::factory()->create(['role' => 'resident', 'name' => 'Ana Reyes']);
    $other_resident = User::factory()->create(['role' => 'resident', 'name' => 'Ben Santos']);

    UserChecklist::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'approved']);
    UserChecklist::create(['user_id' => $other_resident->id, 'service_id' => $other->id, 'status' => 'pending']);

    $sheet = readXlsx($this->actingAs($facilitator)->get(route('facilitator.reports.export.applications', ['status' => 'approved'])))['sheets'][0];
    expect($sheet)->toContain('Ana Reyes')->not->toContain('Ben Santos')->toContain('<autoFilter');

    $sheet = readXlsx($this->actingAs($facilitator)->get(route('facilitator.reports.export.applications', ['service_id' => $other->id])))['sheets'][0];
    expect($sheet)->toContain('Ben Santos')->not->toContain('Ana Reyes');

    $sheet = readXlsx($this->actingAs($facilitator)->get(route('facilitator.reports.export.applications', ['from' => now()->addDay()->toDateString()])))['sheets'][0];
    expect($sheet)->not->toContain('Ana Reyes');

    $this->actingAs($facilitator)
        ->get(route('facilitator.reports.export.applications', ['from' => '2026-12-31', 'to' => '2026-01-01']))
        ->assertSessionHasErrors('to');
});

test('master report contains a summary and a sheet for every dataset', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    [$service] = makeServiceWithRequirement();
    $resident = User::factory()->create(['role' => 'resident']);
    UserChecklist::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'approved']);

    $xlsx = readXlsx($this->actingAs($facilitator)->get(route('facilitator.reports.export.all')));

    expect($xlsx['sheets'])->toHaveCount(5)
        ->and($xlsx['workbook'])->toContain('Summary')->toContain('Applications')->toContain('Residents')->toContain('Eligibility')->toContain('Inquiries')
        ->and($xlsx['sheets'][0])->toContain('GovAssist Master Report')->toContain('Applications by program')->toContain('Education Assistance');
});

test('facilitator cannot delete facilitators or themselves but can delete residents', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    $otherFacilitator = User::factory()->create(['role' => 'facilitator']);
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($facilitator)->delete(route('facilitator.users.destroy', $facilitator))->assertSessionHas('error');
    $this->actingAs($facilitator)->delete(route('facilitator.users.destroy', $otherFacilitator))->assertSessionHas('error');
    expect(User::count())->toBe(3);

    $this->actingAs($facilitator)->delete(route('facilitator.users.destroy', $resident))->assertSessionHas('success');
    expect(User::count())->toBe(2);
});

test('facilitator cannot create an application for a non-resident user', function () {
    [$service] = makeServiceWithRequirement();
    $facilitator = User::factory()->create(['role' => 'facilitator']);

    $this->actingAs($facilitator)
        ->post(route('facilitator.applications.store'), [
            'user_id' => $facilitator->id,
            'service_id' => $service->id,
            'status' => 'pending',
        ])
        ->assertSessionHasErrors('user_id');
});

test('admin can add eligibility questions without technical fields', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    [$service] = makeServiceWithRequirement();

    $this->actingAs($facilitator)->post(route('facilitator.eligibility.store'), [
        'service_id' => $service->id,
        'question_text' => 'Is your household income below 15,000 PHP?',
        'type' => 'number',
        'operator' => '<=',
        'expected_value' => '15000',
    ])->assertSessionHasNoErrors();

    $this->actingAs($facilitator)->post(route('facilitator.eligibility.store'), [
        'service_id' => $service->id,
        'question_text' => 'Tell us about your situation',
        'type' => 'text',
    ])->assertSessionHasNoErrors();

    $number = EligibilityQuestion::where('type', 'number')->first();
    $text = EligibilityQuestion::where('type', 'text')->first();

    expect($number->passing_rule_label)->toBe('No more than 15000')
        ->and($text->passing_rule_label)->toBe('Not checked')
        ->and($text->question_text_ceb)->not->toBe('');

    $this->actingAs($facilitator)->post(route('facilitator.eligibility.store'), [
        'service_id' => $service->id,
        'question_text' => 'Bad',
        'type' => 'number',
        'operator' => '<=',
        'expected_value' => 'abc',
    ])->assertSessionHasErrors('expected_value');

    $this->actingAs($facilitator)->get(route('facilitator.eligibility'))->assertOk()->assertSee('No more than 15000');
    $this->actingAs($facilitator)->get(route('facilitator.eligibility.edit', $number))->assertOk();
});

function reviewScenario(string $status, array $overrides = []): array
{
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    Process::fake(['*' => Process::result(output: json_encode(array_merge([
        'status' => $status,
        'match' => $status === 'verified',
        'score' => 0.5,
        'method' => 'tesseract',
        'found' => ['barangay'],
        'missing' => ['indigency'],
        'issues' => ['The image looks blurry. Please hold the camera steady and retake the photo.'],
        'message' => 'Part of the document could not be matched automatically. A facilitator will check it.',
    ], $overrides)))]);

    $resident = User::factory()->create(['role' => 'resident']);
    [$service, $requirement] = makeServiceWithRequirement();
    Storage::disk('public')->put('templates/t.jpg', 'template');
    DocumentTemplate::create([
        'service_id' => $service->id,
        'requirement_id' => $requirement->id,
        'name_en' => 'Barangay, Indigency',
        'name_ceb' => 'Barangay, Indigency',
        'file_path' => 'templates/t.jpg',
    ]);

    makeEligibleFor($resident, $service);
    $response = test()->actingAs($resident)->post(route('resident.eligibility.upload', [$service->id, $requirement->id]), [
        'document' => UploadedFile::fake()->image('doc.jpg'),
    ]);

    return [$response, UserChecklistItem::first()];
}

test('a document that needs a second look is kept as pending with a note for the facilitator', function () {
    [$response, $item] = reviewScenario('review');

    $response->assertSessionHas('success');
    expect($item->status)->toBe('pending')
        ->and($item->review_result)->toBe('review')
        ->and($item->review_note)->toContain('Not found: indigency')
        ->and($item->review_note)->toContain('blurry');
});

test('residents can only upload image files', function () {
    Storage::fake('public');
    [$service, $requirement] = makeServiceWithRequirement();
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->post(route('resident.eligibility.upload', [$service->id, $requirement->id]), [
            'document' => UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors('document');
});

test('facilitator can re-check a document against its template', function () {
    [, $item] = reviewScenario('review');
    $facilitator = User::factory()->create(['role' => 'facilitator']);

    Process::fake(['*' => Process::result(output: json_encode([
        'status' => 'verified', 'match' => true, 'score' => 1, 'method' => 'tesseract',
        'found' => ['barangay', 'indigency'], 'missing' => [], 'issues' => [],
        'message' => 'Document verified successfully.',
    ]))]);

    $this->actingAs($facilitator)
        ->post(route('facilitator.checklist_items.auto_verify', $item))
        ->assertSessionHas('success');

    expect($item->fresh()->status)->toBe('approved')
        ->and($item->fresh()->review_result)->toBe('verified');
});

test('exports link to the login-protected review pages instead of embedding documents', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);
    [$service] = makeServiceWithRequirement();
    $resident = User::factory()->create(['role' => 'resident', 'valid_id_path' => 'valid_ids/a.jpg']);
    $checklist = UserChecklist::create(['user_id' => $resident->id, 'service_id' => $service->id, 'status' => 'pending']);

    $response = $this->actingAs($facilitator)->get(route('facilitator.reports.export.applications'));
    $zip = new ZipArchive;
    $zip->open($response->baseResponse->getFile()->getPathname());
    $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
    $rels = $zip->getFromName('xl/worksheets/_rels/sheet1.xml.rels');
    $zip->close();

    expect($sheet)->toContain('<hyperlinks>')->toContain('Open application')
        ->and($rels)->toContain(route('facilitator.applications.show', $checklist))->toContain('TargetMode="External"');

    $response = $this->actingAs($facilitator)->get(route('facilitator.reports.export.residents'));
    $zip->open($response->baseResponse->getFile()->getPathname());
    $rels = $zip->getFromName('xl/worksheets/_rels/sheet1.xml.rels');
    $zip->close();

    expect($rels)->toContain(route('facilitator.users.valid_id', $resident));

    $this->actingAs($resident)->get(route('facilitator.applications.show', $checklist))->assertForbidden();
});
