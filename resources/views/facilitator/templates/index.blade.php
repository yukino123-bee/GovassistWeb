@extends('layouts.facilitator')

@section('title', 'Manage Templates - GovAssist')

@section('page_title', 'Document Templates Manager')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-base font-extrabold text-slate-800 flex items-center">
                <svg class="w-5 h-5 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Program Document Templates
            </h3>
            <p class="text-xs text-slate-500 mt-1">Configure and manage template files for all assistance programs. These files will be used to automatically verify resident document submissions.</p>
        </div>
    </div>

    <!-- Alert status -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl text-emerald-850 text-xs font-semibold shadow-xs">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-xl text-rose-850 text-xs font-semibold shadow-xs">
            {{ session('error') }}
        </div>
    @endif

    <!-- Program Tabs Navigation -->
    <div class="border-b border-slate-200 mb-6">
        <nav class="-mb-px flex space-x-1 sm:space-x-8 overflow-x-auto no-scrollbar" aria-label="Tabs">
            @foreach($services as $index => $service)
                @php
                    $icon = 'academic-cap';
                    if ($service->service_name === 'Medical Assistance Program' || $service->service_name === 'Medical Assistance') {
                        $icon = 'heart';
                    } elseif ($service->service_name === 'Burial Assistance Program' || $service->service_name === 'Burial Assistance') {
                        $icon = 'shield-exclamation';
                    } elseif ($service->service_name === 'Transportation Assistance Program' || $service->service_name === 'Transportation') {
                        $icon = 'truck';
                    } elseif ($service->service_name === 'Employment Assistance' || $service->service_name === 'Employment') {
                        $icon = 'briefcase';
                    }
                @endphp
                <button type="button" onclick="switchTab({{ $service->id }})" id="tab-btn-{{ $service->id }}" class="tab-button border-b-2 py-3.5 px-3 text-xs font-bold transition-all whitespace-nowrap flex items-center space-x-1.5 focus:outline-none {{ $index === 0 ? 'border-red-700 text-red-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    @if($icon === 'academic-cap')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479L12 21l-6.825-3.943a12.084 12.084 0 01.665-6.479L12 14z" /></svg>
                    @elseif($icon === 'heart')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    @elseif($icon === 'shield-exclamation')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    @elseif($icon === 'truck')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 104 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                    @elseif($icon === 'briefcase')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    @endif
                    <span>{{ $service->service_name }}</span>
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Tab Contents -->
    @foreach($services as $index => $service)
        <div id="tab-content-{{ $service->id }}" class="tab-content {{ $index === 0 ? '' : 'hidden' }}">
            <div class="overflow-hidden bg-white border border-slate-200 rounded-xl shadow-2xs mt-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                                <th class="px-6 py-3.5">Checklist Requirement</th>
                                <th class="px-6 py-3.5">Configured Template</th>
                                <th class="px-6 py-3.5">Type</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($service->requirements as $req)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800 block text-xs">{{ $req->text }}</span>
                                        <span class="text-xs font-extrabold uppercase tracking-wider mt-1.5 inline-block px-2 py-0.5 rounded-xl {{ $req->is_required ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-550 border border-slate-200' }}">
                                            {{ $req->is_required ? 'Mandatory' : 'Optional' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($req->template)
                                            <div class="flex items-start space-x-2">
                                                <div class="p-1 bg-red-50 rounded text-red-700 mt-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                </div>
                                                <div>
                                                    <span class="font-extrabold text-slate-805 block text-xs">Keywords:</span>
                                                    <span class="text-xs text-slate-500 block mt-0.5 font-medium">{{ $req->template->name_en }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-xl border border-slate-200/50">No Template Set</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($req->template)
                                            <span class="text-xs font-mono text-slate-500 uppercase">
                                                {{ pathinfo($req->template->file_path, PATHINFO_EXTENSION) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            @if($req->template)
                                                <!-- View / Download -->
                                                <a href="{{ Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($req->template->file_path) }}" target="_blank" title="View Current Template" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                </a>

                                                <!-- Edit/Change -->
                                                <button type="button" 
                                                    onclick="openUploadModal({{ $service->id }}, '{{ addslashes($service->service_name) }}', {{ $req->id }}, '{{ addslashes($req->text) }}', {{ json_encode($req->template) }})"
                                                    title="Change Template File" class="p-1.5 text-slate-400 hover:text-red-700 rounded-lg hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                </button>

                                                <!-- Delete -->
                                                <form action="{{ route('facilitator.templates.destroy', $req->template->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document template? Computerized image matching will be disabled for this requirement.')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Remove Template" class="text-rose-600 hover:text-rose-700 focus:outline-none p-1.5 rounded-lg hover:bg-rose-50 border border-transparent hover:border-rose-100 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Set Template -->
                                                <button type="button" 
                                                    onclick="openUploadModal({{ $service->id }}, '{{ addslashes($service->service_name) }}', {{ $req->id }}, '{{ addslashes($req->text) }}')"
                                                    class="px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-xs">
                                                    Set Template
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-semibold italic">No requirements defined for this service program.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Upload Template Modal -->
<div id="upload-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" aria-hidden="true" onclick="closeUploadModal()"></div>

        <!-- Modal panel centering trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Box -->
        <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-start justify-between border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-sm font-extrabold text-slate-800" id="modal_title">
                        Configure Document Template
                    </h3>
                    <button type="button" onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('facilitator.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <input type="hidden" name="service_id" id="modal_service_id">
                    <input type="hidden" name="requirement_id" id="modal_requirement_id">

                    <!-- Program Read-Only Display -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">Service Program</label>
                        <input type="text" id="modal_service_display" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 font-bold outline-none cursor-not-allowed" disabled>
                    </div>

                    <!-- Requirement Read-Only Display -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">Requirement Name</label>
                        <input type="text" id="modal_requirement_display" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 font-bold outline-none cursor-not-allowed" disabled>
                    </div>

                    <!-- Keywords -->
                    <div class="space-y-1">
                        <label for="keywords" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Keywords for Verification</label>
                        <input type="text" name="keywords" id="keywords" placeholder="e.g. Barangay, Indigency, Certificate" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required>
                        <p class="text-xs text-slate-400 mt-1">Enter key words or phrases (comma-separated) that MUST be present in the uploaded document to pass automated verification.</p>
                    </div>

                    <!-- File input -->
                    <div class="space-y-1 pt-1">
                        <label for="template_file" id="file_input_label" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Select Document File</label>
                        <input type="file" name="template_file" id="template_file" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:uppercase file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer" required>
                        <p class="text-xs text-slate-400 mt-1">Accepted formats: PDF, JPG, PNG, JPEG. Max size: 5MB.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100 mt-6">
                        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-50 font-extrabold text-xs uppercase tracking-wider rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-colors">
                            Save Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(serviceId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(el => {
            el.classList.remove('border-red-700', 'text-red-700');
            el.classList.add('border-transparent', 'text-slate-500');
        });

        document.getElementById('tab-content-' + serviceId).classList.remove('hidden');
        const btn = document.getElementById('tab-btn-' + serviceId);
        btn.classList.add('border-red-700', 'text-red-700');
        btn.classList.remove('border-transparent', 'text-slate-500');
    }

    function openUploadModal(serviceId, serviceName, requirementId, requirementText, templateData = null) {
        document.getElementById('modal_service_id').value = serviceId;
        document.getElementById('modal_requirement_id').value = requirementId;
        document.getElementById('modal_service_display').value = serviceName;
        document.getElementById('modal_requirement_display').value = requirementText;

        if (templateData) {
            document.getElementById('modal_title').innerText = 'Update Document Template';
            document.getElementById('keywords').value = templateData.name_en || '';
            document.getElementById('file_input_label').innerText = 'Replace Document File:';
            document.getElementById('template_file').required = true;
        } else {
            document.getElementById('modal_title').innerText = 'Configure Document Template';
            document.getElementById('keywords').value = '';
            document.getElementById('file_input_label').innerText = 'Select Document File:';
            document.getElementById('template_file').required = true;
        }

        document.getElementById('upload-modal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('upload-modal').classList.add('hidden');
    }
</script>
@endsection
