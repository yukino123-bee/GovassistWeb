@extends('layouts.facilitator')

@section('title', 'View Citizen - GovAssist')

@section('page_title', 'Citizens Registry')

@section('content')
<div class="bg-white border border-slate-200 shadow-sm">
    <!-- Section Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Citizen Details
        </h3>
        <a href="{{ route('facilitator.users') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-extrabold uppercase tracking-widest transition-colors rounded-none flex items-center">
            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Registry
        </a>
    </div>

    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Profile Column -->
        <div class="col-span-1 md:border-r border-slate-100 pr-0 md:pr-8 flex flex-col items-center text-center">
            @if($user->avatar)
                <img src="{{ Str::startsWith($user->avatar, 'http') ? $user->avatar : Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($user->avatar) }}" alt="{{ $user->name }}" class="w-32 h-32 object-cover border-4 border-slate-50 mb-4 shadow-sm">
            @else
                <div class="w-32 h-32 bg-slate-100 border-4 border-slate-50 mb-4 shadow-sm flex items-center justify-center text-slate-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            @endif
            
            <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-sm font-mono text-slate-500 mt-1">{{ $user->email }}</p>
            
            <div class="mt-2 text-xs text-slate-400">
                Registered on {{ $user->created_at->format('M d, Y') }}
            </div>
            
            <div class="mt-6 flex flex-col space-y-2 w-full">
                <a href="{{ route('facilitator.users.edit', $user) }}" class="w-full py-2 bg-red-50 text-red-700 text-[10px] font-extrabold uppercase tracking-widest hover:bg-red-100 transition-colors text-center border border-red-100">
                    Edit Profile
                </a>
                <form action="{{ route('facilitator.users.destroy', $user) }}" method="POST" class="w-full" id="delete-user-{{ $user->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="showConfirmModal('Are you sure you want to delete this citizen?', () => document.getElementById('delete-user-{{ $user->id }}').submit())" class="w-full py-2 text-slate-500 text-[10px] font-extrabold uppercase tracking-widest hover:text-red-600 hover:bg-slate-50 transition-colors text-center">
                        Delete Citizen
                    </button>
                </form>
            </div>
        </div>

        <!-- Details Column -->
        <div class="col-span-2 space-y-8">
            <div>
                <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                    Personal Information
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50 p-6 border border-slate-100">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Contact Number</span>
                        <p class="text-sm text-slate-800 font-medium">{{ $user->contact_number ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Date of Birth</span>
                        <p class="text-sm text-slate-800 font-medium">{{ $user->dob ? $user->dob->format('F j, Y') : 'Not provided' }}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Civil Status</span>
                        <p class="text-sm text-slate-800 font-medium capitalize">{{ $user->civil_status ?? 'Not provided' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Address</span>
                        <p class="text-sm text-slate-800 font-medium">{{ $user->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <div>
                <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Documents
                </h4>
                @if($user->valid_id_path)
                    <div class="flex items-start bg-white p-4 border border-slate-200 shadow-sm">
                        <div class="mr-4 text-red-700 bg-red-50 p-2 border border-red-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                        <div class="flex-1 flex justify-between items-center">
                            <div>
                                <h5 class="text-sm font-bold text-slate-800">Valid Identification</h5>
                                <p class="text-xs text-slate-500 mt-0.5">Primary identification document</p>
                            </div>
                            <a href="{{ asset('storage/' . $user->valid_id_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-[10px] font-extrabold uppercase tracking-widest hover:border-red-300 hover:text-red-700 transition-all">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                View
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-100 p-4 flex items-start text-yellow-800">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-xs font-medium leading-relaxed">No valid ID has been uploaded by this citizen yet. Some services might require identification.</p>
                    </div>
                @endif
            </div>

            <!-- Applications -->
            @if(isset($user->checklists) && $user->checklists->count() > 0)
            <hr class="border-slate-100">
            <div>
                <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    Recent Applications
                </h4>
                <div class="space-y-3">
                    @foreach($user->checklists as $application)
                        <div class="flex items-center justify-between p-4 border border-slate-100 bg-slate-50 hover:bg-white hover:shadow-sm transition-all">
                            <div>
                                <h5 class="text-sm font-bold text-slate-800">{{ $application->service->service_name ?? 'Unknown Service' }}</h5>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">{{ $application->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-widest
                                    {{ $application->status === 'approved' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                      ($application->status === 'rejected' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200') }}">
                                    {{ $application->status }}
                                </span>
                                <a href="{{ route('facilitator.applications.show', $application) }}" class="p-1 text-slate-400 hover:text-red-700 transition-colors bg-white border border-slate-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
