@extends('layouts.facilitator')

@section('title', 'Facilitator Dashboard - GovAssist')

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-4">

    <!-- Dashboard Top row: Summary Chart & Metrics Grid (items-start prevents vertical stretching) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Application Summary Analytics Card (Left Column with Toggle Tabs) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <!-- Card Header with Segmented Switcher Buttons -->
                <div class="border-b border-slate-200 pb-3 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        Application Summary
                    </h3>
                    
                    <!-- Table-like switch buttons -->
                    <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200 self-start sm:self-auto shrink-0 shadow-2xs">
                        <button type="button" onclick="switchSummaryTab('status')" id="tab-btn-status" class="text-xs font-extrabold px-3.5 py-1.5 rounded-lg transition-all duration-200 bg-white text-slate-800 shadow-3xs">
                            By Status
                        </button>
                        <button type="button" onclick="switchSummaryTab('programs')" id="tab-btn-programs" class="text-xs font-extrabold px-3.5 py-1.5 rounded-lg transition-all duration-200 text-slate-500 hover:text-slate-800">
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

                <!-- Tab 1: By Status (Modern Stacked Horizontal Progress & Clean Cards) -->
                <div id="summary-view-status" class="block space-y-4">
                    <!-- Stacked Horizontal Bar -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-500">
                            <span>Status Distribution</span>
                            <span>Total Applications: {{ $totalApplications }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden flex shadow-inner border border-slate-200">
                            @if($totalApplications > 0)
                                @if($approvedApplications > 0)
                                    <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $pctApproved }}%" title="Approved: {{ $approvedApplications }}"></div>
                                @endif
                                @if($pendingApplications > 0)
                                    <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $pctPending }}%" title="Pending Review: {{ $pendingApplications }}"></div>
                                @endif
                                @if($rejectedApplications > 0)
                                    <div class="bg-rose-500 h-full transition-all duration-500" style="width: {{ $pctRejected }}%" title="Rejected: {{ $rejectedApplications }}"></div>
                                @endif
                            @else
                                <div class="bg-slate-200 h-full w-full" title="No Applications"></div>
                            @endif
                        </div>
                    </div>

                    <!-- Metrics Columns (No double-borders) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Approved -->
                        <div class="bg-emerald-50/50 border border-emerald-100/60 p-3.5 rounded-xl text-center shadow-2xs">
                            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Approved</span>
                            <span class="text-2xl font-black text-slate-850 block mt-1 leading-none">{{ $approvedApplications }}</span>
                            <span class="text-xs font-bold text-emerald-600 block mt-1.5">{{ $pctApproved }}% of total</span>
                        </div>

                        <!-- Pending Review -->
                        <div class="bg-amber-50/50 border border-amber-100/60 p-3.5 rounded-xl text-center shadow-2xs">
                            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block">Pending Review</span>
                            <span class="text-2xl font-black text-slate-850 block mt-1 leading-none">{{ $pendingApplications }}</span>
                            <span class="text-xs font-bold text-amber-600 block mt-1.5">{{ $pctPending }}% of total</span>
                        </div>

                        <!-- Rejected -->
                        <div class="bg-rose-50/50 border border-rose-100/60 p-3.5 rounded-xl text-center shadow-2xs">
                            <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Rejected</span>
                            <span class="text-2xl font-black text-slate-850 block mt-1 leading-none">{{ $rejectedApplications }}</span>
                            <span class="text-xs font-bold text-rose-600 block mt-1.5">{{ $pctRejected }}% of total</span>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: By Program (High-Fidelity SVG Vertical Bar Chart) -->
                <div id="summary-view-programs" class="hidden space-y-4">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 shadow-2xs">
                        <span class="text-xs font-extrabold text-slate-455 uppercase tracking-wider block mb-3">Program Application Volume Chart</span>
                        
                        <!-- High Fidelity SVG Vertical Bar Chart -->
                        <div class="w-full flex justify-center">
                            <svg class="w-full max-w-[420px] h-[170px]" viewBox="0 0 400 170">
                                <defs>
                                    <!-- Bar Gradient definition -->
                                    <linearGradient id="chartBarGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#b91c1c" />
                                        <stop offset="100%" stop-color="#7f1d1d" />
                                    </linearGradient>
                                </defs>
                                
                                <!-- Horizontal Grid Lines & Y-Axis Scale Values -->
                                @php
                                    $maxVal = max($servicesBreakdown->pluck('checklists_count')->max(), 1);
                                    // Scale points with static values if max checklists is small (prevents double scale labels)
                                    if ($maxVal < 4) {
                                        $scaleMax = 3;
                                        $scalePoints = [
                                            ['y' => 130, 'val' => 0],
                                            ['y' => 95, 'val' => 1],
                                            ['y' => 60, 'val' => 2],
                                            ['y' => 25, 'val' => 3],
                                        ];
                                    } else {
                                        $scaleMax = $maxVal;
                                        $scalePoints = [
                                            ['y' => 130, 'val' => 0],
                                            ['y' => 95, 'val' => round($scaleMax * 0.33)],
                                            ['y' => 60, 'val' => round($scaleMax * 0.66)],
                                            ['y' => 25, 'val' => $scaleMax],
                                        ];
                                    }
                                @endphp
                                @foreach($scalePoints as $scale)
                                    <text x="18" y="{{ $scale['y'] + 4 }}" text-anchor="end" class="fill-slate-455 font-extrabold text-[10px]">{{ $scale['val'] }}</text>
                                    <line x1="28" y1="{{ $scale['y'] }}" x2="385" y2="{{ $scale['y'] }}" stroke="currentColor" class="text-slate-200" stroke-width="1" stroke-dasharray="3 3" />
                                @endforeach

                                <!-- Chart Baseline -->
                                <line x1="28" y1="130" x2="385" y2="150" stroke="currentColor" class="text-slate-300" stroke-width="1.5" />

                                <!-- Graphical Bars -->
                                @foreach($servicesBreakdown as $index => $svc)
                                    @php
                                        $barHeight = ($svc->checklists_count / $scaleMax) * 105;
                                        $x = 42 + $index * 68;
                                        $y = 130 - $barHeight;
                                        
                                        // Map abbreviations
                                        $abbrev = '';
                                        if (str_contains($svc->name_en, 'Educational')) $abbrev = 'EDUC';
                                        elseif (str_contains($svc->name_en, 'Medical')) $abbrev = 'MED';
                                        elseif (str_contains($svc->name_en, 'Burial')) $abbrev = 'BUR';
                                        elseif (str_contains($svc->name_en, 'Transportation')) $abbrev = 'TRAN';
                                        else $abbrev = 'EMP';
                                    @endphp
                                    
                                    <!-- Dynamic Bar -->
                                    @if($barHeight > 0)
                                        <rect x="{{ $x }}" y="{{ $y }}" width="32" height="{{ $barHeight }}" fill="url(#chartBarGrad)" rx="5" class="transition-all duration-500 hover:opacity-85" />
                                        <!-- Value label above the bar -->
                                        <text x="{{ $x + 16 }}" y="{{ $y - 5 }}" text-anchor="middle" class="fill-slate-800 font-black text-[10px]">{{ $svc->checklists_count }}</text>
                                    @else
                                        <rect x="{{ $x }}" y="128" width="32" height="2" fill="currentColor" class="text-slate-300" rx="1" />
                                        <text x="{{ $x + 16 }}" y="122" text-anchor="middle" class="fill-slate-400 font-extrabold text-[10px]">0</text>
                                    @endif
                                    
                                    <!-- X-Axis Label -->
                                    <text x="{{ $x + 16 }}" y="148" text-anchor="middle" class="fill-slate-455 font-black text-[10px] uppercase tracking-wider">{{ $abbrev }}</text>
                                @endforeach
                            </svg>
                        </div>

                        <!-- Improved Program Legend list (Premium List Row Layout) -->
                        <div class="mt-4 space-y-1.5 border-t border-slate-200 pt-3">
                            @foreach($servicesBreakdown as $svc)
                                @php
                                    $abbrev = '';
                                    if (str_contains($svc->name_en, 'Educational')) $abbrev = 'EDUC';
                                    elseif (str_contains($svc->name_en, 'Medical')) $abbrev = 'MED';
                                    elseif (str_contains($svc->name_en, 'Burial')) $abbrev = 'BUR';
                                    elseif (str_contains($svc->name_en, 'Transportation')) $abbrev = 'TRAN';
                                    else $abbrev = 'EMP';
                                @endphp
                                <div class="flex items-center justify-between p-2 bg-white border border-slate-200/80 rounded-xl hover:bg-slate-50 transition-colors shadow-3xs">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-extrabold rounded-lg text-xs border border-slate-200 shrink-0">{{ $abbrev }}</span>
                                        <span class="font-bold text-slate-700 truncate text-xs">{{ $svc->name_en }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 font-black text-xs rounded-lg border border-red-100 shrink-0 shadow-3xs">
                                        {{ $svc->checklists_count === 0 ? 'none' : $svc->checklists_count }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Overview Metrics Container (Right Column - natural height, does not stretch) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="border-b border-slate-200 pb-3 mb-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Overview Metrics
                </h3>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <!-- Total Citizens (Light Blue Theme) -->
                <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-xl flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
                    <div>
                        <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Citizens</span>
                        <span class="text-2xl font-black text-slate-850 block mt-1 transition-colors duration-300 group-hover:text-blue-700 leading-none">{{ $totalUsers }}</span>
                        <div class="flex items-center space-x-1 mt-2 text-blue-600 text-xs font-bold leading-none">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>12.5%</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40 transition-all duration-350 group-hover:rotate-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Pending Applications (Light Amber Theme) -->
                <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-xl flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
                    <div>
                        <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Pending Apps</span>
                        <span class="text-2xl font-black text-slate-850 block mt-1 transition-colors duration-300 group-hover:text-amber-700 leading-none">{{ $pendingApplications }}</span>
                        <div class="flex items-center space-x-1 mt-2 text-amber-600 text-xs font-bold leading-none">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>8.3%</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40 transition-all duration-350 group-hover:rotate-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Approved Applications (Light Emerald Theme) -->
                <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-xl flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
                    <div>
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Approved Apps</span>
                        <span class="text-2xl font-black text-slate-850 block mt-1 transition-colors duration-300 group-hover:text-emerald-700 leading-none">{{ $approvedApplications }}</span>
                        <div class="flex items-center space-x-1 mt-2 text-emerald-600 text-xs font-bold leading-none">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>15.7%</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40 transition-all duration-350 group-hover:rotate-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Rejected Applications (Light Rose Theme) -->
                <div class="group bg-rose-50/70 border border-rose-200 p-3.5 rounded-xl flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-rose-300">
                    <div>
                        <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block leading-tight">Rejected Apps</span>
                        <span class="text-2xl font-black text-slate-850 block mt-1 transition-colors duration-300 group-hover:text-rose-700 leading-none">{{ $rejectedApplications }}</span>
                        <div class="flex items-center space-x-1 mt-2 text-rose-600 text-xs font-bold leading-none">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>5.1%</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-200/40 transition-all duration-350 group-hover:rotate-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Middle Section: Helpdesk & Quick Actions (items-start prevents vertical stretching) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Helpdesk & Reassessments Stats (50% Width) -->
        <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div class="border-b border-slate-200 pb-3">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        Helpdesk & Reassessments
                    </h3>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="group/stats bg-indigo-50/70 border border-indigo-200 p-3.5 rounded-xl text-center hover:bg-white hover:shadow-md hover:border-indigo-300 transition-all duration-300">
                        <span class="text-[11px] font-bold text-indigo-750 uppercase tracking-wider block">Open Inquiries</span>
                        <span class="text-2xl font-black text-slate-800 block mt-1 transition-colors duration-300 group-hover/stats:text-indigo-800">{{ $pendingInquiries }}</span>
                        <span class="text-xs font-medium text-slate-400 block mt-1">out of {{ $totalInquiries }} total</span>
                    </div>
                    <div class="group/stats bg-orange-50/70 border border-orange-200 p-3.5 rounded-xl text-center hover:bg-white hover:shadow-md hover:border-orange-300 transition-all duration-300">
                        <span class="text-[11px] font-bold text-orange-750 uppercase tracking-wider block">Pending Reassess</span>
                        <span class="text-2xl font-black text-slate-800 block mt-1 transition-colors duration-300 group-hover/stats:text-orange-800">{{ $pendingReassessments }}</span>
                        <span class="text-xs font-medium text-slate-400 block mt-1">requires review</span>
                    </div>
                </div>
            </div>
            
            <div class="pt-3.5 border-t border-slate-200 grid grid-cols-2 gap-3 mt-3.5">
                <a href="{{ route('facilitator.inquiries') }}" class="py-2.5 px-4 bg-red-700 hover:bg-red-800 active:bg-red-900 text-white font-extrabold text-xs uppercase tracking-wider text-center rounded-xl shadow-xs transition-all duration-205 hover:-translate-y-0.5">Manage Inquiries</a>
                <a href="{{ route('facilitator.reassessments') }}" class="py-2.5 px-4 bg-red-700 hover:bg-red-800 active:bg-red-900 text-white font-extrabold text-xs uppercase tracking-wider text-center rounded-xl shadow-xs transition-all duration-205 hover:-translate-y-0.5">Reassessments</a>
            </div>
        </div>

        <!-- Quick Administration Actions (50% Width) -->
        <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-200 pb-3 mb-3.5">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        Quick Actions
                    </h3>
                </div>
                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('facilitator.services.create') }}" class="group flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl hover:bg-red-50/20 hover:border-red-200 transition-all duration-200 text-xs font-bold text-slate-700 hover:text-red-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-550 group-hover:bg-red-100 group-hover:border-red-200 group-hover:text-red-700 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span>Create Assistance Service</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-1 group-hover:text-red-500 transition-all duration-200">&rarr;</span>
                    </a>
                    <a href="{{ route('facilitator.requirements') }}" class="group flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl hover:bg-red-50/20 hover:border-red-200 transition-all duration-200 text-xs font-bold text-slate-700 hover:text-red-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-550 group-hover:bg-red-100 group-hover:border-red-200 group-hover:text-red-700 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                </svg>
                            </div>
                            <span>Manage Requirements Checklist</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-1 group-hover:text-red-500 transition-all duration-200">&rarr;</span>
                    </a>
                    <a href="{{ route('facilitator.eligibility') }}" class="group flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl hover:bg-red-50/20 hover:border-red-200 transition-all duration-200 text-xs font-bold text-slate-700 hover:text-red-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-550 group-hover:bg-red-100 group-hover:border-red-200 group-hover:text-red-700 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span>Edit Eligibility Questions</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-1 group-hover:text-red-500 transition-all duration-200">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Recent Applications Table -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="pb-2 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Recent Applications
            </h3>
            <a href="{{ route('facilitator.applications') }}" class="text-xs font-bold text-red-700 hover:text-red-800 uppercase tracking-wider">View All →</a>
        </div>

        <div class="overflow-hidden bg-white border border-slate-200 rounded-xl shadow-2xs mt-2.5">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-650">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                            <th class="px-5 py-3">Citizen Name</th>
                            <th class="px-5 py-3">Assistance Service</th>
                            <th class="px-5 py-3">Submitted At</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentApplications as $app)
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-5 py-3 font-bold text-slate-850">{{ $app->user?->name ?? 'Citizen' }}</td>
                                <td class="px-5 py-3 text-slate-700">{{ $app->service?->name_en ?? 'Service' }}</td>
                                <td class="px-5 py-3 text-slate-450">{{ $app->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-3">
                                    @if($app->status === 'pending')
                                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200/50 uppercase tracking-wider">Pending</span>
                                    @elseif($app->status === 'approved')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200/50 uppercase tracking-wider">Approved</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200/50 uppercase tracking-wider">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('facilitator.applications.show', $app->id) }}" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-2.5 py-1 rounded-lg border border-red-100/60 transition-all">
                                        Process &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-center text-slate-400 text-xs italic">No recent applications</td>
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
            
            // Adjust button active state styling (table segmented style)
            statusBtn.classList.add('bg-white', 'text-slate-800', 'shadow-3xs');
            statusBtn.classList.remove('text-slate-500');
            programsBtn.classList.remove('bg-white', 'text-slate-800', 'shadow-3xs');
            programsBtn.classList.add('text-slate-500');
        } else {
            statusView.classList.add('hidden');
            programsView.classList.remove('hidden');
            
            // Adjust button active state styling (table segmented style)
            programsBtn.classList.add('bg-white', 'text-slate-800', 'shadow-3xs');
            programsBtn.classList.remove('text-slate-500');
            statusBtn.classList.remove('bg-white', 'text-slate-800', 'shadow-3xs');
            statusBtn.classList.add('text-slate-500');
        }
    }
</script>
@endsection
