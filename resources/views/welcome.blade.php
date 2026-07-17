<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support Services Facilitator's Office - GovAssist</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-slate-50 min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-slate-200 py-4 px-6 md:px-12 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center overflow-hidden p-0.5 shadow-sm border border-slate-200">
                <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="text-lg font-bold tracking-wider text-slate-900 block">GovAssist</span>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">SSFO - Zamboanga Del Sur</span>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            @auth
                <a href="{{ Auth::user()->isFacilitator() ? route('facilitator.dashboard') : route('citizen.home') }}" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-xs font-bold uppercase tracking-wider transition-colors">
                    Go to Portal
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold uppercase tracking-wider transition-colors">
                    Log In
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-xs font-bold uppercase tracking-wider transition-colors">
                    Register
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Hero Banner Section -->
    <main class="flex-grow flex items-center justify-center py-16 px-6 md:px-12">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            <!-- Intro Text -->
            <div class="space-y-6">
                <div class="inline-flex items-center space-x-2 bg-red-50 text-red-700 px-3 py-1 text-xs font-bold uppercase tracking-widest border border-red-100">
                    <span>Zamboanga Del Sur</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight tracking-tight">
                    Government Service Assistance Simplified.
                </h1>
                <p class="text-slate-600 text-sm md:text-base leading-relaxed font-light">
                    Verify qualification requirements for Educational, Medical, Burial, Transportation, and Employment assistance programs. Instantly check eligibility, generate complete document checklists, and submit applications online.
                </p>
                <div class="pt-2 flex flex-col sm:flex-row gap-4">
                    @auth
                        <a href="{{ Auth::user()->isFacilitator() ? route('facilitator.dashboard') : route('citizen.home') }}" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white text-xs font-bold uppercase tracking-widest text-center transition-colors">
                            Enter Your Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white text-xs font-bold uppercase tracking-widest text-center transition-colors">
                            Create Account
                        </a>
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 text-xs font-bold uppercase tracking-widest text-center transition-colors">
                            Access Login Portal
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Visual Cards / Portals -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                <!-- Citizen Portal Card -->
                <div class="bg-white border border-slate-200 shadow-sm p-6 hover:border-slate-400 transition-colors">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="p-2.5 border border-red-200 text-red-700 bg-red-50/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Citizen Portal</h3>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Residents & Applicants</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-light leading-relaxed mb-4">
                        Check program eligibility rules, generate checklists, view notices, and submit documents directly to our facilitators.
                    </p>
                    <a href="{{ route('login') }}" class="text-[10px] font-bold uppercase tracking-wider text-red-700 hover:text-red-900 flex items-center space-x-1">
                        <span>Go to Login</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Facilitator Portal Card -->
                <div class="bg-white border border-slate-200 shadow-sm p-6 hover:border-slate-400 transition-colors">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="p-2.5 border border-red-200 text-red-700 bg-red-50/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Facilitator Panel</h3>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Office Administrators</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-light leading-relaxed mb-4">
                        Manage available assistance programs, configure eligibility criteria, verify submitted documents, and approve applications.
                    </p>
                    <a href="{{ route('login') }}" class="text-[10px] font-bold uppercase tracking-wider text-red-700 hover:text-red-900 flex items-center space-x-1">
                        <span>Go to Dashboard</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-[10px] text-slate-400 uppercase tracking-widest font-bold">
        <p>&copy; 2026 Support Services Facilitator's Office (SSFO) - Zamboanga Del Sur. All rights reserved.</p>
    </footer>

</body>
</html>
