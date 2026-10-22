<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SmsService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);

        $identifier = trim((string) $request->input('login'));
        $contactNumber = User::normalizeContactNumber($identifier);

        $credentials = $contactNumber !== null
            ? ['contact_number' => $contactNumber]
            : ['email' => $identifier];
        $credentials['password'] = $request->input('password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Set locale in session based on user preference
            session(['locale' => Auth::user()->language]);

            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'login' => __('The provided credentials do not match our records.'),
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $identifier = trim((string) $request->input('login', $request->input('email')));
        $contactNumber = User::normalizeContactNumber($identifier);

        if ($contactNumber !== null) {
            return $this->sendPasswordResetCode($contactNumber);
        }

        $request->merge(['email' => $identifier]);
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if (in_array($status, [Password::ResetLinkSent, Password::InvalidUser], true)) {
            return back()->with('status', __('messages.password_reset_link_sent'));
        }

        return back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    protected function sendPasswordResetCode(string $contactNumber): RedirectResponse
    {
        $user = User::where('contact_number', $contactNumber)->first();

        if ($user !== null) {
            $code = (string) random_int(100000, 999999);
            Cache::put('password_reset_code_'.$user->id, Hash::make($code), now()->addMinutes(15));
            Cache::forget('password_reset_attempts_'.$user->id);

            app(SmsService::class)->send(
                $contactNumber,
                "SSFO GovAssist: Your password reset code is {$code}. Valid for 15 minutes. Do not share this code."
            );
        }

        return redirect()->route('password.code')
            ->with('reset_contact_number', $contactNumber)
            ->with('status', __('messages.password_reset_code_sent'));
    }

    public function showResetPasswordCode(Request $request): View
    {
        return view('auth.reset-password-code', [
            'contactNumber' => session('reset_contact_number', old('contact_number')),
        ]);
    }

    public function resetPasswordWithCode(Request $request): RedirectResponse
    {
        $request->merge([
            'contact_number' => User::normalizeContactNumber((string) $request->input('contact_number'))
                ?? $request->input('contact_number'),
            'code' => preg_replace('/\s+/', '', (string) $request->input('code')),
        ]);

        $validated = $request->validate([
            'contact_number' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $invalid = fn (): RedirectResponse => back()
            ->withErrors(['code' => __('messages.password_reset_code_invalid')])
            ->withInput($request->only('contact_number'))
            ->with('reset_contact_number', $validated['contact_number']);

        $user = User::where('contact_number', $validated['contact_number'])->first();
        $hashedCode = $user ? Cache::get('password_reset_code_'.$user->id) : null;

        if ($user === null || ! is_string($hashedCode)) {
            return $invalid();
        }

        $attemptsKey = 'password_reset_attempts_'.$user->id;
        $attempts = (int) Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, now()->addMinutes(15));

        if ($attempts > 5) {
            Cache::forget('password_reset_code_'.$user->id);

            return $invalid();
        }

        if (! Hash::check($validated['code'], $hashedCode)) {
            return $invalid();
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        Cache::forget('password_reset_code_'.$user->id);
        Cache::forget($attemptsKey);

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', __('passwords.reset'));
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'email' => $request->string('email'),
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->merge([
            'contact_number' => User::normalizeContactNumber((string) $request->input('contact_number'))
                ?? $request->input('contact_number'),
            'email' => $request->filled('email') ? strtolower(trim((string) $request->input('email'))) : null,
        ]);

        $namePattern = "regex:/^[\pL\pM .'-]+$/u";

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', $namePattern],
            'middle_name' => ['nullable', 'string', 'max:255', $namePattern],
            'last_name' => ['required', 'string', 'max:255', $namePattern],
            'dob' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'sex' => ['required', Rule::in(User::SEXES)],
            'civil_status' => ['required', Rule::in(User::CIVIL_STATUSES)],
            'barangay' => ['required', 'string', 'max:255'],
            'street_address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,contact_number'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'privacy_consent' => ['accepted'],
        ]);

        $fullName = collect([
            $validated['first_name'],
            $validated['middle_name'] ?? null,
            $validated['last_name'],
        ])->filter()->implode(' ');

        $user = User::create([
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'dob' => $validated['dob'],
            'sex' => $validated['sex'],
            'civil_status' => $validated['civil_status'],
            'barangay' => $validated['barangay'],
            'street_address' => $validated['street_address'],
            'address' => $validated['street_address'].', '.$validated['barangay'],
            'email' => $validated['email'] ?? null,
            'contact_number' => $validated['contact_number'],
            'password' => Hash::make($validated['password']),
            'role' => 'resident',
            'language' => session('locale', 'en'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function toggleLanguage(Request $request)
    {
        $lang = $request->input('language');
        if (! in_array($lang, ['en', 'ceb', 'fil'])) {
            $lang = 'en';
        }

        session(['locale' => $lang]);

        if (Auth::check()) {
            $user = Auth::user();
            $user->language = $lang;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    public function verifyNotice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->route('resident.home')
                    : view('auth.verify');
    }

    public function verifyEmail(Request $request)
    {
        if ($request->has('otp')) {
            $request->merge([
                'otp' => preg_replace('/\s+/', '', (string) $request->input('otp')),
            ]);
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $cachedOtp = Cache::get('verification_otp_'.$user->id);

        if (! is_string($cachedOtp) || ! hash_equals($cachedOtp, (string) $request->input('otp'))) {
            return back()->withErrors(['otp' => 'The OTP you entered is invalid or has expired.']);
        }

        // OTP is correct
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        Cache::forget('verification_otp_'.$user->id);

        return redirect()->route('resident.home');
    }

    public function verifyResend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        $channels = $request->user()->email ? 'mobile number and email' : 'mobile number';

        return back()->with('message', "A new verification code has been sent to your {$channels}.");
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->isFacilitator()) {
            return redirect()->route('facilitator.dashboard');
        }

        return redirect()->route('resident.home');
    }
}
