@extends('layouts.facilitator')

@section('title', 'Manage Templates - GovAssist')

@section('page_title', 'Document Templates Manager')

@section('content')
<div class="space-y-5">

    @php
        $totalPrograms = $services->count();
        $totalReqs = $services->sum(fn($s) => $s->requirements->count());
        $configuredTemplates = $services->sum(fn($s) => $s->requirements->whereNotNull('template')->count());
    @endphp

    <!-- Overview Metrics Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <!-- Programs -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Configured Programs</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalPrograms }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>

        <!-- Total Templates -->
        <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Active OCR Templates</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $configuredTemplates }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- Total Requirements -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Total Checklist Items</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $totalReqs }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Section Card -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <!-- Section Header with Red Square Indicator -->
        <div class="border-b border-slate-200 pb-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Program Document Templates Manager
                </h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Configure OCR verification rules and keywords for resident document submissions.</p>
            </div>
        </div>

        <!-- Program Tabs Navigation -->
        <div class="border-b border-slate-200">
            <nav class="-mb-px flex space-x-1 sm:space-x-6 overflow-x-auto no-scrollbar" aria-label="Tabs">
                @foreach($services as $index => $service)
                    <button type="button" onclick="switchTab({{ $service->id }})" id="tab-btn-{{ $service->id }}" class="tab-button border-b-2 py-3 px-3 text-xs font-bold transition-all whitespace-nowrap flex items-center space-x-1.5 focus:outline-none {{ $index === 0 ? 'border-red-700 text-red-700' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                        <span>{{ $service->name_en }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Contents -->
        @foreach($services as $index => $service)
            <div id="tab-content-{{ $service->id }}" class="tab-content {{ $index === 0 ? '' : 'hidden' }}">
                <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs mt-2">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-650">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                                    <th class="px-5 py-3">Checklist Requirement</th>
                                    <th class="px-5 py-3">OCR Keywords</th>
                                    <th class="px-5 py-3">Requirement Type</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($service->requirements as $req)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <span class="font-bold text-slate-850 block text-xs">{{ $req->name_en }}</span>
                                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $req->name_ceb }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if($req->template)
                                                <div class="flex items-center space-x-2">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                    <span class="text-xs text-slate-700 font-mono bg-slate-100 px-2.5 py-0.5 rounded-lg border border-slate-200">{{ $req->template->name_en }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-xs">No keywords configured</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if($req->is_required)
                                                <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200 uppercase tracking-wider">Mandatory</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-500 text-xs font-extrabold rounded-full border border-slate-200 uppercase tracking-wider">Optional</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button type="button" onclick="openUploadModal({{ $service->id }}, '{{ addslashes($service->name_en) }}', {{ $req->id }}, '{{ addslashes($req->name_en) }}', {{ $req->template ? json_encode($req->template) : 'null' }})" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-2.5 py-1 rounded-lg border border-red-100 transition-all shadow-3xs">
                                                    {{ $req->template ? 'Edit' : 'Configure' }}
                                                </button>
                                                @if($req->template)
                                                    <form action="{{ route('facilitator.templates.destroy', $req->template->id) }}" method="POST" class="inline" id="delete-template-{{ $req->template->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="showConfirmModal('Delete this document template?', () => document.getElementById('delete-template-{{ $req->template->id }}').submit(), 'Delete Template')" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Delete Template">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-xs italic">No requirements defined for this service program.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Upload Template Modal -->
<div id="upload-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-slate-900/40 transition-opacity" onclick="closeUploadModal()"></div>

        <div class="relative z-10 bg-white rounded-lg max-w-lg w-full border border-slate-200 shadow-2xl p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center" id="modal_title">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Configure Document Template
                </h3>
                <button type="button" onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('facilitator.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <input type="hidden" name="service_id" id="modal_service_id">
                <input type="hidden" name="requirement_id" id="modal_requirement_id">

                <div class="space-y-1">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">Service Program</label>
                    <input type="text" id="modal_service_display" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-md text-xs text-slate-600 font-bold outline-none cursor-not-allowed" disabled>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">Requirement Name</label>
                    <input type="text" id="modal_requirement_display" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-md text-xs text-slate-600 font-bold outline-none cursor-not-allowed" disabled>
                </div>

                <div class="space-y-1">
                    <label for="keywords" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Words that must appear on the document</label>
                    <input type="text" name="keywords" id="keywords" placeholder="e.g. Barangay, Indigency, Certificate (separate with commas)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required>
                    <p class="text-[10px] text-slate-400 mt-1">Comma-separated words or phrases required to be detected by OCR.</p>
                </div>

                <div class="space-y-1">
                    <label for="template_file" id="file_input_label" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Select Sample Template File</label>
                    <input type="file" name="template_file" id="template_file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-extrabold file:uppercase file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer" required>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-extrabold text-xs uppercase tracking-wider rounded-md transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-md shadow-xs transition-colors">
                        Save Template
                    </button>
                </div>
            </form>
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

        document.getElementById('tab-content-' + serviceId)?.classList.remove('hidden');
        const btn = document.getElementById('tab-btn-' + serviceId);
        if (btn) {
            btn.classList.add('border-red-700', 'text-red-700');
            btn.classList.remove('border-transparent', 'text-slate-500');
        }
    }

    function openUploadModal(serviceId, serviceName, requirementId, requirementText, templateData = null) {
        document.getElementById('modal_service_id').value = serviceId;
        document.getElementById('modal_requirement_id').value = requirementId;
        document.getElementById('modal_service_display').value = serviceName;
        document.getElementById('modal_requirement_display').value = requirementText;

        if (templateData) {
            document.getElementById('modal_title').innerText = 'Update Document Template';
            document.getElementById('keywords').value = templateData.name_en || '';
            document.getElementById('file_input_label').innerText = 'Replace Sample File:';
            document.getElementById('template_file').required = false;
        } else {
            document.getElementById('modal_title').innerText = 'Configure Document Template';
            document.getElementById('keywords').value = '';
            document.getElementById('file_input_label').innerText = 'Select Sample File:';
            document.getElementById('template_file').required = true;
        }

        document.getElementById('upload-modal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('upload-modal').classList.add('hidden');
    }
</script>
@endsection
