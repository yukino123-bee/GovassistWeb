@extends('layouts.facilitator')

@section('title', 'Registered Citizens - GovAssist')

@section('page_title', 'Citizens Registry')

@section('content')
<div class="bg-white border border-slate-200 shadow-sm">
    <!-- Section Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Registered Citizens Registry
        </h3>
        <a href="{{ route('facilitator.users.create') }}" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold uppercase tracking-widest transition-colors rounded-none flex items-center">
            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Citizen
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/60">
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email Address</th>
                    <th class="px-6 py-3">Contact No.</th>
                    <th class="px-6 py-3">Civil Status</th>
                    <th class="px-6 py-3">Date of Birth</th>
                    <th class="px-6 py-3">Valid ID</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3.5 font-bold text-slate-800">{{ $user->name }}</td>
                        <td class="px-6 py-3.5 font-mono text-slate-500">{{ $user->email }}</td>
                        <td class="px-6 py-3.5">{{ $user->contact_number ?? '—' }}</td>
                        <td class="px-6 py-3.5 capitalize">{{ $user->civil_status ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-slate-400">
                            {{ $user->dob ? $user->dob->format('Y-m-d') : '—' }}
                        </td>
                        <td class="px-6 py-3.5">
                            @if($user->valid_id_path)
                                <a href="{{ asset('storage/' . $user->valid_id_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 text-[10px] font-extrabold uppercase tracking-wider hover:bg-red-100 transition-colors rounded-none">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    View ID
                                </a>
                            @else
                                <span class="text-slate-400 italic">No ID uploaded</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right space-x-2">
                            <a href="{{ route('facilitator.users.edit', $user) }}" class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 hover:text-red-700 transition-colors">Edit</a>
                            <form action="{{ route('facilitator.users.destroy', $user) }}" method="POST" class="inline" id="delete-user-{{ $user->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showConfirmModal('Are you sure you want to delete this citizen?', () => document.getElementById('delete-user-{{ $user->id }}').submit())" class="text-[10px] font-extrabold uppercase tracking-widest text-red-400 hover:text-red-700 transition-colors focus:outline-none">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-400 font-medium">No citizens registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
