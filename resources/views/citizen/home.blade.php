@extends('layouts.citizen')

@section('title', __('messages.app_name') . ' - Dashboard')

@section('header_title', __('messages.app_name'))

@section('content')
<div class="space-y-8">

    <!-- Welcome Header Banner (No rounded corners, vibrant red solid) -->
    <div class="bg-red-700 text-white p-6 border-l-4 border-red-300 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <span class="text-xs uppercase tracking-widest text-red-200 font-bold">{{ Auth::check() ? __('messages.welcome_back') : 'Welcome to GovAssist' }}</span>
            <h2 class="text-2xl font-bold mt-1 tracking-tight text-white">{{ Auth::check() ? Auth::user()->name : 'Guest User' }}</h2>
            <p class="text-xs text-red-100 mt-1 max-w-xl">
                Welcome to your GovAssist Portal. Manage applications, verify eligibility, and submit requirements checklists.
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            @auth
                <a href="{{ route('citizen.eligibility') }}" class="px-4 py-2 bg-white hover:bg-red-50 transition-colors text-xs font-bold uppercase tracking-wider text-red-700">
                    Check Eligibility
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 bg-white hover:bg-red-50 transition-colors text-xs font-bold uppercase tracking-wider text-red-700">
                    Log In to Access Features
                </a>
            @endauth
        </div>
    </div>

    <!-- Search Form -->
    <form action="{{ route('citizen.home') }}" method="GET" class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </span>
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="w-full pl-11 pr-12 py-3 bg-white border border-slate-200 focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">
        @if($search)
            <a href="{{ route('citizen.home') }}" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        @endif
    </form>

    <!-- Two-column responsive desktop layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Services (2 cols on lg) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                    {{ __('messages.all_services') }}
                </h3>
            </div>

            @if($categories->isEmpty())
                <div class="p-8 text-center bg-white border border-slate-200 text-xs text-slate-400 uppercase tracking-wider font-semibold">
                    No services found matching your search criteria.
                </div>
            @else
                @foreach($categories as $category)
                    @if($category->governmentServices->isNotEmpty())
                        <div class="space-y-3">
                            <!-- Category Banner -->
                            <h4 class="text-xs font-extrabold uppercase tracking-widest text-red-700 bg-red-50/60 px-4 py-2.5 border-l-4 border-red-700 shadow-sm">
                                {{ $category->category_name }}
                            </h4>

                            <!-- Services Grid -->
                            <div class="grid grid-cols-1 {{ $category->governmentServices->count() > 1 ? 'md:grid-cols-2' : '' }} gap-4">
                                @foreach($category->governmentServices as $service)
                                    @php
                                        // Retrieve translation based on locale
                                        $trans = $service->translations->where('language_code', app()->getLocale())->first();
                                        $serviceName = $trans ? $trans->service_name : $service->service_name;
                                        $serviceDesc = $trans ? $trans->description : $service->description;
                                    @endphp
                                    <div class="bg-white border-l-4 border-l-red-600 border-t border-r border-b border-slate-200 p-6 flex flex-col justify-between hover:border-red-600 hover:shadow-md transition-all duration-300 shadow-sm">
                                        <div>
                                            <h5 class="text-sm font-extrabold text-slate-900 tracking-tight">{{ $serviceName }}</h5>
                                            <p class="text-xs text-slate-500 mt-2.5 leading-relaxed line-clamp-3">
                                                {{ $serviceDesc }}
                                            </p>
                                        </div>
                                        
                                        <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between">
                                            <a href="{{ route('citizen.eligibility.assess', $service->id) }}" class="text-[10px] font-extrabold uppercase tracking-widest text-red-700 hover:text-red-900 flex items-center space-x-1.5 group">
                                                <span>Check Eligibility</span>
                                                <span class="inline-block transform group-hover:translate-x-1 transition-transform duration-200 text-xs font-bold">&rarr;</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Right: Application Progress tracker -->
        <div class="space-y-6">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                    {{ __('messages.recent_applications') }}
                </h3>
            </div>

            <div class="bg-white border border-slate-200 p-5 shadow-sm">
                @if($applications->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        {{ __('messages.no_applications') }}
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($applications as $app)
                            @php
                                $appTrans = $app->service->translations->where('language_code', app()->getLocale())->first();
                                $appName = $appTrans ? $appTrans->service_name : $app->service->service_name;
                            @endphp
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900 hover:text-red-700 transition-colors">
                                            {{ $appName }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 mt-1 uppercase tracking-wider">
                                            {{ __('messages.submitted_on') }} {{ $app->created_at->format('Y-m-d') }}
                                        </span>
                                    </div>
                                    <div class="ml-2">
                                        @if($app->status === 'pending')
                                            <span class="inline-block px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-800 text-[9px] font-bold uppercase tracking-wider">
                                                {{ __('messages.status_pending') }}
                                            </span>
                                        @elseif($app->status === 'approved')
                                            <span class="inline-block px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[9px] font-bold uppercase tracking-wider">
                                                {{ __('messages.status_approved') }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 bg-rose-50 border border-rose-200 text-rose-800 text-[9px] font-bold uppercase tracking-wider">
                                                {{ __('messages.status_rejected') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2.5 flex justify-end">
                                    <a href="{{ route('citizen.eligibility.checklist', $app->service_id) }}" class="text-[9px] font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 border border-slate-200 px-2 py-1 bg-slate-50">
                                        {{ __('messages.view_details') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Inquiry Quick Assist -->
            <div class="bg-red-50/50 border border-red-100 p-5">
                <h4 class="text-xs font-bold text-red-900 uppercase tracking-widest mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Need Assistance?
                </h4>
                <p class="text-xs text-red-800/80 leading-relaxed">
                    Ask GovBot about application procedures, requirements guidelines, and validation updates.
                </p>
                <div class="mt-4">
                    <a href="{{ route('citizen.inquiry') }}" class="inline-block w-full text-center px-4 py-2 bg-red-700 hover:bg-red-800 transition-colors text-white text-[10px] font-bold uppercase tracking-wider">
                        Open GovBot Assistant
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
