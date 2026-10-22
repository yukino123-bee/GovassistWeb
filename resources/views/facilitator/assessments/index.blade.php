@extends('layouts.facilitator')

@section('title', 'Eligibility Assessments History - GovAssist')

@section('page_title', 'Resident Eligibility Calculations')

@section('content')
<div class="space-y-5">

    @php
        $totalAssessments = $stats['total'];
        $eligibleCount = $stats['eligible'];
        $ineligibleCount = $stats['ineligible'];
        $passRate = $totalAssessments > 0 ? round(($eligibleCount / $totalAssessments) * 100) : 0;
    @endphp

    <!-- Overview Metrics Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Total Assessments -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Assessments</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalAssessments }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>

        <!-- Eligible Results -->
        <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Eligible Results</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $eligibleCount }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Ineligible Results -->
        <div class="group bg-rose-50/70 border border-rose-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-rose-300">
            <div>
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block leading-tight">Ineligible Results</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-rose-700 transition-colors">{{ $ineligibleCount }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Pass Rate -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Eligibility Pass Rate</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $passRate }}%</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Section Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <!-- Section Header with Red Square Indicator -->
        <div class="border-b border-slate-200 pb-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Historical Assessment Calculations Log
            </h3>

            <!-- Search Filter -->
            <form method="GET" action="{{ route('facilitator.assessments') }}" class="relative">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search assessments..." class="text-xs bg-slate-50 border border-slate-200 rounded-md pl-8 pr-3 py-1.5 font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-red-600 transition-all w-52">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>

        <!-- Table Container -->
        <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-650" id="assessments-table">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                            <th class="px-5 py-3">Resident Name</th>
                            <th class="px-5 py-3">Service Program</th>
                            <th class="px-5 py-3">Calculated Status</th>
                            <th class="px-5 py-3">Date Assessed</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assessments as $assess)
                            <tr class="assess-row hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($assess->user->name ?? 'R', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-850 text-xs block assess-resident">{{ $assess->user->name ?? 'Resident' }}</span>
                                            <span class="text-xs text-slate-400 font-mono block">{{ $assess->user->email ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-slate-700 text-xs assess-service">
                                    {{ $assess->service->name_en ?? 'Service' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($assess->status === 'eligible')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200 uppercase tracking-wider">
                                            Eligible
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200 uppercase tracking-wider">
                                            Ineligible
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-450 text-xs whitespace-nowrap">
                                    <span class="font-bold text-slate-700 block">{{ $assess->created_at->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">{{ $assess->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <a href="{{ route('facilitator.assessments.show', $assess) }}" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-2.5 py-1 rounded-lg border border-red-100 transition-all shadow-3xs">
                                            View
                                        </a>
                                        <form action="{{ route('facilitator.assessments.destroy', $assess) }}" method="POST" class="inline" id="delete-assess-{{ $assess->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="showConfirmModal('Are you sure you want to delete this assessment record?', () => document.getElementById('delete-assess-{{ $assess->id }}').submit(), 'Delete Assessment')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Delete Assessment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-xs italic">No resident assessments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($assessments->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                <span>Showing {{ $assessments->firstItem() }}&ndash;{{ $assessments->lastItem() }} of {{ $assessments->total() }}</span>
                {{ $assessments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
