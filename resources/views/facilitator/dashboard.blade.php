@extends('layouts.facilitator')

@section('title', 'Facilitator Dashboard - GovAssist')

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-4">

    <!-- Overview Metrics (full-width, 4 columns) -->
    <div class="bg-white rounded-lg border border-slate-200/80 p-5 shadow-sm hover:shadow-sm transition-all duration-200 ">
        <div class="border-b border-slate-100 pb-3 mb-3.5">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-1.5 h-4 bg-gradient-to-b from-red-600 to-red-800 rounded-full mr-2.5 inline-block"></span>
                Overview Metrics
            </h3>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Residents (Blue Gradient) -->
            <div class="group bg-gradient-to-br from-blue-50/90 via-sky-50/40 to-white border border-blue-200/80 p-4 rounded-lg flex items-center justify-between transition-all duration-200 hover:shadow-md hover:border-blue-300 hover:-translate-y-0.5">
                <div>
                    <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Residents</span>
                    <span class="text-2xl font-bold text-slate-900 block mt-1 tracking-tight leading-none group-hover:text-blue-700 transition-colors">{{ $totalUsers }}</span>
                    <span class="text-[9px] text-slate-400 font-bold block mt-1">Registry directory</span>
                </div>
                <div class="w-8 h-8 rounded-md bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-500/25 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            <!-- Pending Applications (Amber Gradient) -->
            <div class="group bg-gradient-to-br from-amber-50/90 via-yellow-50/40 to-white border border-amber-200/80 p-4 rounded-lg flex items-center justify-between transition-all duration-200 hover:shadow-md hover:border-amber-300 hover:-translate-y-0.5">
                <div>
                    <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Pending Applications</span>
                    <span class="text-2xl font-bold text-slate-900 block mt-1 tracking-tight leading-none group-hover:text-amber-700 transition-colors">{{ $pendingApplications }}</span>
                    <span class="text-[9px] text-amber-600 font-bold block mt-1">Requires review</span>
                </div>
                <div class="w-8 h-8 rounded-md bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-500/25 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Approved Applications (Emerald Gradient) -->
            <div class="group bg-gradient-to-br from-emerald-50/90 via-teal-50/40 to-white border border-emerald-200/80 p-4 rounded-lg flex items-center justify-between transition-all duration-200 hover:shadow-md hover:border-emerald-300 hover:-translate-y-0.5">
                <div>
                    <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Approved Applications</span>
                    <span class="text-2xl font-bold text-slate-900 block mt-1 tracking-tight leading-none group-hover:text-emerald-700 transition-colors">{{ $approvedApplications }}</span>
                    <span class="text-[9px] text-emerald-600 font-bold block mt-1">Granted assistance</span>
                </div>
                <div class="w-8 h-8 rounded-md bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-500/25 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Rejected Applications (Rose Gradient) -->
            <div class="group bg-gradient-to-br from-rose-50/90 via-red-50/40 to-white border border-rose-200/80 p-4 rounded-lg flex items-center justify-between transition-all duration-200 hover:shadow-md hover:border-rose-300 hover:-translate-y-0.5">
                <div>
                    <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block leading-tight">Rejected Applications</span>
                    <span class="text-2xl font-bold text-slate-900 block mt-1 tracking-tight leading-none group-hover:text-rose-700 transition-colors">{{ $rejectedApplications }}</span>
                    <span class="text-[9px] text-rose-500 font-bold block mt-1">Ineligible / denied</span>
                </div>
                <div class="w-8 h-8 rounded-md bg-gradient-to-br from-rose-600 to-red-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-500/25 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Top row: Summary Chart & Metrics Grid (Modern elevated cards) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
        
        <!-- Application Summary Analytics Card (Left Column) -->
        <div class="lg:col-span-7 h-full bg-white rounded-lg border border-slate-200/80 p-5 shadow-sm hover:shadow-sm transition-all duration-200 flex flex-col justify-between">
            <div>
                <!-- Card Header with Segmented Switcher Buttons -->
                <div class="border-b border-slate-100 pb-3 mb-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center">
                        <span class="w-1.5 h-4 bg-gradient-to-b from-red-600 to-red-800 rounded-full mr-2.5 inline-block"></span>
                        Application Summary
                    </h3>
                    
                    <!-- Segmented tab switcher -->
                    <div class="flex items-center bg-slate-100/90 p-1 rounded-md border border-slate-200/80 self-start sm:self-auto shrink-0 shadow-inner space-x-1">
                        <button type="button" onclick="switchSummaryTab('status')" id="tab-btn-status" class="text-xs font-bold px-3.5 py-1 rounded-lg transition-all duration-200 bg-white text-slate-800 shadow-sm border border-slate-200/50">
                            By Status
                        </button>
                        <button type="button" onclick="switchSummaryTab('programs')" id="tab-btn-programs" class="text-xs font-bold px-3.5 py-1 rounded-lg transition-all duration-200 text-slate-500 hover:text-slate-800">
                            By Program
                        </button>
                    </div>
                </div>

                @php
                    $total = $totalApplications ?: 1;
                    $pctPending = round(($pendingApplications / $total) * 100);
                    $pctApproved = round(($approvedApplications / $total) * 100);
                    $pctRejected = round(($rejectedApplications / $total) * 100);
                @endphp

                <!-- Tab 1: By Status (Gradient Progress Bar & Elevated Cards) -->
                <div id="summary-view-status" class="block space-y-3.5">
                    <!-- Progress Bar Section -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-extrabold text-slate-500 uppercase tracking-wider text-[10px]">Status Distribution</span>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full font-bold text-[10px] border border-slate-200">
                                Total: {{ $totalApplications }} Applications
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 h-3.5 rounded-full overflow-hidden p-0.5 flex gap-1 border border-slate-200/80 shadow-inner">
                            @if($totalApplications > 0)
                                @if($approvedApplications > 0)
                                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500 shadow-xs" style="width: {{ $pctApproved }}%" title="Approved: {{ $approvedApplications }} ({{ $pctApproved }}%)"></div>
                                @endif
                                @if($pendingApplications > 0)
                                    <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-full rounded-full transition-all duration-500 shadow-xs" style="width: {{ $pctPending }}%" title="Pending Review: {{ $pendingApplications }} ({{ $pctPending }}%)"></div>
                                @endif
                                @if($rejectedApplications > 0)
                                    <div class="bg-gradient-to-r from-rose-500 to-rose-600 h-full rounded-full transition-all duration-500 shadow-xs" style="width: {{ $pctRejected }}%" title="Rejected: {{ $rejectedApplications }} ({{ $pctRejected }}%)"></div>
                                @endif
                            @else
                                <div class="bg-slate-200 h-full w-full rounded-full" title="No Applications"></div>
                            @endif
                        </div>
                    </div>

                    <!-- 3 Elevated Metric Columns -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Approved -->
                        <div class="relative overflow-hidden bg-gradient-to-b from-emerald-50/70 via-emerald-50/20 to-white border border-emerald-200/80 rounded-lg p-3 text-center shadow-xs transition-all duration-200 hover:shadow-md hover:border-emerald-300 group">
                            <div class="h-1 w-8 bg-emerald-500 rounded-full mx-auto mb-1"></div>
                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Approved</span>
                            <span class="text-2xl font-bold text-slate-850 block mt-0.5 leading-none">{{ $approvedApplications }}</span>
                            <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-100/70 border border-emerald-200/60 rounded-full px-2 py-0.5 mt-1.5">
                                {{ $pctApproved }}% of total
                            </span>
                        </div>

                        <!-- Pending Review -->
                        <div class="relative overflow-hidden bg-gradient-to-b from-amber-50/70 via-amber-50/20 to-white border border-amber-200/80 rounded-lg p-3 text-center shadow-xs transition-all duration-200 hover:shadow-md hover:border-amber-300 group">
                            <div class="h-1 w-8 bg-amber-500 rounded-full mx-auto mb-1"></div>
                            <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Pending Review</span>
                            <span class="text-2xl font-bold text-slate-850 block mt-0.5 leading-none">{{ $pendingApplications }}</span>
                            <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-100/70 border border-amber-200/60 rounded-full px-2 py-0.5 mt-1.5">
                                {{ $pctPending }}% of total
                            </span>
                        </div>

                        <!-- Rejected -->
                        <div class="relative overflow-hidden bg-gradient-to-b from-rose-50/70 via-rose-50/20 to-white border border-rose-200/80 rounded-lg p-3 text-center shadow-xs transition-all duration-200 hover:shadow-md hover:border-rose-300 group">
                            <div class="h-1 w-8 bg-rose-500 rounded-full mx-auto mb-1"></div>
                            <span class="text-[10px] font-bold text-rose-800 uppercase tracking-wider block">Rejected</span>
                            <span class="text-2xl font-bold text-slate-850 block mt-0.5 leading-none">{{ $rejectedApplications }}</span>
                            <span class="inline-flex items-center text-[10px] font-bold text-rose-700 bg-rose-100/70 border border-rose-200/60 rounded-full px-2 py-0.5 mt-1.5">
                                {{ $pctRejected }}% of total
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: By Program (SVG Vertical Bar Chart) -->
                <div id="summary-view-programs" class="hidden space-y-3">
                    <div class="bg-slate-50 border border-slate-200 rounded-md p-3 shadow-2xs">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Program Application Volume Chart</span>
                        
                        <div class="w-full flex justify-center">
                            <svg class="w-full max-w-[420px] h-[150px]" viewBox="0 0 400 150">
                                <defs>
                                    <linearGradient id="chartBarGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#b91c1c" />
                                        <stop offset="100%" stop-color="#7f1d1d" />
                                    </linearGradient>
                                </defs>
                                
                                @php
                                    $maxVal = max($servicesBreakdown->pluck('checklists_count')->max(), 1);
                                    if ($maxVal < 4) {
                                        $scaleMax = 3;
                                        $scalePoints = [
                                            ['y' => 115, 'val' => 0],
                                            ['y' => 85, 'val' => 1],
                                            ['y' => 55, 'val' => 2],
                                            ['y' => 25, 'val' => 3],
                                        ];
                                    } else {
                                        $scaleMax = $maxVal;
                                        $scalePoints = [
                                            ['y' => 115, 'val' => 0],
                                            ['y' => 85, 'val' => round($scaleMax * 0.33)],
                                            ['y' => 55, 'val' => round($scaleMax * 0.66)],
                                            ['y' => 25, 'val' => $scaleMax],
                                        ];
                                    }
                                @endphp
                                @foreach($scalePoints as $scale)
                                    <text x="18" y="{{ $scale['y'] + 4 }}" text-anchor="end" class="fill-slate-400 font-extrabold text-[10px]">{{ $scale['val'] }}</text>
                                    <line x1="28" y1="{{ $scale['y'] }}" x2="385" y2="{{ $scale['y'] }}" stroke="currentColor" class="text-slate-200" stroke-width="1" stroke-dasharray="3 3" />
                                @endforeach

                                <line x1="28" y1="115" x2="385" y2="115" stroke="currentColor" class="text-slate-300" stroke-width="1.5" />

                                @foreach($servicesBreakdown as $index => $svc)
                                    @php
                                        $barHeight = ($svc->checklists_count / $scaleMax) * 90;
                                        $x = 42 + $index * 68;
                                        $y = 115 - $barHeight;
                                        
                                        $abbrev = '';
                                        if (str_contains($svc->name_en, 'Educational')) $abbrev = 'EDUC';
                                        elseif (str_contains($svc->name_en, 'Medical')) $abbrev = 'MED';
                                        elseif (str_contains($svc->name_en, 'Burial')) $abbrev = 'BUR';
                                        elseif (str_contains($svc->name_en, 'Transportation')) $abbrev = 'TRAN';
                                        else $abbrev = 'EMP';
                                    @endphp
                                    
                                    @if($barHeight > 0)
                                        <rect x="{{ $x }}" y="{{ $y }}" width="32" height="{{ $barHeight }}" fill="url(#chartBarGrad)" rx="5" class="transition-all duration-500 hover:opacity-85 shadow-sm" />
                                        <text x="{{ $x + 16 }}" y="{{ $y - 4 }}" text-anchor="middle" class="fill-slate-800 font-bold text-[10px]">{{ $svc->checklists_count }}</text>
                                    @else
                                        <rect x="{{ $x }}" y="113" width="32" height="2" fill="currentColor" class="text-slate-300" rx="1" />
                                        <text x="{{ $x + 16 }}" y="108" text-anchor="middle" class="fill-slate-400 font-extrabold text-[10px]">0</text>
                                    @endif
                                    
                                    <text x="{{ $x + 16 }}" y="132" text-anchor="middle" class="fill-slate-500 font-bold text-[10px] uppercase tracking-wider">{{ $abbrev }}</text>
                                @endforeach
                            </svg>
                        </div>

                        <!-- Legend list -->
                        <div class="mt-2.5 space-y-1 border-t border-slate-200 pt-2">
                            @foreach($servicesBreakdown as $svc)
                                @php
                                    $abbrev = '';
                                    if (str_contains($svc->name_en, 'Educational')) $abbrev = 'EDUC';
                                    elseif (str_contains($svc->name_en, 'Medical')) $abbrev = 'MED';
                                    elseif (str_contains($svc->name_en, 'Burial')) $abbrev = 'BUR';
                                    elseif (str_contains($svc->name_en, 'Transportation')) $abbrev = 'TRAN';
                                    else $abbrev = 'EMP';
                                @endphp
                                <div class="flex items-center justify-between p-1.5 bg-white border border-slate-200/80 rounded-md hover:bg-slate-50 transition-colors shadow-3xs">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-700 font-extrabold rounded text-[10px] border border-slate-200 shrink-0">{{ $abbrev }}</span>
                                        <span class="font-bold text-slate-700 truncate text-xs">{{ $svc->name_en }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 font-bold text-xs rounded border border-red-100 shrink-0 shadow-3xs">
                                        {{ $svc->checklists_count === 0 ? 'none' : $svc->checklists_count }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Helpdesk & Reassessments Stats (50% Width) -->
        <div class="lg:col-span-5 h-full bg-white rounded-lg border border-slate-200/80 p-5 shadow-sm hover:shadow-sm transition-all duration-200 flex flex-col justify-between">
            <div class="space-y-3.5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center">
                        <span class="w-1.5 h-4 bg-gradient-to-b from-red-600 to-red-800 rounded-full mr-2.5 inline-block"></span>
                        Helpdesk & Reassessments
                    </h3>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="group/stats bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 border border-indigo-200/80 p-3 rounded-lg text-center hover:shadow-md hover:border-indigo-300 transition-all duration-200">
                        <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider block">Open Inquiries</span>
                        <span class="text-2xl font-bold text-slate-900 block mt-1 tracking-tight group-hover/stats:text-indigo-700 transition-colors">{{ $pendingInquiries }}</span>
                        <span class="text-[10px] font-bold text-slate-400 block mt-1">out of {{ $totalInquiries }} total</span>
                    </div>
                    <div class="group/stats bg-gradient-to-br from-amber-50/80 via-white to-slate-50 border border-amber-200/80 p-3 rounded-lg text-center hover:shadow-md hover:border-amber-300 transition-all duration-200">
                        <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">Pending Reassess</span>
                        <span class="text-2xl font-bold text-slate-900 block mt-1 tracking-tight group-hover/stats:text-amber-700 transition-colors">{{ $pendingReassessments }}</span>
                        <span class="text-[10px] font-bold text-slate-400 block mt-1">requires review</span>
                    </div>
                </div>
            </div>
            
            <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-3 mt-3.5">
                <a href="{{ route('facilitator.inquiries') }}" class="py-2.5 px-4 bg-gradient-to-r from-red-700 to-red-800 hover:from-red-800 hover:to-red-900 active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider text-center rounded-md shadow-xs hover:shadow-md transition-all flex items-center justify-center space-x-1.5">
                    <span>Manage Inquiries</span>
                </a>
                <a href="{{ route('facilitator.reassessments') }}" class="py-2.5 px-4 bg-gradient-to-r from-red-700 to-red-800 hover:from-red-800 hover:to-red-900 active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider text-center rounded-md shadow-xs hover:shadow-md transition-all flex items-center justify-center space-x-1.5">
                    <span>Reassessments</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Items requiring attention -->
    <section aria-label="Requires attention" class="bg-white border border-slate-200 rounded-md">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-red-800">Requires attention</h2>
            <span class="text-xs text-slate-500">Open items awaiting a facilitator action</span>
        </div>
        <ul class="divide-y divide-slate-100 text-sm">
            @foreach([
                ['Applications pending review', $pendingApplications, route('facilitator.applications'), 'Residents are waiting for their documents to be validated.'],
                ['Reassessment requests pending', $pendingReassessments, route('facilitator.reassessments'), 'Residents asked for their eligibility to be re-evaluated.'],
                ['Inquiries awaiting a reply', $pendingInquiries, route('facilitator.inquiries'), 'Unanswered resident questions.'],
            ] as [$attLabel, $attCount, $attLink, $attHint])
                <li>
                    <a href="{{ $attLink }}" class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="min-w-0">
                            <span class="block font-semibold text-slate-800">{{ $attLabel }}</span>
                            <span class="block text-xs text-slate-500">{{ $attHint }}</span>
                        </span>
                        @if($attCount > 0)
                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-xs font-bold">&#9888; {{ $attCount }} pending</span>
                        @else
                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-xs font-bold">&#10003; All clear</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <!-- Bottom Section: Recent Applications Table -->
    <div class="bg-white rounded-lg border border-slate-200/80 p-5 shadow-sm">
        <div class="pb-2 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-1.5 h-4 bg-gradient-to-b from-red-600 to-red-800 rounded-full mr-2.5 inline-block"></span>
                Recent Applications
            </h3>
            <a href="{{ route('facilitator.applications') }}" class="text-xs font-bold text-red-700 hover:text-red-800 uppercase tracking-wider px-3 py-1 rounded-lg hover:bg-red-50 transition-colors inline-flex items-center space-x-1">
                <span>View All</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-hidden bg-white border border-slate-200/80 rounded-md shadow-2xs mt-2.5">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-650">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50/80">
                            <th class="px-5 py-3">Resident Name</th>
                            <th class="px-5 py-3">Assistance Service</th>
                            <th class="px-5 py-3">Submitted At</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentApplications as $app)
                            <tr class="hover:bg-red-50/20 transition-colors">
                                <td class="px-5 py-3 font-extrabold text-slate-850">{{ $app->user?->name ?? 'Resident' }}</td>
                                <td class="px-5 py-3 text-slate-700 font-medium">{{ $app->service?->name_en ?? 'Service' }}</td>
                                <td class="px-5 py-3 text-slate-400 font-semibold">{{ $app->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-3">
                                    @if($app->status === 'pending')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200 uppercase tracking-wider shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Pending</span>
                                        </span>
                                    @elseif($app->status === 'approved')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200 uppercase tracking-wider shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Approved</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-full border border-rose-200 uppercase tracking-wider shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Rejected</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('facilitator.applications.show', $app->id) }}" class="inline-flex items-center text-[11px] font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-3 py-1.5 rounded-lg border border-red-200/80 hover:border-red-700 transition-all shadow-2xs active:scale-95">
                                        <span>Process</span>
                                        <span class="ml-1">&rarr;</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-xs italic">
                                    No recent applications
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Tab Switcher Script -->
<script>
    function switchSummaryTab(tab) {
        const statusView = document.getElementById('summary-view-status');
        const programsView = document.getElementById('summary-view-programs');
        const statusBtn = document.getElementById('tab-btn-status');
        const programsBtn = document.getElementById('tab-btn-programs');

        if (tab === 'status') {
            statusView.classList.remove('hidden');
            programsView.classList.add('hidden');
            
            statusBtn.classList.add('bg-white', 'text-slate-800', 'shadow-sm', 'border', 'border-slate-200/50');
            statusBtn.classList.remove('text-slate-500');
            programsBtn.classList.remove('bg-white', 'text-slate-800', 'shadow-sm', 'border', 'border-slate-200/50');
            programsBtn.classList.add('text-slate-500');
        } else {
            statusView.classList.add('hidden');
            programsView.classList.remove('hidden');
            
            programsBtn.classList.add('bg-white', 'text-slate-800', 'shadow-sm', 'border', 'border-slate-200/50');
            programsBtn.classList.remove('text-slate-500');
            statusBtn.classList.remove('bg-white', 'text-slate-800', 'shadow-sm', 'border', 'border-slate-200/50');
            statusBtn.classList.add('text-slate-500');
        }
    }
</script>
@endsection
