<?php

use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function registrationPayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Maria',
        'middle_name' => 'Clara',
        'last_name' => 'Santos',
        'dob' => '1985-03-20',
        'sex' => 'Female',
        'civil_status' => 'Married',
        'barangay' => 'Poblacion',
        'street_address' => 'Purok 2, Mabini St.',
        'contact_number' => '09171234567',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'privacy_consent' => '1',
    ], $overrides);
}

test('resident without an email can register with a mobile number and receives an SMS OTP', function () {
    Mail::fake();
    $this->mock(SmsService::class)->shouldReceive('sendOtp')->once()->andReturn(true);

    $this->post(route('register'), registrationPayload())
        ->assertRedirect(route('verification.notice'));

    $user = User::where('contact_number', '09171234567')->firstOrFail();

    expect($user->email)->toBeNull()
        ->and($user->address)->toBe('Purok 2, Mabini St., Poblacion')
        ->and($user->hasVerifiedEmail())->toBeFalse()
        ->and(Cache::get('verification_otp_'.$user->id))->toHaveLength(6);

    Mail::assertNothingSent();
});

test('registration normalizes international mobile numbers', function () {
    $this->post(route('register'), registrationPayload(['contact_number' => '+63 917 123 4567']));

    $this->assertDatabaseHas('users', ['contact_number' => '09171234567']);
});

test('registration requires profile details, consent and a unique mobile number', function () {
    User::factory()->create(['contact_number' => '09171234567']);

    $this->post(route('register'), registrationPayload())
        ->assertSessionHasErrors('contact_number');

    $this->post(route('register'), registrationPayload([
        'contact_number' => '09181234567',
        'dob' => now()->addDay()->toDateString(),
        'sex' => 'Other',
        'barangay' => '',
        'privacy_consent' => null,
    ]))->assertSessionHasErrors(['dob', 'sex', 'barangay', 'privacy_consent']);

    expect(User::count())->toBe(1);
});

test('a provided email must still be unique', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post(route('register'), registrationPayload(['email' => 'taken@example.com']))
        ->assertSessionHasErrors('email');
});

test('resident can log in with a mobile number in any accepted format', function () {
    $user = User::factory()->create([
        'role' => 'resident',
        'email' => null,
        'contact_number' => '09171234567',
        'password' => 'Password123!',
    ]);

    $this->post(route('login'), ['login' => '+639171234567', 'password' => 'Password123!'])
        ->assertRedirect(route('resident.home'));

    $this->assertAuthenticatedAs($user);
});

test('login rejects a wrong password for a mobile number', function () {
    User::factory()->create(['email' => null, 'contact_number' => '09171234567', 'password' => 'Password123!']);

    $this->post(route('login'), ['login' => '09171234567', 'password' => 'wrong'])
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('resident can reset a password with an SMS code sent to their mobile number', function () {
    $user = User::factory()->create(['email' => null, 'contact_number' => '09171234567']);
    $sentCode = null;

    $this->mock(SmsService::class)
        ->shouldReceive('send')
        ->once()
        ->andReturnUsing(function (string $number, string $message) use (&$sentCode): bool {
            preg_match('/\d{6}/', $message, $matches);
            $sentCode = $matches[0];

            return true;
        });

    $this->post(route('password.email'), ['login' => '09171234567'])
        ->assertRedirect(route('password.code'));

    $this->post(route('password.code.update'), [
        'contact_number' => '09171234567',
        'code' => $sentCode,
        'password' => 'NewPassword456!',
        'password_confirmation' => 'NewPassword456!',
    ])->assertRedirect(route('login'));

    expect(Hash::check('NewPassword456!', $user->fresh()->password))->toBeTrue();
});

test('a wrong reset code is rejected and the code locks after repeated failures', function () {
    $user = User::factory()->create(['contact_number' => '09171234567']);
    Cache::put('password_reset_code_'.$user->id, Hash::make('123456'), now()->addMinutes(15));

    foreach (range(1, 5) as $attempt) {
        $this->post(route('password.code.update'), [
            'contact_number' => '09171234567',
            'code' => '000000',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ])->assertSessionHasErrors('code');
    }

    $this->post(route('password.code.update'), [
        'contact_number' => '09171234567',
        'code' => '123456',
        'password' => 'NewPassword456!',
        'password_confirmation' => 'NewPassword456!',
    ])->assertSessionHasErrors('code');

    expect(Hash::check('NewPassword456!', $user->fresh()->password))->toBeFalse();
});

test('password reset by mobile number does not reveal unknown numbers', function () {
    $this->post(route('password.email'), ['login' => '09990000000'])
        ->assertRedirect(route('password.code'))
        ->assertSessionHas('status', __('messages.password_reset_code_sent'));
});

test('registration page shows the new fields', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('name="dob"', false)
        ->assertSee('name="barangay"', false)
        ->assertSee('name="privacy_consent"', false);
});

test('home shows the profile completion prompt until the profile is complete', function () {
    $resident = User::factory()->create(['role' => 'resident', 'dob' => null, 'sex' => null]);

    $this->actingAs($resident)
        ->get(route('resident.home'))
        ->assertOk()
        ->assertSee(__('messages.profile_incomplete_title'))
        ->assertSee(route('resident.profile.edit'), false);
});

test('resident can save sex and a structured address from the profile', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)->post(route('resident.profile.update'), [
        'first_name' => 'Juan',
        'last_name' => 'Cruz',
        'contact_number' => '09171234567',
        'sex' => 'Male',
        'barangay' => 'Fatima',
        'street_address' => 'Purok 1',
    ])->assertSessionDoesntHaveErrors();

    expect($resident->fresh())
        ->sex->toBe('Male')
        ->barangay->toBe('Fatima')
        ->address->toBe('Purok 1, Fatima');

    $this->actingAs($resident)->get(route('resident.profile'))
        ->assertOk()->assertSee('Purok 1, Fatima');
});
