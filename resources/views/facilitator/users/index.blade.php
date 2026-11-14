@extends('layouts.facilitator')

@section('title', 'Registered Residents - GovAssist')

@section('page_title', 'Residents Registry')

@section('content')
<div class="space-y-5">

    @php
        $totalResidents = $stats['total'];
        $verifiedIds = $stats['withId'];
        $missingIds = $stats['withoutId'];
        $completeProfiles = $stats['completeProfiles'];
    @endphp

    <!-- Overview Metrics Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Total Residents -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Residents</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalResidents }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Valid ID Verified -->
        <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">ID Uploaded</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $verifiedIds }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Pending ID Upload -->
        <div class="group bg-rose-50/70 border border-rose-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-rose-300">
            <div>
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block leading-tight">Missing Valid ID</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-rose-700 transition-colors">{{ $missingIds }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- Complete Profiles -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Complete Profiles</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $completeProfiles }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Section Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <!-- Section Header with Red Square Indicator and Actions -->
        <div class="border-b border-slate-200 pb-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Registered Residents Registry
            </h3>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Search Box -->
                <form method="GET" action="{{ route('facilitator.users') }}" class="relative">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search resident..." class="text-xs bg-slate-50 border border-slate-200 rounded-md pl-8 pr-3 py-1.5 font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-red-600 transition-all w-48">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>

                <a href="{{ route('facilitator.users.create') }}" class="px-3.5 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Resident</span>
                </a>
            </div>
        </div>

        <!-- Residents Table -->
        <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-650" id="residents-table">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                            <th class="px-5 py-3">Resident Name</th>
                            <th class="px-5 py-3">Email Address</th>
                            <th class="px-5 py-3">Contact No.</th>
                            <th class="px-5 py-3">Civil Status</th>
                            <th class="px-5 py-3">Date of Birth</th>
                            <th class="px-5 py-3">Valid ID</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="resident-row hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-850 text-xs block resident-name">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-600 text-xs resident-email">{{ $user->email ?: $user->contact_number }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-700">{{ $user->contact_number ?? '—' }}</td>
                                <td class="px-5 py-3.5 capitalize text-xs text-slate-700">{{ $user->civil_status ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-450 text-xs whitespace-nowrap">
                                    {{ $user->dob ? $user->dob->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($user->valid_id_path)
                                        <a href="{{ route('facilitator.users.valid_id', $user) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 bg-red-50 border border-red-100 text-red-700 text-xs font-extrabold uppercase tracking-wider hover:bg-red-700 hover:text-white transition-all rounded-lg shadow-3xs">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View ID
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">No ID uploaded</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <a href="{{ route('facilitator.users.show', $user) }}" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-2.5 py-1 rounded-lg border border-red-100 transition-all shadow-3xs">
                                            View
                                        </a>
                                        <a href="{{ route('facilitator.users.edit', $user) }}" class="inline-flex items-center text-xs font-extrabold text-slate-700 hover:text-slate-900 uppercase tracking-wider bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg border border-slate-200 transition-all">
                                            Edit
                                        </a>
                                        <form action="{{ route('facilitator.users.destroy', $user) }}" method="POST" class="inline" id="delete-user-{{ $user->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="showConfirmModal('Are you sure you want to delete this resident record?', () => document.getElementById('delete-user-{{ $user->id }}').submit(), 'Delete Resident')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Delete Resident">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-xs italic">No registered residents found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
            <div class="flex flex-col items-center justify-center gap-2 text-center text-xs text-slate-500">
                <span>Showing {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} of {{ $users->total() }}</span>
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
