@extends('layouts.facilitator')

@section('title', 'Export Reports - GovAssist')

@section('page_title', 'Reports & Data Export Center')

@section('content')
<div class="space-y-6">

    <!-- Top Hero Banner Card with Gradient Accent -->
    <div class="relative overflow-hidden bg-gradient-to-r from-red-800 via-red-700 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-red-900/40">
        <!-- Background Decorative Pattern -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-red-500/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="space-y-1.5 max-w-2xl">
                <div class="flex items-center space-x-2.5">
                    <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-400/30 flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span>
                        <span>Excel Spreadsheet (.xls) Export Active</span>
                    </span>
                    <span class="px-2 py-0.5 bg-white/10 text-white/80 text-[10px] font-extrabold uppercase tracking-wider rounded-full">
                        GovAssist SSFO
                    </span>
                </div>
                <h2 class="text-lg md:text-xl font-extrabold tracking-tight text-white">
                    Official System Reports & Analytics Center
                </h2>
                <p class="text-xs text-red-100/90 leading-relaxed font-medium">
                    Generate and download comprehensive Excel spreadsheets for residents registry data, financial assistance applications, and eligibility assessment logs.
                </p>
            </div>

            <div class="flex items-center space-x-3 shrink-0">
                <a href="{{ route('facilitator.reports.export.all') }}" class="inline-flex items-center justify-center space-x-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-widest rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-emerald-400/30 group">
                    <svg class="w-4 h-4 text-emerald-100 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export Master Excel Summary</span>
                </a>
            </div>
        </div>
    </div>

    <!-- System Summary Metrics Bar (3 Key Indicators) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Applications -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-3xs hover:border-red-200 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Applications</span>
                <span class="text-xl font-black text-slate-800 block mt-0.5">{{ number_format($totalApplications) }}</span>
                <div class="flex items-center space-x-1.5 mt-1">
                    <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 text-[9px] font-extrabold rounded">{{ $pendingApplications }} Pending</span>
                    <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-extrabold rounded">{{ $approvedApplications }} Approved</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-700 border border-red-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- 2. Residents -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-3xs hover:border-blue-200 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Residents Directory</span>
                <span class="text-xl font-black text-slate-800 block mt-0.5">{{ number_format($totalResidents) }}</span>
                <span class="text-[9px] text-emerald-600 font-extrabold block mt-1">✓ {{ $verifiedResidents }} ID Verified</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- 3. Assessments -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-3xs hover:border-purple-200 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Eligibility Calculations</span>
                <span class="text-xl font-black text-slate-800 block mt-0.5">{{ number_format($totalAssessments) }}</span>
                <span class="text-[9px] text-purple-600 font-extrabold block mt-1">⚡ {{ $eligibleAssessments }} Qualified</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 border border-purple-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Export Reports Grid (3 Modular Export Cards) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Module 1: Assistance Applications Report -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 space-y-5 flex flex-col justify-between hover:shadow-md hover:border-red-200 transition-all duration-200 group">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-red-50 text-red-700 border border-red-100 flex items-center justify-center shadow-3xs group-hover:bg-red-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Assistance Applications</h3>
                            <p class="text-[10px] text-slate-400 font-extrabold">{{ number_format($totalApplications) }} Total Records</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Exports complete application records including applicant details, government program, status (Approved / Rejected / Pending), submission timestamp, and staff notes.
                </p>
            </div>

            <form action="{{ route('facilitator.reports.export.applications') }}" method="GET" class="space-y-4 pt-2">
                <div class="grid grid-cols-1 gap-2 text-xs">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Application Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs font-semibold text-slate-700">
                            <option value="">All Application Statuses</option>
                            <option value="pending">Pending Review Only</option>
                            <option value="approved">Approved Only</option>
                            <option value="rejected">Rejected Only</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Assistance Program</label>
                        <select name="service_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs font-semibold text-slate-700">
                            <option value="">All Assistance Programs</option>
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}">{{ $svc->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center space-x-2 shadow-xs group-hover:shadow-md cursor-pointer">
                    <svg class="w-4.5 h-4.5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download Applications Excel (.xls)</span>
                </button>
            </form>
        </div>

        <!-- Module 2: Residents Registry Directory -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 space-y-5 flex flex-col justify-between hover:shadow-md hover:border-blue-200 transition-all duration-200 group">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center shadow-3xs group-hover:bg-blue-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Residents Registry</h3>
                            <p class="text-[10px] text-slate-400 font-extrabold">{{ number_format($totalResidents) }} Registered Residents</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Exports complete resident demographic database including full legal name, email, mobile contact, complete residential address, civil status, birthdate, and ID status.
                </p>
            </div>

            <form action="{{ route('facilitator.reports.export.residents') }}" method="GET" class="space-y-4 pt-2">
                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Valid ID Verification Status</label>
                    <select name="verified" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs font-semibold text-slate-700">
                        <option value="">All Registered Residents</option>
                        <option value="yes">ID Uploaded & Verified Only</option>
                        <option value="no">Pending ID Verification Only</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center space-x-2 shadow-xs group-hover:shadow-md cursor-pointer">
                    <svg class="w-4.5 h-4.5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download Residents Excel (.xls)</span>
                </button>
            </form>
        </div>

        <!-- Module 3: Eligibility Calculations Report -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 space-y-5 flex flex-col justify-between hover:shadow-md hover:border-purple-200 transition-all duration-200 group">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-700 border border-purple-100 flex items-center justify-center shadow-3xs group-hover:bg-purple-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Eligibility Calculations</h3>
                            <p class="text-[10px] text-slate-400 font-extrabold">{{ number_format($totalAssessments) }} Automated Evaluations</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Exports program qualification audit logs, evaluation outcomes (Eligible vs Ineligible), targeted assistance program names, and calculation dates.
                </p>
            </div>

            <form action="{{ route('facilitator.reports.export.assessments') }}" method="GET" class="space-y-4 pt-2">
                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Evaluation Outcome Filter</label>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs font-semibold text-slate-700">
                        <option value="">All Assessment Outcomes</option>
                        <option value="eligible">Eligible Residents Only</option>
                        <option value="ineligible">Ineligible Residents Only</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center space-x-2 shadow-xs group-hover:shadow-md cursor-pointer">
                    <svg class="w-4.5 h-4.5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download Eligibility Excel (.xls)</span>
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
