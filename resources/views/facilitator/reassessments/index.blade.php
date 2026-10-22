@extends('layouts.facilitator')

@section('title', 'Manage Reassessment Requests - GovAssist')

@section('page_title', 'Reassessment Requests')

@section('content')
<div class="space-y-5">

    @php
        $totalRequests = $requests->count();
        $pendingRequests = $requests->where('status', 'pending')->count();
        $approvedRequests = $requests->where('status', 'approved')->count();
        $rejectedRequests = $requests->where('status', 'rejected')->count();
    @endphp

    <!-- Overview Metrics Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Total Requests -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Reassess Requests</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalRequests }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Pending Review</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $pendingRequests }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Approved -->
        <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Approved Requests</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $approvedRequests }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Rejected -->
        <div class="group bg-rose-50/70 border border-rose-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-rose-300">
            <div>
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block leading-tight">Rejected Requests</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-rose-700 transition-colors">{{ $rejectedRequests }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Section Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <!-- Section Header with Red Square Indicator -->
        <div class="border-b border-slate-200 pb-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Reassessment Requests Queue
            </h3>

            <!-- Search filter -->
            <div class="relative">
                <input type="text" id="reassess-search-input" placeholder="Search requests..." onkeyup="filterReassessTable()" class="text-xs bg-slate-50 border border-slate-200 rounded-md pl-8 pr-3 py-1.5 font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-red-600 transition-all w-52">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-650" id="reassess-table">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                            <th class="px-5 py-3">Date Requested</th>
                            <th class="px-5 py-3">Resident</th>
                            <th class="px-5 py-3">Service Program</th>
                            <th class="px-5 py-3 w-1/3">Reason Given</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($requests as $req)
                            <tr class="reassess-row hover:bg-slate-50/60 transition-colors {{ $req->status === 'pending' ? 'bg-amber-50/20' : '' }}">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="font-bold text-slate-850 block text-xs">{{ $req->created_at->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ $req->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($req->user->name ?? 'R', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-850 text-xs block reassess-resident">{{ $req->user->name }}</span>
                                            <span class="text-xs text-slate-400 font-mono block">{{ $req->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-red-700 text-xs reassess-service">
                                    {{ $req->service->name_en ?? $req->service->service_name }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 leading-relaxed text-xs italic">
                                    "{{ $req->reason }}"
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($req->status === 'pending')
                                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200 uppercase tracking-wider">Pending</span>
                                    @elseif($req->status === 'approved')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200 uppercase tracking-wider">Approved</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200 uppercase tracking-wider">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    @if($req->status === 'pending')
                                        <div class="flex items-center justify-end space-x-1.5">
                                            <form action="{{ route('facilitator.reassessments.update_status', $req->id) }}" method="POST" class="inline" id="approve-req-{{ $req->id }}">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="button" onclick="showConfirmModal('Approve this reassessment request? The resident will be allowed to retake the assessment.', () => document.getElementById('approve-req-{{ $req->id }}').submit(), 'Approve Request')" class="inline-flex items-center text-xs font-extrabold text-emerald-700 hover:text-white uppercase tracking-wider bg-emerald-50 hover:bg-emerald-600 px-2.5 py-1 rounded-lg border border-emerald-100 transition-all shadow-3xs">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('facilitator.reassessments.update_status', $req->id) }}" method="POST" class="inline" id="reject-req-{{ $req->id }}">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="button" onclick="showConfirmModal('Reject this reassessment request?', () => document.getElementById('reject-req-{{ $req->id }}').submit(), 'Reject Request')" class="inline-flex items-center text-xs font-extrabold text-rose-700 hover:text-white uppercase tracking-wider bg-rose-50 hover:bg-rose-600 px-2.5 py-1 rounded-lg border border-rose-100 transition-all shadow-3xs">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-medium text-xs italic">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400 text-xs italic">No reassessment requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Search filter script -->
<script>
    function filterReassessTable() {
        const query = document.getElementById('reassess-search-input').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.reassess-row');

        rows.forEach(row => {
            const resident = row.querySelector('.reassess-resident')?.textContent.toLowerCase() || '';
            const service = row.querySelector('.reassess-service')?.textContent.toLowerCase() || '';
            if (resident.includes(query) || service.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
