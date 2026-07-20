@extends('layouts.facilitator')

@section('title', 'View Eligibility Calculation - GovAssist')

@section('page_title', 'Eligibility Calculation Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Calculation Overview
            </h3>
            <a href="{{ route('facilitator.assessments') }}" class="text-[10px] font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest">
                &larr; Back to Log
            </a>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50">
            <div class="space-y-4">
                <div>
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Citizen Name</span>
                    <span class="text-sm font-bold text-slate-800">{{ $assessment->user->name ?? 'Citizen' }}</span>
                    <span class="text-xs text-slate-500 block">{{ $assessment->user->email ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Service Program</span>
                    <span class="text-sm font-bold text-slate-800">{{ $assessment->service->name_en ?? 'Service' }}</span>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Calculated Status</span>
                    @if($assessment->status === 'eligible')
                        <span class="mt-1 inline-block px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-none border border-emerald-200 uppercase tracking-wider">
                            Eligible
                        </span>
                    @else
                        <span class="mt-1 inline-block px-2.5 py-1 bg-rose-50 text-rose-700 text-[10px] font-extrabold rounded-none border border-rose-200 uppercase tracking-wider">
                            Ineligible
                        </span>
                    @endif
                </div>
                <div>
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Date Assessed</span>
                    <span class="text-sm font-bold text-slate-800">{{ $assessment->created_at->format('M d, Y h:i A') }}</span>
                    <span class="text-xs text-slate-400 block">({{ $assessment->created_at->diffForHumans() }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Answers Details Card -->
    <div class="bg-white border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Citizen's Assessment Answers
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/60">
                        <th class="px-6 py-3">Question</th>
                        <th class="px-6 py-3 w-48">Submitted Answer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assessment->answers as $ans)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block text-xs leading-relaxed">{{ $ans->question }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($ans->answer === 'true' || $ans->answer === 'Yes')
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200 uppercase tracking-wider rounded-none">Yes</span>
                                @elseif($ans->answer === 'false' || $ans->answer === 'No')
                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200 uppercase tracking-wider rounded-none">No</span>
                                @else
                                    <span class="font-mono bg-slate-50 border border-slate-100 px-2 py-1 text-slate-700 text-xs rounded-none">{{ $ans->answer }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-slate-400 italic">No answers recorded for this assessment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
