<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('login page links to the password reset request form', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('href="'.route('password.request').'"', false);

    $this->get(route('password.request'))
        ->assertSuccessful()
        ->assertSee('Send Password Reset Link');
});

test('user can request a password reset link', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHas('status', __('messages.password_reset_link_sent'))
        ->assertSessionDoesntHaveErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('password reset request does not reveal whether an email is registered', function () {
    Notification::fake();

    $this->post(route('password.email'), ['email' => 'unknown@example.com'])
        ->assertSessionHas('status', __('messages.password_reset_link_sent'))
        ->assertSessionDoesntHaveErrors();

    Notification::assertNothingSent();
});

test('invalid password reset token is rejected with a translated message', function () {
    $user = User::factory()->create();

    $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ])->assertSessionHasErrors([
        'email' => __('passwords.token'),
    ]);

    expect(Hash::check('new-secure-password', $user->fresh()->password))->toBeFalse();
});

test('password reset requires a confirmed secure password', function () {
    $user = User::factory()->create();

    $this->post(route('password.update'), [
        'token' => 'token',
        'email' => $user->email,
        'password' => 'short',
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors(['password']);
});

test('password reset requests are rate limited', function () {
    Notification::fake();

    foreach (range(1, 6) as $attempt) {
        $this->post(route('password.email'), [
            'email' => "unknown{$attempt}@example.com",
        ])->assertRedirect();
    }

    $this->post(route('password.email'), [
        'email' => 'unknown7@example.com',
    ])->assertTooManyRequests();

    Notification::assertNothingSent();
});

test('user can reset their password with a valid token', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user): bool {
            $this->get(route('password.reset', [
                'token' => $notification->token,
                'email' => $user->email,
            ]))->assertSuccessful();

            $this->post(route('password.update'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])->assertRedirect(route('login'));

            return Hash::check('new-secure-password', $user->fresh()->password);
        },
    );
});

test('expired password reset token is rejected', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user): bool {
            $this->travel(61)->minutes();

            $this->post(route('password.update'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])->assertSessionHasErrors([
                'email' => __('passwords.token'),
            ]);

            return ! Hash::check('new-secure-password', $user->fresh()->password);
        },
    );
});

test('password reset token cannot be reused', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user): bool {
            $credentials = [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'first-secure-password',
                'password_confirmation' => 'first-secure-password',
            ];

            $this->post(route('password.update'), $credentials)
                ->assertRedirect(route('login'));

            $this->post(route('password.update'), [
                ...$credentials,
                'password' => 'second-secure-password',
                'password_confirmation' => 'second-secure-password',
            ])->assertSessionHasErrors([
                'email' => __('passwords.token'),
            ]);

            return Hash::check('first-secure-password', $user->fresh()->password)
                && ! Hash::check('second-secure-password', $user->fresh()->password);
        },
    );
});
