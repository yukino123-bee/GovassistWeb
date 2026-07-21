@extends('layouts.facilitator')

@section('title', 'Add Service - GovAssist')

@section('page_title', 'Add New Government Service')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Add New Government Service Details
        </h3>
        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-extrabold text-[10px] uppercase tracking-wider rounded-xl border border-emerald-200 shadow-3xs">
            ✨ Tagalog & Cebuano Auto-Generated
        </span>
    </div>

    <form action="{{ route('facilitator.services.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Category Select -->
        <div class="space-y-1.5">
            <label for="category_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Category</label>
            <select name="category_id" id="category_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Service Titles (English & Subanen Only) -->
        <div class="space-y-3">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1">1. Service Title</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name English -->
                <div class="space-y-1.5">
                    <label for="name_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Name (English)</label>
                    <input type="text" name="name_en" id="name_en" placeholder="e.g. Educational Assistance" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
                    <p class="text-[10px] text-slate-400">Tagalog & Cebuano will be translated automatically upon saving.</p>
                </div>

                <!-- Name Subanen (Manual Input Only) -->
                <div class="space-y-1.5">
                    <label for="name_sub" class="block text-xs font-extrabold text-red-700 uppercase tracking-wider">Service Name (Subanen)</label>
                    <input type="text" name="name_sub" id="name_sub" placeholder="Enter Subanen service name..." class="w-full px-4 py-2.5 bg-red-50/20 border border-red-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                </div>
            </div>
        </div>

        <!-- Descriptions (English & Subanen Only) -->
        <div class="space-y-3">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1">2. Service Description</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Description English -->
                <div class="space-y-1.5">
                    <label for="description_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Description (English)</label>
                    <textarea name="description_en" id="description_en" rows="3" placeholder="Provide educational funding and scholarships..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
                    <p class="text-[10px] text-slate-400">Tagalog & Cebuano will be translated automatically upon saving.</p>
                </div>

                <!-- Description Subanen (Manual Input Only) -->
                <div class="space-y-1.5">
                    <label for="description_sub" class="block text-xs font-extrabold text-red-700 uppercase tracking-wider">Description (Subanen)</label>
                    <textarea name="description_sub" id="description_sub" rows="3" placeholder="Enter Subanen description..." class="w-full px-4 py-2.5 bg-red-50/20 border border-red-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800"></textarea>
                </div>
            </div>
        </div>

        <!-- Application Procedures (English & Subanen Only) -->
        <div class="space-y-3">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1">3. Application Procedure</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Procedure English -->
                <div class="space-y-1.5">
                    <label for="procedure_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Procedure (English)</label>
                    <textarea name="procedure_en" id="procedure_en" rows="3" placeholder="Step 1. Submit documents...&#10;Step 2. Wait for review..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
                    <p class="text-[10px] text-slate-400">Tagalog & Cebuano will be translated automatically upon saving.</p>
                </div>

                <!-- Procedure Subanen (Manual Input Only) -->
                <div class="space-y-1.5">
                    <label for="procedure_sub" class="block text-xs font-extrabold text-red-700 uppercase tracking-wider">Procedure (Subanen)</label>
                    <textarea name="procedure_sub" id="procedure_sub" rows="3" placeholder="Enter Subanen procedure..." class="w-full px-4 py-2.5 bg-red-50/20 border border-red-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800"></textarea>
                </div>
            </div>
        </div>

        <!-- Icon selection -->
        <div class="space-y-1.5">
            <label for="icon" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Icon Label</label>
            <select name="icon" id="icon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold">
                <option value="academic-cap">Academic Cap (Education)</option>
                <option value="heart">Heart (Medical)</option>
                <option value="shield-exclamation">Shield Exclamation (Burial)</option>
                <option value="truck">Truck (Transportation)</option>
                <option value="briefcase">Briefcase (Employment)</option>
            </select>
        </div>

        <div class="flex items-center space-x-3 pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold rounded-xl uppercase tracking-widest transition-all shadow-xs">
                Save Service
            </button>
            <a href="{{ route('facilitator.services') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold rounded-xl uppercase tracking-widest transition-all border border-slate-200">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
