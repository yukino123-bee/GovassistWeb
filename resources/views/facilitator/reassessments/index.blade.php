@extends('layouts.facilitator')

@section('title', 'Manage Reassessment Requests - GovAssist')

@section('page_title', 'Reassessment Requests')

@section('content')
<div class="bg-white border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-amber-500 mr-2 block"></span>
            Reassessment Requests
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-white">
                    <th class="px-6 py-3">Date Requested</th>
                    <th class="px-6 py-3">Citizen</th>
                    <th class="px-6 py-3">Service Program</th>
                    <th class="px-6 py-3 w-1/3">Reason</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($requests as $req)
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $req->status === 'pending' ? 'bg-amber-50/20' : '' }}">
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            <span class="font-bold text-slate-700 block">{{ $req->created_at->format('M d, Y') }}</span>
                            <span class="text-[9px] text-slate-400 uppercase tracking-wider">{{ $req->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="font-bold text-slate-800">{{ $req->user->name }}</div>
                            <div class="text-[10px] text-slate-500">{{ $req->user->email }}</div>
                        </td>
                        <td class="px-6 py-3.5 font-bold text-red-700">
                            {{ $req->service->name_en ?? $req->service->service_name }}
                        </td>
                        <td class="px-6 py-3.5 text-slate-600 leading-relaxed text-[11px] italic">
                            "{{ $req->reason }}"
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            @if($req->status === 'pending')
                                <span class="px-2 py-1 bg-amber-100 text-amber-800 text-[9px] font-bold uppercase tracking-wider border border-amber-200">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-[9px] font-bold uppercase tracking-wider border border-emerald-200">Approved</span>
                            @else
                                <span class="px-2 py-1 bg-rose-100 text-rose-800 text-[9px] font-bold uppercase tracking-wider border border-rose-200">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right whitespace-nowrap">
                            @if($req->status === 'pending')
                                <form action="{{ route('facilitator.reassessments.update_status', $req->id) }}" method="POST" class="inline" id="approve-req-{{ $req->id }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="button" onclick="showConfirmModal('Are you sure you want to approve this reassessment? The citizen\'s previous assessment will be deleted so they can retake it.', () => document.getElementById('approve-req-{{ $req->id }}').submit(), 'Approve Request')" class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-700 hover:text-emerald-900 px-2 py-1 hover:bg-emerald-50 transition-colors border border-transparent hover:border-emerald-200">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('facilitator.reassessments.update_status', $req->id) }}" method="POST" class="inline" id="reject-req-{{ $req->id }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="button" onclick="showConfirmModal('Are you sure you want to reject this reassessment request?', () => document.getElementById('reject-req-{{ $req->id }}').submit(), 'Reject Request')" class="text-[10px] font-extrabold uppercase tracking-widest text-rose-700 hover:text-rose-900 px-2 py-1 hover:bg-rose-50 transition-colors border border-transparent hover:border-rose-200 ml-2">
                                        Reject
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] uppercase tracking-wider text-slate-300 font-bold px-2 py-1">No Actions</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 font-medium">No reassessment requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
