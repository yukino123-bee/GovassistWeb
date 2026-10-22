@extends('layouts.resident')

@php
    $serviceTrans = $service->translations->where('language_code', app()->getLocale())->first();
    $serviceName = $serviceTrans ? $serviceTrans->service_name : $service->service_name;
    $serviceDesc = $serviceTrans ? $serviceTrans->description : $service->description;
    $user = Auth::user();
    $missingProfileFields = $user->missingProfileFields();
    $profileCompleted = $missingProfileFields === [];
    $requiredRequirements = $requirements->where('is_required', true);
    $readyRequired = $requiredRequirements->filter(function ($requirement) use ($uploadedDocs) {
        $item = $uploadedDocs[$requirement->id] ?? null;

        return $item && $item->is_submitted && $item->file_path && $item->status !== 'rejected';
    })->count();
    $requiredTotal = $requiredRequirements->count();
@endphp

@section('title', __('messages.checklist_title'))

@section('header_title', __('messages.checklist_title'))

@section('back_button')
<a href="{{ route('resident.eligibility') }}" class="text-white hover:text-red-100 p-2 transition-colors mr-2 flex items-center justify-center rounded-full hover:bg-white/10">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
    </svg>
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Description / Header -->
    <div class="bg-white border border-slate-200 p-6 shadow-sm space-y-2">
        <span class="text-[9px] font-bold uppercase tracking-widest text-red-700">{{ __('messages.checklist_generator') }}</span>
        <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ $serviceName }}</h2>
        <p class="text-xs text-slate-500 leading-relaxed">
            {{ __('messages.checklist_desc') }}
        </p>
    </div>

    <!-- Profile Incomplete Alert -->
    @if(!$profileCompleted)
        <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-900 text-xs shadow-sm space-y-2">
            <p class="font-extrabold uppercase tracking-widest text-[9px] text-amber-800">{{ __('messages.profile_gate_title') }}</p>
            <p class="leading-relaxed font-medium">{{ __('messages.profile_gate_desc') }}</p>
            <ul class="flex flex-wrap gap-1.5">
                @foreach($missingProfileFields as $labelKey)
                    <li class="px-2 py-0.5 bg-white border border-amber-200 text-[10px] font-bold text-amber-800 uppercase tracking-wider">{{ __('messages.'.$labelKey) }}</li>
                @endforeach
            </ul>
            <a href="{{ route('resident.profile.edit') }}" class="inline-block mt-1 bg-amber-600 hover:bg-amber-700 text-white text-[9px] font-extrabold uppercase tracking-widest px-3 py-1.5 transition-colors">
                {{ __('messages.complete_profile_btn') }}
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
            <span class="text-[9px] font-bold uppercase tracking-widest text-red-700">{{ __('messages.app_options') }}</span>
            <h3 class="text-sm font-bold text-slate-900">{{ __('messages.app_type') }}</h3>
            <form action="{{ route('resident.eligibility.set_type', $service->id) }}" method="POST" id="app-type-form">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="border p-4 flex items-start space-x-3 cursor-pointer hover:bg-slate-50 transition-colors {{ ($checklist && $checklist->application_type === 'new') || !$checklist || !$checklist->application_type ? 'border-red-700 bg-red-50/5' : 'border-slate-200' }}">
                        <input type="radio" name="application_type" value="new" onchange="this.form.submit()" class="mt-0.5 text-red-700 focus:ring-red-500" {{ ($checklist && $checklist->application_type === 'new') || !$checklist || !$checklist->application_type ? 'checked' : '' }} {{ $alreadyApplied ? 'disabled' : '' }}>
                        <div>
                            <span class="block text-xs font-bold text-slate-800">{{ __('messages.app_type_new') }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ __('messages.app_type_new_desc') }}</span>
                        </div>
                    </label>
                    <label class="border p-4 flex items-start space-x-3 cursor-pointer hover:bg-slate-50 transition-colors {{ $checklist && $checklist->application_type === 'renewal' ? 'border-red-700 bg-red-50/5' : 'border-slate-200' }}">
                        <input type="radio" name="application_type" value="renewal" onchange="this.form.submit()" class="mt-0.5 text-red-700 focus:ring-red-500" {{ $checklist && $checklist->application_type === 'renewal' ? 'checked' : '' }} {{ $alreadyApplied ? 'disabled' : '' }}>
                        <div>
                            <span class="block text-xs font-bold text-slate-800">{{ __('messages.app_type_renewal') }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ __('messages.app_type_renewal_desc') }}</span>
                        </div>
                    </label>
                </div>
            </form>
        </div>
    @endif

    <!-- Progress -->
    @if(! $alreadyApplied && $requiredTotal > 0)
        <div class="bg-white border border-slate-200 p-4 shadow-sm" role="status">
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <span>{{ $readyRequired }} / {{ $requiredTotal }} {{ __('messages.progress_label') }}</span>
                <span class="{{ $readyRequired === $requiredTotal ? 'text-emerald-700' : 'text-amber-700' }}">{{ (int) round($readyRequired / $requiredTotal * 100) }}%</span>
            </div>
            <div class="mt-2 h-1.5 bg-slate-100"><div class="h-full {{ $readyRequired === $requiredTotal ? 'bg-emerald-600' : 'bg-red-700' }}" style="width: {{ (int) round($readyRequired / $requiredTotal * 100) }}%"></div></div>
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
                                <span class="px-2 py-0.5 bg-red-50 border border-red-200 text-red-700 text-[9px] font-bold uppercase tracking-wider">{{ __('messages.required') }}</span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-500 text-[9px] font-bold uppercase tracking-wider">{{ __('messages.optional') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Upload Badge status -->
                    @php($doc = $uploadedDocs[$req->id] ?? null)
                    <div class="flex-shrink-0">
                        @if($doc && $doc->status === 'rejected')
                            <span class="px-2 py-0.5 bg-rose-50 border border-rose-200 text-rose-800 text-[9px] font-bold uppercase tracking-wider">{{ __('messages.doc_rejected') }}</span>
                        @elseif($doc && $doc->status === 'approved')
                            <span class="px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[9px] font-bold uppercase tracking-wider">{{ __('messages.doc_verified') }}</span>
                        @elseif($doc && $doc->is_submitted)
                            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-800 text-[9px] font-bold uppercase tracking-wider">{{ __('messages.doc_pending') }}</span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-500 text-[9px] font-bold uppercase tracking-wider">{{ __('messages.doc_missing') }}</span>
                        @endif
                    </div>
                </div>

                @if($doc && $doc->is_submitted && $doc->status !== 'approved' && $doc->review_note)
                    <p class="text-[11px] leading-relaxed {{ $doc->status === 'rejected' ? 'text-rose-700' : 'text-slate-500' }}">{{ $doc->review_note }}</p>
                @endif

                <!-- Form Upload -->
                @if(!$alreadyApplied)
                    <form action="{{ route('resident.eligibility.upload', [$service->id, $req->id]) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row sm:items-center gap-2 pt-4 border-t border-slate-100">
                        @csrf
                        <div class="flex-grow">
                            <input type="file" name="document" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:border file:border-slate-200 file:text-[10px] file:font-bold file:uppercase file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 cursor-pointer" required>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                            {{ $doc && $doc->is_submitted ? __('messages.replace_file') : __('messages.upload') }}
                        </button>
                    </form>
                    <p class="text-[10px] text-slate-400">{{ __('messages.upload_formats_hint') }}</p>
                    @if($doc && $doc->is_submitted && $doc->file_path)
                        <a href="{{ route('resident.eligibility.document', $doc->id) }}" target="_blank" rel="noopener" class="inline-block text-red-700 font-bold uppercase text-[10px] tracking-wider hover:underline">{{ __('messages.view_file') }}</a>
                    @endif
                @else
                    @if(isset($uploadedDocs[$req->id]) && $uploadedDocs[$req->id]->is_submitted)
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-bold text-[10px] uppercase text-emerald-700 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('messages.submitted') }}
                            </span>
                            <a href="{{ route('resident.eligibility.document', $uploadedDocs[$req->id]->id) }}" target="_blank" rel="noopener" class="text-red-700 font-bold uppercase text-[10px] tracking-wider hover:underline">
                                {{ __('messages.view_file') }}
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
            <div class="text-center py-2 space-y-3">
                @if($checklist->status === 'approved')
                    <span class="inline-flex items-center px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('messages.status_label') }}: {{ strtoupper(__('messages.status_approved')) }}
                    </span>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        {{ __('messages.approved_msg') }}
                    </p>
                @elseif($checklist->status === 'rejected')
                    <span class="inline-flex items-center px-4 py-2 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ __('messages.status_label') }}: {{ strtoupper(__('messages.status_rejected')) }}
                    </span>
                    @if($checklist->remarks)
                        <div class="p-3 bg-rose-50/50 border border-rose-100 text-rose-900 text-xs rounded-none text-left max-w-lg mx-auto">
                            <strong class="font-bold">{{ __('messages.feedback') }}</strong> {{ $checklist->remarks }}
                        </div>
                    @endif
                    <p class="text-xs text-slate-500 font-medium">{{ __('messages.rejected_fix_msg') }}</p>
                    <form action="{{ route('resident.eligibility.checklist.edit', $service->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-extrabold uppercase tracking-widest transition-colors shadow-sm rounded-none">
                            {{ __('messages.edit_resubmit') }}
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center px-4 py-2 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('messages.status_label') }}: {{ strtoupper(__('messages.doc_pending')) }}
                    </span>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        {{ __('messages.pending_review_msg') }}
                    </p>
                @endif
            </div>
        @else
            <form id="apply-form" action="{{ route('resident.eligibility.apply', $service->id) }}" method="POST">
                @csrf
                
                @if(!$profileCompleted)
                    <button type="button" class="w-full py-3.5 bg-slate-200 text-slate-400 font-bold uppercase tracking-wider text-xs cursor-not-allowed rounded-none" disabled>
                        {{ __('messages.apply_now') }}
                    </button>
                    <p class="text-[10px] text-center text-rose-600 font-bold uppercase tracking-wider mt-3">
                        {{ __('messages.profile_gate_apply_hint') }}
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
        }, '{{ __('messages.submit_application_title') }}');
    }
</script>
@endsection
