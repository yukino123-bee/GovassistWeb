@php
    $currentType = old('type', $question->type ?? 'boolean');
    $currentOperator = old('operator', $question->operator ?? '<=');
    $currentOperator = in_array($currentOperator, ['=', '=='], true) ? '=' : $currentOperator;
    $currentValue = old('expected_value', $question->expected_value ?? '');
    $inputClass = 'w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold';
@endphp

<div id="rule-fields" class="space-y-3.5">
    <div class="space-y-1">
        <label for="rule_type" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">What kind of answer?</label>
        <select name="type" id="rule_type" class="{{ $inputClass }} cursor-pointer" required>
            <option value="boolean" @selected($currentType === 'boolean')>Yes or No</option>
            <option value="number" @selected($currentType === 'number')>A number (income, age, etc.)</option>
            <option value="text" @selected($currentType === 'text')>A written answer</option>
        </select>
    </div>

    <div data-rule="boolean" class="space-y-1">
        <label for="rule_boolean" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">The applicant passes if they answer</label>
        <select name="expected_value" id="rule_boolean" class="{{ $inputClass }} cursor-pointer">
            <option value="true" @selected($currentValue === 'true' || $currentValue === '')>Yes</option>
            <option value="false" @selected($currentValue === 'false')>No</option>
        </select>
    </div>

    <div data-rule="number" class="space-y-1">
        <label for="rule_operator" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">The applicant passes if the number is</label>
        <div class="grid grid-cols-2 gap-3">
            <select name="operator" id="rule_operator" class="{{ $inputClass }} cursor-pointer">
                <option value="<=" @selected($currentOperator === '<=')>No more than</option>
                <option value=">=" @selected($currentOperator === '>=')>At least</option>
                <option value="<" @selected($currentOperator === '<')>Less than</option>
                <option value=">" @selected($currentOperator === '>')>More than</option>
                <option value="=" @selected($currentOperator === '=')>Exactly</option>
            </select>
            <input type="number" step="any" name="expected_value" id="rule_number" value="{{ is_numeric($currentValue) ? $currentValue : '' }}" placeholder="e.g. 15000" class="{{ $inputClass }}">
        </div>
    </div>

    <div data-rule="text" class="space-y-1">
        <label for="rule_text" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">The applicant passes if the answer mentions (optional)</label>
        <input type="text" name="expected_value" id="rule_text" value="{{ $currentType === 'text' ? $currentValue : '' }}" maxlength="255" placeholder="e.g. cebu city, talisay (separate options with commas)" class="{{ $inputClass }}">
        <p class="text-xs text-slate-500">Leave blank to only save the answer for your review without deciding eligibility.</p>
    </div>

    <label class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-200 rounded-md cursor-pointer">
        <input type="hidden" name="is_required" value="0">
        <input type="checkbox" name="is_required" value="1" class="mt-0.5 w-4 h-4 text-red-700 border-slate-300 rounded focus:ring-red-600" @checked(old('is_required', $question->is_required ?? true))>
        <span>
            <span class="block text-xs font-extrabold text-slate-700">Must pass to be eligible</span>
            <span class="block mt-0.5 text-xs text-slate-500">Turn off if this question is only for information.</span>
        </span>
    </label>

    <p id="rule_summary" class="text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-md p-3"></p>
</div>

<script>
    (function () {
        const root = document.getElementById('rule-fields');
        const type = root.querySelector('#rule_type');
        const operator = root.querySelector('#rule_operator');
        const number = root.querySelector('#rule_number');
        const boolean = root.querySelector('#rule_boolean');
        const text = root.querySelector('#rule_text');
        const summary = root.querySelector('#rule_summary');
        const words = {'<=': 'no more than', '>=': 'at least', '<': 'less than', '>': 'more than', '=': 'exactly'};

        function refresh() {
            root.querySelectorAll('[data-rule]').forEach(function (block) {
                const active = block.dataset.rule === type.value;
                block.classList.toggle('hidden', !active);
                block.querySelectorAll('input, select').forEach(function (field) { field.disabled = !active; });
            });
            number.required = type.value === 'number';

            if (type.value === 'boolean') {
                summary.textContent = 'Applicants pass if they answer "' + (boolean.value === 'true' ? 'Yes' : 'No') + '".';
            } else if (type.value === 'number') {
                summary.textContent = 'Applicants pass if the number is ' + words[operator.value] + ' ' + (number.value || '…') + '.';
            } else if (text.value.trim() !== '') {
                summary.textContent = 'Applicants pass if their answer mentions: ' + text.value.trim() + '.';
            } else {
                summary.textContent = 'This question will not affect eligibility.';
            }
        }

        root.addEventListener('input', refresh);
        root.addEventListener('change', refresh);
        refresh();
    })();
</script>
