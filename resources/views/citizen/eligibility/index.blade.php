@extends('layouts.citizen')

@section('title', __('messages.eligibility_title'))

@section('header_title', __('messages.eligibility_title'))

@section('content')
<div class="space-y-8">

    <!-- Description Card -->
    <div class="bg-white border border-slate-200 p-5 shadow-sm">
        <p class="text-xs text-slate-500 leading-relaxed">
            {{ __('messages.eligibility_desc') }}
        </p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Assess Services -->
        <div class="lg:col-span-2 space-y-4">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                    {{ __('messages.assess_services') }}
                </h3>
            </div>
            
            <div class="grid grid-cols-1 {{ $services->count() > 1 ? 'md:grid-cols-2' : '' }} gap-4">
                @foreach($services as $service)
                    @php
                        $serviceTrans = $service->translations->where('language_code', app()->getLocale())->first();
                        $serviceName = $serviceTrans ? $serviceTrans->service_name : $service->service_name;
                    @endphp
                    <div class="bg-white border-l-4 border-l-red-600 border-t border-r border-b border-slate-200 p-6 flex flex-col justify-between hover:border-red-600 hover:shadow-md transition-all duration-300 shadow-sm">
                        <div class="mb-4">
                            <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Government Program</span>
                            <h4 class="text-sm font-extrabold text-slate-900 mt-1.5">{{ $serviceName }}</h4>
                        </div>
                        
                        <div>
                            <a href="{{ route('citizen.eligibility.assess', $service->id) }}" class="inline-block w-full text-center px-4 py-2.5 bg-red-700 hover:bg-red-800 transition-colors text-white text-[10px] font-extrabold uppercase tracking-widest">
                                {{ __('messages.start_assessment') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Assessment History -->
        <div class="space-y-4">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                    {{ __('messages.assessment_history') }}
                </h3>
            </div>
            
            @if($assessments->isEmpty())
                <div class="bg-white border border-slate-200 p-6 text-center text-slate-400 text-xs font-semibold uppercase tracking-wider shadow-sm">
                    No past assessments found.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($assessments as $assess)
                        @php
                            $assessTrans = $assess->service->translations->where('language_code', app()->getLocale())->first();
                            $assessName = $assessTrans ? $assessTrans->service_name : $assess->service->service_name;
                        @endphp
                        <div class="bg-white border border-slate-200 p-5 shadow-sm relative">
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                @if($assess->status === 'eligible')
                                    <span class="px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[9px] font-bold uppercase tracking-wider">
                                        {{ __('messages.eligible') }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-rose-50 border border-rose-200 text-rose-800 text-[9px] font-bold uppercase tracking-wider">
                                        {{ __('messages.ineligible') }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <span class="text-[9px] font-bold text-red-700 block uppercase tracking-wider">REF-ASST-{{ $assess->id }}</span>
                                <h4 class="text-xs font-bold text-slate-900 mt-1 max-w-[70%]">{{ $assessName }}</h4>
                                <span class="text-[10px] text-slate-400 block mt-2">Assessed: {{ $assess->created_at->format('M d, Y h:i A') }}</span>
                            </div>

                            @if($assess->status === 'eligible')
                                <div class="mt-4 pt-3.5 border-t border-slate-100 flex justify-end">
                                    <a href="{{ route('citizen.eligibility.checklist', $assess->service->id) }}" class="text-[10px] font-bold uppercase tracking-wider text-red-700 hover:text-red-900 flex items-center space-x-1">
                                        <span>{{ __('messages.checklist_btn') }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
