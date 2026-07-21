@extends('layouts.facilitator')

@section('title', 'Edit Eligibility Question - GovAssist')

@section('page_title', 'Edit Eligibility Question')

@section('content')
<div class="max-w-3xl mx-auto bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Edit Question details
        </h3>
        <a href="{{ route('facilitator.eligibility') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ route('facilitator.eligibility.update', $question->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="operator" id="operator_hidden" value="{{ $question->operator }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5 md:col-span-2">
                <label for="service_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Link</label>
                <select name="service_id" id="service_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold" required>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}" {{ $question->service_id == $svc->id ? 'selected' : '' }}>{{ $svc->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5 md:col-span-2">
                <label for="question_text_en" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Question (English)</label>
                <textarea name="question_text_en" id="question_text_en" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_en }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label for="question_text_ceb" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Question (Cebuano)</label>
                <textarea name="question_text_ceb" id="question_text_ceb" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_ceb }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label for="question_text_fil" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Question (Filipino)</label>
                <textarea name="question_text_fil" id="question_text_fil" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_fil }}</textarea>
            </div>

            <div class="space-y-1.5 md:col-span-2">
                <label for="question_text_sub" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Question (Subanen)</label>
                <textarea name="question_text_sub" id="question_text_sub" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_sub }}</textarea>
            </div>

            <div class="space-y-1.5 md:col-span-2 border-t border-slate-100 pt-6 mt-2">
                <h4 class="text-xs font-extrabold text-slate-500 uppercase tracking-widest">Logic Configuration</h4>
            </div>

            <div class="space-y-1.5">
                <label for="type" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Question Type</label>
                <select name="type" id="type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold" onchange="toggleInputs(this.value)">
                    <option value="boolean" {{ $question->type == 'boolean' ? 'selected' : '' }}>Yes / No Question</option>
                    <option value="number" {{ $question->type == 'number' ? 'selected' : '' }}>Number / Amount / Count</option>
                    <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>Text / Answer</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label id="expected_val_label" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Expected Answer / Limit</label>
                
                <!-- Dropdown choice for Boolean type -->
                <select id="expected_value_boolean" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold">
                    <option value="true" {{ $question->expected_value == 'true' ? 'selected' : '' }}>Yes</option>
                    <option value="false" {{ $question->expected_value == 'false' ? 'selected' : '' }}>No</option>
                </select>

                <!-- Input for numeric/text types -->
                <input type="text" id="expected_value_text" value="{{ $question->expected_value }}" placeholder="e.g. 15000" class="hidden w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                
                <!-- Real hidden input submitted -->
                <input type="hidden" name="expected_value" id="expected_value" value="{{ $question->expected_value }}">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3 bg-slate-50/20 -mx-6 -mb-6 p-6">
            <a href="{{ route('facilitator.eligibility') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs uppercase tracking-widest transition-all rounded-xl border border-slate-200/50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white rounded-xl font-extrabold text-xs uppercase tracking-widest transition-all shadow-xs">
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    function toggleInputs(type) {
        const opHidden = document.getElementById('operator_hidden');
        const valReal = document.getElementById('expected_value');
        const valBool = document.getElementById('expected_value_boolean');
        const valText = document.getElementById('expected_value_text');
        const label = document.getElementById('expected_val_label');
        
        if (type === 'boolean') {
            opHidden.value = '==';
            if (valBool) valBool.classList.remove('hidden');
            if (valText) valText.classList.add('hidden');
            if (valReal && valBool) valReal.value = valBool.value;
            if (label) label.innerText = 'Expected Answer to Pass';
        } else if (type === 'number') {
            opHidden.value = opHidden.value === '==' ? '<' : opHidden.value; // Keep existing non-equals operator, otherwise default to <
            if (valBool) valBool.classList.add('hidden');
            if (valText) {
                valText.classList.remove('hidden');
                valText.placeholder = 'e.g. 15000';
                valText.readOnly = false;
                if (valReal) valReal.value = valText.value;
            }
            if (label) label.innerText = 'Expected Limit (Less than this value passes)';
        } else if (type === 'text') {
            opHidden.value = '==';
            if (valBool) valBool.classList.add('hidden');
            if (valText) {
                valText.classList.remove('hidden');
                valText.value = 'N/A';
                valText.readOnly = true;
                if (valReal) valReal.value = 'N/A';
            }
            if (label) label.innerText = 'Expected Answer';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const valBool = document.getElementById('expected_value_boolean');
        const valText = document.getElementById('expected_value_text');
        const valReal = document.getElementById('expected_value');
        const typeSelect = document.getElementById('type');
        
        if (valBool && valReal) {
            valBool.addEventListener('change', () => {
                if (typeSelect && typeSelect.value === 'boolean') {
                    valReal.value = valBool.value;
                }
            });
        }
        
        if (valText && valReal) {
            valText.addEventListener('input', () => {
                if (typeSelect && typeSelect.value !== 'boolean') {
                    valReal.value = valText.value;
                }
            });
        }

        // Initialize state based on existing question
        if (typeSelect) {
            toggleInputs(typeSelect.value);
            
            // Override toggle default behavior for values loaded from DB
            const initialVal = "{{ $question->expected_value }}";
            valReal.value = initialVal;
            if (typeSelect.value === 'boolean') {
                if (valBool) valBool.value = initialVal;
            } else {
                if (valText) valText.value = initialVal;
            }
        }
    });
</script>
@endsection
