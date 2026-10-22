@extends('layouts.facilitator')

@section('title', 'Manage Applications - GovAssist')

@section('page_title', 'Resident Applications')

@section('content')
<div class="space-y-5">

    @php
        $pendingApps = $statusCounts['pending'] ?? 0;
        $approvedApps = $statusCounts['approved'] ?? 0;
        $rejectedApps = $statusCounts['rejected'] ?? 0;
        $totalApps = $pendingApps + $approvedApps + $rejectedApps;
        $statusTabs = [
            ['key' => null, 'label' => 'All', 'count' => $totalApps],
            ['key' => 'pending', 'label' => 'Pending review', 'count' => $pendingApps],
            ['key' => 'approved', 'label' => 'Approved', 'count' => $approvedApps],
            ['key' => 'rejected', 'label' => 'Rejected', 'count' => $rejectedApps],
        ];
    @endphp

    <!-- Overview Metrics Bar (Matching Dashboard UX Layout) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Total Applications -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Applications</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalApps }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Pending Review</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $pendingApps }}</span>
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
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Approved</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $approvedApps }}</span>
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
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block leading-tight">Rejected</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-rose-700 transition-colors">{{ $rejectedApps }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Applications Table Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <!-- Section Header with Red Square Indicator and Filters -->
        <div class="border-b border-slate-200 pb-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Application Submissions Manager
            </h3>

            <!-- Search & Program Filter Toolbar -->
            <div class="flex flex-wrap items-center gap-2.5">
                <form action="{{ route('facilitator.applications') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
                    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                    <label for="app-search-input" class="sr-only">Search resident name or email</label>
                    <input type="search" id="app-search-input" name="q" value="{{ $search }}" placeholder="Search name or email..." class="text-xs bg-slate-50 border border-slate-200 rounded-md px-3 py-1.5 font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-red-600 w-52">
                    <label for="service_id" class="sr-only">Program</label>
                    <select name="service_id" id="service_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-xs px-3 py-1.5 rounded-md font-bold text-slate-700 cursor-pointer focus:outline-none focus:border-red-600 focus:bg-white">
                        <option value="">All Programs</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" {{ request('service_id') == $svc->id ? 'selected' : '' }}>{{ $svc->service_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold uppercase tracking-wider rounded-md">Search</button>
                    @if($search !== '' || request('service_id') || $status)
                        <a href="{{ route('facilitator.applications') }}" class="text-xs font-bold text-slate-500 hover:text-red-700 underline">Clear</a>
                    @endif
                </form>

                <a href="{{ route('facilitator.applications.create') }}" class="px-3.5 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>New Application</span>
                </a>
            </div>
        </div>

        <!-- Status tabs (server-side filter) -->
        <nav aria-label="Filter by status" class="flex flex-wrap gap-1.5 border-b border-slate-200 -mt-1 pb-3">
            @foreach($statusTabs as $tab)
                <a href="{{ route('facilitator.applications', array_filter(['status' => $tab['key'], 'q' => $search, 'service_id' => request('service_id')])) }}"
                   @if($status === $tab['key']) aria-current="true" @endif
                   class="px-3 py-1.5 rounded-md text-xs font-bold border {{ $status === $tab['key'] ? 'bg-red-700 text-white border-red-700' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                    {{ $tab['label'] }} <span class="ml-1 tabular-nums {{ $status === $tab['key'] ? 'text-red-100' : 'text-slate-400' }}">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </nav>

        <!-- Applications Table -->
        <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-650" id="applications-table">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                            <th class="px-5 py-3">Resident</th>
                            <th class="px-5 py-3">Service Program</th>
                            <th class="px-5 py-3">Date Applied</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($applications as $app)
                            <tr class="app-row hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($app->user?->name ?? 'R', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-850 block text-xs app-resident-name">{{ $app->user?->name ?? 'Resident' }}</span>
                                            <span class="text-xs text-slate-400 block font-mono app-resident-email">{{ $app->user?->email ?? 'No email' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-700 text-xs block">{{ $app->service?->name_en ?? 'N/A' }}</span>
                                    @if($app->application_type)
                                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md mt-1 inline-block {{ $app->application_type === 'renewal' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                            {{ ucfirst($app->application_type) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-450 text-xs whitespace-nowrap">
                                    <span class="font-bold text-slate-700 block">{{ $app->created_at->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">{{ $app->created_at->format('h:i A') }}</span>
                                    @if($app->status === 'pending' && $app->created_at->diffInDays(now()) >= 3)
                                        <span class="block text-[10px] font-bold text-amber-700">&#9888; Waiting {{ (int) $app->created_at->diffInDays(now()) }} days</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($app->status === 'pending')
                                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200/60 uppercase tracking-wider">Pending</span>
                                    @elseif($app->status === 'approved')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200/60 uppercase tracking-wider">Approved</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200/60 uppercase tracking-wider">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('facilitator.applications.show', $app->id) }}" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-3 py-1.5 rounded-lg border border-red-100 transition-all shadow-3xs">
                                            Process &rarr;
                                        </a>
                                        <form action="{{ route('facilitator.applications.destroy', $app->id) }}" method="POST" class="inline" id="delete-app-{{ $app->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="showConfirmModal('Are you sure you want to delete this application record?', () => document.getElementById('delete-app-{{ $app->id }}').submit(), 'Delete Application')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Delete Application">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-table-row">
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-xs italic">No applications match the current filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($applications->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                <span>Showing {{ $applications->firstItem() }}&ndash;{{ $applications->lastItem() }} of {{ $applications->total() }}</span>
                {{ $applications->links() }}
            </div>
        @endif

    </div>

</div>

@endsection
