@extends('layouts.facilitator')

@section('title', 'Manage Requirements - GovAssist')

@section('page_title', 'Service Requirements')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Requirements List Grouped by Service (2/3 width) -->
    <div class="lg:col-span-2 space-y-6">
        @foreach($services as $svc)
            <div class="bg-white border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/40 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        {{ $svc->name_en }} Requirements
                    </h3>
                    <span class="px-2.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 text-[9px] font-extrabold uppercase tracking-wider">
                        {{ $svc->requirements->count() }} Item(s)
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-white">
                                <th class="px-6 py-3">Requirement</th>
                                <th class="px-6 py-3 w-32">Mandatory</th>
                                <th class="px-6 py-3 text-right w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse($svc->requirements as $req)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-3.5">
                                        <span class="font-bold text-slate-800 block">{{ $req->name_en }}</span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">CEB: {{ $req->name_ceb ?: 'N/A' }} | FIL: {{ $req->name_fil ?: 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if($req->is_required)
                                            <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-none uppercase tracking-wider border border-red-200">Yes</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-extrabold rounded-none uppercase tracking-wider border border-slate-200">No</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <form action="{{ route('facilitator.requirements.destroy', $req->id) }}" method="POST" id="delete-req-{{ $req->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="showConfirmModal('Are you sure you want to delete this requirement?', () => document.getElementById('delete-req-{{ $req->id }}').submit(), 'Delete Requirement')" class="text-rose-600 hover:text-rose-700 focus:outline-none p-1 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-colors rounded-none">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-6 text-center text-slate-400 font-medium italic">No checklist requirements added yet for this program.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create Requirement Form (1/3 width) -->
    <div class="bg-white border border-slate-200 shadow-sm h-fit">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Add Checklist Requirement
            </h3>
        </div>

        <form action="{{ route('facilitator.requirements.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="service_id" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Target Service</label>
                <select name="service_id" id="service_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="requirement_text_en" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Name (English)</label>
                <input type="text" name="requirement_text_en" id="requirement_text_en" placeholder="e.g. Barangay Certificate of Indigency" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <div class="space-y-1.5">
                <label for="requirement_text_ceb" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Name (Cebuano)</label>
                <input type="text" name="requirement_text_ceb" id="requirement_text_ceb" placeholder="e.g. Sertipiko sa Indigency" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <div class="space-y-1.5">
                <label for="requirement_text_fil" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Name (Filipino)</label>
                <input type="text" name="requirement_text_fil" id="requirement_text_fil" placeholder="e.g. Barangay Certificate of Indigency" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <div class="flex items-center space-x-2 pt-1">
                <input type="checkbox" name="is_required" value="1" id="is_required" class="w-4 h-4 text-red-700 border-slate-300 rounded-none focus:ring-red-500" checked>
                <label for="is_required" class="text-[10px] font-extrabold text-slate-700 uppercase tracking-wider select-none cursor-pointer">Mandatory</label>
            </div>

            <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white rounded-none font-extrabold text-[10px] uppercase tracking-widest transition-all">
                Save Requirement
            </button>
        </form>
    </div>

</div>
@endsection
