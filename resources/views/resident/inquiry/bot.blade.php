@extends('layouts.resident')

@section('title', 'Municipal Inquiry & Ticket Helpdesk')

@section('header_title', 'Inquiry & Support Helpdesk')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Page Header & Action Banner -->
    <div class="bg-gradient-to-r from-red-800 to-red-900 rounded-2xl p-6 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-widest rounded-full">LGU Support Portal</span>
                <span class="text-xs text-red-200">Official Municipal Helpdesk</span>
            </div>
            <h2 class="text-xl font-extrabold mt-1.5 tracking-tight">Municipal Inquiry & Support Tickets</h2>
            <p class="text-xs text-red-100/90 mt-1 max-w-xl">
                Submit formal inquiries regarding municipal assistance programs, track official staff responses, and resolve questions efficiently.
            </p>
        </div>

        <button type="button" onclick="openNewTicketModal()" class="shrink-0 bg-white hover:bg-slate-100 text-red-800 font-extrabold text-xs px-5 py-3 rounded-xl uppercase tracking-wider transition-all shadow-md flex items-center space-x-2 cursor-pointer">
            <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Submit New Ticket</span>
        </button>
    </div>

    <!-- Main Workspace (Side-by-side Ticket List & Ticket Details) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Ticket History List (4 Cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[650px]">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Your Support Tickets
                </h3>
                <span class="text-[10px] font-bold text-slate-400">Total: {{ isset($inquiries) ? $inquiries->count() : 0 }}</span>
            </div>

            <!-- Filter Tabs -->
            <div class="flex border-b border-slate-100 text-[10px] font-bold text-slate-500 bg-slate-50/50">
                <button type="button" onclick="filterTickets('all')" id="tab-all" class="flex-1 py-2 text-center border-b-2 border-red-700 text-red-700 font-extrabold cursor-pointer">All</button>
                <button type="button" onclick="filterTickets('pending')" id="tab-pending" class="flex-1 py-2 text-center border-b-2 border-transparent hover:text-slate-700 cursor-pointer">Pending</button>
                <button type="button" onclick="filterTickets('resolved')" id="tab-resolved" class="flex-1 py-2 text-center border-b-2 border-transparent hover:text-slate-700 cursor-pointer">Resolved</button>
            </div>

            <!-- Ticket List Items -->
            <div class="flex-grow overflow-y-auto p-3 space-y-2 divide-y divide-slate-50">
                @if(isset($inquiries) && $inquiries->count() > 0)
                    @foreach($inquiries as $inq)
                        <div id="ticket-item-{{ $inq->id }}" 
                             data-status="{{ strtolower($inq->status) }}"
                             onclick="selectTicket({{ $inq->id }})" 
                             class="ticket-list-item pt-2 first:pt-0 p-3 rounded-xl border border-slate-100 hover:border-red-300 bg-white transition-all cursor-pointer group">
                            
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-[10px] font-extrabold text-slate-900 group-hover:text-red-700 transition-colors">
                                    {{ $inq->ticket_number ?? 'TKT-'.str_pad($inq->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                @if($inq->status === 'resolved')
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Resolved</span>
                                @elseif($inq->status === 'closed')
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase rounded-full bg-slate-100 text-slate-600 border border-slate-200">Closed</span>
                                @elseif($inq->status === 'in_progress')
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase rounded-full bg-blue-50 text-blue-700 border border-blue-200">In Progress</span>
                                @else
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase rounded-full bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                @endif
                            </div>

                            <h4 class="text-xs font-bold text-slate-800 line-clamp-1 group-hover:text-slate-900">
                                {{ $inq->subject ?: ($inq->service ? $inq->service->name_en : 'General Inquiry') }}
                            </h4>
                            
                            <div class="flex items-center justify-between mt-2 text-[10px] text-slate-400">
                                <span class="truncate max-w-[140px]">{{ $inq->service ? $inq->service->name_en : 'General' }}</span>
                                <span>{{ $inq->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-center p-6 space-y-2">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">No Support Tickets Yet</span>
                        <p class="text-[11px] text-slate-400 max-w-xs">Click "+ Submit New Ticket" above to send an official question to LGU staff.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Ticket Workspace & Discussion (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[650px]" id="ticket-workspace">
            
            <!-- Empty State when no ticket is selected -->
            <div id="ticket-empty-state" class="h-full flex flex-col items-center justify-center p-8 text-center bg-slate-50/50">
                <div class="w-16 h-16 bg-red-50 text-red-700 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                    </svg>
                </div>
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Select a Ticket or Open a New Request</h3>
                <p class="text-xs text-slate-500 max-w-md mt-1">
                    Select an inquiry ticket from the sidebar to view official LGU staff responses, or submit a new inquiry ticket for help.
                </p>

                <!-- Quick Common Questions Integration -->
                @if($services->isNotEmpty())
                    <div class="mt-8 text-left w-full max-w-lg bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block mb-2">Instant FAQ Answers</span>
                        <div class="space-y-1.5">
                            @foreach($services->take(3) as $service)
                                @foreach($service->commonQuestions->take(1) as $cq)
                                    <button type="button" onclick="openFaqModal('{{ addslashes($cq->question_text) }}', '{{ addslashes($cq->answer_text) }}')" class="w-full text-left px-3 py-2 bg-slate-50 hover:bg-red-50 hover:text-red-800 text-slate-700 text-xs rounded-lg transition-colors font-medium flex items-center justify-between cursor-pointer">
                                        <span class="truncate">Q: {{ $cq->question_text }}</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Active Ticket View (Hidden by default) -->
            <div id="ticket-detail-view" class="hidden flex-col h-full">
                <!-- Ticket Header Bar -->
                <div class="p-4 border-b border-slate-200 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3 shrink-0">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span id="detail-ticket-number" class="font-mono text-xs font-extrabold text-red-700 bg-red-50 border border-red-200 px-2.5 py-0.5 rounded-lg">TKT-00000</span>
                            <span id="detail-ticket-status" class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                        </div>
                        <h3 id="detail-ticket-subject" class="text-sm font-extrabold text-slate-900 mt-1">Inquiry Subject</h3>
                    </div>

                    <!-- Mark as Resolved Action -->
                    <form id="resolve-ticket-form" method="POST" class="inline">
                        @csrf
                        <button type="submit" id="resolve-ticket-btn" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg uppercase tracking-wider transition-colors shadow-xs flex items-center space-x-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Mark as Resolved</span>
                        </button>
                    </form>
                </div>

                <!-- Ticket Thread Messages Area -->
                <div id="detail-messages-area" class="flex-grow p-6 overflow-y-auto space-y-4 bg-slate-50/30">
                    <!-- Dynamic Messages Output -->
                </div>

                <!-- Bottom Input Form / Locked Banner -->
                <div class="border-t border-slate-200 bg-white p-4 shrink-0">
                    
                    <!-- Open Ticket: Reply Input Form -->
                    <form id="reply-ticket-form" action="{{ route('resident.inquiry.manual') }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="hidden" name="inquiry_id" id="reply-inquiry-id">
                        
                        <div class="flex items-center gap-2">
                            <input type="text" name="inquiry_text" id="reply-inquiry-input" placeholder="Type a follow-up message to municipal staff..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800" required>
                            <button type="submit" id="reply-submit-btn" class="px-5 py-3 bg-red-700 hover:bg-red-800 text-white font-extrabold uppercase tracking-wider text-xs rounded-xl shadow-sm cursor-pointer transition-colors shrink-0">
                                Send
                            </button>
                        </div>
                    </form>

                    <!-- Resolved / Closed Ticket Banner -->
                    <div id="ticket-locked-banner" class="hidden p-3.5 bg-slate-100 border border-slate-200 rounded-xl text-center space-y-1">
                        <div class="flex items-center justify-center space-x-2 text-slate-700 font-extrabold text-xs uppercase tracking-wider">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>This Support Ticket Has Been Resolved & Closed</span>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Further replies are locked to keep municipal records organized. Need help with another question? 
                            <button type="button" onclick="openNewTicketModal()" class="text-red-700 font-bold hover:underline cursor-pointer">Open a new inquiry ticket</button>.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Submit New Inquiry Ticket -->
<div id="new-ticket-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
    <div class="bg-white border border-slate-200 max-w-lg w-full rounded-2xl shadow-2xl p-6 relative animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <svg class="w-5 h-5 text-red-700 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Submit New Support Ticket
            </h3>
            <button type="button" onclick="closeNewTicketModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('resident.inquiry.manual') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="service_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Target Assistance Program (Optional)</label>
                <select name="service_id" id="service_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800 font-semibold">
                    <option value="">General Municipal Inquiry</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id }}">{{ $s->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="subject" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Inquiry Subject / Title</label>
                <input type="text" name="subject" id="subject" placeholder="e.g. Requirement deadline clarification for Medical Assistance" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800" required>
            </div>

            <div>
                <label for="inquiry_text" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Detailed Question / Message</label>
                <textarea name="inquiry_text" id="inquiry_text" rows="4" placeholder="Provide full details about your question so LGU facilitators can assist you accurately..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800" required></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 border-t border-slate-100 pt-4">
                <button type="button" onclick="closeNewTicketModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors shadow-sm cursor-pointer">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FAQ Modal -->
<div id="faq-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
    <div class="bg-white border border-slate-200 max-w-md w-full rounded-2xl shadow-2xl p-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">Instant FAQ Answer</h3>
            <button type="button" onclick="closeFaqModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <h4 id="faq-modal-q" class="text-xs font-extrabold text-red-700 mb-2"></h4>
        <div id="faq-modal-a" class="bg-slate-50 p-4 border border-slate-200 rounded-xl text-xs text-slate-800 leading-relaxed"></div>
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closeFaqModal()" class="px-4 py-2 bg-slate-800 text-white font-extrabold text-xs rounded-xl uppercase tracking-wider cursor-pointer">Close</button>
        </div>
    </div>
</div>

<script>
    const inquiriesData = {};
    @if(isset($inquiries))
        @foreach($inquiries as $inq)
            inquiriesData[{{ $inq->id }}] = {!! json_encode($inq->load(['service', 'responses.responder'])) !!};
        @endforeach
    @endif

    function filterTickets(status) {
        document.querySelectorAll('.ticket-list-item').forEach(item => {
            if (status === 'all' || item.dataset.status === status) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        // Toggle active tab style
        ['all', 'pending', 'resolved'].forEach(t => {
            const btn = document.getElementById('tab-' + t);
            if (t === status) {
                btn.className = 'flex-1 py-2 text-center border-b-2 border-red-700 text-red-700 font-extrabold cursor-pointer';
            } else {
                btn.className = 'flex-1 py-2 text-center border-b-2 border-transparent hover:text-slate-700 text-slate-500 font-bold cursor-pointer';
            }
        });
    }

    function selectTicket(id) {
        const ticket = inquiriesData[id];
        if (!ticket) return;

        document.getElementById('ticket-empty-state').classList.add('hidden');
        const detailView = document.getElementById('ticket-detail-view');
        detailView.classList.remove('hidden');
        detailView.classList.add('flex');

        // Populate Header Info
        document.getElementById('detail-ticket-number').textContent = ticket.ticket_number || ('TKT-' + String(ticket.id).padStart(5, '0'));
        document.getElementById('detail-ticket-subject').textContent = ticket.subject || (ticket.service ? ticket.service.name_en : 'General Inquiry');
        
        const statusBadge = document.getElementById('detail-ticket-status');
        const status = (ticket.status || 'pending').toLowerCase();
        if (status === 'resolved') {
            statusBadge.className = 'px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
            statusBadge.textContent = 'Resolved';
        } else if (status === 'closed') {
            statusBadge.className = 'px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-slate-100 text-slate-600 border border-slate-200';
            statusBadge.textContent = 'Closed';
        } else if (status === 'in_progress') {
            statusBadge.className = 'px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-blue-50 text-blue-700 border border-blue-200';
            statusBadge.textContent = 'In Progress';
        } else {
            statusBadge.className = 'px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-amber-50 text-amber-700 border border-amber-200';
            statusBadge.textContent = 'Pending';
        }

        // Set Resolve Form Action
        document.getElementById('resolve-ticket-form').action = '/resident/inquiry/' + ticket.id + '/resolve';
        document.getElementById('reply-inquiry-id').value = ticket.id;

        // Render Thread Messages
        const messagesArea = document.getElementById('detail-messages-area');
        messagesArea.innerHTML = '';

        // Original Resident Inquiry Entry
        const origBox = document.createElement('div');
        origBox.className = 'bg-white p-4 border border-slate-200 rounded-xl shadow-xs space-y-2';
        origBox.innerHTML = `
            <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                <span class="font-extrabold text-slate-800">Your Initial Question</span>
                <span class="text-[10px] text-slate-400 font-medium">${new Date(ticket.created_at).toLocaleString()}</span>
            </div>
            <p class="text-xs text-slate-700 leading-relaxed">${ticket.inquiry_text}</p>
        `;
        messagesArea.appendChild(origBox);

        // Staff Responses or Follow-ups
        if (ticket.responses && ticket.responses.length > 0) {
            ticket.responses.forEach(resp => {
                const isStaff = resp.responder && (resp.responder.role === 'facilitator' || resp.responder.role === 'admin');
                const respBox = document.createElement('div');
                
                if (isStaff) {
                    respBox.className = 'bg-red-50/60 border border-red-200 p-4 rounded-xl shadow-xs space-y-2 relative';
                    respBox.innerHTML = `
                        <div class="flex items-center justify-between text-xs border-b border-red-100 pb-2">
                            <span class="font-extrabold text-red-900 flex items-center">
                                <span class="w-2 h-2 bg-red-600 rounded-full mr-2"></span>
                                Official LGU Staff Response (${resp.responder ? resp.responder.name : 'Facilitator'})
                            </span>
                            <span class="text-[10px] text-red-700 font-semibold">${new Date(resp.created_at).toLocaleString()}</span>
                        </div>
                        <p class="text-xs text-slate-800 leading-relaxed">${resp.requireent_text}</p>
                    `;
                } else {
                    respBox.className = 'bg-white p-4 border border-slate-200 rounded-xl shadow-xs space-y-2 ml-4';
                    respBox.innerHTML = `
                        <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                            <span class="font-bold text-slate-700">Follow-up Message</span>
                            <span class="text-[10px] text-slate-400">${new Date(resp.created_at).toLocaleString()}</span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed">${resp.requireent_text}</p>
                    `;
                }
                messagesArea.appendChild(respBox);
            });
        } else {
            const waitingNotice = document.createElement('div');
            waitingNotice.className = 'p-4 bg-amber-50/80 border border-amber-200 rounded-xl text-center space-y-1';
            waitingNotice.innerHTML = `
                <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block">Awaiting LGU Staff Review</span>
                <p class="text-[11px] text-amber-700">Your ticket is queued for facilitator review. You will receive an SMS alert as soon as an official answer is posted.</p>
            `;
            messagesArea.appendChild(waitingNotice);
        }

        // Handle Locked State when Resolved or Closed
        const replyForm = document.getElementById('reply-ticket-form');
        const lockedBanner = document.getElementById('ticket-locked-banner');
        const resolveBtn = document.getElementById('resolve-ticket-btn');

        if (status === 'resolved' || status === 'closed') {
            replyForm.classList.add('hidden');
            lockedBanner.classList.remove('hidden');
            if (resolveBtn) resolveBtn.classList.add('hidden');
        } else {
            replyForm.classList.remove('hidden');
            lockedBanner.classList.add('hidden');
            if (resolveBtn) resolveBtn.classList.remove('hidden');
        }
    }

    function openNewTicketModal() {
        document.getElementById('new-ticket-modal').classList.remove('hidden');
    }

    function closeNewTicketModal() {
        document.getElementById('new-ticket-modal').classList.add('hidden');
    }

    function openFaqModal(q, a) {
        document.getElementById('faq-modal-q').textContent = 'Q: ' + q;
        document.getElementById('faq-modal-a').textContent = a;
        document.getElementById('faq-modal').classList.remove('hidden');
    }

    function closeFaqModal() {
        document.getElementById('faq-modal').classList.add('hidden');
    }
</script>
@endsection
