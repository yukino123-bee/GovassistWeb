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

                            <div class="mt-4 pt-3.5 border-t border-slate-100 flex justify-between items-center">
                                @if(isset($reassessmentRequests) && isset($reassessmentRequests[$assess->service->id]))
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 flex items-center space-x-1 bg-amber-50 px-2 py-1 border border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Reassessment Requested</span>
                                    </span>
                                @else
                                    <button type="button" onclick="openReassessmentModal({{ $assess->service->id }}, '{{ addslashes($assessName) }}')" class="text-[10px] font-bold uppercase tracking-wider text-slate-500 hover:text-slate-800 flex items-center space-x-1 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        <span>Request Reassessment</span>
                                    </button>
                                @endif

                                @if($assess->status === 'eligible')
                                    <a href="{{ route('citizen.eligibility.checklist', $assess->service->id) }}" class="text-[10px] font-bold uppercase tracking-wider text-red-700 hover:text-red-900 flex items-center space-x-1">
                                        <span>{{ __('messages.checklist_btn') }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Reassessment Request Modal -->
<div id="reassessmentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border border-slate-200 shadow-xl w-full max-w-md transform scale-95 transition-transform duration-300" id="reassessmentModalContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Request Reassessment
            </h3>
            <button type="button" onclick="closeReassessmentModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="reassessmentForm" method="POST" action="">
            @csrf
            <div class="p-6 space-y-4">
                <p class="text-xs text-slate-600">You are requesting a reassessment for <strong id="reassessmentServiceName" class="text-slate-900"></strong>. Please provide a valid reason why you need to retake the eligibility assessment.</p>
                <div class="space-y-1.5">
                    <label for="reason" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Reason</label>
                    <textarea name="reason" id="reason" rows="3" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required placeholder="e.g. My circumstances have changed since my last assessment..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end space-x-3">
                <button type="button" onclick="closeReassessmentModal()" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-none font-extrabold text-[10px] uppercase tracking-widest transition-all">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-none font-extrabold text-[10px] uppercase tracking-widest transition-all">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReassessmentModal(serviceId, serviceName) {
        document.getElementById('reassessmentServiceName').innerText = serviceName;
        document.getElementById('reassessmentForm').action = `/citizen/eligibility/reassess/${serviceId}`;
        
        const modal = document.getElementById('reassessmentModal');
        const modalContent = document.getElementById('reassessmentModalContent');
        
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }

    function closeReassessmentModal() {
        const modal = document.getElementById('reassessmentModal');
        const modalContent = document.getElementById('reassessmentModalContent');
        
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        modal.classList.add('opacity-0', 'pointer-events-none');
        
        setTimeout(() => {
            document.getElementById('reassessmentForm').reset();
        }, 300);
    }
</script>
@endsection
