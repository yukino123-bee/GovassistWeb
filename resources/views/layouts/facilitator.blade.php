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
            background-color: #f1f5f9;
        }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden text-slate-800">

    <!-- Backdrop for mobile sidebar -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity duration-300"></div>

    <!-- Sidebar — Red base with curved white container matching mockup red/white theme -->
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-80 bg-red-700 text-white flex flex-col shrink-0 z-50 transform -translate-x-full lg:translate-x-0 lg:static h-screen overflow-hidden transition-transform duration-300 ease-in-out shadow-[4px_0_25px_rgba(0,0,0,0.05)]">
        
        <!-- Top Red Header Block (Logo only) — Height matching h-16 topbar header line -->
        <div class="h-16 bg-red-700 text-white flex flex-col justify-center shrink-0 border-b border-red-800 px-5">
            <!-- Logo / Brand Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center overflow-hidden p-0.5 shadow-sm shrink-0">
                        <img src="{{ asset('ssfo_logo.png') }}" alt="SSFO Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="leading-tight">
                        <span class="text-base font-extrabold tracking-wider text-white leading-tight block">GovAssist</span>
                        <span class="text-[10px] text-red-200 uppercase tracking-wider font-extrabold block">SSFO {{ __('messages.admin_facilitator') }}</span>
                    </div>
                </div>
                <!-- Mobile Close Button -->
                <button id="mobile-sidebar-close" class="lg:hidden p-1 text-red-100 hover:text-white transition-colors focus:outline-none" aria-label="Close sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Floating White Menu Container — Curved on the LEFT, sits level right below the h-16 header line -->
        <div class="flex-grow flex flex-col bg-white text-slate-800 rounded-l-[2.5rem] rounded-r-none ml-4 mr-0 mt-4 mb-4 p-3 shadow-inner overflow-hidden">
            <nav id="admin-sidebar-nav" class="flex-grow space-y-2 overflow-hidden">
                
                <!-- Main Menu Section -->
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1 mb-2 px-2">{{ __('messages.admin_main_menu') }}</p>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('facilitator.dashboard') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.dashboard') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.dashboard') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            <span>{{ __('messages.admin_dashboard') }}</span>
                        </a>

                        <!-- Services -->
                        <a href="{{ route('facilitator.services') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.services*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.services*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>{{ __('messages.admin_services') }}</span>
                        </a>

                        <!-- Requirements -->
                        <a href="{{ route('facilitator.requirements') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.requirements*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.requirements*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>{{ __('messages.admin_requirements') }}</span>
                        </a>

                        <!-- Document Templates -->
                        <a href="{{ route('facilitator.templates') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.templates*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.templates*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>{{ __('messages.admin_templates') }}</span>
                        </a>

                        <!-- Eligibility -->
                        <a href="{{ route('facilitator.eligibility') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.eligibility*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.eligibility*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>{{ __('messages.admin_eligibility') }}</span>
                        </a>

                        <!-- Reassessment Requests -->
                        <a href="{{ route('facilitator.reassessments') }}" class="group flex items-center justify-between px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.reassessments*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.reassessments*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>{{ __('messages.admin_reassessments') }}</span>
                            </div>
                            @if(isset($pendingReassessmentsCount) && $pendingReassessmentsCount > 0)
                                <span class="bg-red-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm">{{ $pendingReassessmentsCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Applications Menu Section -->
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1 mb-2 px-2">{{ __('messages.admin_applications_menu') }}</p>
                    <div class="space-y-1">
                        <!-- Applications -->
                        <a href="{{ route('facilitator.applications') }}" class="group flex items-center justify-between px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.applications*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.applications*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                </svg>
                                <span>{{ __('messages.admin_applications_menu') }}</span>
                            </div>
                            @if(isset($pendingAppsCount) && $pendingAppsCount > 0)
                                <span class="bg-red-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm">{{ $pendingAppsCount }}</span>
                            @endif
                        </a>

                        <!-- Inquiries -->
                        <a href="{{ route('facilitator.inquiries') }}" class="group flex items-center justify-between px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.inquiries*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.inquiries*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <span>{{ __('messages.admin_inquiries') }}</span>
                            </div>
                            @if(isset($pendingInquiriesCount) && $pendingInquiriesCount > 0)
                                <span class="bg-red-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm">{{ $pendingInquiriesCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Reports Section -->
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1 mb-2 px-2">{{ __('messages.admin_reports') }}</p>
                    <div class="space-y-1">
                        <!-- Residents -->
                        <a href="{{ route('facilitator.users') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.users*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.users*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>{{ __('messages.admin_residents') }}</span>
                        </a>

                        <!-- Assessments -->
                        <a href="{{ route('facilitator.assessments') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.assessments*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.assessments*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ __('messages.admin_assessments') }}</span>
                        </a>

                        <!-- Export Reports -->
                        <a href="{{ route('facilitator.reports') }}" class="group flex items-center space-x-3 px-3.5 py-2 rounded-xl transition-all duration-200 text-sm font-semibold border {{ Route::is('facilitator.reports*') ? 'bg-red-50/90 text-red-700 border-red-200 shadow-2xs' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-red-700 hover:border-slate-100' }}">
                            <svg class="w-4.5 h-4.5 shrink-0 {{ Route::is('facilitator.reports*') ? 'text-red-700' : 'text-slate-400 group-hover:text-red-700 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export Reports</span>
                        </a>
                    </div>
                </div>


            </nav>
        </div>

        <!-- Log Out Footer Section (Sitting on the Red Sidebar Background) — Curved top-right -->
        <div class="bg-red-700 text-white px-6 py-5 rounded-tr-[2.5rem] shrink-0 shadow-[0_-4px_20px_rgba(184,0,16,0.12)] mt-auto border-t border-red-650/40">
            <form action="{{ route('logout') }}" method="POST" id="facilitator-logout-form">
                @csrf
                <button type="button" onclick="showConfirmModal('Are you sure you want to log out?', () => document.getElementById('facilitator-logout-form').submit())" class="w-full flex items-center justify-center space-x-2 bg-white hover:bg-red-50 text-red-700 px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 focus:outline-none border border-white shadow-md">
                    <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="text-red-700">{{ __('messages.admin_log_out') }}</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="flex-grow flex flex-col min-w-0 overflow-hidden bg-slate-100">
        
        <!-- Topbar Header — Premium red background style with high-clarity white elements -->
        <header class="h-16 bg-red-700 text-white px-6 flex items-center justify-between shrink-0 z-35 shadow-md border-b border-red-800">
            <div class="flex items-center space-x-3">
                <!-- Hamburger menu button -->
                <button id="mobile-sidebar-toggle" class="lg:hidden p-1.5 -ml-1 mr-1 text-white hover:bg-white/10 rounded-lg transition-colors focus:outline-none" aria-label="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-sm font-black text-white uppercase tracking-wider">@yield('page_title', 'Dashboard')</h2>
            </div>

            <!-- Topbar Right -->
            <div class="flex items-center divide-x divide-white/20">
                <!-- Language Selector -->
                <div class="pr-4 flex items-center">
                    <!-- Desktop Selector -->
                    <div class="hidden sm:flex items-center bg-white/10 p-1 rounded-xl border border-white/10">
                        <button type="button" onclick="confirmLanguage('en')" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all {{ app()->getLocale() === 'en' ? 'bg-white text-red-700 shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }}">EN</button>
                        <button type="button" onclick="confirmLanguage('ceb')" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all {{ app()->getLocale() === 'ceb' ? 'bg-white text-red-700 shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }}">CEB</button>
                        <button type="button" onclick="confirmLanguage('fil')" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all {{ app()->getLocale() === 'fil' ? 'bg-white text-red-700 shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }}">FIL</button>
                        <button type="button" onclick="confirmLanguage('sub')" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all {{ app()->getLocale() === 'sub' ? 'bg-white text-red-700 shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }}">SUB</button>
                    </div>
                    <!-- Mobile Selector -->
                    <div class="flex sm:hidden items-center bg-white/10 px-2 py-1.5 rounded-xl border border-white/10">
                        <select id="header-lang-select-mobile-fac" onchange="confirmLanguage(this.value, 'header-lang-select-mobile-fac')" class="bg-transparent text-white text-xs font-extrabold uppercase tracking-wider outline-none cursor-pointer border-none pr-1">
                            <option class="text-slate-800" value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>EN</option>
                            <option class="text-slate-800" value="ceb" {{ app()->getLocale() === 'ceb' ? 'selected' : '' }}>CEB</option>
                            <option class="text-slate-800" value="fil" {{ app()->getLocale() === 'fil' ? 'selected' : '' }}>FIL</option>
                            <option class="text-slate-800" value="sub" {{ app()->getLocale() === 'sub' ? 'selected' : '' }}>SUB</option>
                        </select>
                    </div>
                </div>

                <!-- Notification Bell -->
                <div class="px-4 flex items-center relative" id="notification-dropdown-container">
                    <button id="notification-bell-btn" type="button" class="relative p-1.5 text-white/95 hover:text-white hover:bg-white/10 rounded-lg transition-colors focus:outline-none">
                        <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(isset($adminNotifications) && $adminNotifications->count() > 0)
                            <span class="absolute top-0 right-0.5 w-4 h-4 bg-white rounded-full border border-red-700 text-xs font-black text-red-700 flex items-center justify-center leading-none shadow-sm">{{ $adminNotifications->count() }}</span>
                        @endif
                    </button>

                    <!-- Dropdown Panel -->
                    <div id="notification-dropdown-menu" class="hidden absolute right-0 top-full mt-3 w-80 bg-white border border-slate-200 shadow-2xl z-50 rounded-2xl overflow-hidden transform origin-top-right transition-all duration-200 text-slate-800">
                        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider font-sans">Notifications</span>
                            @if(isset($adminNotifications) && $adminNotifications->count() > 0)
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-xs font-bold tracking-wider uppercase">{{ $adminNotifications->count() }} new</span>
                            @endif
                        </div>
                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            @forelse($adminNotifications ?? [] as $notif)
                                <a href="{{ $notif['link'] }}" class="block px-4 py-3 hover:bg-slate-50/70 transition-colors">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">
                                            @if($notif['type'] === 'application')
                                                📁 {{ $notif['title'] }}
                                            @elseif($notif['type'] === 'inquiry')
                                                💬 {{ $notif['title'] }}
                                            @else
                                                🔄 {{ $notif['title'] }}
                                            @endif
                                        </span>
                                        <span class="text-xs text-slate-400 font-medium">{{ $notif['time']->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-snug">{{ $notif['message'] }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-slate-400 italic">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Date -->
                <div class="px-4 hidden sm:flex items-center space-x-2">
                    <div class="p-1.5 rounded-lg bg-white/10 text-white border border-white/10">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-white/70">TODAY</span>
                        <span class="text-xs font-bold text-white">{{ now()->format('M j, Y') }}</span>
                    </div>
                </div>

                <!-- Avatar Display & Dropdown -->
                <a href="{{ route('facilitator.profile.edit') }}" class="pl-4 flex items-center space-x-2.5 hover:opacity-85 transition-opacity">
                    <div class="hidden sm:flex flex-col items-end leading-tight">
                        <span class="text-xs font-bold text-white">{{ Auth::user()->name }}</span>
                        <span class="text-xs font-extrabold text-white/70 uppercase tracking-wider block">{{ __('messages.admin_facilitator') }}</span>
                    </div>
                    <div class="relative">
                        <div class="w-8 h-8 rounded-full bg-white/10 text-white border border-white/20 flex items-center justify-center font-bold text-sm overflow-hidden shadow-sm">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::disk(env('FILESYSTEM_DISK', 'public'))->url(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->name, 0, 1) }}
                            @endif
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-red-700 rounded-full"></span>
                    </div>
                </a>

            </div>
        </header>

        <!-- Content Area -->
        <main id="main-content-area" class="flex-grow p-4 sm:p-5 overflow-y-auto relative scroll-smooth bg-slate-100">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl text-emerald-800 shadow-xs flex items-center space-x-3">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold text-xs">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-600 rounded-xl text-rose-800 shadow-xs">
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
    <div id="confirm-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/65 backdrop-blur-sm p-4">
        <div class="bg-white border-l-4 border-red-700 max-w-sm w-full p-6 shadow-2xl space-y-4 rounded-2xl">
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-widest text-red-700 mb-1" id="confirm-modal-title">Confirm Action</h4>
                <p class="text-xs text-slate-600 leading-relaxed font-semibold mt-2" id="confirm-modal-message">Are you sure?</p>
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" id="confirm-modal-cancel" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[10px] transition-colors rounded-xl">
                    Cancel
                </button>
                <button type="button" id="confirm-modal-submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-[10px] transition-colors rounded-xl">
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
    <button id="scrollToTopBtn" onclick="document.getElementById('main-content-area').scrollTo({top: 0, behavior: 'smooth'})" class="fixed right-8 bg-red-700 hover:bg-red-800 text-white p-3 shadow-md transition-all duration-300 z-[9999] flex items-center justify-center rounded-xl" style="bottom: -5rem;" aria-label="Scroll to top">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</body>
</html>
