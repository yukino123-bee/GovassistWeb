@extends('layouts.facilitator')

@section('title', 'Admin Profile - GovAssist')

@section('page_title', 'Admin Profile Settings')

@section('breadcrumb_section', 'Account')

@php
    $inputClass = 'w-full text-sm px-3.5 py-2.5 bg-white border rounded-md focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 text-slate-800 transition-colors';
@endphp

@section('content')
<form action="{{ route('facilitator.profile.update') }}" method="POST" enctype="multipart/form-data" class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-4 items-start" novalidate>
    @csrf

    <!-- Identity card -->
    <aside class="lg:col-span-1 bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="h-20 bg-gradient-to-r from-red-800 to-red-700"></div>
        <div class="px-6 pb-6 -mt-12 flex flex-col items-center text-center">
            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow bg-white flex items-center justify-center">
                @if($user->avatar)
                    <img id="avatar-preview" src="{{ Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($user->avatar) }}" alt="Your profile photo" class="w-full h-full object-cover">
                @else
                    <img id="avatar-preview" src="" alt="Profile photo preview" class="w-full h-full object-cover hidden">
                    <div id="avatar-placeholder" class="w-full h-full bg-red-50 text-red-700 flex items-center justify-center text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
            </div>

            <h2 class="mt-3 text-base font-bold text-slate-900 leading-tight break-words max-w-full">{{ $user->name }}</h2>
            <p class="text-xs text-slate-500 break-all">{{ $user->email }}</p>
            <div class="mt-2 flex flex-wrap justify-center gap-1.5">
                <span class="px-2.5 py-0.5 bg-red-50 text-red-700 text-[11px] font-bold uppercase tracking-wider rounded border border-red-200">SSFO Facilitator</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold uppercase tracking-wider rounded border border-emerald-200">&#10003; Active</span>
            </div>

            <label for="avatar-input" class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-300 rounded-md text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer focus-within:ring-2 focus-within:ring-red-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Change photo
                <input type="file" name="avatar" id="avatar-input" class="sr-only" accept="image/*" onchange="previewImage(event)">
            </label>
            <p class="mt-1.5 text-[11px] text-slate-400">JPG or PNG, up to 2 MB.</p>
            @error('avatar')<p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>@enderror

            <dl class="mt-5 w-full border-t border-slate-100 pt-4 text-left text-xs space-y-2">
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Member since</dt><dd class="font-semibold text-slate-800">{{ $user->created_at?->format('M d, Y') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Last updated</dt><dd class="font-semibold text-slate-800">{{ $user->updated_at?->diffForHumans() ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Access</dt><dd class="font-semibold text-slate-800">Full facilitator access</dd></div>
            </dl>
        </div>
    </aside>

    <!-- Editable details -->
    <div class="lg:col-span-2 space-y-4">
        <section class="bg-white border border-slate-200 rounded-lg shadow-sm" aria-labelledby="personal-heading">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 id="personal-heading" class="text-sm font-bold text-slate-900">Personal information</h3>
                <p class="text-xs text-slate-500">Used for sign-in and for notifications sent from your account.</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Full name <span class="text-rose-700" aria-hidden="true">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required class="{{ $inputClass }} {{ $errors->has('name') ? 'border-rose-500' : 'border-slate-300' }}" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                    @error('name')<p id="name-error" class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email address <span class="text-rose-700" aria-hidden="true">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required class="{{ $inputClass }} {{ $errors->has('email') ? 'border-rose-500' : 'border-slate-300' }}" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')<p id="email-error" class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="contact_number" class="block text-xs font-semibold text-slate-700 mb-1">Contact number</label>
                    <input type="tel" id="contact_number" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" autocomplete="tel" placeholder="+63 9XX XXX XXXX" class="{{ $inputClass }} {{ $errors->has('contact_number') ? 'border-rose-500' : 'border-slate-300' }}">
                    @error('contact_number')<p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-lg shadow-sm" aria-labelledby="security-heading">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 id="security-heading" class="text-sm font-bold text-slate-900">Password &amp; security</h3>
                <p class="text-xs text-slate-500">Leave these blank to keep your current password. Changing it requires your current password.</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">Current password</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password" class="{{ $inputClass }} {{ $errors->has('current_password') ? 'border-rose-500' : 'border-slate-300' }}">
                    @error('current_password')<p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">New password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" minlength="8" aria-describedby="password-hint" class="{{ $inputClass }} {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-300' }}">
                    <p id="password-hint" class="mt-1 text-[11px] text-slate-400">At least 8 characters.</p>
                    @error('password')<p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirm new password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="{{ $inputClass }} border-slate-300">
                </div>
                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" id="toggle-passwords" class="rounded border-slate-300 text-red-700 focus:ring-red-600"> Show passwords
                    </label>
                </div>
            </div>
        </section>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2">
            <a href="{{ route('facilitator.dashboard') }}" class="px-5 py-2.5 text-center text-sm font-semibold text-slate-700 border border-slate-300 rounded-md hover:bg-slate-50">Cancel</a>
            <button type="submit" id="profile-save" class="px-6 py-2.5 bg-red-700 hover:bg-red-800 text-white text-sm font-semibold rounded-md inline-flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Save changes</span>
            </button>
        </div>
    </div>
</form>

<script>
    function previewImage(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    document.getElementById('toggle-passwords').addEventListener('change', function () {
        ['current_password', 'password', 'password_confirmation'].forEach(function (id) {
            document.getElementById(id).type = this.checked ? 'text' : 'password';
        }, this);
    });

    document.querySelector('form[action="{{ route('facilitator.profile.update') }}"]').addEventListener('submit', function () {
        const btn = document.getElementById('profile-save');
        btn.disabled = true;
        btn.classList.add('opacity-70');
        btn.querySelector('span').textContent = 'Saving…';
    });
</script>
@endsection
