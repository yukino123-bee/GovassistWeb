@extends('layouts.facilitator')

@section('title', 'Create Application - GovAssist')

@section('page_title', 'Create Application')

@section('content')
<div class="space-y-4 max-w-2xl mx-auto">
    <div>
        <a href="{{ route('facilitator.applications') }}" class="text-xs font-bold text-slate-500 hover:text-red-700 transition-colors inline-flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Applications</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm space-y-5">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                New Application Entry
            </h3>
        </div>

        <form action="{{ route('facilitator.applications.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="user_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Select Resident</label>
                <select name="user_id" id="user_id" class="w-full text-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 font-semibold text-slate-800 transition-all cursor-pointer" required>
                    <option value="">-- Choose a resident --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="service_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Assistance Service Program</label>
                <select name="service_id" id="service_id" class="w-full text-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 font-semibold text-slate-800 transition-all cursor-pointer" required>
                    <option value="">-- Choose a service program --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="status" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Initial Status</label>
                <select name="status" id="status" class="w-full text-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 font-bold text-slate-800 transition-all cursor-pointer" required>
                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('facilitator.applications') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold uppercase tracking-wider rounded-md transition-all">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all">
                    Create Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
