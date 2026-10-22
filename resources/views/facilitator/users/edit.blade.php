@extends('layouts.facilitator')

@section('title', 'Edit Resident - GovAssist')

@section('page_title', 'Edit Resident')

@section('content')
<div class="space-y-4 max-w-2xl mx-auto">
    <div>
        <a href="{{ route('facilitator.users') }}" class="text-xs font-bold text-slate-500 hover:text-red-700 transition-colors inline-flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Registry</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm space-y-5">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Edit Resident Details
            </h3>
        </div>

        <form action="{{ route('facilitator.users.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full text-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all font-semibold text-slate-800" required>
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Email Address (optional)</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full text-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all font-mono text-slate-800" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="contact_number" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $user->contact_number) }}" required class="w-full text-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label for="dob" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}" class="w-full text-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-slate-800 font-semibold cursor-pointer">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('facilitator.users') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold uppercase tracking-wider rounded-md transition-all">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
