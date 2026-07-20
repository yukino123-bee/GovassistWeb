@extends('layouts.facilitator')

@section('title', 'Manage Eligibility Questions - GovAssist')

@section('page_title', 'Eligibility Questions')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Questions List (2/3 width) -->
    <div class="lg:col-span-2 bg-white border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Existing Assessment Questions
            </h3>
        </div>

        <div class="overflow-x-auto p-6 space-y-8">
            @php
                $groupedQuestions = $questions->groupBy(function($q) { return $q->service->service_name; });
            @endphp
            
            @forelse($groupedQuestions as $serviceName => $serviceQuestions)
                <div class="border border-slate-200 rounded-sm overflow-hidden shadow-sm">
                    <div class="bg-slate-50 border-b border-slate-200 px-4 py-3 flex items-center justify-between">
                        <h4 class="font-extrabold text-sm text-red-700 uppercase tracking-wide flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            {{ $serviceName }}
                        </h4>
                        <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-1 border border-slate-200 rounded-full">{{ $serviceQuestions->count() }} Question(s)</span>
                    </div>
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-white">
                                <th class="px-6 py-3 w-1/2">Assessment Question</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Expected Answer</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @foreach($serviceQuestions as $q)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-3.5">
                                        @php
                                            $locale = app()->getLocale();
                                            $mainQuestion = $q->{'question_text_' . $locale} ?: $q->question_text_en;
                                        @endphp
                                        <div class="text-sm font-extrabold text-slate-800 mb-1 leading-snug">{{ $mainQuestion }}</div>
                                        <div class="text-[10px] text-slate-500 mt-1 space-y-0.5">
                                            @if($locale !== 'en' && $q->question_text_en)<p>EN: {{ $q->question_text_en }}</p>@endif
                                            @if($locale !== 'ceb' && $q->question_text_ceb)<p>CEB: {{ $q->question_text_ceb }}</p>@endif
                                            @if($locale !== 'fil' && $q->question_text_fil)<p>FIL: {{ $q->question_text_fil }}</p>@endif
                                            @if($locale !== 'sub' && $q->question_text_sub)<p>SUB: {{ $q->question_text_sub }}</p>@endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 capitalize">{{ $q->type }}</td>
                                    <td class="px-6 py-3.5">
                                        @if($q->type === 'boolean')
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold border border-emerald-200 uppercase tracking-wider rounded-none">
                                                {{ $q->expected_value === 'true' ? 'Yes' : 'No' }}
                                            </span>
                                        @elseif($q->type === 'number')
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-extrabold border border-blue-200 font-mono rounded-none">
                                                {{ $q->expected_value }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-extrabold border border-slate-200 uppercase tracking-wider rounded-none">
                                                {{ $q->expected_value }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('facilitator.eligibility.edit', $q->id) }}" class="text-blue-600 hover:text-blue-700 focus:outline-none p-1 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-colors rounded-none">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('facilitator.eligibility.destroy', $q->id) }}" method="POST" id="delete-q-{{ $q->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="showConfirmModal('Are you sure you want to delete this question?', () => document.getElementById('delete-q-{{ $q->id }}').submit(), 'Delete Question')" class="text-rose-600 hover:text-rose-700 focus:outline-none p-1 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-colors rounded-none">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-slate-400 font-medium">No assessment questions defined yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Add Rule Form (1/3 width) -->
    <div class="bg-white border border-slate-200 shadow-sm h-fit">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Add Eligibility Question
            </h3>
        </div>

        <form action="{{ route('facilitator.eligibility.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="service_id" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Service Link</label>
                <select name="service_id" id="service_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="question_text" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question</label>
                <textarea name="question_text" id="question_text" rows="2" placeholder="e.g. Is your monthly income less than Php 15,000?" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
                <p class="text-[9px] text-slate-400 mt-1">The system will automatically translate this into Cebuano, Filipino, and Subanen.</p>
            </div>

            <div class="space-y-1.5">
                <label for="type" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Answer Type</label>
                <select name="type" id="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" onchange="toggleInputs(this.value)">
                    <option value="boolean">Boolean (Yes/No)</option>
                    <option value="number">Number (Numeric check)</option>
                    <option value="text">Text (Open-ended / Descriptive)</option>
                </select>
            </div>

            <input type="hidden" name="operator" id="operator_hidden" value="==">

            <div class="space-y-1.5">
                <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Expected Value to Pass</label>
                
                <!-- Dropdown choice for Boolean type -->
                <select id="expected_value_boolean" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>

                <!-- Input for numeric/text types -->
                <input type="text" id="expected_value_text" placeholder="e.g. 15000" class="hidden w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                
                <!-- Real hidden input submitted -->
                <input type="hidden" name="expected_value" id="expected_value" value="true">
            </div>

            <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white rounded-none font-extrabold text-[10px] uppercase tracking-widest transition-all">
                Save Eligibility Question
            </button>
        </form>
    </div>

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
            opHidden.value = '<'; // Math behind the scene
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

        // Initialize state
        toggleInputs(typeSelect.value);
    });
</script>
@endsection
