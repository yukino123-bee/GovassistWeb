<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ __('messages.app_name') }}</title>
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

    <!-- Centered Login Box -->
    <div class="flex flex-col items-center justify-center w-full">
        <!-- Sharp Container Card -->
        <div class="w-full max-w-[440px] bg-white p-8 sm:p-10 shadow-xl rounded-none border border-red-100 space-y-6 relative">
            
            <!-- Dialect Switcher in Upper Right Corner -->
            <div class="absolute top-4 right-4 flex items-center space-x-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                <button type="button" onclick="changeLanguage('en')" class="hover:text-red-600 transition-colors {{ app()->getLocale() === 'en' ? 'text-red-600 font-extrabold' : '' }}">EN</button>
                <span class="text-slate-300">|</span>
                <button type="button" onclick="changeLanguage('ceb')" class="hover:text-red-600 transition-colors {{ app()->getLocale() === 'ceb' ? 'text-red-600 font-extrabold' : '' }}">CEB</button>
                <span class="text-slate-300">|</span>
                <button type="button" onclick="changeLanguage('fil')" class="hover:text-red-600 transition-colors {{ app()->getLocale() === 'fil' ? 'text-red-600 font-extrabold' : '' }}">FIL</button>
                <span class="text-slate-300">|</span>
                <button type="button" onclick="changeLanguage('sub')" class="hover:text-red-600 transition-colors {{ app()->getLocale() === 'sub' ? 'text-red-600 font-extrabold' : '' }}">SUB</button>
            </div>
            
            <!-- Logo Header -->
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center overflow-hidden p-1 shadow-sm border border-slate-200 mb-3">
                    <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="w-full h-full object-contain">
                </div>
                <h2 class="text-3xl font-extrabold text-[#d5001c] tracking-tight">GovAssist</h2>
                <p class="text-xs text-slate-400 font-medium mt-1.5 leading-relaxed max-w-[280px]">
                    {{ __('messages.sign_in_subtitle') }}
                </p>
            </div>

            @if($errors->any())
                <div class="p-3.5 bg-red-50 border-l-2 border-red-600 text-red-800 text-xs rounded-none">
                    <ul class="space-y-1 font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-slate-700">Email</label>
                    <div class="relative flex items-center bg-white border border-slate-200 rounded-none px-4 py-3.5 focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500 transition-all">
                        <!-- Mail Icon -->
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your email address" class="w-full bg-transparent focus:outline-none text-slate-800 placeholder-slate-400 text-sm ml-3" required>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                    <div class="relative flex items-center bg-white border border-slate-200 rounded-none px-4 py-3.5 focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500 transition-all">
                        <!-- Lock Icon -->
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input type="password" name="password" id="password" placeholder="Enter your password" class="w-full bg-transparent focus:outline-none text-slate-800 placeholder-slate-400 text-sm ml-3 pr-10" required>
                        <!-- Show/Hide Toggle Button -->
                        <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon')" class="absolute right-4 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Forgot Password -->
                <div class="flex justify-end text-xs pt-1">
                    <a href="#" class="text-[#d5001c] hover:underline font-semibold transition-colors">{{ __('messages.forgot_password') }}?</a>
                </div>

                <!-- Sharp Submit Button -->
                <button type="submit" class="w-full py-4 bg-[#d5001c] hover:bg-[#b80010] text-white font-bold rounded-none transition-all shadow-lg shadow-red-600/10 text-sm">
                    Login
                </button>
            </form>

            <!-- Footer Sign-up link & Copyright inside Container -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div class="text-center text-sm text-slate-500 font-medium">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-[#d5001c] hover:underline ml-1 font-bold">Register</a>
                </div>
                <div class="text-center text-[9px] text-slate-400 uppercase tracking-widest font-bold">
                    &copy; {{ date('Y') }} SSFO GovAssist. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- AJAX Language Toggle Script -->
    <script>
        function changeLanguage(lang) {
            fetch("{{ route('language.toggle') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ language: lang })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }

        function togglePasswordVisibility(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                `; // Eye slashed
            } else {
                field.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `; // Eye open
            }
        }
    </script>
</body>
</html>
