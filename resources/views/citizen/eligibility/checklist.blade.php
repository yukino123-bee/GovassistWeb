@extends('layouts.citizen')

@php
    $serviceTrans = $service->translations->where('language_code', app()->getLocale())->first();
    $serviceName = $serviceTrans ? $serviceTrans->service_name : $service->service_name;
    $serviceDesc = $serviceTrans ? $serviceTrans->description : $service->description;
    $user = Auth::user();
    $profileCompleted = $user && $user->dob && $user->address && $user->civil_status && $user->contact_number && $user->valid_id_path;
@endphp

@section('title', __('messages.checklist_title'))

@section('header_title', __('messages.checklist_title'))

@section('back_button')
<a href="{{ route('citizen.eligibility') }}" class="text-white hover:text-red-100 p-2 transition-colors mr-2 flex items-center justify-center rounded-full hover:bg-white/10">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
    </svg>
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Description / Header -->
    <div class="bg-white border border-slate-200 p-6 shadow-sm space-y-2">
        <span class="text-[9px] font-bold uppercase tracking-widest text-red-700">Checklist Generator</span>
        <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ $serviceName }}</h2>
        <p class="text-xs text-slate-500 leading-relaxed">
            {{ __('messages.checklist_desc') }}
        </p>
    </div>

    <!-- Profile Incomplete Alert -->
    @if(!$profileCompleted)
        <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-900 text-xs shadow-sm space-y-2">
            <p class="font-extrabold uppercase tracking-widest text-[9px] text-amber-800">Incomplete Profile Details</p>
            <p class="leading-relaxed font-medium">You must complete your profile details (Date of Birth, Complete Address, Civil Status, Contact Number, and upload a Valid Government ID) before you can submit this application.</p>
            <a href="{{ route('citizen.profile.edit') }}" class="inline-block mt-1 bg-amber-600 hover:bg-amber-700 text-white text-[9px] font-extrabold uppercase tracking-widest px-3 py-1.5 transition-colors">
                Complete Profile Now
            </a>
        </div>
    @endif

    <!-- Alert status -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-2 border-emerald-500 text-emerald-800 text-xs font-semibold shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border-l-2 border-rose-500 text-rose-800 text-xs font-semibold shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Application Type Selection (for Employment Assistance only) -->
    @if(str_contains(strtolower($service->service_name), 'employment'))
        <div class="bg-white border border-slate-200 p-6 shadow-sm space-y-4">
            <span class="text-[9px] font-bold uppercase tracking-widest text-red-700">Application Options</span>
            <h3 class="text-sm font-bold text-slate-900">Application Type</h3>
            <form action="{{ route('citizen.eligibility.set_type', $service->id) }}" method="POST" id="app-type-form">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="border p-4 flex items-start space-x-3 cursor-pointer hover:bg-slate-50 transition-colors {{ ($checklist && $checklist->application_type === 'new') || !$checklist || !$checklist->application_type ? 'border-red-700 bg-red-50/5' : 'border-slate-200' }}">
                        <input type="radio" name="application_type" value="new" onchange="this.form.submit()" class="mt-0.5 text-red-700 focus:ring-red-500" {{ ($checklist && $checklist->application_type === 'new') || !$checklist || !$checklist->application_type ? 'checked' : '' }} {{ $alreadyApplied ? 'disabled' : '' }}>
                        <div>
                            <span class="block text-xs font-bold text-slate-800">New Employment Application</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">Applies for a new position. Requires PDS, Resume, Recommendation, and Endorsement.</span>
                        </div>
                    </label>
                    <label class="border p-4 flex items-start space-x-3 cursor-pointer hover:bg-slate-50 transition-colors {{ $checklist && $checklist->application_type === 'renewal' ? 'border-red-700 bg-red-50/5' : 'border-slate-200' }}">
                        <input type="radio" name="application_type" value="renewal" onchange="this.form.submit()" class="mt-0.5 text-red-700 focus:ring-red-500" {{ $checklist && $checklist->application_type === 'renewal' ? 'checked' : '' }} {{ $alreadyApplied ? 'disabled' : '' }}>
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Renewal of Employment</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">Applies for renewal. Requires PDS (Personal Data Sheet) only.</span>
                        </div>
                    </label>
                </div>
            </form>
        </div>
    @endif

    <!-- Requirements list -->
    <div class="space-y-4">
        @foreach($requirements as $req)
            <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-bold text-slate-900">{{ $req->text }}</h3>
                            @if($req->is_required)
                                <span class="px-2 py-0.5 bg-red-50 border border-red-200 text-red-700 text-[9px] font-bold uppercase tracking-wider">Required</span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-500 text-[9px] font-bold uppercase tracking-wider">Optional</span>
                            @endif
                        </div>
                    </div>

                    <!-- Upload Badge status -->
                    <div class="flex-shrink-0">
                        @if(isset($uploadedDocs[$req->id]) && $uploadedDocs[$req->id]->is_submitted)
                            <span class="px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[9px] font-bold uppercase tracking-wider">
                                Uploaded
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-800 text-[9px] font-bold uppercase tracking-wider">
                                Missing
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Form Upload -->
                @if(!$alreadyApplied)
                    <form action="{{ route('citizen.eligibility.upload', [$service->id, $req->id]) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row sm:items-center gap-2 pt-4 border-t border-slate-100">
                        @csrf
                        <div class="flex-grow">
                            <input type="file" name="document" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:border file:border-slate-200 file:text-[10px] file:font-bold file:uppercase file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 cursor-pointer" required>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                            Upload
                        </button>
                    </form>
                @else
                    @if(isset($uploadedDocs[$req->id]) && $uploadedDocs[$req->id]->is_submitted)
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-bold text-[10px] uppercase text-emerald-700 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Submitted
                            </span>
                            <a href="{{ asset('storage/' . $uploadedDocs[$req->id]->file_path) }}" target="_blank" class="text-red-700 font-bold uppercase text-[10px] tracking-wider hover:underline">
                                View File
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>

    <!-- Application Submit Action -->
    <div class="bg-slate-100 border border-slate-200 p-6 shadow-inner">
        @if($alreadyApplied)
            <div class="text-center py-2">
                <span class="inline-flex items-center px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold uppercase tracking-widest mb-3">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Application Status: {{ strtoupper($checklist->status ?? 'submitted') }}
                </span>
                <p class="text-xs text-slate-500">Your application has been received and is currently being processed by our facilitators.</p>
            </div>
        @else
            <form id="apply-form" action="{{ route('citizen.eligibility.apply', $service->id) }}" method="POST">
                @csrf
                
                @if(!$profileCompleted)
                    <button type="button" class="w-full py-3.5 bg-slate-200 text-slate-400 font-bold uppercase tracking-wider text-xs cursor-not-allowed rounded-none" disabled>
                        {{ __('messages.apply_now') }}
                    </button>
                    <p class="text-[10px] text-center text-rose-600 font-bold uppercase tracking-wider mt-3">
                        Please complete your profile details and upload a valid government ID to enable submission.
                    </p>
                @elseif($allMandatoryUploaded)
                    <button type="button" onclick="confirmApplySubmit()" class="w-full py-3.5 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-xs rounded-none">
                        {{ __('messages.apply_now') }}
                    </button>
                @else
                    <button type="button" class="w-full py-3.5 bg-slate-200 text-slate-400 font-bold uppercase tracking-wider text-xs cursor-not-allowed rounded-none" disabled>
                        {{ __('messages.apply_now') }}
                    </button>
                    <p class="text-[10px] text-center text-rose-600 font-bold uppercase tracking-wider mt-3">
                        {{ __('messages.apply_disabled') }}
                    </p>
                @endif
            </form>
        @endif
    </div>

</div>

<script>
    function confirmApplySubmit() {
        showConfirmModal('{{ __('messages.confirm_submit_application') }}', () => {
            document.getElementById('apply-form').submit();
        }, 'Submit Application');
    }
</script>
@endsection
