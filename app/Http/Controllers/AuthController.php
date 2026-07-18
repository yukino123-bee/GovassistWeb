<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;

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

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Get current language from session or default
        $lang = session('locale', 'en');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'citizen',
            'language' => $lang,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('citizen.home');
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
                    ? redirect()->route('citizen.home')
                    : view('auth.verify');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $cachedOtp = Cache::get('verification_otp_' . $user->id);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return back()->withErrors(['otp' => 'The OTP you entered is invalid or has expired.']);
        }

        // OTP is correct
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        Cache::forget('verification_otp_' . $user->id);

        return redirect()->route('citizen.home');
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

        return redirect()->route('citizen.home');
    }
}
