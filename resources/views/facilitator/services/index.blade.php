@extends('layouts.facilitator')

@section('title', 'Manage Services - GovAssist')

@section('page_title', 'Government Services')

@section('content')
<div class="space-y-5">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">Create, edit, and configure available government services and assistance programs.</p>
        <a href="{{ route('facilitator.services.create') }}" class="px-4 py-2.5 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold uppercase tracking-widest shadow-sm transition-all flex items-center space-x-1.5 rounded-none">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Service</span>
        </a>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 {{ $services->count() > 1 ? 'md:grid-cols-2' : '' }} gap-5">
        @forelse($services as $service)
            <div class="bg-white border-l-4 border-l-red-600 border-t border-r border-b border-slate-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow shadow-sm">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-50 border border-red-100 text-red-600">
                                @if($service->icon === 'academic-cap')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-9-3h18" /></svg>
                                @elseif($service->icon === 'heart')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                @endif
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 block">Government Program</span>
                                <span class="text-sm font-extrabold text-slate-900">{{ $service->name_en }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Description</span>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $service->description_en }}</p>
                    </div>

                    <div class="flex items-center space-x-5 text-[10px] text-slate-500 pt-1">
                        <span class="flex items-center font-bold">
                            <svg class="w-3.5 h-3.5 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" /></svg>
                            {{ $service->requirements_count }} Requirements
                        </span>
                        <span class="flex items-center font-bold">
                            <svg class="w-3.5 h-3.5 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            {{ $service->eligibility_questions_count }} Eligibility Rules
                        </span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('facilitator.services.edit', $service->id) }}" class="text-[10px] font-bold text-red-700 hover:text-red-800 flex items-center space-x-1 uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <span>Edit</span>
                    </a>

                    <form action="{{ route('facilitator.services.destroy', $service->id) }}" method="POST" id="delete-service-{{ $service->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="showConfirmModal('Are you sure you want to delete this service and all its requirements/questions?', () => document.getElementById('delete-service-{{ $service->id }}').submit(), 'Delete Service')" class="text-[10px] font-bold text-rose-600 hover:text-rose-700 flex items-center space-x-1 focus:outline-none uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white border border-slate-200 shadow-sm p-10 text-center text-slate-400 font-medium text-xs">
                No government services configured yet.
            </div>
        @endforelse
    </div>

</div>
@endsection
