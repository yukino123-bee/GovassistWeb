@extends('layouts.facilitator')

@section('title', 'Process Application - GovAssist')

@section('page_title', 'Process Assistance Application')

@section('content')
<div class="space-y-5 max-w-6xl mx-auto">

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('facilitator.applications') }}" class="w-fit text-xs font-bold text-slate-600 hover:text-red-700 flex items-center space-x-1.5 border border-slate-200 px-3.5 py-2 bg-white rounded-md hover:bg-slate-50 transition-all shadow-3xs">
            <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Back to Applications</span>
        </a>

        <div class="flex items-center space-x-2">
            @if($checklist->status === 'pending')
                <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200 uppercase tracking-wider">Pending Review</span>
            @elseif($checklist->status === 'approved')
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200 uppercase tracking-wider">Approved</span>
            @else
                <span class="px-3 py-1 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200 uppercase tracking-wider">Rejected</span>
            @endif

            @if($checklist->application_type)
                <span class="px-3 py-1 text-xs font-extrabold uppercase tracking-wider rounded-full border {{ $checklist->application_type === 'renewal' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                    {{ ucfirst($checklist->application_type) }} Application
                </span>
            @endif
        </div>
    </div>

    <!-- 1. Application Program Header Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold text-red-700 uppercase tracking-widest block mb-1">Official Assistance Application</span>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-850 tracking-tight">{{ $checklist->service->name_en ?? 'N/A' }}</h1>
                <p class="text-xs text-slate-400 mt-1 font-semibold">{{ $checklist->service->name_ceb ?? '' }}</p>
            </div>
            <div class="text-xs text-slate-500 bg-slate-50 border border-slate-200 p-3 rounded-md flex items-center space-x-3 self-start md:self-auto">
                <div>
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block">Submitted On</span>
                    <span class="font-bold text-slate-700">{{ $checklist->created_at->format('F d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Resident Profile Details Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 sm:p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Resident Profile Details
            </h3>
            <a href="{{ route('facilitator.users.show', $checklist->user) }}" class="text-xs font-bold text-red-700 hover:text-red-800 uppercase tracking-wider">
                Full Profile &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 text-xs">
            <!-- Full Name -->
            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Full Name</span>
                <span class="font-bold text-slate-850 text-sm mt-0.5 block">{{ $checklist->user->name }}</span>
            </div>

            <!-- Email -->
            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Email Address</span>
                <span class="font-mono text-slate-700 mt-0.5 block truncate" title="{{ $checklist->user->email }}">{{ $checklist->user->email }}</span>
            </div>

            <!-- Contact -->
            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Contact Number</span>
                <span class="font-bold text-slate-800 mt-0.5 block">{{ $checklist->user->contact_number ?? 'Not provided' }}</span>
            </div>

            <!-- Civil Status -->
            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Civil Status</span>
                <span class="font-bold text-slate-800 mt-0.5 block capitalize">{{ $checklist->user->civil_status ?? 'Not provided' }}</span>
            </div>

            <!-- Complete Address -->
            <div class="sm:col-span-2 border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Complete Address</span>
                <span class="font-bold text-slate-800 mt-0.5 block">{{ $checklist->user->address ?? 'Not provided' }}</span>
            </div>

            <!-- Date of Birth -->
            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Date of Birth</span>
                <span class="font-bold text-slate-800 mt-0.5 block">{{ $checklist->user->dob ? \Carbon\Carbon::parse($checklist->user->dob)->format('F d, Y') : 'Not provided' }}</span>
            </div>

            <!-- Government ID -->
            <div class="border border-slate-200 bg-slate-50/60 p-3.5 rounded-md flex flex-col justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Valid ID Status</span>
                @if($checklist->user->valid_id_path)
                    <a href="{{ route('facilitator.users.valid_id', $checklist->user) }}" target="_blank" class="mt-1 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-extrabold uppercase tracking-wider border border-red-200 rounded-md hover:bg-red-700 hover:text-white transition-all text-center">
                        View Valid ID &rarr;
                    </a>
                @else
                    <span class="text-rose-600 font-extrabold block mt-1">Not Uploaded</span>
                @endif
            </div>
        </div>
    </div>

    <!-- 3. Submitted Checklist Documents Verification Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 sm:p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Submitted Checklist Documents
            </h3>
            <span class="text-xs font-extrabold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1 rounded-md">
                {{ $uploadedDocs->count() }} / {{ $requirements->count() }} Uploaded
            </span>
        </div>

        <form action="{{ route('facilitator.checklist_items.batch_update', $checklist->id) }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($uploadedDocs as $doc)
                    <div class="p-4 bg-slate-50/60 border border-slate-200 rounded-lg flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider leading-snug">{{ $doc->requirement->name_en }}</h4>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-semibold">{{ $doc->requirement->name_ceb }}</span>
                            </div>
                            
                            <!-- Document Status Badge -->
                            @if($doc->status === 'pending')
                                <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-full border border-amber-200 uppercase tracking-wider whitespace-nowrap">Pending</span>
                            @elseif($doc->status === 'approved')
                                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-full border border-emerald-200 uppercase tracking-wider whitespace-nowrap">Approved</span>
                            @else
                                <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-full border border-rose-200 uppercase tracking-wider whitespace-nowrap">Rejected</span>
                            @endif
                        </div>

                        <!-- Document file actions & status selector -->
                        <div class="pt-3 border-t border-slate-200 space-y-3">
                            @if($doc->file_path)
                                <div class="flex items-center space-x-2 text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-3 py-1.5 rounded-md text-xs font-extrabold uppercase tracking-wide">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Document Uploaded</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <button type="button" onclick="openDocumentWebview('{{ route('facilitator.checklist_items.document', $doc) }}', '{{ $doc->requirement->name_en }}')" class="w-full py-2 bg-red-700 hover:bg-red-800 text-white rounded-md text-xs font-extrabold uppercase tracking-wider shadow-xs transition-colors flex items-center justify-center space-x-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Preview</span>
                                    </button>

                                    <a href="{{ route('facilitator.checklist_items.document', $doc) }}" target="_blank" class="w-full py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-md text-xs font-extrabold uppercase tracking-wider transition-colors flex items-center justify-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        <span>New Tab</span>
                                    </a>
                                </div>

                                @if($doc->requirement->template && $doc->status !== 'approved')
                                    <button type="submit" form="auto-verify-form-{{ $doc->id }}" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-md text-xs font-extrabold shadow-sm transition-colors flex items-center justify-center space-x-1 uppercase tracking-wider" title="Read the document and check it against the template">
                                        <span>✨ Check Document Again</span>
                                    </button>
                                @endif

                                @if($doc->review_result)
                                    @php
                                        $reviewStyle = match ($doc->review_result) {
                                            'verified' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                                            'mismatch' => 'bg-rose-50 border-rose-200 text-rose-800',
                                            default => 'bg-amber-50 border-amber-200 text-amber-800',
                                        };
                                        $reviewLabel = match ($doc->review_result) {
                                            'verified' => 'Automatic check: looks correct',
                                            'mismatch' => 'Automatic check: possibly the wrong document',
                                            default => 'Automatic check: please look at this one',
                                        };
                                    @endphp
                                    <div class="border rounded-md px-3 py-2 text-xs {{ $reviewStyle }}">
                                        <span class="block font-extrabold uppercase tracking-wider text-[10px]">{{ $reviewLabel }}</span>
                                        @if($doc->review_note)<span class="block mt-0.5 leading-snug">{{ $doc->review_note }}</span>@endif
                                    </div>
                                @endif

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Set Item Status</label>
                                    <select name="statuses[{{ $doc->id }}]" class="w-full bg-white text-xs border border-slate-200 rounded-md px-3 py-2 text-slate-800 font-bold focus:outline-none focus:border-red-600 transition-all">
                                        <option value="pending" {{ $doc->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $doc->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $doc->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            @else
                                <div class="flex items-center space-x-2 text-slate-400 bg-slate-100 border border-slate-200 px-3 py-2 rounded-md text-xs font-bold uppercase tracking-wide select-none">
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>No File Uploaded</span>
                                </div>
                                <input type="hidden" name="statuses[{{ $doc->id }}]" value="pending">
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-8 text-center text-slate-400 text-xs italic">No checklist documents assigned to this service.</div>
                @endforelse
            </div>

            @if($uploadedDocs->isNotEmpty())
                <div class="flex justify-end pt-3 border-t border-slate-200">
                    <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase tracking-wider transition-all rounded-md shadow-xs flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Save All Document Statuses</span>
                    </button>
                </div>
            @endif
        </form>

        <!-- Hidden Auto-Verify Forms -->
        @foreach($uploadedDocs as $doc)
            @if($doc->file_path && $doc->requirement->template && $doc->status !== 'approved')
                <form id="auto-verify-form-{{ $doc->id }}" action="{{ route('facilitator.checklist_items.auto_verify', $doc->id) }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endif
        @endforeach
    </div>

    <!-- 4. Application Status Decision Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 sm:p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-200 pb-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Final Application Decision
            </h3>
        </div>

        @php
            $docCounts = $uploadedDocs->groupBy('status')->map->count();
            $missingDocs = $requirements->where('is_required', true)->filter(fn ($r) => ! optional($uploadedDocs->get($r->id))->file_path)->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs max-w-2xl" aria-label="Document review summary">
            <div class="border border-emerald-200 bg-emerald-50 rounded-md p-2.5"><span class="block text-[10px] font-bold uppercase text-emerald-700">&#10003; Verified</span><span class="text-lg font-bold">{{ $docCounts['approved'] ?? 0 }}</span></div>
            <div class="border border-amber-200 bg-amber-50 rounded-md p-2.5"><span class="block text-[10px] font-bold uppercase text-amber-700">&#9888; Awaiting review</span><span class="text-lg font-bold">{{ $uploadedDocs->where('status', 'pending')->whereNotNull('file_path')->count() }}</span></div>
            <div class="border border-rose-200 bg-rose-50 rounded-md p-2.5"><span class="block text-[10px] font-bold uppercase text-rose-700">&#10005; Rejected</span><span class="text-lg font-bold">{{ $docCounts['rejected'] ?? 0 }}</span></div>
            <div class="border border-slate-200 bg-slate-50 rounded-md p-2.5"><span class="block text-[10px] font-bold uppercase text-slate-600">Required, missing</span><span class="text-lg font-bold">{{ $missingDocs }}</span></div>
        </div>

        <form id="decision-form" action="{{ route('facilitator.applications.update_status', $checklist->id) }}" method="POST" class="space-y-4 max-w-2xl" data-pending-docs="{{ $uploadedDocs->where('status', 'pending')->whereNotNull('file_path')->count() }}" data-missing-docs="{{ $missingDocs }}">
            @csrf

            <div class="space-y-1.5">
                <label for="status" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Approval Status</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-bold cursor-pointer">
                    <option value="pending" {{ $checklist->status === 'pending' ? 'selected' : '' }}>Pending Review (Keep in Queue)</option>
                    <option value="approved" {{ $checklist->status === 'approved' ? 'selected' : '' }}>Approved (Eligible / Completed)</option>
                    <option value="rejected" {{ $checklist->status === 'rejected' ? 'selected' : '' }}>Rejected / Denied</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="remarks" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Remarks / Feedback to Resident <span id="remarks-required" class="text-rose-700 normal-case hidden">(required when rejecting)</span></label>
                <textarea name="remarks" id="remarks" rows="3" placeholder="Enter instructions or reason for rejection..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800 font-medium">{{ old('remarks', $checklist->remarks) }}</textarea>
            </div>

            <button type="submit" id="decision-submit" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white rounded-md font-extrabold text-xs uppercase tracking-wider shadow-xs transition-all flex items-center space-x-2">
                <span>Submit Final Decision</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>

        @if($nextPendingApplication)
            <a href="{{ route('facilitator.applications.show', $nextPendingApplication->id) }}" class="inline-flex text-xs font-bold text-red-700 hover:underline">Skip to next pending application &rarr;</a>
        @endif
    </div>

</div>

<!-- Document Webview Modal -->
<div id="document-webview-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4">
    <div class="bg-white rounded-lg border border-slate-200 max-w-4xl w-full h-[85vh] p-6 shadow-2xl flex flex-col justify-between relative overflow-hidden">
        <!-- Close Button -->
        <button type="button" onclick="closeDocumentWebview()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <div class="border-b border-slate-200 pb-3 mb-4 flex items-center space-x-2">
            <span class="w-2.5 h-2.5 bg-red-700 block"></span>
            <h3 id="modal-document-title" class="text-xs font-bold uppercase tracking-wider text-slate-800">
                Document Preview
            </h3>
        </div>

        <div class="flex-grow bg-slate-100 border border-slate-200 rounded-md relative overflow-hidden">
            <iframe id="modal-document-iframe" src="" class="w-full h-full border-none"></iframe>
        </div>
        
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closeDocumentWebview()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-extrabold uppercase tracking-wider rounded-md shadow-xs transition-colors">
                Close Preview
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('decision-form');
        if (!form) return;
        const statusEl = document.getElementById('status');
        const remarksEl = document.getElementById('remarks');
        const hint = document.getElementById('remarks-required');
        const sync = () => {
            const rejecting = statusEl.value === 'rejected';
            remarksEl.required = rejecting;
            hint.classList.toggle('hidden', !rejecting);
        };
        statusEl.addEventListener('change', sync);
        sync();

        form.addEventListener('submit', (e) => {
            if (form.dataset.confirmed === '1') return;
            const decision = statusEl.value;
            if (decision === 'pending') return;
            e.preventDefault();
            let message = 'Set this application to ' + decision.toUpperCase() + '? The resident will be notified by email/SMS.';
            if (decision === 'approved') {
                const pending = parseInt(form.dataset.pendingDocs, 10), missing = parseInt(form.dataset.missingDocs, 10);
                if (pending > 0) message += ' Warning: ' + pending + ' uploaded document(s) have not been reviewed.';
                if (missing > 0) message += ' Warning: ' + missing + ' required document(s) are missing.';
            }
            showConfirmModal(message, () => { form.dataset.confirmed = '1'; form.submit(); }, 'Confirm decision');
        });
    })();

    function openDocumentWebview(fileUrl, title) {
        const modal = document.getElementById('document-webview-modal');
        const iframe = document.getElementById('modal-document-iframe');
        const titleElem = document.getElementById('modal-document-title');
        
        if (modal && iframe && titleElem) {
            titleElem.innerText = title;
            iframe.src = fileUrl;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDocumentWebview() {
        const modal = document.getElementById('document-webview-modal');
        const iframe = document.getElementById('modal-document-iframe');
        
        if (modal && iframe) {
            modal.classList.add('hidden');
            iframe.src = '';
            document.body.classList.remove('overflow-hidden');
        }
    }
</script>
@endsection
