@extends('layouts.facilitator')

@section('title', 'Edit Eligibility Question - GovAssist')

@section('page_title', 'Edit Eligibility Question')

@section('content')
<div class="max-w-3xl mx-auto bg-white border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Edit Question details
        </h3>
        <a href="{{ route('facilitator.eligibility') }}" class="text-[10px] font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ route('facilitator.eligibility.update', $question->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5 md:col-span-2">
                <label for="service_id" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Service Link</label>
                <select name="service_id" id="service_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}" {{ $question->service_id == $svc->id ? 'selected' : '' }}>{{ $svc->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5 md:col-span-2">
                <label for="question_text_en" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question (English)</label>
                <textarea name="question_text_en" id="question_text_en" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_en }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label for="question_text_ceb" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question (Cebuano)</label>
                <textarea name="question_text_ceb" id="question_text_ceb" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_ceb }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label for="question_text_fil" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question (Filipino)</label>
                <textarea name="question_text_fil" id="question_text_fil" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_fil }}</textarea>
            </div>

            <div class="space-y-1.5 md:col-span-2">
                <label for="question_text_sub" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question (Subanen)</label>
                <textarea name="question_text_sub" id="question_text_sub" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>{{ $question->question_text_sub }}</textarea>
            </div>

            <div class="space-y-1.5 md:col-span-2 border-t border-slate-100 pt-6 mt-2">
                <h4 class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-4">Logic Rules</h4>
            </div>

            <div class="space-y-1.5">
                <label for="type" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Answer Type</label>
                <select name="type" id="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" onchange="toggleInputs(this.value)">
                    <option value="boolean" {{ $question->type == 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                    <option value="number" {{ $question->type == 'number' ? 'selected' : '' }}>Number (Numeric check)</option>
                    <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>Text (Open-ended / Descriptive)</option>
                </select>
            </div>

            <input type="hidden" name="operator" id="operator_hidden" value="{{ $question->operator }}">

            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Expected Value to Pass</label>
                
                <!-- Dropdown choice for Boolean type -->
                <select id="expected_value_boolean" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                    <option value="true" {{ $question->expected_value == 'true' ? 'selected' : '' }}>Yes</option>
                    <option value="false" {{ $question->expected_value == 'false' ? 'selected' : '' }}>No</option>
                </select>

                <!-- Input for numeric/text types -->
                <input type="text" id="expected_value_text" value="{{ $question->expected_value }}" placeholder="e.g. 15000" class="hidden w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                
                <!-- Real hidden input submitted -->
                <input type="hidden" name="expected_value" id="expected_value" value="{{ $question->expected_value }}">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
            <a href="{{ route('facilitator.eligibility') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[10px] uppercase tracking-widest transition-all rounded-none">Cancel</a>
            <button type="submit" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white rounded-none font-extrabold text-[10px] uppercase tracking-widest transition-all">
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
        
        if (type === 'boolean') {
            opHidden.value = '==';
            valBool.classList.remove('hidden');
            valText.classList.add('hidden');
            valReal.value = valBool.value;
        } else if (type === 'number') {
            opHidden.value = opHidden.value === '==' ? '<' : opHidden.value; // Keep existing non-equals operator, otherwise default to <
            valBool.classList.add('hidden');
            valText.classList.remove('hidden');
            valText.placeholder = 'e.g. 15000';
            valText.readOnly = false;
            valReal.value = valText.value;
        } else if (type === 'text') {
            opHidden.value = '==';
            valBool.classList.add('hidden');
            valText.classList.remove('hidden');
            valText.value = 'N/A';
            valText.readOnly = true;
            valReal.value = 'N/A';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const valBool = document.getElementById('expected_value_boolean');
        const valText = document.getElementById('expected_value_text');
        const valReal = document.getElementById('expected_value');
        const typeSelect = document.getElementById('type');
        
        valBool.addEventListener('change', () => {
            if (typeSelect.value === 'boolean') {
                valReal.value = valBool.value;
            }
        });
        
        valText.addEventListener('input', () => {
            if (typeSelect.value !== 'boolean') {
                valReal.value = valText.value;
            }
        });

        // Initialize state based on existing question
        const initialType = "{{ $question->type }}";
        const initialVal = "{{ $question->expected_value }}";
        if (initialType === 'boolean') {
            valBool.value = initialVal;
            valBool.classList.remove('hidden');
            valText.classList.add('hidden');
        } else {
            valText.value = initialVal;
            valBool.classList.add('hidden');
            valText.classList.remove('hidden');
        }
    });
</script>
@endsection
