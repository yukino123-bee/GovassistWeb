@extends('layouts.resident')

@section('title', __('messages.terms'))
@section('header_title', __('messages.terms'))

@section('back_button')
<a href="{{ route('resident.profile') }}" class="text-white hover:text-red-100 p-2 transition-colors mr-2 flex items-center justify-center rounded-full hover:bg-white/10">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
    </svg>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 border border-slate-200 shadow-sm space-y-6">
    <h2 class="text-xl font-black text-slate-800 uppercase tracking-widest">{{ __('messages.terms') }}</h2>
    <div class="prose prose-sm prose-slate max-w-none">
        <p class="text-sm text-slate-600 mb-6">Last Updated: {{ date('F d, Y') }}</p>

        <p>By using the GovAssist platform, you agree to comply with and be bound by the following terms and conditions of use. Please review these terms carefully.</p>
        
        <h3 class="text-sm font-bold mt-6 mb-2">1. Acceptance of Agreement</h3>
        <p>You agree to the terms and conditions outlined in this Terms of Use Agreement with respect to our site. This Agreement constitutes the entire and only agreement between us and you, and supersedes all prior agreements, representations, warranties, and understandings.</p>

        <h3 class="text-sm font-bold mt-6 mb-2">2. Information Accuracy</h3>
        <p>While we strive to ensure the accuracy of the information provided through the GovAssist platform, we cannot guarantee its absolute correctness. Users are responsible for providing truthful and accurate information in all applications and forms. Providing false information may result in the rejection of applications or legal action.</p>

        <h3 class="text-sm font-bold mt-6 mb-2">3. User Responsibilities</h3>
        <p>You must not use this site for any unlawful purpose. You shall not upload any malicious content, attempt to breach the platform's security, or use the system in a way that disrupts service for other users.</p>

        <h3 class="text-sm font-bold mt-6 mb-2">4. Account Security</h3>
        <p>You are responsible for maintaining the confidentiality of your account credentials. You agree to notify us immediately of any unauthorized use of your account. We will not be liable for any losses caused by unauthorized access to your account.</p>
    </div>
</div>
@endsection
