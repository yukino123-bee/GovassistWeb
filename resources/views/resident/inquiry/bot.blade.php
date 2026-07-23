@extends('layouts.resident')

@section('title', __('messages.bot_title'))

@section('header_title', __('messages.bot_title'))

@section('content')
<!-- Main Container (ChatGPT style side-by-side view) -->
<div class="max-w-6xl mx-auto h-[calc(100vh-9rem)] flex flex-col md:flex-row gap-6 relative">

    <!-- Sidebar Backdrop for Mobile -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden md:hidden"></div>

    <!-- Sidebar: Inquiry History & Responses -->
    <div id="inquiry-sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200 transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:flex md:w-80 md:h-full flex flex-col shrink-0">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center">
                <svg class="w-4 h-4 mr-2 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Inquiry History
            </span>
            <!-- Close Button for Mobile -->
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-slate-600 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Sidebar Content -->
        <div class="flex-grow overflow-y-auto p-3 space-y-2 bg-slate-50/20">


            <!-- Contact Admin / New Inquiry Action Card -->
            <button type="button" onclick="resetToBotChat()" class="w-full flex items-center justify-center gap-2 bg-red-700 hover:bg-red-800 text-white px-4 py-3 transition-all text-xs font-extrabold uppercase tracking-wider focus:outline-none shadow-sm cursor-pointer mb-3">
                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>New Inquiry</span>
            </button>

            <div id="inquiries-list" class="space-y-2">
                @if(isset($inquiries) && $inquiries->count() > 0)
                    @foreach($inquiries as $inq)
                        <!-- Single Inquiry Item -->
                        <div class="border border-slate-200 p-3 bg-white hover:border-red-700 transition-all cursor-pointer group rounded-none" onclick="selectInquiryById({{ $inq->id }})">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-800 truncate max-w-[120px]">{{ $inq->service ? $inq->service->name_en : 'General Inquiry' }}</span>
                                <span class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 border bg-amber-50 text-amber-700 border-amber-200">
                                    {{ $inq->status }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-1 italic">"{{ $inq->inquiry_text }}"</p>
                        </div>
                    @endforeach
                @else
                    <div class="h-48 flex flex-col items-center justify-center text-center p-4">
                        <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">No Inquiries</span>
                        <p class="text-[10px] text-slate-400 mt-1 max-w-[160px]">Contact Admin to submit a request.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Chat Window -->
    <div class="flex-grow flex flex-col bg-white border border-slate-200 shadow-sm overflow-hidden h-full">
        <!-- Header of the Chat Assistant -->
        <div class="border-b border-slate-200 p-4 flex items-center justify-between bg-slate-50/50">
            <h3 id="chat-header-title" class="text-xs font-bold uppercase tracking-widest text-slate-800 flex items-center">
                <!-- Hamburger Button for Mobile Sidebar Toggle -->
                <button onclick="toggleSidebar()" class="md:hidden mr-3 p-1.5 -ml-1 text-slate-500 hover:text-slate-700 hover:bg-slate-200 transition-colors focus:outline-none" title="Toggle History">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                Inquiry Helpdesk
            </h3>
        </div>

        <!-- Chat Assistant Body -->
        <div id="chat-window" class="flex-grow p-6 overflow-y-auto space-y-4 flex flex-col bg-slate-50/50">
            <!-- Initial Greeting Message -->
            <div class="flex items-start space-x-3 max-w-[85%]">
                <div class="w-7 h-7 bg-red-700 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">
                    HD
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 p-4 text-xs leading-relaxed shadow-sm">
                    Hello! Select an inquiry from the history sidebar, or click "New Inquiry" to message our administrators.
                </div>
            </div>
        </div>

        <!-- Input area -->
        <form id="chat-form" class="border-t border-slate-200 p-4 flex items-center gap-2 bg-white">
            <div class="relative flex-grow flex">
                <input type="text" id="chat-input" placeholder="Type your message to start a new inquiry with admin..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800" required>
                <button type="submit" id="chat-submit-btn" class="px-4 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-[10px] flex items-center justify-center">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Custom Unsend Confirmation Modal -->
<div id="unsend-confirm-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in">
    <div class="bg-white border border-slate-200 max-w-sm w-full rounded-2xl shadow-2xl p-6 relative">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest mb-2 flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Unsend Message
        </h3>
        <p class="text-xs text-slate-500 leading-relaxed font-medium mb-5">
            Are you sure you want to unsend this message? This action is permanent and cannot be undone.
        </p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeUnsendModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="button" id="confirm-unsend-btn" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors cursor-pointer shadow-sm">
                Unsend
            </button>
        </div>
    </div>
</div>

<!-- Guest Info Modal -->
<div id="guest-info-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in">
    <div class="bg-white border border-slate-200 max-w-sm w-full rounded-2xl shadow-2xl p-6 relative">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest mb-2 flex items-center">
            <svg class="w-5 h-5 text-red-700 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Guest Information
        </h3>
        <p class="text-[11px] text-slate-500 leading-relaxed font-medium mb-4">
            Please enter your name and email to send your inquiry directly to our administrators.
        </p>
        <div class="space-y-3 mb-5">
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-400 block mb-1">Name</label>
                <input type="text" id="guest-name-input" placeholder="Your Name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800" required>
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-400 block mb-1">Email Address</label>
                <input type="email" id="guest-email-input" placeholder="yourname@example.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800" required>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeGuestModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="button" onclick="submitGuestInfo()" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl uppercase tracking-wider transition-colors cursor-pointer shadow-sm">
                Submit & Send
            </button>
        </div>
    </div>
</div>
<script>
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatWindow = document.getElementById('chat-window');
    const chatSubmitBtn = document.getElementById('chat-submit-btn');

    // Always read CSRF from meta tag — never use inline Blade token which can go stale
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const currentUserId = {{ Auth::id() ?: 'null' }};
    let currentGuestName = null;
    let currentGuestEmail = null;
    let activeInquiryId = null;

    const inquiriesMap = {};
    @if(isset($inquiries))
        @foreach($inquiries as $inq)
            inquiriesMap[{{ $inq->id }}] = {!! json_encode($inq->load(['service', 'responses.responder'])) !!};
        @endforeach
    @endif

    function selectInquiryById(id) {
        const inq = inquiriesMap[id];
        if (inq) selectInquiry(inq);
    }

    const defaultChatTitleHTML = `
        <!-- Hamburger Button for Mobile Sidebar Toggle -->
        <button onclick="toggleSidebar()" class="md:hidden mr-3 p-1.5 -ml-1 text-slate-500 hover:text-slate-700 hover:bg-slate-200 transition-colors focus:outline-none" title="Toggle History">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
        Inquiry Helpdesk
    `;
    const defaultChatWindowHTML = chatWindow.innerHTML;

    function toggleSidebar() {
        const sidebar = document.getElementById('inquiry-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && backdrop) {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    }

    let pollInterval = null;
    let lastResponseCount = -1;

    function startMessagePolling() {
        stopMessagePolling();
        pollInterval = setInterval(fetchLatestMessages, 3000);
    }

    function stopMessagePolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    function fetchLatestMessages() {
        if (!activeInquiryId) return;

        const email = currentGuestEmail;
        let url = `/resident/inquiry/${activeInquiryId}/messages`;
        if (currentUserId === null && email) {
            url += `?guest_email=${encodeURIComponent(email)}`;
        }

        fetch(url, {
            headers: {
                "Accept": "application/json"
            }
        })
        .then(res => res.ok ? res.json() : null)
        .then(data => {
            if (data && data.success && data.inquiry) {
                const updatedInq = data.inquiry;
                inquiriesMap[updatedInq.id] = updatedInq;
                renderInquiryThread(updatedInq);
            }
        })
        .catch(err => console.error("Poll error:", err));
    }

    function resetToBotChat() {
        stopMessagePolling();
        activeInquiryId = null;
        lastResponseCount = -1;
        const titleEl = document.getElementById('chat-header-title');
        if (titleEl) {
            titleEl.innerHTML = defaultChatTitleHTML;
        }
        chatWindow.innerHTML = defaultChatWindowHTML;
        scrollToBottom();

        if (chatInput) {
            chatInput.disabled = false;
            chatInput.placeholder = "Type your message to start a new inquiry with admin...";
        }
        if (chatSubmitBtn) chatSubmitBtn.disabled = false;

        const sidebar = document.getElementById('inquiry-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
            toggleSidebar();
        }
    }

    function selectInquiry(inq) {
        activeInquiryId = inq.id;
        renderInquiryThread(inq, true);
        startMessagePolling();

        const sidebar = document.getElementById('inquiry-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
            toggleSidebar();
        }
    }

    function renderInquiryThread(inq, force = false) {
        const respCount = (inq.responses || []).length;
        if (!force && activeInquiryId === inq.id && lastResponseCount === respCount) {
            return;
        }
        lastResponseCount = respCount;

        const titleEl = document.getElementById('chat-header-title');
        if (titleEl) {
            const programName = inq.service ? (inq.service.service_name || inq.service.name_en) : 'General Inquiry';
            titleEl.innerHTML = `
                <!-- Hamburger Button for Mobile Sidebar Toggle -->
                <button onclick="toggleSidebar()" class="md:hidden mr-3 p-1.5 -ml-1 text-slate-500 hover:text-slate-700 hover:bg-slate-200 transition-colors focus:outline-none" title="Toggle History">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                Inquiry #${inq.id} - ${programName}
            `;
        }

        chatWindow.innerHTML = '';
        appendMessage('user', inq.inquiry_text, '', '', inq.id, 'inquiry');

        let isFirstResponse = true;
        if (inq.responses && inq.responses.length > 0) {
            inq.responses.forEach(resp => {
                const respText = resp.response_text || resp.requireent_text || '';

                if (isFirstResponse && respText === inq.inquiry_text) {
                    const isByGuest = resp.responded_by === null;
                    const isByResident = resp.responder && resp.responder.role === 'resident';
                    if (isByGuest || isByResident) {
                        isFirstResponse = false;
                        return;
                    }
                }
                isFirstResponse = false;

                const isByMe = resp.responded_by === null || resp.responded_by === currentUserId || (resp.responder && resp.responder.role === 'resident');
                const isByAdmin = resp.responder && (resp.responder.role === 'facilitator' || resp.responder.role === 'admin');

                if (isByMe && !isByAdmin) {
                    appendMessage('user', respText, '', '', resp.id, 'reply');
                } else {
                    const responderName = resp.responder ? resp.responder.name : 'GovAssist Admin';
                    appendMessage('admin', respText, '', responderName);
                }
            });
        }

        scrollToBottom();

        if (chatInput) {
            chatInput.placeholder = "Type your reply...";
        }
    }

    function prependInquiryToSidebar(inq) {
        // Save the updated/created inquiry in inquiriesMap
        inquiriesMap[inq.id] = inq;

        const list = document.getElementById('inquiries-list');
        if (!list) return;

        // Find and remove any existing list item for this inquiry to keep history clean and avoid duplicate cards
        const existingItem = list.querySelector('[onclick*="selectInquiryById(' + inq.id + ')"]');
        if (existingItem) {
            existingItem.remove();
        }

        const noInquiriesEl = list.querySelector('.h-48');
        if (noInquiriesEl) {
            list.innerHTML = '';
        }

        const itemDiv = document.createElement('div');
        itemDiv.className = "border border-slate-200 p-3 bg-white hover:border-red-700 transition-all cursor-pointer group rounded-none";
        itemDiv.setAttribute('onclick', 'selectInquiryById(' + inq.id + ')');

        const serviceName = inq.service ? (inq.service.service_name || inq.service.name_en) : 'General Inquiry';
        itemDiv.innerHTML = `
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-slate-800 truncate max-w-[120px]">${serviceName}</span>
                <span class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 border bg-amber-50 text-amber-700 border-amber-200">
                    ${inq.status}
                </span>
            </div>
            <p class="text-[11px] text-slate-500 line-clamp-1 italic">"${inq.inquiry_text}"</p>
        `;
        
        list.insertBefore(itemDiv, list.firstChild);
    }

    let pendingMsg = '';

    }

    function closeGuestModal() {
        const modal = document.getElementById('guest-info-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openGuestModal(msg = null) {
        if (msg) {
            pendingMsg = msg;
        } else {
            pendingMsg = '';
        }
        const modal = document.getElementById('guest-info-modal');
        if (modal) {
            // Pre-fill from localStorage if exists
            const savedName = currentGuestName;
            const savedEmail = currentGuestEmail;
            if (savedName) document.getElementById('guest-name-input').value = savedName;
            if (savedEmail) document.getElementById('guest-email-input').value = savedEmail;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function submitGuestInfo() {
        const name = document.getElementById('guest-name-input').value.trim();
        const email = document.getElementById('guest-email-input').value.trim();

        if (!name || !email) {
            alert('Please fill in both fields.');
            return;
        }

        if (!email.includes('@')) {
            alert('Please enter a valid email address.');
            return;
        }

        currentGuestName = name;
        currentGuestEmail = email;

        closeGuestModal();

        if (pendingMsg) {
            appendMessage('user', pendingMsg);
            chatInput.value = '';
            scrollToBottom();
            sendManualInquiry(pendingMsg, name, email);
            pendingMsg = '';
        } else {
            // Editing guest info — reload so the server can load this guest's history
            window.location.reload();
        }
    }



    function sendManualInquiry(msg, guestName = null, guestEmail = null) {
        if (activeInquiryId) {
            // Replying to an existing inquiry thread
            const url = `/resident/inquiry/${activeInquiryId}/reply`;
            const bodyData = { message: msg };

            // Always include guest credentials for guest users
            const name = guestName || currentGuestName;
            const email = guestEmail || currentGuestEmail;
            if (currentUserId === null && name && email) {
                bodyData.guest_name = name;
                bodyData.guest_email = email;
            }

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken(),
                    "Accept": "application/json"
                },
                body: JSON.stringify(bodyData)
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(t => { throw new Error('Reply failed: ' + res.status + ' ' + t); });
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    fetchLatestMessages();
                } else {
                    console.error("Reply error:", data);
                    appendMessage('admin', "Failed to send reply. Please try again.");
                    scrollToBottom();
                }
            })
            .catch(err => {
                console.error("Chat error:", err);
                appendMessage('admin', "Connection error. Please try again.");
                scrollToBottom();
            });
        } else {
            // Creating a new inquiry thread
            const bodyData = { inquiry_text: msg };
            const name = guestName || currentGuestName;
            const email = guestEmail || currentGuestEmail;

            if (name && email) {
                bodyData.guest_name = name;
                bodyData.guest_email = email;
            }

            fetch("{{ route('resident.inquiry.manual') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken(),
                    "Accept": "application/json"
                },
                body: JSON.stringify(bodyData)
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(t => { throw new Error('Submit failed: ' + res.status + ' ' + t); });
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    activeInquiryId = data.inquiry.id;
                    inquiriesMap[data.inquiry.id] = data.inquiry;
                    prependInquiryToSidebar(data.inquiry);
                    renderInquiryThread(data.inquiry, true);
                    startMessagePolling();
                } else {
                    console.error("Inquiry error:", data);
                    appendMessage('admin', "Failed to send inquiry. Please try again.");
                    scrollToBottom();
                }
            })
            .catch(err => {
                console.error("Error creating manual inquiry:", err);
                appendMessage('admin', "Connection error. Please try again.");
                scrollToBottom();
            });
        }
    }

    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = chatInput.value.trim();
            if(!msg) return;

            if (currentUserId === null) {
                const savedName = currentGuestName;
                const savedEmail = currentGuestEmail;
                if (!savedName || !savedEmail) {
                    openGuestModal(msg);
                    return;
                } else {
                    appendMessage('user', msg);
                    chatInput.value = '';
                    scrollToBottom();
                    sendManualInquiry(msg, savedName, savedEmail);
                }
            } else {
                appendMessage('user', msg);
                chatInput.value = '';
                scrollToBottom();
                sendManualInquiry(msg);
            }
        });
    }

    let deleteTarget = null;

    function confirmUnsend(id, type) {
        deleteTarget = { id, type };
        const modal = document.getElementById('unsend-confirm-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeUnsendModal() {
        const modal = document.getElementById('unsend-confirm-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Bind event for custom unsend confirmation button
    const confirmUnsendBtn = document.getElementById('confirm-unsend-btn');
    if (confirmUnsendBtn) {
        confirmUnsendBtn.onclick = () => {
            if (!deleteTarget) return;

            const url = deleteTarget.type === 'inquiry'
                ? `/resident/inquiry/${deleteTarget.id}`
                : `/resident/inquiry/replies/${deleteTarget.id}`;

            fetch(url, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken(),
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeUnsendModal();
                    window.location.reload();
                }
            })
            .catch(err => console.error("Error unsending:", err));
        };
    }

    function appendMessage(sender, text, time = '', name = '', targetId = null, targetType = null) {
        const messageDiv = document.createElement('div');
        if (sender === 'user') {
            messageDiv.className = "flex items-start justify-end space-x-3 max-w-[85%] self-end group/msg relative";
            messageDiv.innerHTML = `
                <div class="flex flex-col items-end">
                    <div class="bg-red-700 text-white p-4 text-xs leading-relaxed font-semibold shadow-sm">
                        ${text}
                    </div>
                    ${(targetId && targetType) ? `
                        <button type="button" onclick="confirmUnsend(${targetId}, '${targetType}')" class="text-[9px] text-red-500 hover:text-red-700 font-bold uppercase tracking-wider mt-1 opacity-0 group-hover/msg:opacity-100 transition-opacity cursor-pointer">
                            Unsend
                        </button>
                    ` : ''}
                </div>
                <div class="w-7 h-7 bg-red-100 text-red-900 border border-red-200 flex items-center justify-center flex-shrink-0 text-[9px] font-bold">
                    ME
                </div>
            `;
        } else if (sender === 'admin') {
            messageDiv.className = "flex items-start space-x-3 max-w-[85%]";
            messageDiv.innerHTML = `
                <div class="w-7 h-7 bg-red-700 text-white flex items-center justify-center flex-shrink-0 text-[9px] font-bold">
                    AD
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 p-4 text-xs leading-relaxed shadow-sm">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">${name || 'GovAssist Admin'}</span>
                    ${text}
                    ${time ? `<span class="block text-[8px] text-slate-400 mt-2 text-right uppercase tracking-wider">${time}</span>` : ''}
                </div>
            `;
        } else {
            messageDiv.className = "flex items-start space-x-3 max-w-[85%]";
            messageDiv.innerHTML = `
                <div class="w-7 h-7 bg-red-700 text-white flex items-center justify-center flex-shrink-0 text-[9px] font-bold">
                    HD
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 p-4 text-xs leading-relaxed shadow-sm">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Inquiry Helpdesk</span>
                    ${text}
                    ${time ? `<span class="block text-[8px] text-slate-400 mt-2 text-right uppercase tracking-wider">${time}</span>` : ''}
                </div>
            `;
        }
        chatWindow.appendChild(messageDiv);
    }

    function scrollToBottom() {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    resetToBotChat();
</script>
@endsection
