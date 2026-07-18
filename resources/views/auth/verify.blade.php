<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - {{ __('messages.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-[#faf5f6] text-slate-800 min-h-screen flex items-center justify-center p-6">

    <div class="flex flex-col items-center justify-center w-full">
        <!-- Sharp Container Card -->
        <div class="w-full max-w-[440px] bg-white p-8 sm:p-10 shadow-xl rounded-none border border-red-100 space-y-6 relative">
            
            <!-- Logo Header -->
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center overflow-hidden p-1 shadow-sm border border-slate-200 mb-3">
                    <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="w-full h-full object-contain">
                </div>
                <h2 class="text-3xl font-extrabold text-[#d5001c] tracking-tight">GovAssist</h2>
                <p class="text-sm text-slate-500 font-medium mt-3 leading-relaxed">
                    Verify Your Email Address
                </p>
            </div>

            <div class="text-sm text-slate-600 text-center leading-relaxed">
                Thanks for signing up! We've sent a 6-digit One-Time Password (OTP) to your email address. Please enter it below to verify your account.
            </div>

            @if (session('message'))
                <div class="p-3.5 bg-green-50 border-l-2 border-green-600 text-green-800 text-xs font-semibold rounded-none">
                    {{ session('message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 bg-red-50 border-l-2 border-red-600 text-red-800 text-xs font-semibold rounded-none">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-4 pt-2">
                @csrf
                <div class="space-y-2">
                    <label for="otp" class="block text-sm font-bold text-slate-700 text-center">Enter 6-Digit OTP</label>
                    <input type="text" name="otp" id="otp" maxlength="6" class="w-full bg-white border border-slate-200 rounded-none px-4 py-3.5 focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500 transition-all text-center text-2xl font-bold tracking-[10px] text-slate-800" required autofocus>
                </div>
                <button type="submit" class="w-full py-4 bg-[#d5001c] hover:bg-[#b80010] text-white font-bold rounded-none transition-all shadow-lg shadow-red-600/10 text-sm">
                    Verify Email
                </button>
            </form>

            <div class="text-center pt-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="text-xs text-slate-500 hover:text-red-600 font-semibold underline transition-colors">
                        Didn't receive the code? Resend OTP
                    </button>
                </form>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-slate-500 hover:text-red-600 font-semibold underline transition-colors">
                        Logout
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="pt-4 space-y-3">
                <div class="text-center text-[9px] text-slate-400 uppercase tracking-widest font-bold">
                    &copy; {{ date('Y') }} SSFO GovAssist. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
