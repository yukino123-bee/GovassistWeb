@extends('layouts.facilitator')

@section('title', 'Manage Inquiries - GovAssist')

@section('page_title', 'Resident Inquiries & Helpdesk')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- List of Inquiries (5/12 width - Messenger-like Chat List) -->
    <div class="lg:col-span-5 space-y-3 max-h-[600px] overflow-y-auto pr-1 no-scrollbar">
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-4 sticky top-0 z-10 mb-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2 block animate-pulse"></span>
                Resident Inbox
            </h3>
        </div>

        <div class="space-y-2.5">
            @forelse($inquiries as $inq)
                @php
                    $isPending = $inq->status === 'pending';
                    $isInProgress = $inq->status === 'in_progress';
                    $isResolved = $inq->status === 'resolved';
                    
                    $senderName = $inq->user ? $inq->user->name : $inq->guest_name;
                    $senderEmail = $inq->user ? $inq->user->email : $inq->guest_email;
                    $initial = strtoupper(substr($senderName, 0, 1));
                @endphp
                <div onclick="selectInquiry(this, {{ json_encode($inq->load(['user.checklists.service', 'user.inquiries', 'responses.responder'])) }})" 
                     id="inquiry-card-{{ $inq->id }}" 
                     class="inquiry-card group flex items-start space-x-3.5 p-4 bg-white border border-slate-200 hover:border-red-200 hover:bg-red-50/10 rounded-2xl cursor-pointer transition-all duration-200 shadow-3xs relative">
                    
                    <!-- Left Avatar with status indicator dot -->
                    <div class="relative shrink-0">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-sm shadow-3xs group-hover:bg-red-50 group-hover:text-red-700 group-hover:border-red-100 transition-colors">
                            {{ $initial }}
                        </div>
                        @if($isPending)
                            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-red-600 rounded-full ring-2 ring-white"></span>
                        @elseif($isInProgress)
                            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-amber-500 rounded-full ring-2 ring-white"></span>
                        @else
                            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white"></span>
                        @endif
                    </div>

                    <!-- Middle Content Area -->
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-800 text-xs truncate group-hover:text-red-700 transition-colors">{{ $senderName }}</span>
                            <span class="text-xs text-slate-400 font-medium whitespace-nowrap ml-2">
                                {{ $inq->created_at ? $inq->created_at->diffForHumans(null, true) : '' }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1.5 flex-wrap gap-y-1">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-750 text-xs font-black border border-blue-100 rounded-lg">Helpdesk</span>
                            <span class="text-xs text-slate-500 font-semibold truncate max-w-[140px]">
                                {{ $inq->service ? $inq->service->name_en : 'General Inquiry' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed font-normal">
                            {{ $inq->inquiry_text }}
                        </p>
                    </div>

                    <!-- Status Text Pill on Right -->
                    <div class="shrink-0 self-center pl-2">
                        @if($isPending)
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 text-xs font-black border border-red-200 rounded-lg uppercase tracking-wider">Pending</span>
                        @elseif($isInProgress)
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-black border border-amber-200 rounded-lg uppercase tracking-wider">Active</span>
                        @else
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-black border border-emerald-200 rounded-lg uppercase tracking-wider">Done</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white border border-slate-200 rounded-2xl text-slate-450 italic text-xs shadow-3xs">No inquiries found.</div>
            @endforelse
        </div>
    </div>

    <!-- Active Chat Conversation Window (7/12 width - Messenger-like Chat Thread) -->
    <div class="lg:col-span-7">
        
        <!-- Reply Pane (Messenger-like Conversation Thread) -->
        <div id="reply-pane" class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden min-h-[600px] flex flex-col justify-between hidden">
            <!-- Header bar displaying sender details with Clickable User Profile trigger -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center space-x-3 cursor-pointer group" id="thread-user-header" onclick="openActiveUserProfile()">
                    <div class="w-10 h-10 rounded-full bg-red-700 text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:bg-red-800 transition-colors" id="thread-avatar">
                        C
                    </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest block group-hover:text-red-700 transition-colors" id="inq-sender">Resident</h3>
                                <span class="text-[9px] bg-red-50 text-red-700 px-2 py-0.5 rounded-full font-bold border border-red-100 group-hover:bg-red-700 group-hover:text-white transition-all">View Profile ↗</span>
                            </div>
                            <p class="text-[10px] text-slate-400 font-semibold truncate" id="inq-sub-text">resident@example.com</p>
                        </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-xs font-black text-slate-450 uppercase tracking-widest" id="inq-status-badge">Status</span>
                    
                    <!-- Delete Chat Form/Button -->
                    <form id="delete-chat-form" method="POST" class="inline-flex items-center">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="openDeleteConfirmModal()" class="p-1.5 bg-red-50 hover:bg-red-700 text-red-700 hover:text-white border border-red-200 hover:border-red-700 transition-all rounded-lg cursor-pointer" title="Delete Conversation">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Scrollable Message Bubble Area -->
            <div class="flex-grow overflow-y-auto p-6 space-y-4 max-h-[360px] bg-slate-50/30" id="thread-message-list">
                
                <!-- Original inquiry message (styled as user bubble on the left) -->
                <div class="flex items-start space-x-3 max-w-[85%]">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-3xs cursor-pointer hover:bg-red-100 hover:text-red-700 transition-colors" id="sender-circle-avatar" onclick="openActiveUserProfile()">
                        C
                    </div>
                    <div class="bg-white border border-slate-200 text-slate-800 p-3.5 rounded-2xl rounded-tl-none shadow-3xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider" id="sender-bubble-name">CITIZEN</span>
                        </div>
                        <p class="text-xs leading-relaxed font-medium" id="inq-text">Inquiry text goes here...</p>
                    </div>
                </div>

                <!-- Responses history loaded dynamically -->
                <div class="space-y-4" id="inq-responses">
                    <!-- Dynamically populated -->
                </div>
            </div>

            <!-- Footer area containing reply form -->
            <div class="p-4 border-t border-slate-100 bg-white">
                <!-- Reply Form -->
                <div id="reply-form-container">
                    <form id="reply-form" method="POST" class="space-y-3">
                        @csrf
                        <div class="relative flex items-center">
                            <textarea name="message" id="reply_message" rows="2" placeholder="Type your response here..." class="w-full pl-4 pr-16 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-650 transition-all text-xs text-slate-800 rounded-xl resize-none shadow-inner" required></textarea>
                            <button type="submit" class="absolute right-2 px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold rounded-lg uppercase tracking-widest shadow-xs transition-all">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Placeholder when no inquiry selected -->
        <div id="placeholder-pane" class="bg-white border border-slate-200 shadow-sm min-h-[600px] flex flex-col items-center justify-center py-16 text-center text-slate-400 rounded-2xl overflow-hidden">
            <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1">No Inquiry Selected</h4>
            <p class="text-xs max-w-xs leading-relaxed">Select a resident inquiry from the inbox on the left to view the thread, view user details, and send a reply.</p>
        </div>

    </div>

</div>

<!-- User Profile Inspection Modal -->
<div id="resident-profile-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white border border-slate-200 max-w-lg w-full rounded-2xl shadow-2xl overflow-hidden transform transition-all relative">
        
        <!-- Header banner -->
        <div class="bg-red-700 text-white px-6 py-5 flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-full bg-white text-red-700 flex items-center justify-center font-black text-lg shadow-sm" id="modal-user-avatar">
                    C
                </div>
                <div>
                    <h3 class="text-sm font-extrabold uppercase tracking-wide leading-tight text-white" id="modal-user-name">Resident Profile</h3>
                    <p class="text-xs text-red-200 font-semibold" id="modal-user-email">email@example.com</p>
                </div>
            </div>
            <button type="button" onclick="closeResidentProfileModal()" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Profile Details Body -->
        <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
            
            <!-- Account Overview Cards -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] font-black uppercase text-slate-400 block tracking-wider mb-0.5">Account Role</span>
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wide" id="modal-user-role">Registered Resident</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] font-black uppercase text-slate-400 block tracking-wider mb-0.5">Valid ID Verification</span>
                    <span class="text-xs font-bold text-slate-800" id="modal-user-id-status">Not Uploaded</span>
                </div>
            </div>

            <!-- Contact & Personal Details -->
            <div class="border border-slate-200 rounded-xl p-4 space-y-3 bg-white shadow-3xs">
                <h4 class="text-xs font-extrabold uppercase tracking-widest text-slate-700 flex items-center">
                    <span class="w-2 h-2 bg-red-700 mr-2 rounded-full"></span>
                    Personal Information
                </h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-1">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Contact Number</span>
                        <span class="font-extrabold text-slate-800" id="modal-user-phone">N/A</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Civil Status</span>
                        <span class="font-extrabold text-slate-800" id="modal-user-civil">N/A</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Date of Birth</span>
                        <span class="font-extrabold text-slate-800" id="modal-user-dob">N/A</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Total Inquiries</span>
                        <span class="font-extrabold text-slate-800" id="modal-user-inquiry-count">0</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Complete Address</span>
                        <span class="font-semibold text-slate-800" id="modal-user-address">N/A</span>
                    </div>
                </div>
            </div>

            <!-- Submitted Assistance Applications -->
            <div class="border border-slate-200 rounded-xl p-4 space-y-3 bg-white shadow-3xs">
                <h4 class="text-xs font-extrabold uppercase tracking-widest text-slate-700 flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-red-700 mr-2 rounded-full"></span>
                        Submitted Assistance Applications
                    </span>
                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold" id="modal-user-app-count">0</span>
                </h4>
                <div class="space-y-2 pt-1" id="modal-user-applications-list">
                    <p class="text-xs text-slate-400 italic">No assistance applications submitted yet.</p>
                </div>
            </div>

        </div>

        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeResidentProfileModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors">
                Close Profile
            </button>
        </div>

    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div id="delete-confirm-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in">
    <div class="bg-white border border-slate-200 max-w-sm w-full rounded-2xl shadow-2xl p-6 relative">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest mb-2 flex items-center">
            <svg class="w-5 h-5 text-red-650 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Confirm Deletion
        </h3>
        <p class="text-xs text-slate-500 leading-relaxed font-medium mb-5">
            Are you sure you want to delete this chat conversation? This action is permanent and cannot be undone.
        </p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeDeleteConfirmModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="button" onclick="executeChatDeletion()" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors cursor-pointer shadow-sm">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
    let activeInquiryData = null;
    let adminPollInterval = null;
    let lastAdminResponseCount = -1;

    function startAdminMessagePolling() {
        stopAdminMessagePolling();
        adminPollInterval = setInterval(fetchLatestAdminMessages, 3000);
    }

    function stopAdminMessagePolling() {
        if (adminPollInterval) {
            clearInterval(adminPollInterval);
            adminPollInterval = null;
        }
    }

    function fetchLatestAdminMessages() {
        if (!activeInquiryData) return;

        fetch(`/facilitator/inquiries/${activeInquiryData.id}/messages`, {
            headers: {
                "Accept": "application/json"
            }
        })
        .then(res => res.ok ? res.json() : null)
        .then(data => {
            if (data && data.success && data.inquiry) {
                activeInquiryData = data.inquiry;
                selectInquiry(null, data.inquiry);
            }
        })
        .catch(err => console.error("Admin poll error:", err));
    }

    function selectInquiry(cardElement, inq, force = false) {
        activeInquiryData = inq;
        startAdminMessagePolling();

        const respCount = (inq.responses || []).length;
        const isNewMessage = force || lastAdminResponseCount !== respCount;
        lastAdminResponseCount = respCount;
        
        // Remove active styling from all inbox cards
        if (cardElement) {
            document.querySelectorAll('.inquiry-card').forEach(el => {
                el.classList.remove('bg-red-50/30', 'border-red-300/80', 'ring-2', 'ring-red-500/5');
                el.classList.add('bg-white', 'border-slate-200');
            });
            
            // Add active styling to selected inbox card
            cardElement.classList.remove('bg-white', 'border-slate-200');
            cardElement.classList.add('bg-red-50/30', 'border-red-300/80', 'ring-2', 'ring-red-500/5');
        }

        const placeholderPane = document.getElementById('placeholder-pane');
        if (placeholderPane) placeholderPane.classList.add('hidden');

        const replyPane = document.getElementById('reply-pane');
        if (replyPane) replyPane.classList.remove('hidden');

        const senderName = inq.user ? inq.user.name : (inq.guest_name || 'Guest Resident');
        const senderEmail = inq.user ? inq.user.email : (inq.guest_email || 'No email provided');
        const initial = senderName.substring(0, 1).toUpperCase();
        
        // Update delete action URL dynamically
        const deleteForm = document.getElementById('delete-chat-form');
        if (deleteForm) {
            deleteForm.action = `/facilitator/inquiries/${inq.id}`;
        }

        // Update header & avatar
        const inqSenderEl = document.getElementById('inq-sender');
        if (inqSenderEl) inqSenderEl.innerText = senderName;

        const inqSubTextEl = document.getElementById('inq-sub-text');
        if (inqSubTextEl) inqSubTextEl.innerText = senderEmail;

        const threadAvatarEl = document.getElementById('thread-avatar');
        if (threadAvatarEl) threadAvatarEl.innerText = initial;

        const circleAvatarEl = document.getElementById('sender-circle-avatar');
        if (circleAvatarEl) circleAvatarEl.innerText = initial;

        const bubbleNameEl = document.getElementById('sender-bubble-name');
        if (bubbleNameEl) bubbleNameEl.innerText = senderName.toUpperCase();

        const inqTextEl = document.getElementById('inq-text');
        if (inqTextEl) inqTextEl.innerText = inq.inquiry_text;

        if (isNewMessage) {
            const responsesContainer = document.getElementById('inq-responses');
            if (responsesContainer) {
                responsesContainer.innerHTML = '';

                let isFirstResponse = true;
                if (inq.responses && inq.responses.length > 0) {
                    inq.responses.forEach(resp => {
                        const respContent = resp.response_text || resp.requireent_text || resp.requirement_text || '';

                        if (isFirstResponse && respContent === inq.inquiry_text) {
                            const isByGuest = resp.responded_by === null;
                            const isByResident = resp.responder && resp.responder.role === 'resident';
                            if (isByGuest || isByResident) {
                                isFirstResponse = false;
                                return;
                            }
                        }
                        isFirstResponse = false;

                        const isResidentOrGuest = !resp.responded_by || (resp.responder && resp.responder.role === 'resident');
                        const isFacilitator = resp.responder && (resp.responder.role === 'facilitator' || resp.responder.role === 'admin');

                        const messageDiv = document.createElement('div');

                        if (isResidentOrGuest && !isFacilitator) {
                            const residentName = inq.user ? inq.user.name : (inq.guest_name || 'Resident');
                            const residentInitial = residentName.substring(0, 1).toUpperCase();
                            messageDiv.className = 'flex items-start space-x-3 max-w-[85%]';
                            messageDiv.innerHTML = `
                                <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-3xs cursor-pointer hover:bg-red-100 transition-colors" onclick="openActiveUserProfile()">
                                    ${residentInitial}
                                </div>
                                <div class="bg-white border border-slate-200 text-slate-800 p-3.5 rounded-2xl rounded-tl-none shadow-3xs">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">${residentName}</span>
                                        <span class="text-[8px] text-slate-400 ml-2">${new Date(resp.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                                    </div>
                                    <p class="text-xs leading-relaxed font-medium">${respContent}</p>
                                </div>
                            `;
                        } else {
                            const adminName = resp.responder ? resp.responder.name : 'GovAssist Admin';
                            const adminInitial = adminName.substring(0, 1).toUpperCase();
                            messageDiv.className = 'flex items-start justify-end space-x-3 max-w-[85%] ml-auto';
                            messageDiv.innerHTML = `
                                <div class="bg-red-700 text-white p-3.5 rounded-2xl rounded-tr-none shadow-3xs">
                                    <div class="flex items-center justify-between mb-1 opacity-80">
                                        <span class="text-[9px] font-black text-red-200 uppercase tracking-wider">${adminName}</span>
                                        <span class="text-[8px] text-red-200 ml-2">${new Date(resp.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                                    </div>
                                    <p class="text-xs leading-relaxed font-medium">${respContent}</p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-red-50 text-red-700 border border-red-100 flex items-center justify-center font-bold text-xs shrink-0 shadow-3xs">
                                    ${adminInitial}
                                </div>
                            `;
                        }
                        responsesContainer.appendChild(messageDiv);
                    });
                }
            }

            setTimeout(() => {
                const listContainer = document.getElementById('thread-message-list');
                if (listContainer) {
                    listContainer.scrollTop = listContainer.scrollHeight;
                }
            }, 100);
        }

        const replyFormContainer = document.getElementById('reply-form-container');
        if (replyFormContainer) {
            replyFormContainer.classList.remove('hidden');
            const replyForm = document.getElementById('reply-form');
            if (replyForm) {
                replyForm.action = `/facilitator/inquiries/${inq.id}/reply`;
            }
        }

        const statusBadge = document.getElementById('inq-status-badge');
        if (statusBadge) {
            statusBadge.innerText = 'STATUS: ' + (inq.status || 'PENDING').toUpperCase();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const replyForm = document.getElementById('reply-form');
        if (replyForm) {
            replyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!activeInquiryData) return;

                const textarea = document.getElementById('reply_message');
                const message = textarea ? textarea.value.trim() : '';
                if (!message) return;

                const submitBtn = replyForm.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                fetch(`/facilitator/inquiries/${activeInquiryData.id}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(res => res.json())
                .then(data => {
                    if (submitBtn) submitBtn.disabled = false;
                    if (data.success && data.inquiry) {
                        if (textarea) textarea.value = '';
                        activeInquiryData = data.inquiry;
                        selectInquiry(null, data.inquiry, true);
                    }
                })
                .catch(err => {
                    if (submitBtn) submitBtn.disabled = false;
                    console.error("Reply submit error:", err);
                });
            });
        }
    });

    function openActiveUserProfile() {
        if (!activeInquiryData) return;
        const user = activeInquiryData.user;
        const modal = document.getElementById('resident-profile-modal');
        if (!modal) return;

        const name = user ? user.name : (activeInquiryData.guest_name || 'Guest Resident');
        const email = user ? user.email : (activeInquiryData.guest_email || 'N/A');
        const initial = name.substring(0, 1).toUpperCase();

        document.getElementById('modal-user-avatar').innerText = initial;
        document.getElementById('modal-user-name').innerText = name;
        document.getElementById('modal-user-email').innerText = email;
        document.getElementById('modal-user-role').innerText = user ? (user.role ? user.role.toUpperCase() : 'REGISTERED CITIZEN') : 'GUEST CITIZEN';
        
        document.getElementById('modal-user-phone').innerText = user && user.contact_number ? user.contact_number : 'N/A';
        document.getElementById('modal-user-civil').innerText = user && user.civil_status ? user.civil_status : 'N/A';
        document.getElementById('modal-user-dob').innerText = user && user.dob ? new Date(user.dob).toLocaleDateString() : 'N/A';
        document.getElementById('modal-user-address').innerText = user && user.address ? user.address : 'N/A';

        // Valid ID status
        const idStatusEl = document.getElementById('modal-user-id-status');
        if (user && user.valid_id_path) {
            idStatusEl.innerText = 'Verified / Uploaded';
            idStatusEl.className = 'text-xs font-extrabold text-emerald-600 uppercase tracking-wide';
        } else {
            idStatusEl.innerText = 'Not Uploaded';
            idStatusEl.className = 'text-xs font-extrabold text-amber-600 uppercase tracking-wide';
        }

        // Inquiries count
        const inquiryCount = user && user.inquiries ? user.inquiries.length : 1;
        document.getElementById('modal-user-inquiry-count').innerText = inquiryCount;

        // Applications list
        const appListEl = document.getElementById('modal-user-applications-list');
        const appCountEl = document.getElementById('modal-user-app-count');
        appListEl.innerHTML = '';

        if (user && user.checklists && user.checklists.length > 0) {
            appCountEl.innerText = user.checklists.length;
            user.checklists.forEach(app => {
                const svcName = app.service ? app.service.name_en : 'Government Service';
                const statusColor = app.status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : (app.status === 'rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                
                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs';
                itemDiv.innerHTML = `
                    <span class="font-bold text-slate-800">${svcName}</span>
                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase border rounded-lg ${statusColor}">${app.status || 'Pending'}</span>
                `;
                appListEl.appendChild(itemDiv);
            });
        } else {
            appCountEl.innerText = '0';
            appListEl.innerHTML = '<p class="text-xs text-slate-400 italic">No assistance applications submitted yet.</p>';
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeResidentProfileModal() {
        const modal = document.getElementById('resident-profile-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openDeleteConfirmModal() {
        const modal = document.getElementById('delete-confirm-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDeleteConfirmModal() {
        const modal = document.getElementById('delete-confirm-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function executeChatDeletion() {
        const form = document.getElementById('delete-chat-form');
        if (form) {
            form.submit();
        }
    }
</script>
@endsection
