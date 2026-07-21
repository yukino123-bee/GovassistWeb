@extends('layouts.citizen')

@section('title', __('messages.bot_title'))

@section('header_title', __('messages.bot_title'))

@section('content')
<!-- Main Container (Full-width bot view) -->
<div class="max-w-4xl mx-auto h-[calc(100vh-9rem)] flex flex-col">

    <!-- Header of the Chat Assistant -->
    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
            Inquiry Assistance (GovBot)
        </h3>

        <!-- Button/Icon to trigger the Admin contact form modal -->
        <button type="button" onclick="openAdminContactModal()" class="flex items-center space-x-2 bg-red-700 hover:bg-red-800 text-white px-3.5 py-2 transition-all text-[10px] font-extrabold uppercase tracking-wider focus:outline-none shadow-sm cursor-pointer" title="{{ __('messages.contact_admin') }}">
            <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Contact Admin</span>
        </button>
    </div>

    <!-- Chat Assistant Body -->
    <div class="flex-grow flex flex-col bg-white border border-slate-200 shadow-sm mt-3 overflow-hidden">
        <!-- Messages view -->
        <div id="chat-window" class="flex-grow p-6 overflow-y-auto space-y-4 flex flex-col bg-slate-50/50">
            <!-- Bot Initial Message -->
            <div class="flex items-start space-x-3 max-w-[85%]">
                <div class="w-7 h-7 bg-red-700 text-white flex items-center justify-center flex-shrink-0 text-[10px] font-bold">
                    GB
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 p-4 text-xs leading-relaxed shadow-sm">
                    {{ __('messages.bot_greeting') }}
                </div>
            </div>
        </div>

        <!-- Input area -->
        <form id="chat-form" class="border-t border-slate-200 p-4 flex items-center gap-2 bg-white">
            <!-- Voice button -->
            <button type="button" id="voice-btn" class="p-3 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 flex-shrink-0 transition-colors flex items-center justify-center">
                <svg id="mic-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                </svg>
                <div id="mic-pulse" class="w-2.5 h-2.5 bg-red-700 rounded-full animate-ping hidden"></div>
            </button>

            <div class="relative flex-grow flex">
                <input type="text" id="chat-input" placeholder="{{ __('messages.bot_placeholder') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-red-700 transition-all text-xs text-slate-800" required>
                <button type="submit" class="px-4 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-[10px] flex items-center justify-center">
                    Send
                </button>
            </div>
        </form>
    </div>

    @if(isset($inquiries) && $inquiries->count() > 0)
        <div class="mt-6 bg-white border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-800 flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                My Submitted Inquiries & Admin Responses
            </h3>
            
            <div class="space-y-4">
                @foreach($inquiries as $inq)
                    <div class="border border-slate-200 p-4 space-y-3 bg-slate-50/50">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">{{ $inq->service ? $inq->service->name_en : 'General Inquiry' }}</span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 border {{ $inq->status === 'resolved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                {{ $inq->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 bg-white p-3 border border-slate-200 italic font-medium">"{{ $inq->inquiry_text }}"</p>
                        
                        @if($inq->responses && $inq->responses->count() > 0)
                            <div class="pl-4 border-l-2 border-red-700 space-y-2 pt-1">
                                <span class="text-[10px] font-black uppercase text-red-700">Admin Responses:</span>
                                @foreach($inq->responses as $resp)
                                    <div class="bg-white p-3 border border-slate-200 text-xs text-slate-800">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-extrabold text-slate-700">{{ $resp->responder ? $resp->responder->name : 'GovAssist Admin' }}</span>
                                            <span class="text-[9px] text-slate-400">{{ $resp->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="leading-relaxed">{{ $resp->response_text }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[10px] text-slate-400 italic">No admin response yet. You will receive an email notification when a facilitator replies.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Admin Contact Modal -->
<div id="admin-contact-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white border-l-4 border-red-700 max-w-md w-full p-6 shadow-xl space-y-4 rounded-none transform transition-all relative">
        
        <!-- Close Button -->
        <button type="button" onclick="closeAdminContactModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 focus:outline-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <div class="border-b border-slate-200 pb-2">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                {{ __('messages.contact_admin') }}
            </h3>
        </div>
        
        <p class="text-[11px] text-slate-500 leading-relaxed">
            {{ __('messages.contact_admin_desc') }}
        </p>
        
        <form action="{{ route('citizen.inquiry.manual') }}" method="POST" class="space-y-4">
            @csrf
            
            @guest
                <div class="space-y-1.5">
                    <label for="guest_name" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.your_name') }}</label>
                    <input type="text" name="guest_name" id="guest_name" placeholder="Juan Dela Cruz" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800" required>
                </div>
                <div class="space-y-1.5">
                    <label for="guest_email" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.your_email') }}</label>
                    <input type="email" name="guest_email" id="guest_email" placeholder="juan@example.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800" required>
                </div>
            @endguest

            <div class="space-y-1.5">
                <label for="service_id" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.related_program') }}</label>
                <select name="service_id" id="service_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800">
                    <option value="">{{ __('messages.general_inquiry') }}</option>
                    @foreach($services as $svc)
                        @php
                            $svcName = app()->getLocale() === 'ceb' ? $svc->name_ceb : (app()->getLocale() === 'fil' ? ($svc->name_fil ?? $svc->name_en) : $svc->name_en);
                        @endphp
                        <option value="{{ $svc->id }}">{{ $svcName ?: $svc->service_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="inquiry_text" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">{{ __('messages.your_message') }}</label>
                <textarea name="inquiry_text" id="inquiry_text" rows="4" placeholder="..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800" required></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-[10px] shadow-sm transition-all active:scale-[0.98]">
                {{ __('messages.send_inquiry') }}
            </button>
        </form>
    </div>
</div>

<script>
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatWindow = document.getElementById('chat-window');
    const voiceBtn = document.getElementById('voice-btn');
    const micIcon = document.getElementById('mic-icon');
    const micPulse = document.getElementById('mic-pulse');

    function openAdminContactModal() {
        const modal = document.getElementById('admin-contact-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeAdminContactModal() {
        const modal = document.getElementById('admin-contact-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = chatInput.value.trim();
            if(!msg) return;

            // Append User message
            appendMessage('user', msg);
            chatInput.value = '';
            scrollToBottom();

            // Send to controller
            fetch("{{ route('citizen.inquiry.chat') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message: msg })
            })
            .then(res => res.json())
            .then(data => {
                appendMessage('bot', data.reply, data.time);
                scrollToBottom();
            })
            .catch(err => {
                console.error("Chat error:", err);
                appendMessage('bot', "Connection error. Please try again.");
                scrollToBottom();
            });
        });
    }

    function appendMessage(sender, text, time = '') {
        const messageDiv = document.createElement('div');
        if (sender === 'user') {
            messageDiv.className = "flex items-start justify-end space-x-3 max-w-[85%] self-end";
            messageDiv.innerHTML = `
                <div class="bg-red-700 text-white p-4 text-xs leading-relaxed font-semibold shadow-sm">
                    ${text}
                </div>
                <div class="w-7 h-7 bg-red-100 text-red-900 border border-red-200 flex items-center justify-center flex-shrink-0 text-[9px] font-bold">
                    ME
                </div>
            `;
        } else {
            messageDiv.className = "flex items-start space-x-3 max-w-[85%]";
            messageDiv.innerHTML = `
                <div class="w-7 h-7 bg-red-700 text-white flex items-center justify-center flex-shrink-0 text-[9px] font-bold">
                    GB
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 p-4 text-xs leading-relaxed shadow-sm">
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

    // Voice recognition (Web Speech API)
    if (voiceBtn) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (SpeechRecognition) {
            const recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.lang = "{{ app()->getLocale() === 'ceb' ? 'fil-PH' : 'en-US' }}"; // Approximate ceb/fil

            recognition.onstart = () => {
                micIcon.classList.add('hidden');
                micPulse.classList.remove('hidden');
                chatInput.placeholder = "{{ __('messages.voice_listen') }}";
            };

            recognition.onspeechend = () => {
                recognition.stop();
            };

            recognition.onend = () => {
                micIcon.classList.remove('hidden');
                micPulse.classList.add('hidden');
                chatInput.placeholder = "{{ __('messages.bot_placeholder') }}";
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                chatInput.value = transcript;
                chatForm.dispatchEvent(new Event('submit'));
            };

            recognition.onerror = (event) => {
                console.error("Speech recognition error:", event.error);
                alert("{{ __('messages.voice_error') }}");
            };

            voiceBtn.addEventListener('click', () => {
                recognition.start();
            });
        } else {
            voiceBtn.title = "Voice recognition not supported in this browser.";
            voiceBtn.classList.add('opacity-50', 'cursor-not-allowed');
            voiceBtn.disabled = true;
        }
    }
</script>
@endsection
