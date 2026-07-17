@extends('layouts.facilitator')

@section('title', 'Manage Inquiries - GovAssist')

@section('page_title', 'Citizen Inquiries & Helpdesk')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- List of Inquiries (2/3 width) -->
    <div class="lg:col-span-2 bg-white border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Submitted Inquiries
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/60">
                        <th class="px-6 py-3">Citizen</th>
                        <th class="px-6 py-3">Inquiry Text</th>
                        <th class="px-6 py-3">Service</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($inquiries as $inq)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <span class="font-bold text-slate-800 block">{{ $inq->user->name }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $inq->user->email }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-medium text-slate-700 max-w-xs truncate">
                                {{ $inq->inquiry_text }}
                            </td>
                            <td class="px-6 py-3.5">
                                {{ $inq->service ? $inq->service->name_en : 'General Inquiry' }}
                            </td>
                            <td class="px-6 py-3.5">
                                @if($inq->status === 'resolved')
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-none uppercase tracking-wider border border-emerald-200">Resolved</span>
                                @elseif($inq->status === 'in_progress')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-none uppercase tracking-wider border border-amber-200">In Progress</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-none uppercase tracking-wider border border-red-200">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <button onclick="selectInquiry({{ json_encode($inq->load('responses.responder')) }})" class="px-3 py-1.5 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold rounded-none uppercase tracking-wider shadow-sm transition-all">
                                    View & Reply
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium">No inquiries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reply Pane (1/3 width) -->
    <div id="reply-pane" class="bg-white border border-slate-200 shadow-sm h-fit hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
                Inquiry Thread
            </h3>
        </div>

        <div class="p-6 space-y-4">
            <!-- Inquiry message -->
            <div class="p-3 bg-slate-50 border border-slate-200">
                <span class="block text-[9px] font-extrabold text-red-700 uppercase tracking-widest mb-1" id="inq-sender">Citizen</span>
                <p class="text-xs text-slate-700 leading-relaxed font-medium" id="inq-text">Inquiry content goes here...</p>
            </div>

            <!-- Responses history -->
            <div class="space-y-2 max-h-52 overflow-y-auto p-2 bg-slate-50/50 border border-slate-100" id="inq-responses">
                <!-- Dynamically populated -->
            </div>

            <!-- Reply form -->
            <form id="reply-form" method="POST" class="space-y-3">
                @csrf
                <div class="space-y-1.5 relative">
                    <div class="flex items-center justify-between">
                        <label for="reply_message" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Your Reply</label>
                        <button type="button" id="btn-ai-draft" onclick="generateAIDraft()" class="flex items-center text-[9px] font-extrabold uppercase tracking-widest text-red-700 hover:text-red-800 transition-colors bg-red-50 hover:bg-red-100 px-2 py-1 rounded-none border border-red-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            AI Draft
                        </button>
                    </div>
                    <textarea name="message" id="reply_message" rows="4" placeholder="Type your response to the citizen..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
                    <div id="ai-loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
                        <span class="text-[10px] font-extrabold text-red-700 uppercase tracking-widest animate-pulse">Generating Draft...</span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-[10px] font-extrabold rounded-none uppercase tracking-widest shadow-sm transition-all">
                        Send Reply
                    </button>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider" id="inq-status-badge">Status</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Placeholder when no inquiry selected -->
    <div id="placeholder-pane" class="bg-white border border-slate-200 shadow-sm h-fit flex flex-col items-center justify-center py-16 text-center text-slate-400">
        <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1">No Inquiry Selected</h4>
        <p class="text-xs max-w-xs leading-relaxed">Select a citizen inquiry from the table to view the thread and send a reply.</p>
    </div>

</div>

<script>
    let currentInquiryId = null;

    function selectInquiry(inq) {
        currentInquiryId = inq.id;
        document.getElementById('placeholder-pane').classList.add('hidden');
        const replyPane = document.getElementById('reply-pane');
        replyPane.classList.remove('hidden');

        document.getElementById('inq-sender').innerText = inq.user.name + ' (' + inq.user.email + ')';
        document.getElementById('inq-text').innerText = inq.inquiry_text;

        const responsesContainer = document.getElementById('inq-responses');
        responsesContainer.innerHTML = '';

        if (inq.responses && inq.responses.length > 0) {
            inq.responses.forEach(resp => {
                const isCitizen = resp.responder.role === 'citizen';
                const div = document.createElement('div');
                div.className = 'p-2.5 border-l-2 ' + (isCitizen ? 'border-red-700 bg-red-50/20' : 'border-slate-400 bg-slate-100/50');
                div.innerHTML = `
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[9px] font-extrabold text-slate-600 uppercase tracking-wider">${resp.responder.name}</span>
                        <span class="text-[8px] text-slate-400">${new Date(resp.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                    <p class="text-xs text-slate-700">${resp.response_text || resp.requireent_text}</p>
                `;
                responsesContainer.appendChild(div);
            });
        } else {
            responsesContainer.innerHTML = '<p class="text-[10px] text-slate-400 italic text-center py-2">No replies yet.</p>';
        }

        const replyForm = document.getElementById('reply-form');
        replyForm.action = `/facilitator/inquiries/${inq.id}/reply`;

        const statusBadge = document.getElementById('inq-status-badge');
        statusBadge.innerText = 'Status: ' + inq.status.toUpperCase();
    }

    function generateAIDraft() {
        if (!currentInquiryId) return;
        
        const loader = document.getElementById('ai-loading');
        const textarea = document.getElementById('reply_message');
        loader.classList.remove('hidden');
        
        fetch(`/facilitator/inquiries/${currentInquiryId}/ai-draft`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.draft) {
                textarea.value = data.draft;
            }
        })
        .catch(err => {
            console.error('AI Draft Error:', err);
            alert('Failed to generate AI draft.');
        })
        .finally(() => {
            loader.classList.add('hidden');
        });
    }
</script>
@endsection
