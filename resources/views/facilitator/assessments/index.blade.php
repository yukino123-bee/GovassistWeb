@extends('layouts.facilitator')

@section('title', 'Eligibility Assessments History - GovAssist')

@section('page_title', 'Citizen Eligibility Calculations')

@section('content')
<div class="bg-white border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Historical Assessment Log
        </h3>
        <a href="{{ route('facilitator.assessments.create') }}" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold uppercase tracking-widest transition-colors rounded-none flex items-center">
            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Assessment
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/60">
                    <th class="px-6 py-3">Reference No.</th>
                    <th class="px-6 py-3">Citizen Name</th>
                    <th class="px-6 py-3">Service Program</th>
                    <th class="px-6 py-3">Calculated Status</th>
                    <th class="px-6 py-3">Date Assessed</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($assessments as $assess)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3.5 font-bold text-red-700 text-xs font-mono">{{ $assess->reference_number }}</td>
                        <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $assess->user->name }}</td>
                        <td class="px-6 py-3.5">{{ $assess->service->name_en }}</td>
                        <td class="px-6 py-3.5">
                            @if($assess->status === 'eligible')
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-none border border-emerald-200 uppercase tracking-wide">
                                    Eligible
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-none border border-rose-200 uppercase tracking-wide">
                                    Ineligible
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-slate-400 text-xs">
                            {{ $assess->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-3.5 text-right space-x-2">
                            <a href="{{ route('facilitator.assessments.edit', $assess) }}" class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 hover:text-red-700 transition-colors">Edit</a>
                            <form action="{{ route('facilitator.assessments.destroy', $assess) }}" method="POST" class="inline" id="delete-assess-{{ $assess->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showConfirmModal('Are you sure you want to delete this assessment?', () => document.getElementById('delete-assess-{{ $assess->id }}').submit())" class="text-[10px] font-extrabold uppercase tracking-widest text-red-400 hover:text-red-700 transition-colors focus:outline-none">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 font-medium">No citizen assessments recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
