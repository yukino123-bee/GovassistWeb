@extends('layouts.facilitator')

@section('title', 'Process Application - GovAssist')

@section('page_title', 'Process Assistance Application')

@section('content')
<div class="space-y-6">

    <!-- Top Summary Info -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('facilitator.applications') }}" class="w-fit text-xs font-bold text-slate-500 hover:text-slate-700 flex items-center space-x-1.5 border border-slate-200 px-3 py-1.5 bg-white rounded-none hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Back to Applications</span>
        </a>
    </div>

    <!-- Consolidated Application Card -->
    <div class="bg-white border border-slate-200 shadow-md rounded-none overflow-hidden max-w-5xl mx-auto border-t-4 border-t-red-700">
        <!-- 1. Header (Program Name) -->
        <div class="bg-slate-50 border-b border-slate-200 px-8 py-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[9px] font-extrabold text-red-700 uppercase tracking-widest block mb-1">Official Assistance Application</span>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">{{ $checklist->service->name_en ?? 'N/A' }}</h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $checklist->service->name_ceb ?? '' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if($checklist->application_type)
                    <div class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-wider rounded-none">
                        Type: <span class="text-red-700">{{ $checklist->application_type === 'renewal' ? 'Renewal' : 'New' }}</span>
                    </div>
                @endif
                <div class="px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 text-[10px] font-black uppercase tracking-wider rounded-none">
                    Ref ID: #{{ $checklist->id }}
                </div>
            </div>
        </div>

        <div class="p-8 space-y-8 divide-y divide-slate-200">
            
            <!-- 2. Profile Details -->
            <div class="space-y-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                    <svg class="w-4 h-4 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Citizen Profile Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 text-xs">
                    <!-- Name -->
                    <div class="border border-slate-200 bg-slate-50/20 p-3.5 flex flex-col justify-center rounded-none shadow-sm">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Full Name</span>
                        <span class="font-extrabold text-slate-800 text-sm mt-0.5">{{ $checklist->user->name }}</span>
                    </div>

                    <!-- Email -->
                    <div class="border border-slate-200 bg-slate-50/20 p-3.5 flex flex-col justify-center rounded-none shadow-sm">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Email Address</span>
                        <span class="font-mono text-slate-800 mt-1 truncate" title="{{ $checklist->user->email }}">{{ $checklist->user->email }}</span>
                    </div>

                    <!-- Contact -->
                    <div class="border border-slate-200 bg-slate-50/20 p-3.5 flex flex-col justify-center rounded-none shadow-sm">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Contact Number</span>
                        <span class="font-bold text-slate-800 mt-0.5">{{ $checklist->user->contact_number ?? 'Not provided' }}</span>
                    </div>

                    <!-- Status -->
                    <div class="border border-slate-200 bg-slate-50/20 p-3.5 flex flex-col justify-center rounded-none shadow-sm">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Civil Status</span>
                        <span class="font-bold text-slate-800 mt-0.5 capitalize">{{ $checklist->user->civil_status ?? 'Not provided' }}</span>
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2 border border-slate-200 bg-slate-50/20 p-3.5 flex flex-col justify-center rounded-none shadow-sm">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Complete Address</span>
                        <span class="font-bold text-slate-800 mt-0.5 leading-normal">{{ $checklist->user->address ?? 'Not provided' }}</span>
                    </div>

                    <!-- DOB -->
                    <div class="border border-slate-200 bg-slate-50/20 p-3.5 flex flex-col justify-center rounded-none shadow-sm">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Date of Birth</span>
                        <span class="font-bold text-slate-800 mt-0.5">{{ $checklist->user->dob ? \Carbon\Carbon::parse($checklist->user->dob)->format('F d, Y') : 'Not provided' }}</span>
                    </div>

                    <!-- Gov ID -->
                    <div class="border border-slate-200 bg-slate-50/20 p-3.5 flex flex-col justify-center rounded-none shadow-sm">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Government ID Verification</span>
                        @if($checklist->user->valid_id_path)
                            <a href="{{ asset('storage/' . $checklist->user->valid_id_path) }}" target="_blank" class="mt-1 px-3 py-1.5 bg-red-50 text-red-700 text-[10px] font-black uppercase tracking-wider border border-red-200 rounded-none hover:bg-red-100 transition-colors inline-block text-center shadow-xs">
                                View Valid ID
                            </a>
                        @else
                            <span class="text-rose-600 font-extrabold block mt-1">Not Uploaded</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 3. Submitted Checklist Documents -->
            <div class="pt-8 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Submitted Checklist Documents
                    </h3>

                    @if($uploadedDocs->whereNotNull('file_path')->isNotEmpty())
                        <a href="{{ route('facilitator.applications.download_all', $checklist->id) }}" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold uppercase tracking-widest rounded-none shadow-sm transition-colors flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Download All Files (ZIP)</span>
                        </a>
                    @endif
                </div>

                <form action="{{ route('facilitator.checklist_items.batch_update', $checklist->id) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($uploadedDocs as $doc)
                            <div class="p-5 bg-slate-50/50 border border-slate-200 rounded-none flex flex-col justify-between space-y-4 h-full shadow-sm hover:border-slate-300 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider leading-tight">{{ $doc->requirement->name_en }}</h4>
                                        <span class="text-[10px] text-slate-400 block mt-1 font-semibold">{{ $doc->requirement->name_ceb }}</span>
                                    </div>
                                    
                                    <!-- Document Status Badge -->
                                    @if($doc->status === 'pending')
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[9px] font-extrabold rounded-none border border-amber-200 uppercase tracking-wider whitespace-nowrap">pending</span>
                                    @elseif($doc->status === 'approved')
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-extrabold rounded-none border border-emerald-200 uppercase tracking-wider whitespace-nowrap">approved</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[9px] font-extrabold rounded-none border border-rose-200 uppercase tracking-wider whitespace-nowrap">rejected</span>
                                    @endif
                                </div>

                                <!-- Document file view & status changer -->
                                <div class="pt-4 border-t border-slate-200/60 space-y-4">
                                    @if($doc->file_path)
                                        <!-- Inline Document Webview Wrapper -->
                                        <div class="relative w-full h-48 bg-slate-200 border border-slate-300 overflow-hidden shadow-inner">
                                            <div class="absolute top-0 left-0 right-0 bg-slate-800/90 text-white text-[9px] font-extrabold px-3 py-1.5 uppercase tracking-widest z-10 flex items-center justify-between">
                                                <span>Live Document Webview</span>
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                            </div>
                                            <iframe src="{{ asset('storage/' . $doc->file_path) }}" class="w-full h-full pt-6 border-none"></iframe>
                                        </div>

                                        <div class="flex flex-col sm:flex-row items-center gap-2">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="w-full text-center text-red-700 font-extrabold hover:underline flex items-center justify-center space-x-1.5 border border-red-200 py-2.5 bg-white hover:bg-red-50 transition-colors text-[10px] uppercase tracking-widest shadow-xs">
                                                <svg class="w-3.5 h-3.5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                <span>Fullscreen View</span>
                                            </a>
                                            
                                            @if($checklist->user->valid_id_path && $doc->status === 'pending')
                                                <button type="submit" form="auto-verify-form-{{ $doc->id }}" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-none text-[10px] font-extrabold shadow-sm transition-colors border border-slate-900 flex items-center justify-center space-x-1 uppercase tracking-widest" title="Compare this document with the user's valid ID">
                                                    <span>Auto Verify</span>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="space-y-1.5">
                                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Update Document Status</label>
                                            <select name="statuses[{{ $doc->id }}]" class="w-full bg-white text-[10px] border border-slate-300 rounded-none px-3 py-2 text-slate-800 font-extrabold uppercase tracking-wider focus:outline-none cursor-pointer focus:border-red-700">
                                                <option value="pending" {{ $doc->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $doc->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                                <option value="rejected" {{ $doc->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                            </select>
                                        </div>
                                    @else
                                        <!-- No File Placeholder -->
                                        <div class="w-full py-12 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 bg-slate-100/40 text-slate-400 select-none">
                                            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span class="text-[10px] font-extrabold uppercase tracking-widest">No File Uploaded</span>
                                        </div>
                                        <input type="hidden" name="statuses[{{ $doc->id }}]" value="pending">
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 py-12 text-center text-slate-400 italic">No checklist documents assigned to this service.</div>
                        @endforelse
                    </div>

                    @if($uploadedDocs->isNotEmpty())
                        <div class="flex justify-end pt-6 border-t border-slate-200">
                            <button type="submit" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold uppercase tracking-widest transition-colors rounded-none shadow-sm flex items-center justify-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Save All Document Statuses</span>
                            </button>
                        </div>
                    @endif
                </form>

                <!-- Hidden Auto-Verify Forms -->
                @foreach($uploadedDocs as $doc)
                    @if($doc->file_path && $checklist->user->valid_id_path && $doc->status === 'pending')
                        <form id="auto-verify-form-{{ $doc->id }}" action="{{ route('facilitator.checklist_items.auto_verify', $doc->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endif
                @endforeach
            </div>

            <!-- 4. Application Status Decision -->
            <div class="pt-8 space-y-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                    <svg class="w-4 h-4 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Application Status Decision
                </h3>

                <!-- Dashboard Detached Slip Layout -->
                <div class="border-2 border-dashed border-slate-300 bg-slate-50/50 p-6 max-w-2xl shadow-xs">
                    <div class="mb-4">
                        <span class="text-[9px] font-black text-red-700 bg-red-50 border border-red-200 px-2 py-1 uppercase tracking-widest">FACILITATOR ACTION BOARD (OFFICIAL USE ONLY)</span>
                    </div>

                    <form action="{{ route('facilitator.applications.update_status', $checklist->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="space-y-1.5">
                            <label for="status" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Final Approval Decision</label>
                            <select name="status" id="status" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800 font-bold cursor-pointer">
                                <option value="pending" {{ $checklist->status === 'pending' ? 'selected' : '' }}>Pending (Under Review)</option>
                                <option value="approved" {{ $checklist->status === 'approved' ? 'selected' : '' }}>Approved (Eligible / Completed)</option>
                                <option value="rejected" {{ $checklist->status === 'rejected' ? 'selected' : '' }}>Rejected / Denied</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="remarks" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Remarks / Feedback</label>
                            <textarea name="remarks" id="remarks" rows="3" placeholder="Enter reason for rejection or instructions..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800">{{ old('remarks', $checklist->remarks) }}</textarea>
                        </div>

                        <button type="submit" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white rounded-none font-bold text-xs tracking-wider shadow-md shadow-red-950/20 transition-all active:scale-[0.98] border border-red-800 uppercase">
                            Submit Final Status Decision
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
