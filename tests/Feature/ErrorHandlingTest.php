<?php

use App\Models\User;
use App\Services\DocumentOcrVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('unknown pages show the friendly 404 page', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertSee('We could not find that page');
});

test('a resident opening a facilitator page gets the friendly 403 page', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)->get('/facilitator/dashboard')
        ->assertForbidden()
        ->assertSee('You do not have access to this page');
});

test('the OCR checker logs and degrades to facilitator review when the service fails', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    Storage::disk('public')->put('documents/doc.jpg', 'document');
    Storage::disk('public')->put('templates/t.jpg', 'template');
    Http::fake(['*' => Http::response('tesseract missing', 500)]);
    Log::spy();

    $result = app(DocumentOcrVerifier::class)->verify('public', 'documents/doc.jpg', 'templates/t.jpg', 'indigency');

    expect($result['status'])->toBe('review')
        ->and($result['method'])->toBe('verification_error');
    Log::shouldHaveReceived('warning')->once();
});
