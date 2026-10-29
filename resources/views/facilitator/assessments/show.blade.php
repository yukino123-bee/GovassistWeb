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
            @if($assessment->status === 'pending')
                <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200 uppercase tracking-wider">
                    Pending Review
                </span>
            @elseif($assessment->status === 'eligible')
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

    <!-- Answers Review Card -->
    <form method="POST" action="{{ route('facilitator.assessments.decide', $assessment) }}" class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm space-y-4">
        @csrf
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Review Resident's Responses
            </h3>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">
                {{ $assessment->answers->count() }} Answer(s) Recorded
            </span>
        </div>

        <p class="text-xs text-slate-500">Flag each response as eligible or not eligible, then confirm the final result. The score is a guide only; you make the decision. The resident is notified by SMS.</p>

        @php $passing = $assessment->service->passing_percentage ?? 100; $minimum = $assessment->service->minimum_percentage ?? 0; @endphp
        <div id="score-box" data-passing="{{ $passing }}" data-minimum="{{ $minimum }}" class="border border-slate-200 bg-slate-50 rounded-md px-4 py-3 flex items-center justify-between text-xs">
            <span class="font-bold text-slate-600">Score (required questions): <span id="score-value" class="text-slate-900 text-sm">{{ $assessment->scorePercentage() }}%</span> <span class="text-slate-400">/ passing {{ $passing }}% · minimum {{ $minimum }}%</span></span>
            <span id="score-verdict" class="font-extrabold uppercase tracking-wider"></span>
        </div>
        @error('decision')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror

        @error('flags')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
        @error('flags.*')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror

        <div class="divide-y divide-slate-100 border border-slate-200 rounded-md">
            @forelse($assessment->answers as $ans)
                @php $current = old('flags.'.$ans->id, is_null($ans->is_eligible) ? null : ($ans->is_eligible ? 'eligible' : 'ineligible')); @endphp
                <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" data-answer-row data-required="{{ $ans->is_required ? 1 : 0 }}">
                    <div class="min-w-0">
                        <span class="font-bold text-slate-850 block text-xs leading-relaxed">{{ $ans->question }}</span>
                        <span class="inline-block mt-1.5 font-mono bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-slate-800 font-bold text-xs rounded-lg">
                            {{ in_array($ans->answer, ['true', 'Yes'], true) ? 'Yes' : (in_array($ans->answer, ['false', 'No'], true) ? 'No' : $ans->answer) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 shrink-0 text-xs font-bold">
                        <label class="flex items-center gap-1.5 text-emerald-700 cursor-pointer">
                            <input type="radio" name="flags[{{ $ans->id }}]" value="eligible" class="text-emerald-600" @checked($current === 'eligible') required> Eligible
                        </label>
                        <label class="flex items-center gap-1.5 text-rose-700 cursor-pointer">
                            <input type="radio" name="flags[{{ $ans->id }}]" value="ineligible" class="text-rose-600" @checked($current === 'ineligible') required> Not Eligible
                        </label>
                    </div>
                </div>
            @empty
                <div class="px-5 py-6 text-center text-slate-400 text-xs italic">No answers recorded for this assessment.</div>
            @endforelse
        </div>

        @if($assessment->answers->isNotEmpty())
            <div class="flex items-center gap-3">
                <button type="submit" id="confirm-eligible" name="decision" value="eligible" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold uppercase tracking-wider rounded-md">Confirm Eligible</button>
                <button type="submit" name="decision" value="ineligible" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold uppercase tracking-wider rounded-md">Confirm Not Eligible</button>
            </div>
        @endif
    </form>

    <script>
        (function () {
            const box = document.getElementById('score-box');
            if (!box) return;
            const passing = Number(box.dataset.passing);
            const minimum = Number(box.dataset.minimum);
            const rows = [...document.querySelectorAll('[data-answer-row]')];
            const update = () => {
                let scored = rows.filter(r => r.dataset.required === '1');
                if (!scored.length) scored = rows;
                const ok = scored.filter(r => r.querySelector('input[value="eligible"]:checked')).length;
                const pct = scored.length ? Math.round(ok / scored.length * 100) : 0;
                const flagged = rows.every(r => r.querySelector('input:checked'));
                document.getElementById('score-value').textContent = pct + '%';
                const verdict = document.getElementById('score-verdict');
                const state = !flagged ? 'pending' : (pct >= passing ? 'pass' : (pct >= minimum ? 'review' : 'fail'));
                const labels = {pending: 'Flag all responses', pass: 'Meets passing %', review: 'Your call: between minimum and passing', fail: 'Below minimum: cannot be eligible'};
                const colors = {pending: 'text-slate-400', pass: 'text-emerald-700', review: 'text-amber-600', fail: 'text-rose-700'};
                verdict.textContent = labels[state];
                verdict.className = 'font-extrabold uppercase tracking-wider ' + colors[state];
                const btn = document.getElementById('confirm-eligible');
                btn.disabled = state === 'fail';
                btn.classList.toggle('opacity-40', state === 'fail');
                btn.classList.toggle('cursor-not-allowed', state === 'fail');
            };
            rows.forEach(r => r.addEventListener('change', update));
            update();
        })();
    </script>

</div>
@endsection
