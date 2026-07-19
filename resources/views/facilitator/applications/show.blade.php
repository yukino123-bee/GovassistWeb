@extends('layouts.facilitator')

@section('title', 'Process Application - GovAssist')

@section('page_title', 'Process Assistance Application')

@section('content')
<div class="space-y-8">

    <!-- Top Summary Info -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('facilitator.applications') }}" class="w-fit text-xs font-bold text-slate-500 hover:text-slate-700 flex items-center space-x-1.5 border border-slate-200 px-3 py-1.5 bg-white rounded-none hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Back to Applications</span>
        </a>

        @if($checklist->application_type)
            <div class="flex items-center space-x-2">
                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Application Type:</span>
                <span class="px-2.5 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold border border-red-200 uppercase tracking-wider">
                    {{ $checklist->application_type === 'renewal' ? 'Renewal of Employment' : 'New Employment' }}
                </span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Checklist & Verification (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Documents verification card -->
            <div class="bg-white rounded-none border border-slate-200 p-6">
                <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center">
                    <svg class="w-5 h-5 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Submitted Checklist Documents
                </h3>

                <div class="space-y-4">
                    @forelse($uploadedDocs as $doc)
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-none space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $doc->requirement->name_en }}</h4>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $doc->requirement->name_ceb }}</span>
                                </div>
                                
                                <!-- Document Status Badge -->
                                @if($doc->status === 'pending')
                                    <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-none border border-amber-200 uppercase tracking-wider">pending</span>
                                @elseif($doc->status === 'approved')
                                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-none border border-emerald-200 uppercase tracking-wider">approved</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-none border border-rose-200 uppercase tracking-wider">rejected</span>
                                @endif
                            </div>

                            <!-- Document file view -->
                            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-red-700 font-bold hover:underline flex items-center space-x-1 border border-red-200 px-2 py-1 bg-white hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    <span>Download File</span>
                                </a>

                                <div class="flex items-center">
                                    <!-- Auto Verify Button -->
                                    @if($checklist->user->valid_id_path && $doc->status === 'pending')
                                    <form action="{{ route('facilitator.checklist_items.auto_verify', $doc->id) }}" method="POST" class="mr-2">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-none text-xs font-bold shadow-sm transition-colors border border-slate-900 flex items-center space-x-1" title="Compare this document with the user's valid ID">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                            <span>Auto Verify</span>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- Individual Doc Status Changer -->
                                    <form action="{{ route('facilitator.checklist_items.update_status', $doc->id) }}" method="POST" class="flex items-center space-x-2">
                                        @csrf
                                        <select name="status" class="bg-white text-xs border border-slate-200 rounded-none px-2 py-1 text-slate-800 font-bold focus:outline-none cursor-pointer">
                                            <option value="pending" {{ $doc->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $doc->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="rejected" {{ $doc->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                        <button type="submit" class="px-3 py-1 bg-red-700 hover:bg-red-800 text-white rounded-none text-xs font-bold shadow-sm transition-colors border border-red-800">
                                            Update
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 font-medium">No documents uploaded for this application.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Citizen Profile & Main Status Decision (1/3 width) -->
        <div class="space-y-6">
            <!-- Citizen Profile details -->
            <div class="bg-white rounded-none border border-slate-200 p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-800 border-b border-slate-200 pb-3 flex items-center">
                    <svg class="w-5 h-5 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Citizen Profile Details
                </h3>

                <div class="space-y-3.5 text-xs text-slate-600">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Full Name</span>
                        <span class="font-bold text-slate-800 text-sm block">{{ $checklist->user->name }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Email Address</span>
                        <span class="font-mono block text-slate-800">{{ $checklist->user->email }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Contact Number</span>
                        <span class="font-medium text-slate-800 block">{{ $checklist->user->contact_number ?? 'Not provided' }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Civil Status</span>
                        <span class="font-medium text-slate-800 block capitalize">{{ $checklist->user->civil_status ?? 'Not provided' }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Complete Address</span>
                        <span class="font-medium text-slate-800 block leading-relaxed">{{ $checklist->user->address ?? 'Not provided' }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Date of Birth</span>
                        <span class="font-medium text-slate-800 block">{{ $checklist->user->dob ? \Carbon\Carbon::parse($checklist->user->dob)->format('F d, Y') : 'Not provided' }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Government ID Verification</span>
                        @if($checklist->user->valid_id_path)
                            <a href="{{ asset('storage/' . $checklist->user->valid_id_path) }}" target="_blank" class="mt-1 px-3 py-1.5 bg-red-50 text-red-700 text-[10px] font-black uppercase tracking-wider border border-red-200 rounded-none hover:bg-red-100 transition-colors inline-block">
                                View valid ID image
                            </a>
                        @else
                            <span class="text-rose-600 font-semibold block mt-1">No ID card uploaded yet</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Application overall decision status -->
            <div class="bg-white rounded-none border border-slate-200 p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-800 border-b border-slate-200 pb-3 flex items-center">
                    <svg class="w-5 h-5 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Application Status Decision
                </h3>

                <form action="{{ route('facilitator.applications.update_status', $checklist->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="status" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Final Approval Decision</label>
                        <select name="status" id="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800 font-bold cursor-pointer">
                            <option value="pending" {{ $checklist->status === 'pending' ? 'selected' : '' }}>Pending (Under Review)</option>
                            <option value="approved" {{ $checklist->status === 'approved' ? 'selected' : '' }}>Approved (Eligible / Completed)</option>
                            <option value="rejected" {{ $checklist->status === 'rejected' ? 'selected' : '' }}>Rejected / Denied</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="remarks" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Remarks / Feedback</label>
                        <textarea name="remarks" id="remarks" rows="3" placeholder="Enter reason for rejection or instructions..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800">{{ old('remarks', $checklist->remarks) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white rounded-none font-bold text-xs tracking-wider shadow-md shadow-red-950/20 transition-all active:scale-[0.98] border border-red-800">
                        Submit Final Status Decision
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
