@extends('layouts.facilitator')

@section('title', 'Admin Profile - GovAssist')
@section('page_title', 'Admin Profile Settings')

@section('content')
<div class="max-w-4xl bg-white border border-slate-200 shadow-sm rounded-sm overflow-hidden">
    <!-- Header -->
    <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <div class="flex items-center space-x-3">
            <span class="w-3 h-3 bg-red-700 block rounded-sm"></span>
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-widest">Administrator Profile</h3>
                <p class="text-xs text-slate-500 mt-0.5">Manage your personal information and security settings</p>
            </div>
        </div>
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-white px-3 py-1 border border-slate-200 rounded-sm">
            Role: System Facilitator
        </div>
    </div>

    <form action="{{ route('facilitator.profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row">
        @csrf

        <!-- Left Column: Avatar & Basic Info -->
        <div class="md:w-1/3 p-8 border-r border-slate-100 bg-slate-50/30 flex flex-col items-center">
            <div class="relative group cursor-pointer mb-6">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-md bg-white flex items-center justify-center">
                    @if($user->avatar)
                        <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" alt="Admin Avatar" class="w-full h-full object-cover">
                    @else
                        <img id="avatar-preview" src="" alt="Avatar Preview" class="w-full h-full object-cover hidden">
                        <div id="avatar-placeholder" class="w-full h-full bg-slate-100 text-slate-400 flex items-center justify-center text-4xl font-black">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <!-- Overlay for hover -->
                <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="text-white text-[10px] font-bold uppercase tracking-wider">Change Photo</span>
                </div>
                <!-- Hidden File Input -->
                <input type="file" name="avatar" id="avatar-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" onchange="previewImage(event)">
            </div>

            <div class="text-center w-full">
                <h2 class="text-lg font-extrabold text-slate-800">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500 mt-1 mb-4">{{ $user->email }}</p>
                
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-green-50 text-green-700 text-[10px] font-extrabold uppercase tracking-widest rounded-full border border-green-200">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    <span>Active Account</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Form Fields -->
        <div class="md:w-2/3 p-8 space-y-8">
            
            <!-- Personal Details Section -->
            <div class="space-y-4">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest border-b border-slate-100 pb-2">Personal Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full text-sm px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-sm focus:bg-white focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors font-medium text-slate-700" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full text-sm px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-sm focus:bg-white focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors font-medium text-slate-700" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Contact Number</label>
                        <input type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" class="w-full text-sm px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-sm focus:bg-white focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors font-medium text-slate-700" placeholder="+63 9XX XXX XXXX">
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="space-y-4">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest border-b border-slate-100 pb-2">Security & Password</h4>
                <p class="text-[11px] text-slate-500 mb-2">Leave these fields blank if you do not wish to change your password.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">New Password</label>
                        <input type="password" name="password" class="w-full text-sm px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-sm focus:bg-white focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors font-medium text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full text-sm px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-sm focus:bg-white focus:ring-1 focus:ring-red-700 focus:border-red-700 outline-none transition-colors font-medium text-slate-700">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 mt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-[#d5001c] hover:bg-[#b80010] text-white text-[11px] font-extrabold uppercase tracking-widest transition-all rounded-sm shadow-md shadow-red-600/20 active:scale-95 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const placeholder = document.getElementById('avatar-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
