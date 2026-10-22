@extends('layouts.facilitator')

@section('title', 'Manage Requirements - GovAssist')

@section('page_title', 'Service Requirements')

@section('content')
<div class="space-y-5">

    @php
        $totalReqs = $requirements->count();
        $mandatoryReqs = $requirements->where('is_required', true)->count();
        $optionalReqs = $requirements->where('is_required', false)->count();
    @endphp

    <!-- Overview Metrics Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <!-- Total Requirements -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Requirements</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalReqs }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>

        <!-- Mandatory Requirements -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Mandatory Documents</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $mandatoryReqs }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- Optional Requirements -->
        <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Optional Documents</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $optionalReqs }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Content: Left Requirements List + Right Add Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- Left: Requirements Grouped by Program (8/12) -->
        <div class="lg:col-span-8 space-y-4">
            @foreach($services as $svc)
                <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-3.5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                            {{ $svc->name_en }} Requirements
                        </h3>
                        <span class="px-2.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 text-xs font-extrabold uppercase tracking-wider rounded-md">
                            {{ $svc->requirements->count() }} Item(s)
                        </span>
                    </div>

                    <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-650">
                                <thead>
                                    <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                                        <th class="px-5 py-3">Document Requirement</th>
                                        <th class="px-5 py-3 w-28">Mandatory</th>
                                        <th class="px-5 py-3 text-right w-20">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($svc->requirements as $req)
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-5 py-3.5">
                                                <span class="font-bold text-slate-850 block text-xs">{{ $req->name_en }}</span>
                                                <span class="text-[10px] text-slate-400 block mt-0.5 font-medium">CEB: {{ $req->name_ceb ?: 'N/A' }} | FIL: {{ $req->name_fil ?: 'N/A' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($req->is_required)
                                                    <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full uppercase tracking-wider border border-amber-200">Required</span>
                                                @else
                                                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-500 text-xs font-extrabold rounded-full uppercase tracking-wider border border-slate-200">Optional</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                                <form action="{{ route('facilitator.requirements.destroy', $req->id) }}" method="POST" id="delete-req-{{ $req->id }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="showConfirmModal('Are you sure you want to delete this requirement?', () => document.getElementById('delete-req-{{ $req->id }}').submit(), 'Delete Requirement')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Delete Requirement">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-5 py-6 text-center text-slate-400 text-xs italic">No checklist requirements added yet for this program.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right: Add Requirement Form (4/12) -->
        <div class="lg:col-span-4 bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4 sticky top-4">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Add New Requirement
                </h3>
            </div>

            <form action="{{ route('facilitator.requirements.store') }}" method="POST" class="space-y-3.5">
                @csrf

                <div class="space-y-1">
                    <label for="service_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Program</label>
                    <select name="service_id" id="service_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold cursor-pointer" required>
                        <option value="">-- Select Service --</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="requirement_text_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Requirement Name (English)</label>
                    <input type="text" name="requirement_text_en" id="requirement_text_en" placeholder="e.g. Certificate of Indigency" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
                </div>

                <div class="space-y-1">
                    <label for="requirement_text_ceb" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Requirement Name (Cebuano)</label>
                    <input type="text" name="requirement_text_ceb" id="requirement_text_ceb" placeholder="e.g. Sertipiko sa Indigency" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
                </div>

                <div class="space-y-1">
                    <label for="requirement_text_fil" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Requirement Name (Filipino)</label>
                    <input type="text" name="requirement_text_fil" id="requirement_text_fil" placeholder="e.g. Sertipiko ng Indigency" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_required" id="is_required" value="1" class="w-4 h-4 text-red-700 rounded border-slate-300 focus:ring-red-600" checked>
                    <label for="is_required" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
                        Mandatory Document (Required for submission)
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all flex items-center justify-center space-x-1.5 mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Save Requirement</span>
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
