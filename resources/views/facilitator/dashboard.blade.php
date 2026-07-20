@extends('layouts.facilitator')

@section('title', 'Facilitator Dashboard - GovAssist')

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Citizens -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Total Citizens</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $totalUsers }}</span>
            </div>
            <div class="p-2.5 bg-red-50 border border-red-100 text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Pending Apps</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $pendingApplications }}</span>
            </div>
            <div class="p-2.5 bg-amber-50 border border-amber-100 text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Approved Applications -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Approved Apps</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $approvedApplications }}</span>
            </div>
            <div class="p-2.5 bg-emerald-50 border border-emerald-100 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Rejected Applications -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Rejected Apps</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $rejectedApplications }}</span>
            </div>
            <div class="p-2.5 bg-rose-50 border border-rose-100 text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Application Outcomes Analytics -->
        <div class="bg-white border border-slate-200 p-6 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Application Outcomes
                </h3>
            </div>
            
            @php
                $total = $totalApplications ?: 1;
                $pctPending = round(($pendingApplications / $total) * 100);
                $pctApproved = round(($approvedApplications / $total) * 100);
                $pctRejected = round(($rejectedApplications / $total) * 100);
            @endphp

            <div class="space-y-4 text-xs">
                <!-- Approved Progress Bar -->
                <div class="space-y-1.5">
                    <div class="flex justify-between font-bold text-slate-700">
                        <span>Approved</span>
                        <span>{{ $pctApproved }}% ({{ $approvedApplications }})</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-none overflow-hidden">
                        <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $pctApproved }}%"></div>
                    </div>
                </div>

                <!-- Pending Progress Bar -->
                <div class="space-y-1.5">
                    <div class="flex justify-between font-bold text-slate-700">
                        <span>Pending Review</span>
                        <span>{{ $pctPending }}% ({{ $pendingApplications }})</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-none overflow-hidden">
                        <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $pctPending }}%"></div>
                    </div>
                </div>

                <!-- Rejected Progress Bar -->
                <div class="space-y-1.5">
                    <div class="flex justify-between font-bold text-slate-700">
                        <span>Rejected</span>
                        <span>{{ $pctRejected }}% ({{ $rejectedApplications }})</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-none overflow-hidden">
                        <div class="bg-rose-500 h-full transition-all duration-500" style="width: {{ $pctRejected }}%"></div>
                    </div>
                </div>
                
                <div class="pt-2 border-t border-slate-100 text-center">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Applications: {{ $totalApplications }}</span>
                </div>
            </div>
        </div>

        <!-- Helpdesk & Reassessments Stats -->
        <div class="bg-white border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div class="space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        Helpdesk & Reassessments
                    </h3>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 border border-slate-150 p-4 text-center">
                        <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block">Open Inquiries</span>
                        <span class="text-2xl font-black text-slate-800 block mt-1">{{ $pendingInquiries }}</span>
                        <span class="text-[9px] font-bold text-slate-400 block mt-1">out of {{ $totalInquiries }} total</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-150 p-4 text-center">
                        <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block">Pending Reassess</span>
                        <span class="text-2xl font-black text-slate-800 block mt-1">{{ $pendingReassessments }}</span>
                        <span class="text-[9px] font-bold text-slate-400 block mt-1">requires review</span>
                    </div>
                </div>
            </div>
            
            <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-2">
                <a href="{{ route('facilitator.inquiries') }}" class="py-2 px-3 bg-red-700 hover:bg-red-800 text-white font-extrabold text-[9px] uppercase tracking-wider text-center transition-colors">Manage Inquiries</a>
                <a href="{{ route('facilitator.reassessments') }}" class="py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-[9px] uppercase tracking-wider text-center transition-colors">Reassessments</a>
            </div>
        </div>

        <!-- Quick Administration Actions -->
        <div class="bg-white border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Quick Actions
                </h3>
            </div>
            <div class="grid grid-cols-1 gap-2">
                <a href="{{ route('facilitator.services.create') }}" class="flex items-center justify-between p-3 bg-slate-50 border border-slate-150 hover:bg-slate-100 hover:border-slate-300 transition-all text-xs font-bold text-slate-700">
                    <span>Create Assistance Service</span>
                    <span class="text-slate-400">&rarr;</span>
                </a>
                <a href="{{ route('facilitator.requirements') }}" class="flex items-center justify-between p-3 bg-slate-50 border border-slate-150 hover:bg-slate-100 hover:border-slate-300 transition-all text-xs font-bold text-slate-700">
                    <span>Manage Requirements Checklist</span>
                    <span class="text-slate-400">&rarr;</span>
                </a>
                <a href="{{ route('facilitator.eligibility') }}" class="flex items-center justify-between p-3 bg-slate-50 border border-slate-150 hover:bg-slate-100 hover:border-slate-300 transition-all text-xs font-bold text-slate-700">
                    <span>Edit Eligibility Questions</span>
                    <span class="text-slate-400">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Volume Breakdown by Service Program -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Service Program Volume Breakdown (1/3 width) -->
        <div class="bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                        <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                        Program Application Counts
                    </h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($servicesBreakdown as $svc)
                        <div class="px-6 py-3.5 flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-700">{{ $svc->name_en }}</span>
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 font-extrabold rounded-none border border-red-100">{{ $svc->checklists_count }} apps</span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 italic text-xs">No applications registered yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50/50 text-center">
                <a href="{{ route('facilitator.services') }}" class="text-[10px] font-extrabold text-red-700 hover:text-red-800 uppercase tracking-wider">Configure Services &rarr;</a>
            </div>
        </div>

        <!-- Recent Applications Table (2/3 width) -->
        <div class="lg:col-span-2 bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    {{ __('messages.admin_recent_applications') }}
                </h3>
                <a href="{{ route('facilitator.applications') }}" class="text-[10px] font-bold text-red-700 hover:text-red-800 uppercase tracking-wider">{{ __('messages.admin_view_all') }} →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/60">
                            <th class="px-6 py-3">{{ __('messages.admin_citizen_name') }}</th>
                            <th class="px-6 py-3">{{ __('messages.admin_assistance_service') }}</th>
                            <th class="px-6 py-3">{{ __('messages.admin_submitted_at') }}</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">{{ __('messages.admin_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentApplications as $app)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 font-bold text-slate-800">{{ $app->user?->name ?? 'Citizen' }}</td>
                                <td class="px-6 py-3.5 text-slate-600">{{ $app->service?->name_en ?? 'Service' }}</td>
                                <td class="px-6 py-3.5 text-slate-400">{{ $app->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-3.5">
                                    @if($app->status === 'pending')
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-none border border-amber-200 uppercase tracking-wide">Pending</span>
                                    @elseif($app->status === 'approved')
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-none border border-emerald-200 uppercase tracking-wide">Approved</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-none border border-rose-200 uppercase tracking-wide">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('facilitator.applications.show', $app->id) }}" class="text-[10px] font-bold text-red-700 hover:text-red-800 uppercase tracking-wider hover:underline">
                                        Process →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs italic">{{ __('messages.admin_no_applications') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
