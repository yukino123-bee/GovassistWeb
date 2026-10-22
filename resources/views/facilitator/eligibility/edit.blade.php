@extends('layouts.facilitator')

@section('title', 'Edit Eligibility Question - GovAssist')

@section('page_title', 'Edit Eligibility Question')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div>
        <a href="{{ route('facilitator.eligibility') }}" class="text-xs font-bold text-slate-500 hover:text-red-700 transition-colors inline-flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Questions</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 shadow-sm rounded-lg p-6 space-y-5">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Edit Question Details
            </h3>
        </div>

        <form action="{{ route('facilitator.eligibility.update', $question->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

                        <div class="space-y-1.5">
                <label for="service_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Link</label>
                <select name="service_id" id="service_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold cursor-pointer" required>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}" {{ $question->service_id == $svc->id ? 'selected' : '' }}>{{ $svc->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="question_text_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Question (English)</label>
                <textarea name="question_text_en" id="question_text_en" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required>{{ $question->question_text_en }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="question_text_ceb" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Cebuano (filled in automatically)</label>
                    <textarea name="question_text_ceb" id="question_text_ceb" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required>{{ $question->question_text_ceb }}</textarea>
                </div>

                <div class="space-y-1.5">
                    <label for="question_text_fil" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Filipino (filled in automatically)</label>
                    <textarea name="question_text_fil" id="question_text_fil" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required>{{ $question->question_text_fil }}</textarea>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 space-y-4">
                <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Passing Rule</h4>
                @include('facilitator.eligibility._rule-fields', ['question' => $question])
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('facilitator.eligibility') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs uppercase tracking-wider transition-all rounded-md">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white rounded-md font-extrabold text-xs uppercase tracking-wider transition-all shadow-xs">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
