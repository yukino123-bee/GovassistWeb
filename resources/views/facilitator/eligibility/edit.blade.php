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

            <div class="space-y-1.5">
                <label for="operator" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Operator</label>
                <select name="operator" id="operator" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                    <option value="==" {{ $question->operator == '==' ? 'selected' : '' }}>equals (==)</option>
                    <option value=">" {{ $question->operator == '>' ? 'selected' : '' }}>greater than (&gt;)</option>
                    <option value="<" {{ $question->operator == '<' ? 'selected' : '' }}>less than (&lt;)</option>
                    <option value=">=" {{ $question->operator == '>=' ? 'selected' : '' }}>greater than or equal (&gt;=)</option>
                    <option value="<=" {{ $question->operator == '<=' ? 'selected' : '' }}>less than or equal (&lt;=)</option>
                </select>
            </div>

            <div class="space-y-1.5 md:col-span-2">
                <label for="expected_value" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Expected Value to Pass</label>
                <input type="text" name="expected_value" id="expected_value" value="{{ $question->expected_value }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
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
        const opSelect = document.getElementById('operator');
        const valInput = document.getElementById('expected_value');
        if (type === 'boolean') {
            opSelect.value = '==';
            valInput.value = 'true';
            valInput.placeholder = 'true or false';
            opSelect.disabled = false;
            valInput.readOnly = false;
        } else if (type === 'number') {
            opSelect.value = '<';
            valInput.value = '';
            valInput.placeholder = 'e.g. 15000';
            opSelect.disabled = false;
            valInput.readOnly = false;
        } else if (type === 'text') {
            opSelect.value = '==';
            valInput.value = 'N/A';
            valInput.placeholder = 'Any answer accepted';
            valInput.readOnly = true;
        }
    }
</script>
@endsection
