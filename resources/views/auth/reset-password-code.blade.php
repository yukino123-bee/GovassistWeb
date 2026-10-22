<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.reset_password_title') }} - {{ __('messages.app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#faf5f6] p-6 text-slate-800 antialiased">
    <main class="w-full max-w-[440px] space-y-6 border border-red-100 bg-white p-8 shadow-xl sm:p-10">
        <div class="flex flex-col items-center gap-3 text-center">
            <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="h-20 w-20 rounded-full border border-slate-200 object-contain p-1 shadow-sm">
            <div>
                <h1 class="text-2xl font-extrabold text-[#d5001c]">{{ __('messages.reset_password_title') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('messages.reset_code_instructions') }}</p>
            </div>
        </div>

        @if (session('status'))
            <div class="border-l-2 border-emerald-600 bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="border-l-2 border-red-600 bg-red-50 p-3.5 text-xs font-semibold text-red-800">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.code.update') }}" method="POST" class="space-y-5">
            @csrf
            <div class="space-y-2">
                <label for="contact_number" class="block text-sm font-bold text-slate-700">{{ __('messages.contact_number') }}</label>
                <input type="tel" inputmode="numeric" name="contact_number" id="contact_number" value="{{ old('contact_number', $contactNumber) }}" autocomplete="tel" required
                    class="w-full border border-slate-200 px-4 py-3.5 text-sm outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <div class="space-y-2">
                <label for="code" class="block text-sm font-bold text-slate-700">{{ __('messages.reset_code') }}</label>
                <input type="text" inputmode="numeric" name="code" id="code" maxlength="6" autocomplete="one-time-code" required autofocus
                    class="w-full border border-slate-200 px-4 py-3.5 text-center text-xl font-bold tracking-[8px] outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <div class="space-y-2">
                <label for="password" class="block text-sm font-bold text-slate-700">{{ __('messages.new_password') }}</label>
                <input type="password" name="password" id="password" autocomplete="new-password" required
                    class="w-full border border-slate-200 px-4 py-3.5 text-sm outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-bold text-slate-700">{{ __('messages.confirm_password') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required
                    class="w-full border border-slate-200 px-4 py-3.5 text-sm outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <button type="submit" class="w-full bg-[#d5001c] py-4 text-sm font-bold text-white shadow-lg shadow-red-600/10 transition hover:bg-[#b80010]">
                {{ __('messages.reset_password_btn') }}
            </button>
        </form>

        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 border-t border-slate-100 pt-5 text-xs font-bold text-slate-600 hover:text-red-700">
            <span aria-hidden="true">&larr;</span>
            <span>{{ __('messages.back_to_login') }}</span>
        </a>
    </main>
</body>
</html>
