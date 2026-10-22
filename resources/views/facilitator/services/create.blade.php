@extends('layouts.facilitator')

@section('title', 'Add Service - GovAssist')

@section('page_title', 'Add Government Service')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div>
        <a href="{{ route('facilitator.services') }}" class="text-xs font-bold text-slate-500 hover:text-red-700 transition-colors inline-flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Services</span>
        </a>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Add New Government Service Details
            </h3>
            <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 font-extrabold text-xs uppercase tracking-wider rounded-full border border-emerald-200">
                ✨ Auto-Translation Active
            </span>
        </div>

        <form action="{{ route('facilitator.services.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Category Select -->
            <div class="space-y-1.5">
                <label for="category_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Category</label>
                <select name="category_id" id="category_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold cursor-pointer">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Service Title -->
            <div class="space-y-1.5">
                <label for="name_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Name (English)</label>
                <input type="text" name="name_en" id="name_en" placeholder="e.g. Educational Assistance" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold" required>
                <p class="text-[10px] text-slate-400 font-medium">Cebuano & Tagalog translations will be generated automatically upon saving.</p>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="description_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Description (English)</label>
                <textarea name="description_en" id="description_en" rows="3" placeholder="Provide educational funding and scholarships..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required></textarea>
                <p class="text-[10px] text-slate-400 font-medium">Cebuano & Tagalog translations will be generated automatically upon saving.</p>
            </div>

            <!-- Application Procedure -->
            <div class="space-y-1.5">
                <label for="procedure_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Application Procedure (English)</label>
                <textarea name="procedure_en" id="procedure_en" rows="3" placeholder="Step 1. Submit documents...&#10;Step 2. Wait for review..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required></textarea>
                <p class="text-[10px] text-slate-400 font-medium">Cebuano & Tagalog translations will be generated automatically upon saving.</p>
            </div>

            <!-- Icon Selection -->
            <div class="space-y-1.5">
                <label for="icon" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Icon Symbol</label>
                <select name="icon" id="icon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold cursor-pointer">
                    <option value="academic-cap">🎓 Academic Cap (Education)</option>
                    <option value="heart">❤️ Heart (Medical / Health)</option>
                    <option value="briefcase">💼 Briefcase (Employment / General)</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('facilitator.services') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold uppercase tracking-wider rounded-md transition-all">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all">
                    Create Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
