<?php

use App\Mail\VerifyOtpEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('user registration sends an email with a 6-digit OTP and caches it', function () {
    Mail::fake();

    $response = $this->post(route('register'), [
        'first_name' => 'Maria',
        'middle_name' => 'Clara',
        'last_name' => 'Santos',
        'email' => 'maria@example.com',
        'contact_number' => '09171234567',
        'dob' => '1990-05-12',
        'sex' => 'Male',
        'civil_status' => 'Single',
        'barangay' => 'Poblacion',
        'street_address' => 'Purok 3, Rizal St.',
        'privacy_consent' => '1',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'maria@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasVerifiedEmail())->toBeFalse();

    $cachedOtp = Cache::get('verification_otp_'.$user->id);
    expect($cachedOtp)->not->toBeNull()
        ->and(strlen($cachedOtp))->toBe(6)
        ->and(ctype_digit($cachedOtp))->toBeTrue();

    Mail::assertSent(VerifyOtpEmail::class, function (VerifyOtpEmail $mail) use ($user, $cachedOtp) {
        return $mail->hasTo($user->email)
            && $mail->otpCode === $cachedOtp
            && $mail->envelope()->subject === 'Verify Your Email Address';
    });
});

test('unverified resident is redirected to verification notice when accessing protected routes', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    $this->actingAs($user)
        ->get(route('resident.home'))
        ->assertRedirect(route('verification.notice'));

    $this->actingAs($user)
        ->get(route('resident.eligibility'))
        ->assertRedirect(route('verification.notice'));
});

test('verification notice page displays the 6-digit OTP prompt and resend form', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertSuccessful()
        ->assertSee('Verify Your Account')
        ->assertSee('Enter 6-Digit OTP')
        ->assertSee(route('verification.verify'))
        ->assertSee(route('verification.send'))
        ->assertSee('Didn\'t receive the code? Resend OTP', false);
});

test('resident can successfully verify email using the correct OTP', function () {
    Event::fake([Verified::class]);

    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    $otp = '654321';
    Cache::put('verification_otp_'.$user->id, $otp, now()->addMinutes(15));

    $response = $this->actingAs($user)->post(route('verification.verify'), [
        'otp' => $otp,
    ]);

    $response->assertRedirect(route('resident.home'));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue()
        ->and($user->fresh()->email_verified_at)->not->toBeNull()
        ->and(Cache::has('verification_otp_'.$user->id))->toBeFalse();

    Event::assertDispatched(Verified::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

test('resident can verify email even if OTP input contains whitespace', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    $otp = '654321';
    Cache::put('verification_otp_'.$user->id, $otp, now()->addMinutes(15));

    $response = $this->actingAs($user)->post(route('verification.verify'), [
        'otp' => ' 654 321 ',
    ]);

    $response->assertRedirect(route('resident.home'));
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('verification fails when submitting an incorrect OTP', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    Cache::put('verification_otp_'.$user->id, '123456', now()->addMinutes(15));

    $response = $this->actingAs($user)->post(route('verification.verify'), [
        'otp' => '999999',
    ]);

    $response->assertSessionHasErrors([
        'otp' => 'The OTP you entered is invalid or has expired.',
    ]);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse()
        ->and(Cache::get('verification_otp_'.$user->id))->toBe('123456');
});

test('verification fails when OTP has expired or was not generated', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    // No cache entry or expired
    $response = $this->actingAs($user)->post(route('verification.verify'), [
        'otp' => '123456',
    ]);

    $response->assertSessionHasErrors([
        'otp' => 'The OTP you entered is invalid or has expired.',
    ]);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verification validates that OTP is required and exactly 6 characters', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    $this->actingAs($user)->post(route('verification.verify'), [
        'otp' => '',
    ])->assertSessionHasErrors('otp');

    $this->actingAs($user)->post(route('verification.verify'), [
        'otp' => '12345', // 5 digits
    ])->assertSessionHasErrors('otp');

    $this->actingAs($user)->post(route('verification.verify'), [
        'otp' => '1234567', // 7 digits
    ])->assertSessionHasErrors('otp');
});

test('resident can resend verification OTP', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create([
        'role' => 'resident',
    ]);

    $oldOtp = '111111';
    Cache::put('verification_otp_'.$user->id, $oldOtp, now()->addMinutes(15));

    $response = $this->actingAs($user)->post(route('verification.send'));

    $response->assertSessionHas('message', 'A new verification code has been sent to your mobile number and email.');

    $newOtp = Cache::get('verification_otp_'.$user->id);
    expect($newOtp)->not->toBeNull()
        ->and(strlen($newOtp))->toBe(6);

    Mail::assertSent(VerifyOtpEmail::class, function (VerifyOtpEmail $mail) use ($user, $newOtp) {
        return $mail->hasTo($user->email) && $mail->otpCode === $newOtp;
    });
});

test('already verified user visiting verification notice is redirected to resident home', function () {
    $user = User::factory()->create([
        'role' => 'resident',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertRedirect(route('resident.home'));
});

test('verify otp email mailable renders the OTP code and expiration message', function () {
    $mailable = new VerifyOtpEmail('987654');

    $mailable->assertSeeInHtml('987654');
    $mailable->assertSeeInHtml('This code will expire in 15 minutes.');
    $mailable->assertSeeInHtml('GovAssist');
    $mailable->assertHasSubject('Verify Your Email Address');
});

test('sendEmailVerificationNotification handles external transport exceptions gracefully', function () {
    Log::shouldReceive('error')->atLeast()->once();

    Mail::shouldReceive('to->send')->andThrow(new RuntimeException('SMTP Connection Timeout'));

    $user = User::factory()->unverified()->create([
        'contact_number' => null,
    ]);

    $user->sendEmailVerificationNotification();

    expect(Cache::has('verification_otp_'.$user->id))->toBeTrue();
});
