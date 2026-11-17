<?php

use App\Models\User;
use App\Services\SystemStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('facilitator gets a status row for every technology', function () {
    $facilitator = User::factory()->create(['role' => 'facilitator']);

    $response = $this->actingAs($facilitator)->getJson(route('facilitator.system-status'));

    $response->assertOk();
    $names = collect($response->json())->pluck('name');
    expect($names)->toContain('Database', 'Cache', 'OCR engine (Tesseract)', 'SMS');
    expect(collect($response->json())->every(fn ($row) => isset($row['ok'], $row['detail'])))->toBeTrue();
});

test('database check reports ok when the database answers', function () {
    $database = collect(app(SystemStatus::class)->checks())->firstWhere('name', 'Database');

    expect($database['ok'])->toBeTrue();
});

test('residents cannot read the system status', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)->getJson(route('facilitator.system-status'))->assertForbidden();
});
