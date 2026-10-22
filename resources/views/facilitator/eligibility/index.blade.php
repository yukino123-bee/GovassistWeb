@extends('layouts.facilitator')

@section('title', 'Manage Eligibility Questions - GovAssist')

@section('page_title', 'Eligibility Questions')

@section('content')
<div class="space-y-5">

    @php
        $totalQuestions = $questions->count();
        $requiredQuestions = $questions->where('is_required', true)->count();
        $coveredPrograms = $questions->pluck('service_id')->unique()->count();
    @endphp

    <!-- Overview Metrics Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <!-- Total Questions -->
        <div class="group bg-blue-50/70 border border-blue-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-blue-300">
            <div>
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block leading-tight">Total Assessment Questions</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-blue-700 transition-colors">{{ $totalQuestions }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Mandatory Rules -->
        <div class="group bg-amber-50/70 border border-amber-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-amber-300">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block leading-tight">Required Rules</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-amber-700 transition-colors">{{ $requiredQuestions }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>

        <!-- Programs Covered -->
        <div class="group bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-lg flex items-center justify-between transition-all duration-300 hover:bg-white hover:shadow-md hover:border-emerald-300">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block leading-tight">Configured Programs</span>
                <span class="text-2xl font-bold text-slate-850 block mt-1 leading-none group-hover:text-emerald-700 transition-colors">{{ $coveredPrograms }}</span>
            </div>
            <div class="w-9 h-9 rounded-md bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200/40">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Content: Left Questions List + Right Add Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- Questions List (8/12) -->
        <div class="lg:col-span-8 space-y-4">
            @php
                $groupedQuestions = $questions->groupBy(function($q) { return $q->service?->service_name ?? 'Other'; });
            @endphp
            
            @forelse($groupedQuestions as $serviceName => $serviceQuestions)
                <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-3.5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <h4 class="font-extrabold text-xs text-slate-800 uppercase tracking-wider flex items-center">
                            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                            {{ $serviceName }}
                        </h4>
                        <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 border border-slate-200 rounded-md">
                            {{ $serviceQuestions->count() }} Question(s)
                        </span>
                    </div>

                    <div class="overflow-hidden bg-white border border-slate-200 rounded-md shadow-2xs">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-650">
                                <thead>
                                    <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                                        <th class="px-5 py-3 w-1/2">Assessment Question</th>
                                        <th class="px-5 py-3">Applicant Passes If</th>
                                        <th class="px-5 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($serviceQuestions as $q)
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-5 py-3.5">
                                                @php
                                                    $locale = app()->getLocale();
                                                    $mainQuestion = $q->{'question_text_' . $locale} ?: $q->question_text_en;
                                                @endphp
                                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                                    <span class="text-xs font-bold text-slate-850 leading-snug">{{ $mainQuestion }}</span>
                                                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-full border {{ $q->is_required ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                                        {{ $q->is_required ? 'Required' : 'Optional' }}
                                                    </span>
                                                </div>
                                                <div class="text-[10px] text-slate-400 space-y-0.5">
                                                    @if($locale !== 'en' && $q->question_text_en)<p>EN: {{ $q->question_text_en }}</p>@endif
                                                    @if($locale !== 'ceb' && $q->question_text_ceb)<p>CEB: {{ $q->question_text_ceb }}</p>@endif
                                                    @if($locale !== 'fil' && $q->question_text_fil)<p>FIL: {{ $q->question_text_fil }}</p>@endif
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-lg border border-slate-200">
                                                    {{ $q->passing_rule_label }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('facilitator.eligibility.edit', $q) }}" class="inline-flex items-center text-xs font-extrabold text-red-700 hover:text-white uppercase tracking-wider bg-red-50 hover:bg-red-700 px-2.5 py-1 rounded-lg border border-red-100 transition-all shadow-3xs">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('facilitator.eligibility.destroy', $q) }}" method="POST" class="inline" id="delete-q-{{ $q->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="showConfirmModal('Are you sure you want to delete this eligibility question?', () => document.getElementById('delete-q-{{ $q->id }}').submit(), 'Delete Question')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Delete Question">
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
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-lg p-8 text-center text-slate-400 text-xs italic">
                    No assessment questions configured yet.
                </div>
            @endforelse
        </div>

        <!-- Add Question Form (4/12) -->
        <div class="lg:col-span-4 bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4 sticky top-4">
            <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                    <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                    Add Question
                </h3>
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-full border border-emerald-200 uppercase">
                    ✨ Translated for you
                </span>
            </div>

            <form action="{{ route('facilitator.eligibility.store') }}" method="POST" class="space-y-3.5">
                @csrf

                <div class="space-y-1">
                    <label for="service_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Service Program</label>
                    <select name="service_id" id="service_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-semibold cursor-pointer" required>
                        <option value="">-- Select Service --</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="question_text" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Question (write it in English)</label>
                    <textarea name="question_text" id="question_text" rows="2" placeholder="e.g. Is your monthly household income below 15,000 PHP?" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium" required></textarea>
                </div>

                @include('facilitator.eligibility._rule-fields', ['question' => null])

                <button type="submit" class="w-full py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-all flex items-center justify-center space-x-1.5 mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Save Question</span>
                </button>
            </form>
        </div>

    </div>

</div>

@endsection
