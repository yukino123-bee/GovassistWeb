@extends('layouts.facilitator')

@section('title', 'Edit Citizen - GovAssist')
@section('page_title', 'Edit Citizen')

@section('content')
<div class="mb-4">
    <a href="{{ route('facilitator.users') }}" class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 hover:text-red-700 transition-colors flex items-center inline-flex">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Registry
    </a>
</div>

<div class="max-w-2xl bg-white border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center space-x-2">
        <span class="w-2.5 h-2.5 bg-red-700 block"></span>
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">Citizen Details</h3>
    </div>
    <form action="{{ route('facilitator.users.update', $user) }}" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors" required>
        </div>
        <div>
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors">
            </div>
        </div>
        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold uppercase tracking-widest transition-colors rounded-none shadow-sm">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
