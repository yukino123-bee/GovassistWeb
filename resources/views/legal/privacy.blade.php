@extends('layouts.resident')

@section('title', __('messages.privacy'))
@section('header_title', __('messages.privacy'))

@section('back_button')
<a href="{{ route('resident.profile') }}" class="text-white hover:text-red-100 p-2 transition-colors mr-2 flex items-center justify-center rounded-full hover:bg-white/10">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
    </svg>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 border border-slate-200 shadow-sm space-y-6">
    <h2 class="text-xl font-black text-slate-800 uppercase tracking-widest">{{ __('messages.privacy') }}</h2>
    <div class="prose prose-sm prose-slate max-w-none">
        <p class="text-sm text-slate-600 mb-6">Last Updated: {{ date('F d, Y') }}</p>

        <p>Your privacy is critically important to us. This Privacy Policy outlines how the GovAssist platform collects, uses, and protects your personal data in accordance with data protection laws.</p>
        
        <h3 class="text-sm font-bold mt-6 mb-2">1. Data Collection</h3>
        <p>We collect personal information such as your name, address, contact details, date of birth, and demographic data when you register and apply for government services through our platform. We also securely store submitted documents such as valid IDs.</p>

        <h3 class="text-sm font-bold mt-6 mb-2">2. Data Usage</h3>
        <p>Your data is used strictly for the purpose of processing your applications, assessing your eligibility for government services, and communicating with you regarding your requests. We do not use your data for commercial marketing purposes.</p>

        <h3 class="text-sm font-bold mt-6 mb-2">3. Data Protection</h3>
        <p>We implement robust security measures, including encryption and secure servers, to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>

        <h3 class="text-sm font-bold mt-6 mb-2">4. Data Sharing</h3>
        <p>Your information is only accessible to authorized facilitators and government officials responsible for processing your applications. We do not sell, trade, or rent your personal identification information to third parties.</p>

        <h3 class="text-sm font-bold mt-6 mb-2">5. Your Rights</h3>
        <p>You have the right to access, update, or request deletion of your personal information at any time through your account settings or by contacting our support team.</p>
    </div>
</div>
@endsection
