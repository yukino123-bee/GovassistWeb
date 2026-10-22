@extends('layouts.facilitator')

@section('title', 'Manage Services - GovAssist')

@section('page_title', 'Government Services')

@section('content')
<div class="space-y-5">

    @php
        $totalServices = $services->count();
        $totalRequirements = $services->sum('requirements_count');
        $totalRules = $services->sum('eligibility_questions_count');
    @endphp

    <!-- Overview Metrics Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <!-- Total Services -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Assistance Programs</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalServices }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>

        <!-- Total Requirements -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Checklist Requirements</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $totalRequirements }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>

        <!-- Eligibility Rules -->
        <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Eligibility Criteria Rules</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $totalRules }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Section Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <!-- Section Header -->
        <div class="border-b border-slate-200 pb-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Government Assistance Programs
                </h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Manage services, requirement checklists, and eligibility assessment logic.</p>
            </div>

            <a href="{{ route('facilitator.services.create') }}" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all flex items-center space-x-1.5 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Service</span>
            </a>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($services as $service)
                <div class="bg-slate-50/60 border border-slate-200 p-5 rounded-lg flex flex-col justify-between hover:bg-white hover:border-red-200 hover:shadow-md transition-all">
                    <div class="space-y-3.5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-md bg-red-50 text-red-700 border border-red-100 flex items-center justify-center shrink-0 shadow-3xs">
                                    @if($service->icon === 'academic-cap')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-9-3h18" /></svg>
                                    @elseif($service->icon === 'heart')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Government Program</span>
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight">{{ $service->name_en }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block">Description</span>
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $service->description_en }}</p>
                        </div>

                        <div class="flex items-center space-x-3 text-xs pt-1">
                            <span class="px-2.5 py-1 bg-white text-slate-700 font-bold rounded-lg border border-slate-200 shadow-3xs flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" /></svg>
                                <span>{{ $service->requirements_count }} Requirements</span>
                            </span>
                            <span class="px-2.5 py-1 bg-white text-slate-700 font-bold rounded-lg border border-slate-200 shadow-3xs flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                <span>{{ $service->eligibility_questions_count }} Eligibility Rules</span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('facilitator.services.edit', $service->id) }}" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-3 py-1.5 rounded-lg border border-red-100 transition-all shadow-3xs">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            <span>Edit Service</span>
                        </a>

                        <form action="{{ route('facilitator.services.destroy', $service->id) }}" method="POST" id="delete-service-{{ $service->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="showConfirmModal('Are you sure you want to delete this service and all associated requirements and questions?', () => document.getElementById('delete-service-{{ $service->id }}').submit(), 'Delete Service')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Delete Service">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-10 text-center text-slate-400 text-xs italic">
                    No government services configured yet.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
