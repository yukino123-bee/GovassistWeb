@extends('layouts.resident')

@section('title', __('messages.app_name') . ' - Dashboard')

@section('header_title', __('messages.app_name'))

@section('content')
<div class="space-y-8">

    <!-- Welcome Header Banner (No rounded corners, vibrant red solid) -->
    <div class="relative bg-red-700 text-white p-6 pr-14 border-l-4 border-red-300 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between" role="status">
        <div>
            <span class="text-xs uppercase tracking-widest text-red-200 font-bold">{{ Auth::check() ? __('messages.welcome_back') : __('messages.welcome_guest') }}</span>
            @auth
                <h2 class="text-2xl font-bold mt-1 tracking-tight text-white">{{ Auth::user()->name }}</h2>
            @endauth
            <p class="text-xs text-red-100 mt-1 max-w-xl">
                {{ __('messages.dashboard_intro') }}
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            @auth
                <a href="{{ route('resident.eligibility') }}" class="px-4 py-2 bg-white hover:bg-red-50 transition-colors text-xs font-bold uppercase tracking-wider text-red-700">
                    {{ __('messages.check_eligibility') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 bg-white hover:bg-red-50 transition-colors text-xs font-bold uppercase tracking-wider text-red-700">
                    {{ __('messages.login_to_access') }}
                </a>
            @endauth
        </div>
        {{-- shortcut: hides the welcome banner until the next page load, no saved preference --}}
        <button type="button" onclick="this.closest('[role=status]').remove()" aria-label="{{ __('messages.inq_close') }}" class="absolute top-2 right-2 w-10 h-10 flex items-center justify-center text-3xl text-red-200 hover:text-white leading-none">&times;</button>
    </div>

    @auth
        @php
            $missingFields = Auth::user()->missingProfileFields();
            $completion = (int) round((App\Models\User::REQUIRED_PROFILE_FIELD_COUNT - count($missingFields)) / App\Models\User::REQUIRED_PROFILE_FIELD_COUNT * 100);
        @endphp
        @if(count($missingFields) > 0)
            <div class="relative bg-amber-50 border border-amber-200 border-l-4 border-l-amber-500 p-5 pr-14 flex flex-col md:flex-row md:items-center md:justify-between gap-4" role="status">
                <div class="min-w-0">
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-amber-900">{{ __('messages.profile_incomplete_title') }} <span class="text-amber-700">&middot; {{ $completion }}%</span></h3>
                    <p class="text-xs text-amber-800 mt-1.5">{{ __('messages.profile_incomplete_desc') }}</p>
                    <ul class="mt-2.5 flex flex-wrap gap-1.5">
                        @foreach($missingFields as $labelKey)
                            <li class="px-2 py-0.5 bg-white border border-amber-200 text-[10px] font-bold text-amber-800 uppercase tracking-wider">{{ __('messages.'.$labelKey) }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-3 h-1.5 bg-amber-100 max-w-xs"><div class="h-full bg-amber-500" style="width: {{ $completion }}%"></div></div>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('resident.profile.edit') }}" class="text-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 transition-colors text-white text-[10px] font-bold uppercase tracking-wider">
                        {{ __('messages.complete_profile_btn') }}
                    </a>
                </div>
                {{-- shortcut: hides the banner until the next page load, no saved preference --}}
                <button type="button" onclick="this.closest('[role=status]').remove()" aria-label="{{ __('messages.inq_close') }}" class="absolute top-2 right-2 w-10 h-10 flex items-center justify-center text-3xl text-amber-700 hover:text-amber-900 leading-none">&times;</button>
            </div>
        @endif
    @endauth

    <!-- Search Form -->
    <form action="{{ route('resident.home') }}" method="GET" class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </span>
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="w-full pl-11 pr-12 py-3 bg-white border border-slate-200 focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">
        @if($search)
            <a href="{{ route('resident.home') }}" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
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
                                {{ $category->category_name === 'Educational Assistance' ? __('messages.educational_assistance') : $category->category_name }}
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
                                            <a href="{{ route('resident.eligibility.assess', $service->id) }}" class="text-[10px] font-extrabold uppercase tracking-widest text-red-700 hover:text-red-900 flex items-center space-x-1.5 group">
                                                <span>{{ __('messages.check_eligibility') }}</span>
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

            @auth
                <div class="grid grid-cols-3 gap-3">
                    @foreach([
                        ['stat_total', $applications->count(), 'text-slate-900'],
                        ['stat_pending', $applications->where('status', 'pending')->count(), 'text-amber-700'],
                        ['stat_approved', $applications->where('status', 'approved')->count(), 'text-emerald-700'],
                    ] as [$statLabel, $statValue, $statColor])
                        <div class="bg-white border border-slate-200 px-3 py-3 text-center shadow-sm">
                            <span class="block text-xl font-extrabold {{ $statColor }}">{{ $statValue }}</span>
                            <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">{{ __('messages.'.$statLabel) }}</span>
                        </div>
                    @endforeach
                </div>
            @endauth

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
                                    <a href="{{ route('resident.eligibility.checklist', $app->service_id) }}" class="text-[9px] font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 border border-slate-200 px-2 py-1 bg-slate-50">
                                        {{ __('messages.view_details') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- How it works -->
            <div class="bg-white border border-slate-200 p-5 shadow-sm">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-widest mb-4">{{ __('messages.how_it_works') }}</h4>
                <ol class="space-y-4">
                    @foreach([['step_check', 'step_check_desc'], ['step_docs', 'step_docs_desc'], ['step_track', 'step_track_desc']] as $i => [$stepTitle, $stepDesc])
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 bg-red-700 text-white text-[11px] font-extrabold flex items-center justify-center">{{ $i + 1 }}</span>
                            <div>
                                <span class="block text-xs font-bold text-slate-900">{{ __('messages.'.$stepTitle) }}</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed">{{ __('messages.'.$stepDesc) }}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
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
                    Message our Inquiry Helpdesk about application procedures, requirements guidelines, and validation updates.
                </p>
                <div class="mt-4">
                    <a href="{{ route('resident.inquiry') }}" class="inline-block w-full text-center px-4 py-2 bg-red-700 hover:bg-red-800 transition-colors text-white text-[10px] font-bold uppercase tracking-wider">
                        Open Inquiry Helpdesk
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
