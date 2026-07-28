<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Set locale in session based on user preference
            session(['locale' => Auth::user()->language]);

            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => __('The provided credentials do not match our records.'),
        ])->onlyInput('email');
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
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if (in_array($status, [Password::ResetLinkSent, Password::InvalidUser], true)) {
            return back()->with('status', __('messages.password_reset_link_sent'));
        }

        return back()->withErrors(['email' => __($status)])->onlyInput('email');
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
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'middle_name' => ['nullable', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'last_name' => ['required', 'string', 'max:255', "regex:/^[\pL\pM .'-]+$/u"],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $lang = session('locale', 'en');
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
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'resident',
            'language' => $lang,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('resident.home');
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
        if (! in_array($lang, ['en', 'ceb', 'fil', 'sub'])) {
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

        return back()->with('message', 'A new OTP has been sent to your email!');
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->isFacilitator()) {
            return redirect()->route('facilitator.dashboard');
        }

        return redirect()->route('resident.home');
    }
}
