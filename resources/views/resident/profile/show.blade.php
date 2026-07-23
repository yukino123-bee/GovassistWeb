@extends('layouts.resident')

@section('title', __('messages.profile_title'))

@section('header_title', __('messages.profile_title'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- User Header Panel -->
    <div class="bg-white border border-slate-200 p-6 shadow-sm flex flex-col sm:flex-row items-center sm:space-x-6">
        <!-- Avatar Container -->
        <div class="relative w-20 h-20 mb-4 sm:mb-0 flex-shrink-0">
            <div class="w-full h-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 font-bold text-2xl overflow-hidden">
                @if(Auth::user()->avatar)
                    <img src="{{ Storage::disk(env('FILESYSTEM_DISK', 'public'))->url(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
                @endif
            </div>
            
            <!-- Upload Avatar Overlay Trigger -->
            <form action="{{ route('resident.profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form" class="absolute bottom-0 right-0">
                @csrf
                <label class="w-7 h-7 bg-red-700 hover:bg-red-800 text-white flex items-center justify-center border border-white cursor-pointer shadow-md transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <input type="file" name="avatar" id="avatar-input" class="hidden" onchange="confirmAvatarUpload()">
                </label>
            </form>
        </div>

        <div class="text-center sm:text-left">
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ Auth::user()->name }}</h2>
            <span class="text-xs text-slate-500 mt-1 block">{{ Auth::user()->email }}</span>
        </div>
    </div>

    <!-- Alert success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-2 border-emerald-500 text-emerald-800 text-xs font-semibold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Menu List -->
    <div class="space-y-6">
        <!-- Account Settings Group -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ __('messages.account_settings') }}</h3>
            
            <div class="bg-white border border-slate-200 shadow-sm divide-y divide-slate-100">
                <!-- Edit Profile -->
                <a href="{{ route('resident.profile.edit') }}" class="flex items-center justify-between p-4 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 border border-red-200 text-red-700 bg-red-50/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('messages.edit_profile') }}</span>
                            <span class="block text-[10px] text-slate-500 mt-1">{{ __('messages.edit_profile_desc') }}</span>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Language Toggle -->
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 border border-red-200 text-red-700 bg-red-50/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5c-.347 2.187-1.512 4.385-3 6.166" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('messages.language') }}</span>
                            <span class="block text-[10px] text-slate-500 mt-1">{{ __('messages.language_desc') }}</span>
                        </div>
                    </div>
                    <div>
                        <select id="profile-lang-select" onchange="confirmLanguage(this.value, 'profile-lang-select')" class="bg-slate-50 text-slate-800 text-[10px] font-bold uppercase tracking-wider px-3 py-2 border border-slate-200 focus:outline-none cursor-pointer">
                            <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                            <option value="ceb" {{ app()->getLocale() === 'ceb' ? 'selected' : '' }}>Cebuano</option>
                            <option value="fil" {{ app()->getLocale() === 'fil' ? 'selected' : '' }}>Filipino</option>
                            <option value="sub" {{ app()->getLocale() === 'sub' ? 'selected' : '' }}>Subanen</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legal Group -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ __('messages.legal') }}</h3>
            
            <div class="bg-white border border-slate-200 shadow-sm divide-y divide-slate-100">
                <!-- Terms -->
                <a href="#" class="flex items-center justify-between p-4 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 border border-red-200 text-red-700 bg-red-50/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('messages.terms') }}</span>
                            <span class="block text-[10px] text-slate-500 mt-1">{{ __('messages.terms_desc') }}</span>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Privacy -->
                <a href="#" class="flex items-center justify-between p-4 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 border border-red-200 text-red-700 bg-red-50/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('messages.privacy') }}</span>
                            <span class="block text-[10px] text-slate-500 mt-1">{{ __('messages.privacy_desc') }}</span>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Sign Out Button -->
    <div class="pt-4">
        <form action="{{ route('logout') }}" method="POST" id="profile-logout-form">
            @csrf
            <button type="button" onclick="showConfirmModal('{{ __('messages.confirm_logout') }}', () => document.getElementById('profile-logout-form').submit())" class="w-full py-3.5 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-xs transition-colors rounded-none">
                {{ __('messages.logout') }}
            </button>
        </form>
    </div>

</div>

<script>
    function confirmAvatarUpload() {
        const input = document.getElementById('avatar-input');
        if (input.files && input.files[0]) {
            showConfirmModal('{{ __('messages.confirm_avatar') }}', () => {
                document.getElementById('avatar-form').submit();
            }, 'Update Avatar');
        }
    }
</script>
@endsection
