@extends('layouts.facilitator')

@section('title', 'Add Service - GovAssist')

@section('page_title', 'Add New Government Service')

@section('content')
<div class="max-w-2xl bg-white rounded-none border border-slate-200 p-6">
    <form action="{{ route('facilitator.services.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Category Select -->
        <div class="space-y-1.5">
            <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Category</label>
            <select name="category_id" id="category_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Name English -->
            <div class="space-y-1.5">
                <label for="name_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Name (English)</label>
                <input type="text" name="name_en" id="name_en" placeholder="e.g. Educational Assistance" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>
            </div>

            <!-- Name Cebuano -->
            <div class="space-y-1.5">
                <label for="name_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Name (Cebuano)</label>
                <input type="text" name="name_ceb" id="name_ceb" placeholder="e.g. Tabang sa Edukasyon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>
            </div>

            <!-- Name Filipino -->
            <div class="space-y-1.5">
                <label for="name_fil" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Name (Filipino)</label>
                <input type="text" name="name_fil" id="name_fil" placeholder="e.g. Tulong sa Edukasyon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Description English -->
            <div class="space-y-1.5">
                <label for="description_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (English)</label>
                <textarea name="description_en" id="description_en" rows="3" placeholder="Provide educational funding and scholarships..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required></textarea>
            </div>

            <!-- Description Cebuano -->
            <div class="space-y-1.5">
                <label for="description_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (Cebuano)</label>
                <textarea name="description_ceb" id="description_ceb" rows="3" placeholder="Naghatag og pinansyal nga tabang sa edukasyon..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required></textarea>
            </div>

            <!-- Description Filipino -->
            <div class="space-y-1.5">
                <label for="description_fil" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (Filipino)</label>
                <textarea name="description_fil" id="description_fil" rows="3" placeholder="Nagbibigay ng pinansyal na tulong sa edukasyon..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800"></textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Procedure English -->
            <div class="space-y-1.5">
                <label for="procedure_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Application Procedure (English)</label>
                <textarea name="procedure_en" id="procedure_en" rows="3" placeholder="Step 1. Submit documents...&#10;Step 2. Wait for review..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required></textarea>
            </div>

            <!-- Procedure Cebuano -->
            <div class="space-y-1.5">
                <label for="procedure_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Application Procedure (Cebuano)</label>
                <textarea name="procedure_ceb" id="procedure_ceb" rows="3" placeholder="Lakang 1. Isumite ang dokumento...&#10;Lakang 2. Paghulat sa pagsusi..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required></textarea>
            </div>

            <!-- Procedure Filipino -->
            <div class="space-y-1.5">
                <label for="procedure_fil" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Application Procedure (Filipino)</label>
                <textarea name="procedure_fil" id="procedure_fil" rows="3" placeholder="Hakbang 1. Isumite ang dokumento...&#10;Hakbang 2. Maghintay para sa pagsusuri..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800"></textarea>
            </div>
        </div>

        <!-- Icon selection -->
        <div class="space-y-1.5">
            <label for="icon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Icon Label</label>
            <select name="icon" id="icon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800">
                <option value="academic-cap">Academic Cap (Education)</option>
                <option value="heart">Heart (Medical)</option>
                <option value="shield-exclamation">Shield Exclamation (Burial)</option>
                <option value="truck">Truck (Transportation)</option>
                <option value="briefcase">Briefcase (Employment)</option>
            </select>
        </div>

        <div class="flex items-center space-x-3 pt-4">
            <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-bold rounded-none shadow-md transition-all">
                Save Service
            </button>
            <a href="{{ route('facilitator.services') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-none transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
