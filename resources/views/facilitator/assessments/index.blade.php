@extends('layouts.facilitator')

@section('title', 'Eligibility Assessments History - GovAssist')

@section('page_title', 'Resident Eligibility Calculations')

@section('content')
<div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Historical Assessment Log
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                    <th class="px-6 py-3.5">Resident Name</th>
                    <th class="px-6 py-3.5">Service Program</th>
                    <th class="px-6 py-3.5">Calculated Status</th>
                    <th class="px-6 py-3.5">Date Assessed</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($assessments as $assess)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800 text-xs">{{ $assess->user->name ?? 'Resident' }}</td>
                        <td class="px-6 py-4 text-xs text-slate-700">{{ $assess->service->name_en ?? 'Service' }}</td>
                        <td class="px-6 py-4">
                            @if($assess->status === 'eligible')
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200 uppercase tracking-wide">
                                    Eligible
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 uppercase tracking-wide">
                                    Ineligible
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs">
                            {{ $assess->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('facilitator.assessments.show', $assess) }}" class="text-xs font-extrabold uppercase tracking-widest text-slate-555 hover:text-red-700 transition-colors border border-transparent hover:border-slate-200 px-2 py-1.5 rounded-lg hover:bg-slate-50">View</a>
                                <form action="{{ route('facilitator.assessments.destroy', $assess) }}" method="POST" class="inline" id="delete-assess-{{ $assess->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="showConfirmModal('Are you sure you want to delete this assessment?', () => document.getElementById('delete-assess-{{ $assess->id }}').submit())" class="text-xs font-extrabold uppercase tracking-widest text-red-400 hover:text-red-700 transition-colors focus:outline-none px-2 py-1.5">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium">No resident assessments recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
