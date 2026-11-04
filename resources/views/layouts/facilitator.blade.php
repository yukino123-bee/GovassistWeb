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
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Public Sans', 'Segoe UI', system-ui, -apple-system, 'Noto Sans', 'Liberation Sans', Arial, sans-serif; -webkit-text-size-adjust: 100%; text-rendering: optimizeLegibility; background-color: #f4f6f9; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        html { color-scheme: light; }
        #main-content-area { scrollbar-width: thin; scrollbar-color: #94a3b8 transparent; overflow-x: hidden; }
        body { height: 100vh; height: 100dvh; }
        :focus-visible { outline: 2px solid #d5001c; outline-offset: 2px; }
        /* Collapsible desktop sidebar */
        @media (min-width: 1024px) {
            #admin-sidebar { width: 16rem; transition: width .2s ease; }
            body.sidebar-collapsed #admin-sidebar { width: 4.25rem; }
            body.sidebar-collapsed #admin-sidebar .nav-label,
            body.sidebar-collapsed #admin-sidebar .nav-group-title,
            body.sidebar-collapsed #admin-sidebar .brand-text,
            body.sidebar-collapsed #admin-sidebar .nav-badge { display: none; }
            body.sidebar-collapsed #admin-sidebar .nav-link { justify-content: center; }
            body.sidebar-collapsed #sidebar-collapse-icon { transform: rotate(180deg); }
        }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden text-slate-800">

    <a href="#main-content-area" class="sr-only focus:not-sr-only focus:absolute focus:z-[200] focus:bg-white focus:px-3 focus:py-2 focus:text-sm">Skip to main content</a>

    <!-- Backdrop for mobile sidebar -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden"></div>

    @php
        $navGroups = [
            __('messages.admin_main_menu') => [
                ['route' => 'facilitator.dashboard', 'match' => 'facilitator.dashboard', 'label' => __('messages.admin_dashboard'), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z'],
            ],
            'Case Processing' => [
                ['route' => 'facilitator.applications', 'match' => 'facilitator.applications*', 'label' => __('messages.admin_applications_menu'), 'badge' => $pendingAppsCount ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                ['route' => 'facilitator.reassessments', 'match' => 'facilitator.reassessments*', 'label' => __('messages.admin_reassessments'), 'badge' => $pendingReassessmentsCount ?? 0, 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                ['route' => 'facilitator.inquiries', 'match' => 'facilitator.inquiries*', 'label' => __('messages.admin_inquiries'), 'badge' => $pendingInquiriesCount ?? 0, 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
            ],
            'Service Catalogue' => [
                ['route' => 'facilitator.services', 'match' => 'facilitator.services*', 'label' => __('messages.admin_services'), 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['route' => 'facilitator.requirements', 'match' => 'facilitator.requirements*', 'label' => __('messages.admin_requirements'), 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['route' => 'facilitator.templates', 'match' => 'facilitator.templates*', 'label' => __('messages.admin_templates'), 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                ['route' => 'facilitator.eligibility', 'match' => 'facilitator.eligibility*', 'label' => __('messages.admin_eligibility'), 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ],
            __('messages.admin_reports') => [
                ['route' => 'facilitator.users', 'match' => 'facilitator.users*', 'label' => __('messages.admin_residents'), 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['route' => 'facilitator.assessments', 'match' => 'facilitator.assessments*', 'label' => __('messages.admin_assessments'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 14l2 2 4-4'],
                ['route' => 'facilitator.reports', 'match' => 'facilitator.reports*', 'label' => 'Export Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ],
        ];
    @endphp

    <!-- Sidebar -->
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-[#a1000f] to-[#6f000a] text-red-50 flex flex-col shrink-0 z-50 transform -translate-x-full lg:translate-x-0 lg:static h-screen overflow-hidden transition-transform duration-300 ease-in-out border-r border-[#6f000a]" aria-label="Primary">
        <div class="h-20 flex items-center justify-between shrink-0 border-b border-white/10 px-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shrink-0">
                    <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="w-full h-full object-contain">
                </div>
                <div class="brand-text leading-tight min-w-0">
                    <span class="text-xl font-extrabold tracking-wide text-white block">GovAssist</span>
                    <span class="text-xs text-red-200 uppercase tracking-wider font-semibold block truncate">SSFO {{ __('messages.admin_facilitator') }}</span>
                </div>
            </div>
            <button id="mobile-sidebar-close" class="lg:hidden p-1 text-red-100 hover:text-white focus:outline-none" aria-label="Close sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <nav id="admin-sidebar-nav" class="flex-grow overflow-y-auto no-scrollbar px-3 py-4 space-y-5">
            @foreach($navGroups as $groupTitle => $links)
                <div>
                    <p class="nav-group-title text-[10px] font-semibold uppercase tracking-widest text-red-200/80 px-3 mb-1.5">{{ $groupTitle }}</p>
                    <div class="space-y-0.5">
                        @foreach($links as $link)
                            @php($active = Route::is($link['match']))
                            <a href="{{ route($link['route']) }}" title="{{ $link['label'] }}" @if($active) aria-current="page" @endif
                               class="nav-link group flex items-center justify-between gap-3 px-3 py-2 rounded-md text-[13px] font-medium border-l-2 transition-colors {{ $active ? 'bg-white/10 text-white border-amber-400' : 'text-red-100 border-transparent hover:bg-white/5 hover:text-white' }}">
                                <span class="flex items-center gap-3 min-w-0">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-white' : 'text-red-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $link['icon'] }}" /></svg>
                                    <span class="nav-label truncate">{{ $link['label'] }}</span>
                                </span>
                                @if(($link['badge'] ?? 0) > 0)
                                    <span class="nav-badge bg-amber-400 text-slate-900 text-[10px] font-bold px-1.5 min-w-5 text-center py-0.5 rounded" title="{{ $link['badge'] }} pending">{{ $link['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-white/10 p-3 space-y-2">
            <button type="button" id="sidebar-collapse-btn" class="hidden lg:flex w-full items-center justify-center gap-2 px-3 py-2 rounded-md text-xs text-red-100 hover:bg-white/5 hover:text-white" aria-label="Collapse sidebar">
                <svg id="sidebar-collapse-icon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                <span class="nav-label">Collapse</span>
            </button>
            <form action="{{ route('logout') }}" method="POST" id="facilitator-logout-form">
                @csrf
                <button type="button" title="{{ __('messages.admin_log_out') }}" onclick="showConfirmModal('Are you sure you want to log out?', () => document.getElementById('facilitator-logout-form').submit(), 'Sign out')" class="nav-link w-full flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded-md text-xs font-semibold transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span class="nav-label">{{ __('messages.admin_log_out') }}</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="flex-grow flex flex-col min-w-0 overflow-hidden">

        <header class="h-20 bg-white border-t-4 border-t-[#b80010] text-slate-800 px-4 sm:px-6 flex items-center justify-between shrink-0 z-30 border-b border-slate-200">
            <div class="flex items-center gap-3 min-w-0">
                <button id="mobile-sidebar-toggle" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:bg-slate-100 rounded-md focus:outline-none" aria-label="Open navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div class="min-w-0 leading-tight">
                    <nav aria-label="Breadcrumb" class="hidden sm:block text-[11px] text-slate-500">
                        <a href="{{ route('facilitator.dashboard') }}" class="hover:text-slate-800">GovAssist</a>
                        <span class="mx-1">/</span>
                        <span>@yield('breadcrumb_section', 'Facilitator Portal')</span>
                    </nav>
                    <h1 class="text-xl font-bold text-[#96000e] truncate">@yield('page_title', 'Dashboard')</h1>
                </div>
            </div>

            <div class="flex items-center gap-1 sm:gap-3">
                <!-- Language Selector -->
                <div class="hidden sm:flex items-center border border-slate-200 rounded-md overflow-hidden" role="group" aria-label="Language">
                    @foreach(['en' => 'EN', 'ceb' => 'CEB', 'fil' => 'FIL'] as $code => $label)
                        <button type="button" onclick="confirmLanguage('{{ $code }}')" @if(app()->getLocale() === $code) aria-pressed="true" @endif class="text-xs font-semibold px-2.5 py-1.5 {{ app()->getLocale() === $code ? 'bg-[#96000e] text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="sm:hidden">
                    <select id="header-lang-select-mobile-fac" aria-label="Language" onchange="confirmLanguage(this.value, 'header-lang-select-mobile-fac')" class="border border-slate-200 rounded-md text-xs font-semibold px-1.5 py-1.5 bg-white">
                        @foreach(['en' => 'EN', 'ceb' => 'CEB', 'fil' => 'FIL'] as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Notification Bell -->
                <div class="relative" id="notification-dropdown-container">
                    <button id="notification-bell-btn" type="button" aria-label="Notifications" aria-haspopup="true" class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-md focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        @if(isset($adminNotifications) && $adminNotifications->count() > 0)
                            <span class="absolute top-0.5 right-0.5 min-w-4 h-4 px-1 bg-amber-400 text-slate-900 font-bold text-[10px] rounded-full flex items-center justify-center">{{ $adminNotifications->count() }}</span>
                        @endif
                    </button>

                    <div id="notification-dropdown-menu" class="hidden absolute right-0 top-full mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white border border-slate-200 shadow-lg z-50 rounded-md overflow-hidden text-slate-800">
                        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Pending items</span>
                            @if(isset($adminNotifications) && $adminNotifications->count() > 0)
                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold uppercase rounded">{{ $adminNotifications->count() }} awaiting action</span>
                            @endif
                        </div>
                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            @forelse($adminNotifications ?? [] as $notif)
                                <a href="{{ $notif['link'] }}" class="block px-4 py-3 hover:bg-slate-50">
                                    <div class="flex justify-between items-start gap-2 mb-0.5">
                                        <span class="text-xs font-semibold text-slate-800">
                                            <span class="inline-block text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded mr-1 {{ $notif['type'] === 'application' ? 'bg-blue-100 text-blue-800' : ($notif['type'] === 'inquiry' ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-800') }}">{{ $notif['type'] === 'application' ? 'Application' : ($notif['type'] === 'inquiry' ? 'Inquiry' : 'Reassessment') }}</span>{{ $notif['title'] }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $notif['time']->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-snug">{{ $notif['message'] }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-slate-500">Nothing is awaiting action.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="hidden md:block text-right leading-tight border-l border-slate-200 pl-3">
                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Today</span>
                    <span class="block text-xs font-semibold text-slate-700">{{ now()->format('M j, Y') }}</span>
                </div>

                <!-- Account -->
                <a href="{{ route('facilitator.profile.edit') }}" class="flex items-center gap-2.5 border-l border-slate-200 pl-3 hover:opacity-90" title="Account settings">
                    <div class="hidden sm:flex flex-col items-end leading-tight">
                        <span class="text-xs font-semibold text-slate-800">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Facilitator</span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-[#96000e] text-white flex items-center justify-center font-bold text-sm overflow-hidden">
                        @if(Auth::user()->avatar)
                            <img src="{{ Storage::disk('public')->url(Auth::user()->avatar) }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ substr(Auth::user()->name, 0, 1) }}
                        @endif
                    </div>
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <main id="main-content-area" class="flex-grow p-3 sm:p-4 overflow-y-auto relative scroll-smooth bg-[#f4f6f9]">
            <!-- Alert Messages -->
            @if(session('success'))
                <div role="status" class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-md text-emerald-800 flex items-center space-x-3">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold text-xs">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-600 rounded-md text-rose-800 flex items-center space-x-3">
                    <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="font-semibold text-xs">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-600 rounded-md text-rose-800">
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
    <div id="confirm-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/60 p-4">
        <div role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title" class="bg-white border-t-4 border-[#b80010] max-w-sm w-full p-6 shadow-xl space-y-4 rounded-md">
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-widest text-red-700 mb-1" id="confirm-modal-title">Confirm Action</h4>
                <p class="text-xs text-slate-600 leading-relaxed font-semibold mt-2" id="confirm-modal-message">Are you sure?</p>
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" id="confirm-modal-cancel" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[10px] transition-colors rounded-md">
                    Cancel
                </button>
                <button type="button" id="confirm-modal-submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-[10px] transition-colors rounded-md">
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

        function confirmLanguage(lang, elementId = null) {
            if (lang === "{{ app()->getLocale() }}") return;
            showConfirmModal("Are you sure you want to change the language?", () => {
                changeLanguage(lang);
            }, "Change Language");
            
            if (elementId) {
                const cancelBtn = document.getElementById('confirm-modal-cancel');
                const onCancelReset = () => {
                    document.getElementById(elementId).value = "{{ app()->getLocale() }}";
                    cancelBtn.removeEventListener('click', onCancelReset);
                };
                cancelBtn.addEventListener('click', onCancelReset);
            }
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

            // Notification dropdown toggle
            const notifBtn = document.getElementById('notification-bell-btn');
            const notifMenu = document.getElementById('notification-dropdown-menu');
            if (notifBtn && notifMenu) {
                notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notifMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', (e) => {
                    if (!notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
                        notifMenu.classList.add('hidden');
                    }
                });
            }

            // Mobile Sidebar Toggle
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggleBtn = document.getElementById('mobile-sidebar-toggle');
            const closeBtn = document.getElementById('mobile-sidebar-close');

            function openSidebar() {
                if (sidebar) sidebar.classList.remove('-translate-x-full');
                if (backdrop) backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

            // Desktop collapse (remembered per browser)
            const collapseBtn = document.getElementById('sidebar-collapse-btn');
            try { if (localStorage.getItem('admin_sidebar_collapsed') === '1') document.body.classList.add('sidebar-collapsed'); } catch (e) {}
            if (collapseBtn) collapseBtn.addEventListener('click', () => {
                const collapsed = document.body.classList.toggle('sidebar-collapsed');
                try { localStorage.setItem('admin_sidebar_collapsed', collapsed ? '1' : '0'); } catch (e) {}
            });
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
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

        // Scroll to Top functionality for the main container (Placed in Bottom Right)
        const mainContent = document.getElementById('main-content-area');
        if (mainContent) {
            mainContent.addEventListener('scroll', function() {
                const btn = document.getElementById('scrollToTopBtn');
                if (btn) {
                    if (mainContent.scrollTop > 100) {
                        btn.style.bottom = '2rem';
                    } else {
                        btn.style.bottom = '-5rem';
                    }
                }
            });
        }
    </script>

    <!-- Scroll to Top Button (Bottom Right Position) -->
    <button id="scrollToTopBtn" onclick="document.getElementById('main-content-area').scrollTo({top: 0, behavior: 'smooth'})" class="fixed right-8 bg-[#b80010] hover:bg-[#96000e] text-white p-3 shadow-md transition-all duration-300 z-[9999] flex items-center justify-center rounded-md" style="bottom: -5rem;" aria-label="Scroll to top">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</body>
</html>
