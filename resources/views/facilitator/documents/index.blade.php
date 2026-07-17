@extends('layouts.facilitator')

@section('title', 'Checklist Documents Log - GovAssist')

@section('page_title', 'Citizen Uploaded Documents')

@section('content')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center">
        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
        </svg>
        All Uploaded Citizen Documents
    </h3>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead>
                <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                    <th class="pb-3">Citizen</th>
                    <th class="pb-3">Checklist Requirement</th>
                    <th class="pb-3">Validation Status</th>
                    <th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($documents as $doc)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4">
                            <span class="font-bold text-slate-800 block text-xs">{{ $doc->user->name }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $doc->user->email }}</span>
                        </td>
                        <td class="py-4">
                            <span class="text-xs font-semibold text-slate-700 block">{{ $doc->requirement->name_en }}</span>
                            <span class="text-[9px] text-slate-400 block mt-0.5">Program: {{ $doc->requirement->service->name_en }}</span>
                        </td>
                        <td class="py-4">
                            @if($doc->status === 'pending')
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200/50 uppercase tracking-wide">pending</span>
                            @elseif($doc->status === 'approved')
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200/50 uppercase tracking-wide">approved</span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-full border border-rose-200/50 uppercase tracking-wide">rejected</span>
                            @endif
                        </td>
                        <td class="py-4 text-right">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="px-3 py-1.5 bg-red-50 text-red-700 text-[10px] font-black uppercase tracking-wider rounded-lg hover:bg-red-100 transition-colors inline-block">
                                View File
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-400 font-medium">No checklist documents uploaded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
