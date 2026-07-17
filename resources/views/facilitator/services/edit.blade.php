@extends('layouts.facilitator')

@section('title', 'Edit Service - GovAssist')

@section('page_title', 'Edit Government Service')

@section('content')
<div class="max-w-2xl bg-white rounded-none border border-slate-200 p-6">
    <form action="{{ route('facilitator.services.update', $service->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Category Select -->
        <div class="space-y-1.5">
            <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Category</label>
            <select name="category_id" id="category_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $service->category_id == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Name English -->
            <div class="space-y-1.5">
                <label for="name_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Name (English)</label>
                <input type="text" name="name_en" id="name_en" value="{{ old('name_en', $service->name_en) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>
            </div>

            <!-- Name Cebuano -->
            <div class="space-y-1.5">
                <label for="name_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Name (Cebuano)</label>
                <input type="text" name="name_ceb" id="name_ceb" value="{{ old('name_ceb', $service->name_ceb) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>
            </div>

            <!-- Name Filipino -->
            <div class="space-y-1.5">
                <label for="name_fil" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Name (Filipino)</label>
                <input type="text" name="name_fil" id="name_fil" value="{{ old('name_fil', $service->name_fil) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Description English -->
            <div class="space-y-1.5">
                <label for="description_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (English)</label>
                <textarea name="description_en" id="description_en" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>{{ old('description_en', $service->description_en) }}</textarea>
            </div>

            <!-- Description Cebuano -->
            <div class="space-y-1.5">
                <label for="description_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (Cebuano)</label>
                <textarea name="description_ceb" id="description_ceb" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>{{ old('description_ceb', $service->description_ceb) }}</textarea>
            </div>

            <!-- Description Filipino -->
            <div class="space-y-1.5">
                <label for="description_fil" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Description (Filipino)</label>
                <textarea name="description_fil" id="description_fil" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800">{{ old('description_fil', $service->description_fil) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Procedure English -->
            <div class="space-y-1.5">
                <label for="procedure_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Application Procedure (English)</label>
                <textarea name="procedure_en" id="procedure_en" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>{{ old('procedure_en', $service->procedure_en) }}</textarea>
            </div>

            <!-- Procedure Cebuano -->
            <div class="space-y-1.5">
                <label for="procedure_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Application Procedure (Cebuano)</label>
                <textarea name="procedure_ceb" id="procedure_ceb" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800" required>{{ old('procedure_ceb', $service->procedure_ceb) }}</textarea>
            </div>

            <!-- Procedure Filipino -->
            <div class="space-y-1.5">
                <label for="procedure_fil" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Application Procedure (Filipino)</label>
                <textarea name="procedure_fil" id="procedure_fil" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800">{{ old('procedure_fil', $service->procedure_fil) }}</textarea>
            </div>
        </div>

        <!-- Icon selection -->
        <div class="space-y-1.5">
            <label for="icon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Service Icon Label</label>
            <select name="icon" id="icon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-sm text-slate-800">
                <option value="academic-cap" {{ $service->icon === 'academic-cap' ? 'selected' : '' }}>Academic Cap (Education)</option>
                <option value="heart" {{ $service->icon === 'heart' ? 'selected' : '' }}>Heart (Medical)</option>
                <option value="shield-exclamation" {{ $service->icon === 'shield-exclamation' ? 'selected' : '' }}>Shield Exclamation (Burial)</option>
                <option value="truck" {{ $service->icon === 'truck' ? 'selected' : '' }}>Truck (Transportation)</option>
                <option value="briefcase" {{ $service->icon === 'briefcase' ? 'selected' : '' }}>Briefcase (Employment)</option>
            </select>
        </div>

        <div class="flex items-center space-x-3 pt-4">
            <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-bold rounded-none shadow-md transition-all">
                Update Service
            </button>
            <a href="{{ route('facilitator.services') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-none transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
