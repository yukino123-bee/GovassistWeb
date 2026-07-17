<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Facilitator Portal - GovAssist')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }
        .sidebar-link-active {
            background-color: white;
            color: #b91c1c;
            font-weight: 700;
        }
        .sidebar-link-active svg {
            color: #b91c1c;
        }
    </style>
</head>
<body class="antialiased flex min-h-screen">

    <!-- Sidebar — sharp-edged red/white, matching citizen header -->
    <aside class="w-64 bg-red-700 text-white flex flex-col border-r border-red-800 flex-shrink-0 sticky top-0 h-screen overflow-y-auto">
        <!-- Logo / Brand Header -->
        <div class="px-5 py-5 border-b border-red-800 flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center overflow-hidden p-0.5 shadow-sm border border-red-200 flex-shrink-0">
                <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="text-base font-extrabold tracking-wider text-white leading-tight block">GovAssist</span>
                <span class="text-[10px] text-red-200 uppercase tracking-widest font-extrabold">SSFO Facilitator</span>
            </div>
        </div>

        <!-- Admin Info strip -->
        <a href="{{ route('facilitator.profile.edit') }}" class="px-5 py-3 border-b border-red-800 bg-red-800/20 flex items-center space-x-3 hover:bg-red-800/40 transition-colors cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-white border border-red-200 flex items-center justify-center text-red-700 font-extrabold text-sm flex-shrink-0 overflow-hidden">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->name, 0, 1) }}
                @endif
            </div>
            <div class="min-w-0">
                <span class="block text-xs font-bold text-white truncate">{{ Auth::user()->name }}</span>
                <span class="block text-[9px] text-red-200 truncate">{{ Auth::user()->email }}</span>
            </div>
        </a>

        <!-- Navigation Links -->
        <nav id="admin-sidebar-nav" class="flex-grow px-3 py-4 space-y-0.5">
            <!-- Section Label -->
            <p class="text-[9px] font-extrabold uppercase tracking-widest text-red-300 px-3 pb-2 pt-1">Main Menu</p>

            <!-- Dashboard -->
            <a href="{{ route('facilitator.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.dashboard') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Services -->
            <a href="{{ route('facilitator.services') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.services*') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span>Services</span>
            </a>

            <!-- Requirements -->
            <a href="{{ route('facilitator.requirements') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.requirements*') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Requirements</span>
            </a>

            <!-- Eligibility -->
            <a href="{{ route('facilitator.eligibility') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.eligibility*') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Eligibility Rules</span>
            </a>

            <p class="text-[9px] font-extrabold uppercase tracking-widest text-red-300 px-3 pb-2 pt-4">Applications</p>

            <!-- Applications -->
            <a href="{{ route('facilitator.applications') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.applications*') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                </svg>
                <span>Applications</span>
            </a>

            <!-- Inquiries -->
            <a href="{{ route('facilitator.inquiries') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.inquiries*') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span>Inquiries</span>
            </a>

            <p class="text-[9px] font-extrabold uppercase tracking-widest text-red-300 px-3 pb-2 pt-4">Reports</p>

            <!-- Users -->
            <a href="{{ route('facilitator.users') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.users*') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Citizens Registry</span>
            </a>

            <!-- Assessments -->
            <a href="{{ route('facilitator.assessments') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-none transition-all duration-150 text-xs font-bold {{ Route::is('facilitator.assessments*') ? 'bg-white/20 text-white border-l-2 border-white/70' : 'text-red-100 hover:bg-white/10 hover:text-white border-l-2 border-transparent' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Assessments</span>
            </a>
        </nav>

        <!-- Log Out -->
        <div class="p-4 border-t border-red-800">
            <form action="{{ route('logout') }}" method="POST" id="facilitator-logout-form">
                @csrf
                <button type="button" onclick="showConfirmModal('Are you sure you want to log out?', () => document.getElementById('facilitator-logout-form').submit())" class="w-full flex items-center justify-center space-x-2 bg-white/10 hover:bg-white text-white hover:text-red-700 px-4 py-2.5 rounded-none font-bold text-xs transition-all duration-200 focus:outline-none border border-white/20 hover:border-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="flex-grow flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar Header -->
        <header class="h-14 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center space-x-3">
                <span class="w-1 h-5 bg-red-700 block"></span>
                <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">@yield('page_title', 'Dashboard')</h2>
            </div>

            <!-- Topbar Right -->
            <div class="flex items-center divide-x divide-slate-200">

                <!-- Language Selector -->
                <div class="pr-4 flex items-center">
                    <div class="flex items-center bg-slate-100 p-0.5 rounded-none border border-slate-200">
                        <button type="button" onclick="confirmLanguage('en')" class="text-[9px] font-extrabold px-2.5 py-1 transition-all {{ app()->getLocale() === 'en' ? 'bg-red-700 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200' }}">EN</button>
                        <button type="button" onclick="confirmLanguage('ceb')" class="text-[9px] font-extrabold px-2.5 py-1 transition-all {{ app()->getLocale() === 'ceb' ? 'bg-red-700 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200' }}">CEB</button>
                        <button type="button" onclick="confirmLanguage('fil')" class="text-[9px] font-extrabold px-2.5 py-1 transition-all {{ app()->getLocale() === 'fil' ? 'bg-red-700 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200' }}">FIL</button>
                    </div>
                </div>

                <!-- Notification Bell -->
                <div class="px-4 flex items-center">
                    <button class="relative p-1.5 text-slate-400 hover:text-red-700 transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-600 rounded-full border border-white"></span>
                    </button>
                </div>

                <!-- Date -->
                <div class="px-4 hidden sm:flex flex-col items-start leading-tight">
                    <span class="text-[8px] font-extrabold uppercase tracking-widest text-red-600">TODAY</span>
                    <span class="text-[10px] font-bold text-slate-600">{{ now()->format('M j, Y') }}</span>
                </div>

                <!-- Name + Role + Avatar -->
                <a href="{{ route('facilitator.profile.edit') }}" class="pl-4 flex items-center space-x-2.5 hover:opacity-80 transition-opacity">
                    <div class="hidden sm:flex flex-col items-end leading-tight">
                        <span class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Facilitator</span>
                    </div>
                    <div class="relative">
                        <div class="w-8 h-8 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-700 font-bold text-sm overflow-hidden shadow-sm">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->name, 0, 1) }}
                            @endif
                        </div>
                        <!-- Online dot -->
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                    </div>
                </a>

            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-grow p-6 overflow-y-auto">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-none text-emerald-800 shadow-sm flex items-center space-x-3">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold text-xs">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-600 rounded-none text-rose-800 shadow-sm">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-bold text-xs uppercase tracking-wider">Please correct the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Reusable Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white border-l-4 border-red-700 max-w-sm w-full p-6 shadow-xl space-y-4 rounded-none">
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-widest text-red-700 mb-1" id="confirm-modal-title">Confirm Action</h4>
                <p class="text-xs text-slate-600 leading-relaxed font-semibold mt-2" id="confirm-modal-message">Are you sure?</p>
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" id="confirm-modal-cancel" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[10px] transition-colors rounded-none">
                    Cancel
                </button>
                <button type="button" id="confirm-modal-submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-[10px] transition-colors rounded-none">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
        let confirmCallback = null;

        function showConfirmModal(message, onConfirm, title = 'Confirm Action') {
            const modal = document.getElementById('confirm-modal');
            const msgEl  = document.getElementById('confirm-modal-message');
            const titleEl = document.getElementById('confirm-modal-title');
            if (!modal || !msgEl || !titleEl) return;
            titleEl.textContent = title;
            msgEl.textContent   = message;
            confirmCallback     = onConfirm;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function hideConfirmModal() {
            const modal = document.getElementById('confirm-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            confirmCallback = null;
        }

        function confirmLanguage(lang) {
            if (lang === "{{ app()->getLocale() }}") return;
            showConfirmModal("Are you sure you want to change the language?", () => {
                changeLanguage(lang);
            }, "Change Language");
        }

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
            })
            .catch(err => console.error("Language switch error:", err));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const cancelBtn = document.getElementById('confirm-modal-cancel');
            const submitBtn = document.getElementById('confirm-modal-submit');
            if (cancelBtn) cancelBtn.addEventListener('click', hideConfirmModal);
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    if (confirmCallback) confirmCallback();
                    hideConfirmModal();
                });
            }
        });
    </script>

    <!-- Sidebar scroll-position persistence -->
    <script>
        (function () {
            const SCROLL_KEY = 'admin_sidebar_scroll';
            const nav = document.getElementById('admin-sidebar-nav');
            if (!nav) return;

            // Restore saved scroll position immediately
            const saved = sessionStorage.getItem(SCROLL_KEY);
            if (saved !== null) {
                nav.scrollTop = parseInt(saved, 10);
            }

            // Save scroll position before the page unloads
            window.addEventListener('beforeunload', function () {
                sessionStorage.setItem(SCROLL_KEY, nav.scrollTop);
            });
        })();
    </script>
</body>
</html>
