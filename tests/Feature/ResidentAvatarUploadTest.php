<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('stores an uploaded avatar on the public disk', function () {
    Storage::fake('public');
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->post(route('resident.profile.avatar'), ['avatar' => UploadedFile::fake()->image('me.png')])
        ->assertSessionHas('success');

    $resident->refresh();
    expect($resident->avatar)->toStartWith('avatars/');
    Storage::disk('public')->assertExists($resident->avatar);
});
