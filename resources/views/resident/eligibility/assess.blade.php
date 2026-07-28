@extends('layouts.resident')

@php
    $serviceTrans = $service->translations->where('language_code', app()->getLocale())->first();
    $serviceName = $serviceTrans ? $serviceTrans->service_name : $service->service_name;
@endphp

@section('title', 'Assess: ' . $serviceName)

@section('header_title', 'Assess Service')

@section('back_button')
<a href="{{ route('resident.eligibility') }}" class="text-white hover:text-red-100 p-2 transition-colors mr-2 flex items-center justify-center rounded-full hover:bg-white/10">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
    </svg>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Service Card -->
    <div class="bg-white border-l-4 border-l-red-600 border-t border-r border-b border-slate-200 p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">{{ $serviceName }}</h2>
        <p class="text-xs text-slate-500 mt-1.5">Please answer all questions below honestly to check your qualification status.</p>
    </div>

    <!-- Questions Form -->
    <form id="assess-form" action="{{ route('resident.eligibility.assess.submit', $service->id) }}" method="POST" class="space-y-4">
        @csrf

        @foreach($questions as $q)
            <div class="bg-white border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <label class="block text-sm font-bold text-slate-800 tracking-tight">
                        {{ $q->question_text }}
                    </label>
                    @unless($q->is_required)
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-extrabold uppercase tracking-wider">
                            Optional
                        </span>
                    @endunless
                </div>

                @if($q->type === 'boolean')
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center cursor-pointer text-sm font-semibold text-slate-800 hover:text-red-700 transition-colors">
                            <input type="radio" name="question_{{ $q->id }}" value="true" class="w-4 h-4 text-red-700 border-slate-300 focus:ring-red-600 mr-2 rounded-none" {{ $q->is_required ? 'required' : '' }}>
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center cursor-pointer text-sm font-semibold text-slate-800 hover:text-red-700 transition-colors">
                            <input type="radio" name="question_{{ $q->id }}" value="false" class="w-4 h-4 text-red-700 border-slate-300 focus:ring-red-600 mr-2 rounded-none" {{ $q->is_required ? 'required' : '' }}>
                            <span>No</span>
                        </label>
                    </div>
                @elseif($q->type === 'number')
                    @php
                        $isHouseholdIncome = Illuminate\Support\Str::of($q->question_text_en)
                            ->lower()
                            ->containsAll(['household', 'income']);
                    @endphp
                    <input
                        type="number"
                        name="question_{{ $q->id }}"
                        placeholder="{{ $isHouseholdIncome ? 'Enter an amount from 2,000 to 15,000' : 'Enter numeric value...' }}"
                        @if($isHouseholdIncome) min="2000" max="15000" @endif
                        class="w-full px-4 py-3 bg-white border border-slate-200 focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800 rounded-none shadow-sm"
                        {{ $q->is_required ? 'required' : '' }}
                    >
                    @if($isHouseholdIncome)
                        <p class="text-[11px] font-semibold text-slate-500">Valid household income range: ₱2,000–₱15,000.</p>
                    @endif
                @elseif($q->type === 'text')
                    <textarea name="question_{{ $q->id }}" rows="3" placeholder="Enter your response..." class="w-full px-4 py-3 bg-white border border-slate-200 focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800 rounded-none shadow-sm" {{ $q->is_required ? 'required' : '' }}></textarea>
                @endif
            </div>
        @endforeach

        <button type="button" onclick="confirmAssessSubmit()" class="w-full py-4 bg-red-700 hover:bg-red-800 text-white font-extrabold uppercase tracking-widest transition-all text-xs shadow-sm rounded-none">
            {{ __('messages.submit_assessment') }}
        </button>
    </form>

</div>

<script>
    function confirmAssessSubmit() {
        const form = document.getElementById('assess-form');
        if (form.checkValidity()) {
            showConfirmModal('{{ __('messages.confirm_submit_assessment') }}', () => {
                form.submit();
            }, 'Submit Assessment');
        } else {
            form.reportValidity();
        }
    }
</script>
@endsection
