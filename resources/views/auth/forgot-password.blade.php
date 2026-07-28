<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.forgot_password_title') }} - {{ __('messages.app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#faf5f6] p-6 text-slate-800 antialiased">
    <main class="w-full max-w-[440px] space-y-6 border border-red-100 bg-white p-8 shadow-xl sm:p-10">
        <div class="flex flex-col items-center gap-3 text-center">
            <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="h-20 w-20 rounded-full border border-slate-200 object-contain p-1 shadow-sm">
            <div>
                <h1 class="text-2xl font-extrabold text-[#d5001c]">{{ __('messages.forgot_password_title') }}</h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ __('messages.forgot_password_instructions') }}</p>
            </div>
        </div>

        @if (session('status'))
            <div class="border-l-2 border-emerald-600 bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @error('email')
            <div class="border-l-2 border-red-600 bg-red-50 p-3.5 text-xs font-semibold text-red-800">{{ $message }}</div>
        @enderror

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            <div class="space-y-2">
                <label for="email" class="block text-sm font-bold text-slate-700">{{ __('messages.email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required autofocus
                    class="w-full border border-slate-200 px-4 py-3.5 text-sm outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <button type="submit" class="w-full bg-[#d5001c] py-4 text-sm font-bold text-white shadow-lg shadow-red-600/10 transition hover:bg-[#b80010]">
                {{ __('messages.send_password_reset_link') }}
            </button>
        </form>

        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 border-t border-slate-100 pt-5 text-xs font-bold text-slate-600 hover:text-red-700">
            <span aria-hidden="true">&larr;</span>
            <span>{{ __('messages.back_to_login') }}</span>
        </a>
    </main>
</body>
</html>
