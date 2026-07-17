@extends('layouts.facilitator')

@section('title', 'Manage Templates - GovAssist')

@section('page_title', 'Document Templates Manager')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Templates List (2/3 width) -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Available Downloadable Templates
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="pb-3">Template Name</th>
                        <th class="pb-3">Program / Requirement</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($templates as $tpl)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4">
                                <span class="font-bold text-slate-800 block text-xs">{{ $tpl->name_en }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $tpl->name_ceb }}</span>
                            </td>
                            <td class="py-4">
                                <span class="font-bold text-slate-700 block text-[11px]">{{ $tpl->service->service_name ?? 'N/A' }}</span>
                                @php
                                    $reqName = 'N/A';
                                    if ($tpl->requirement) {
                                        $reqName = json_decode($tpl->requirement->requirement_text, true)['en'] ?? 'Requirement';
                                    }
                                @endphp
                                <span class="text-[10px] text-slate-500 block mt-0.5">{{ $reqName }}</span>
                            </td>
                            <td class="py-4 text-right flex items-center justify-end space-x-2">
                                <a href="{{ asset('storage/' . $tpl->file_path) }}" target="_blank" class="p-1 text-slate-400 hover:text-slate-600 rounded hover:bg-slate-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                                <form action="{{ route('facilitator.templates.destroy', $tpl->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 focus:outline-none p-1 rounded hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400 font-medium">No templates uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Template (1/3 width) -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 h-fit">
        <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Upload New Document Template
        </h3>

        <form action="{{ route('facilitator.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Service (Program) -->
            <div class="space-y-1.5">
                <label for="service_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Government Service (Program)</label>
                <select name="service_id" id="service_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required>
                    <option value="" disabled selected>Select a program...</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Specific Requirement -->
            <div class="space-y-1.5">
                <label for="requirement_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Specific Requirement</label>
                <select name="requirement_id" id="requirement_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required>
                    <option value="" disabled selected>Select a requirement...</option>
                    @foreach($requirements as $req)
                        @php
                            $reqName = json_decode($req->requirement_text, true)['en'] ?? 'Requirement';
                        @endphp
                        <option value="{{ $req->id }}">{{ $reqName }} ({{ $req->service->service_name ?? 'Any' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Name English -->
            <div class="space-y-1.5">
                <label for="name_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Template Name (English)</label>
                <input type="text" name="name_en" id="name_en" placeholder="e.g. Indigency Form Template" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <!-- Name Cebuano -->
            <div class="space-y-1.5">
                <label for="name_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Template Name (Cebuano)</label>
                <input type="text" name="name_ceb" id="name_ceb" placeholder="e.g. Barangay Indigency Porma" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <!-- Description English -->
            <div class="space-y-1.5">
                <label for="description_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (English)</label>
                <textarea name="description_en" id="description_en" rows="2" placeholder="To be filled up by the applicant..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800"></textarea>
            </div>

            <!-- Description Cebuano -->
            <div class="space-y-1.5">
                <label for="description_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (Cebuano)</label>
                <textarea name="description_ceb" id="description_ceb" rows="2" placeholder="Kinahanglan sulatan sa aplikante..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800"></textarea>
            </div>

            <!-- File Upload -->
            <div class="space-y-1.5">
                <label for="template_file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Select Document File</label>
                <input type="file" name="template_file" id="template_file" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer" required>
            </div>

            <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white rounded-xl font-bold text-xs tracking-wider shadow-md shadow-red-950/20 transition-all active:scale-[0.98]">
                Upload Template
            </button>
        </form>
    </div>

</div>
@endsection
