@extends('layouts.facilitator')

@section('title', 'Facilitator Dashboard - GovAssist')

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Welcome Banner -->
    <div class="bg-red-700 p-6 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-[10px] uppercase tracking-widest text-red-200 font-bold block">Welcome back,</span>
            <h3 class="text-xl font-extrabold text-white tracking-tight mt-0.5">{{ Auth::user()->name }}</h3>
            <p class="text-xs text-red-100 mt-1">Here's an overview of GovAssist activity.</p>
        </div>
        <div class="hidden sm:flex items-center space-x-2 text-red-200">
            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
            </svg>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Citizens -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Total Citizens</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $totalUsers }}</span>
            </div>
            <div class="p-2.5 bg-red-50 border border-red-100 text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Gov Services -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Gov Services</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $totalServices }}</span>
            </div>
            <div class="p-2.5 bg-red-50 border border-red-100 text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        <!-- Open Applications -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Open Applications</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $openInquiries }}</span>
            </div>
            <div class="p-2.5 bg-amber-50 border border-amber-100 text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                </svg>
            </div>
        </div>

        <!-- Assessments -->
        <div class="bg-white border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Assessments</span>
                <span class="text-3xl font-black text-slate-800 block mt-1">{{ $totalAssessments }}</span>
            </div>
            <div class="p-2.5 bg-red-50 border border-red-100 text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Recent Applications Table -->
    <div class="bg-white border border-slate-200 shadow-sm">
        <!-- Section Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Recent Submitted Applications
            </h3>
            <a href="{{ route('facilitator.applications') }}" class="text-[10px] font-bold text-red-700 hover:text-red-800 uppercase tracking-wider">View All →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/60">
                        <th class="px-6 py-3">Citizen Name</th>
                        <th class="px-6 py-3">Assistance Service</th>
                        <th class="px-6 py-3">Submitted At</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentApplications as $app)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-bold text-slate-800">{{ $app->user->name }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $app->service->name_en }}</td>
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
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium text-xs">No recent applications submitted.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
