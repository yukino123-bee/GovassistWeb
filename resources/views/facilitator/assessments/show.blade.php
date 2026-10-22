@extends('layouts.facilitator')

@section('title', 'View Eligibility Calculation - GovAssist')

@section('page_title', 'Eligibility Calculation Details')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    <!-- Top Navigation -->
    <div>
        <a href="{{ route('facilitator.assessments') }}" class="text-xs font-bold text-slate-500 hover:text-red-700 transition-colors inline-flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Assessments Log</span>
        </a>
    </div>

    <!-- Overview Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Calculation Overview
            </h3>
            @if($assessment->status === 'eligible')
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200 uppercase tracking-wider">
                    Eligible
                </span>
            @else
                <span class="px-3 py-1 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200 uppercase tracking-wider">
                    Ineligible
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 text-xs">
            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Resident Name</span>
                <span class="font-bold text-slate-850 text-sm mt-0.5 block">{{ $assessment->user->name ?? 'Resident' }}</span>
                <span class="text-xs text-slate-500 font-mono block mt-0.5">{{ $assessment->user->email ?? 'N/A' }}</span>
            </div>

            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Service Program</span>
                <span class="font-bold text-slate-850 text-sm mt-0.5 block">{{ $assessment->service->name_en ?? 'Service' }}</span>
                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ $assessment->service->name_ceb ?? '' }}</span>
            </div>

            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Date Calculated</span>
                <span class="font-bold text-slate-850 block mt-0.5">{{ $assessment->created_at->format('M d, Y h:i A') }}</span>
                <span class="text-[10px] text-slate-400 font-medium block mt-0.5">({{ $assessment->created_at->diffForHumans() }})</span>
            </div>
        </div>
    </div>

    <!-- Answers Details Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Resident's Assessment Answers
            </h3>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">
                {{ $assessment->answers->count() }} Answer(s) Recorded
            </span>
        </div>

        <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-650">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                            <th class="px-5 py-3">Criterion Question</th>
                            <th class="px-5 py-3 w-48 text-right">Submitted Answer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assessment->answers as $ans)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-850 block text-xs leading-relaxed">{{ $ans->question }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    @if($ans->answer === 'true' || $ans->answer === 'Yes')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200 uppercase tracking-wider rounded-full">Yes</span>
                                    @elseif($ans->answer === 'false' || $ans->answer === 'No')
                                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-extrabold border border-rose-200 uppercase tracking-wider rounded-full">No</span>
                                    @else
                                        <span class="font-mono bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-slate-800 font-bold text-xs rounded-lg">{{ $ans->answer }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-6 text-center text-slate-400 text-xs italic">No answers recorded for this assessment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
