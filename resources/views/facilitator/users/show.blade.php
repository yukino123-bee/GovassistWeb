@extends('layouts.facilitator')

@section('title', 'View Resident - GovAssist')

@section('page_title', 'Resident Profile')

@section('content')
<div class="space-y-5 max-w-5xl mx-auto">

    <!-- Top Navigation -->
    <div>
        <a href="{{ route('facilitator.users') }}" class="text-xs font-bold text-slate-500 hover:text-red-700 transition-colors inline-flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Residents Registry</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
        
        <!-- Profile Summary Card (4/12) -->
        <div class="md:col-span-4 bg-white border border-slate-200 rounded-lg p-6 shadow-sm flex flex-col items-center text-center space-y-4">
            <div class="relative">
                @if($user->avatar)
                    <img src="{{ Str::startsWith($user->avatar, 'http') ? $user->avatar : Storage::disk('public')->url($user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-slate-100 shadow-md">
                @else
                    <div class="w-24 h-24 rounded-full bg-red-50 border-4 border-slate-100 shadow-sm flex items-center justify-center text-red-700 font-bold text-3xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                @if($user->valid_id_path)
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white flex items-center justify-center text-white text-[10px]" title="ID Verified">✓</span>
                @endif
            </div>

            <div class="space-y-1">
                <h2 class="text-base font-bold text-slate-850 leading-tight">{{ $user->name }}</h2>
                <p class="text-xs font-mono text-slate-500">{{ $user->email ?: $user->contact_number }}</p>
                <div class="pt-1">
                    <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 font-extrabold text-[10px] uppercase tracking-wider rounded-full border border-blue-200">
                        Registered Resident
                    </span>
                </div>
            </div>

            <div class="text-[11px] text-slate-400 font-medium">
                Registered on {{ $user->created_at->format('M d, Y') }}
            </div>

            <div class="w-full pt-4 border-t border-slate-100 flex flex-col space-y-2">
                <a href="{{ route('facilitator.users.edit', $user) }}" class="w-full py-2 bg-red-50 text-red-700 hover:bg-red-700 hover:text-white text-xs font-extrabold uppercase tracking-wider rounded-md transition-all border border-red-100 text-center shadow-3xs">
                    Edit Profile
                </a>
                <form action="{{ route('facilitator.users.destroy', $user) }}" method="POST" class="w-full" id="delete-user-{{ $user->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="showConfirmModal('Are you sure you want to delete this resident record?', () => document.getElementById('delete-user-{{ $user->id }}').submit(), 'Delete Resident')" class="w-full py-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 text-xs font-extrabold uppercase tracking-wider rounded-md transition-all border border-transparent hover:border-rose-100 text-center">
                        Delete Resident
                    </button>
                </form>
            </div>
        </div>

        <!-- Detailed Information (8/12) -->
        <div class="md:col-span-8 space-y-4">
            
            <!-- Personal Info Card -->
            <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        Personal Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                    <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Contact Number</span>
                        <span class="font-bold text-slate-850 mt-0.5 block">{{ $user->contact_number ?? 'Not provided' }}</span>
                    </div>

                    <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Date of Birth</span>
                        <span class="font-bold text-slate-850 mt-0.5 block">{{ $user->dob ? $user->dob->format('F j, Y') : 'Not provided' }}</span>
                    </div>

                    <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Civil Status</span>
                        <span class="font-bold text-slate-850 mt-0.5 block capitalize">{{ $user->civil_status ?? 'Not provided' }}</span>
                    </div>

                    <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Email Verification</span>
                        <span class="font-bold mt-0.5 block {{ $user->email_verified_at ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                        </span>
                    </div>

                    <div class="sm:col-span-2 border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Residential Address</span>
                        <span class="font-bold text-slate-850 mt-0.5 block">{{ $user->address ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>

            <!-- Documents Card -->
            <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        Government Identification
                    </h3>
                </div>

                @if($user->valid_id_path)
                    <div class="flex items-center justify-between p-4 bg-slate-50/60 border border-slate-200 rounded-md">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-md bg-red-50 text-red-700 border border-red-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-800 uppercase">Valid Government ID</h5>
                                <span class="text-[10px] text-emerald-600 font-extrabold block mt-0.5">✓ Uploaded Document Available</span>
                            </div>
                        </div>

                        <a href="{{ route('facilitator.users.valid_id', $user) }}" target="_blank" class="px-3.5 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all flex items-center space-x-1">
                            <span>View Document</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                @else
                    <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-md flex items-center space-x-3 text-amber-800">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-xs font-medium">No valid government ID has been uploaded by this resident yet.</p>
                    </div>
                @endif
            </div>

            <!-- Applications History -->
            @if(isset($user->checklists) && $user->checklists->count() > 0)
                <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-3.5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                            Assistance Applications History
                        </h3>
                        <span class="text-xs font-bold text-slate-500">{{ $user->checklists->count() }} Submission(s)</span>
                    </div>

                    <div class="space-y-2">
                        @foreach($user->checklists as $application)
                            <div class="flex items-center justify-between p-3.5 border border-slate-200 bg-slate-50/60 rounded-md hover:bg-white hover:border-red-200 hover:shadow-xs transition-all">
                                <div>
                                    <h5 class="text-xs font-bold text-slate-800">{{ $application->service->name_en ?? 'Unknown Service' }}</h5>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ $application->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if($application->status === 'approved')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200 uppercase tracking-wider">Approved</span>
                                    @elseif($application->status === 'rejected')
                                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200 uppercase tracking-wider">Rejected</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200 uppercase tracking-wider">Pending</span>
                                    @endif

                                    <a href="{{ route('facilitator.applications.show', $application) }}" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-2.5 py-1 rounded-lg border border-red-100 transition-all shadow-3xs">
                                        Review &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
