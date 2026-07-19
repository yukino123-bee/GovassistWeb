@extends('layouts.citizen')

@section('title', __('messages.edit_profile'))

@section('header_title', __('messages.edit_profile'))

@section('back_button')
<a href="{{ route('citizen.profile') }}" class="text-white hover:text-red-100 p-2 transition-colors mr-2 flex items-center justify-center rounded-full hover:bg-white/10">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
    </svg>
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Avatar Header Area -->
    <div class="bg-white border border-slate-200 p-6 shadow-sm flex flex-col items-center">
        <div class="relative w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-md bg-white flex items-center justify-center">
            <div id="avatar-placeholder" class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-3xl {{ Auth::user()->avatar ? 'hidden' : '' }}">
                <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <img id="avatar-preview" src="{{ Auth::user()->avatar ? Storage::disk(env('FILESYSTEM_DISK', 'public'))->url(Auth::user()->avatar) : '' }}" alt="Avatar" class="w-full h-full object-cover {{ Auth::user()->avatar ? '' : 'hidden' }}">
            
            <form action="{{ route('citizen.profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form-edit" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity cursor-pointer">
                @csrf
                <label class="w-full h-full flex flex-col items-center justify-center text-white cursor-pointer">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Change</span>
                    <input type="file" name="avatar" class="hidden" accept="image/*" onchange="previewCitizenImage(event); document.getElementById('avatar-form-edit').submit();">
                </label>
            </form>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('citizen.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if($errors->any())
            <div class="p-4 bg-rose-50 border-l-2 border-rose-500 text-rose-800 text-xs font-semibold">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Left Column: Personal Information -->
            <div class="space-y-6">
                <div class="border-b border-slate-200 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Personal Information</h3>
                </div>

                <!-- Full Name -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.full_name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800" required>
                </div>

                <!-- Date of Birth -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                    <label for="dob" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.dob') }}</label>
                    <input type="date" name="dob" id="dob" value="{{ old('dob', Auth::user()->dob ? Auth::user()->dob->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">
                </div>

                <!-- Civil Status -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                    <label for="civil_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.civil_status') }}</label>
                    <input type="text" name="civil_status" id="civil_status" value="{{ old('civil_status', Auth::user()->civil_status) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">
                </div>
            </div>

            <!-- Right Column: Contact & Security -->
            <div class="space-y-6">
                <div class="border-b border-slate-200 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Contact & Security</h3>
                </div>

                <!-- Email Address -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.email_address') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800" required>
                </div>

                <!-- Contact Info -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                    <label for="contact_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.contact_info') }}</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', Auth::user()->contact_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">
                </div>

                <!-- Valid ID Upload -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-3">
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.upload_id') }}</span>
                    @if(Auth::user()->valid_id_path)
                        <div class="flex items-center space-x-2 text-xs text-emerald-700 font-semibold p-2 bg-emerald-50 border border-emerald-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('messages.id_uploaded_status') }}</span>
                        </div>
                    @endif
                    <input type="file" name="valid_id" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:border file:border-slate-200 file:text-[10px] file:font-bold file:uppercase file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 cursor-pointer">
                </div>
            </div>

        </div>

        <!-- Full Width: Address & Password -->
        <div class="space-y-6 pt-4">
            <div class="border-b border-slate-200 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Additional details</h3>
            </div>

            <!-- Complete Address -->
            <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.address') }}</label>
                <textarea name="address" id="address" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">{{ old('address', Auth::user()->address) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password (Optional) -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.new_password_optional') }}</label>
                    <input type="password" name="password" id="password" placeholder="{{ __('messages.password_placeholder') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">
                </div>

                <!-- Password Confirmation -->
                <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-2">
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('messages.password_placeholder') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-sm text-slate-800">
                </div>
            </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="w-full py-3.5 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-xs transition-colors">
            {{ __('messages.save_changes') }}
        </button>

    </form>

</div>

<script>
    function previewCitizenImage(event) {
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
