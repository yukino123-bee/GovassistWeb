@extends('layouts.facilitator')

@section('title', 'Edit Profile - GovAssist')
@section('page_title', 'Admin Profile')

@section('content')
<div class="max-w-2xl bg-white border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center space-x-2">
        <span class="w-2.5 h-2.5 bg-red-700 block"></span>
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">Update Information</h3>
    </div>
    <form action="{{ route('facilitator.profile.update') }}" method="POST" class="p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors" required>
        </div>
        <div>
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors" required>
        </div>
        <div>
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">New Password (Optional)</label>
            <input type="password" name="password" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors">
        </div>
        <div>
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full text-sm px-3 py-2 border border-slate-200 rounded-none focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors">
        </div>
        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold uppercase tracking-widest transition-colors rounded-none shadow-sm">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
