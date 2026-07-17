@extends('layouts.citizen')

@php
    $serviceTrans = $assessment->service->translations->where('language_code', app()->getLocale())->first();
    $serviceName = $serviceTrans ? $serviceTrans->service_name : $assessment->service->service_name;
@endphp

@section('title', __('messages.assessment_result'))

@section('header_title', __('messages.assessment_result'))

@section('back_button')
<a href="{{ route('citizen.eligibility') }}" class="text-white hover:text-red-100 p-2 transition-colors mr-2 flex items-center justify-center rounded-full hover:bg-white/10">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
    </svg>
</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white border border-slate-200 p-8 shadow-sm text-center space-y-6">
        
        @if($assessment->status === 'eligible')
            <!-- Success Icon -->
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center border border-emerald-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <div class="space-y-2">
                <span class="text-[9px] font-bold text-red-700 uppercase tracking-widest block">REF-ASST-{{ $assessment->id }}</span>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ $serviceName }}</h2>
                <p class="text-sm font-bold text-emerald-700 uppercase tracking-wider">{{ __('messages.congrats_eligible') }}</p>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed pt-2">
                    You meet the criteria for this program. You can now generate your document checklist and submit your official application file.
                </p>
            </div>

            <div class="pt-4 space-y-3">
                <a href="{{ route('citizen.eligibility.checklist', $assessment->service->id) }}" class="w-full block py-3.5 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-xs">
                    {{ __('messages.checklist_btn') }}
                </a>
                <a href="{{ route('citizen.eligibility') }}" class="w-full block py-3 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-[10px] uppercase tracking-wider">
                    {{ __('messages.back_to_eligibility') }}
                </a>
            </div>
        @else
            <!-- Failure Icon -->
            <div class="w-16 h-16 bg-rose-50 text-rose-600 mx-auto flex items-center justify-center border border-rose-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            
            <div class="space-y-2">
                <span class="text-[9px] font-bold text-red-700 uppercase tracking-widest block">REF-ASST-{{ $assessment->id }}</span>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ $serviceName }}</h2>
                <p class="text-sm font-bold text-rose-700 uppercase tracking-wider">{{ __('messages.sorry_ineligible') }}</p>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed pt-2">
                    You do not meet the criteria for this program at this time. Please review the requirements or contact a facilitator.
                </p>
            </div>

            <div class="pt-4">
                <a href="{{ route('citizen.eligibility') }}" class="w-full block py-3.5 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-xs">
                    {{ __('messages.back_to_eligibility') }}
                </a>
            </div>
        @endif

    </div>

</div>
@endsection
