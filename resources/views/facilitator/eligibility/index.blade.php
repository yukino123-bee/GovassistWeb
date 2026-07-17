@extends('layouts.facilitator')

@section('title', 'Manage Eligibility Rules - GovAssist')

@section('page_title', 'Eligibility Rules')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Questions List (2/3 width) -->
    <div class="lg:col-span-2 bg-white border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Existing Assessment Questions & Rules
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/60">
                        <th class="px-6 py-3">Assessment Rule</th>
                        <th class="px-6 py-3">Service</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Criterion</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($questions as $q)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <span class="font-bold text-slate-800 block leading-relaxed">{{ $q->question_text_en }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    CEB: {{ $q->question_text_ceb ?: 'N/A' }} | FIL: {{ $q->question_text_fil ?: 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-600">{{ $q->service->name_en }}</td>
                            <td class="px-6 py-3.5 capitalize">{{ $q->type }}</td>
                            <td class="px-6 py-3.5">
                                <code class="px-2 py-1 bg-slate-100 rounded-none text-slate-700 font-mono border border-slate-200">
                                    {{ $q->operator }} {{ $q->expected_value }}
                                </code>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <form action="{{ route('facilitator.eligibility.destroy', $q->id) }}" method="POST" id="delete-q-{{ $q->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="showConfirmModal('Are you sure you want to delete this question?', () => document.getElementById('delete-q-{{ $q->id }}').submit(), 'Delete Question')" class="text-rose-600 hover:text-rose-700 focus:outline-none p-1 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-colors rounded-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium">No assessment rules defined yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Rule Form (1/3 width) -->
    <div class="bg-white border border-slate-200 shadow-sm h-fit">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Add Eligibility Rule
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
                <label for="question_text_en" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question (English)</label>
                <textarea name="question_text_en" id="question_text_en" rows="2" placeholder="e.g. Is your monthly income less than Php 15,000?" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
            </div>

            <div class="space-y-1.5">
                <label for="question_text_ceb" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question (Cebuano)</label>
                <textarea name="question_text_ceb" id="question_text_ceb" rows="2" placeholder="e.g. Ang imo bang binulan nga kita ubos sa Php 15,000?" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
            </div>

            <div class="space-y-1.5">
                <label for="question_text_fil" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Question (Filipino)</label>
                <textarea name="question_text_fil" id="question_text_fil" rows="2" placeholder="e.g. Mababa ba sa Php 15,000 ang inyong buwanang kita?" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
            </div>

            <div class="space-y-1.5">
                <label for="type" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Answer Type</label>
                <select name="type" id="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" onchange="toggleInputs(this.value)">
                    <option value="boolean">Boolean (Yes/No)</option>
                    <option value="number">Number (Numeric check)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="operator" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Operator</label>
                <select name="operator" id="operator" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800">
                    <option value="==">equals (==)</option>
                    <option value=">">greater than (&gt;)</option>
                    <option value="<">less than (&lt;)</option>
                    <option value=">=">greater than or equal (&gt;=)</option>
                    <option value="<=">less than or equal (&lt;=)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="expected_value" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Expected Value to Pass</label>
                <input type="text" name="expected_value" id="expected_value" placeholder="e.g. true or 15000" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white rounded-none font-extrabold text-[10px] uppercase tracking-widest transition-all">
                Save Eligibility Rule
            </button>
        </form>
    </div>

</div>

<script>
    function toggleInputs(type) {
        const opSelect = document.getElementById('operator');
        const valInput = document.getElementById('expected_value');
        if (type === 'boolean') {
            opSelect.value = '==';
            valInput.value = 'true';
            valInput.placeholder = 'true or false';
        } else {
            opSelect.value = '<';
            valInput.value = '';
            valInput.placeholder = 'e.g. 15000';
        }
    }
</script>
@endsection
