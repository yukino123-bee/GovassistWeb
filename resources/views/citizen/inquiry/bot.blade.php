@extends('layouts.citizen')

@section('title', __('messages.bot_title'))

@section('header_title', __('messages.bot_title'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-[calc(100vh-9rem)]">

    <!-- Left: Manual Inquiry Form -->
    <div class="lg:col-span-1 flex flex-col space-y-6">
        <div class="border-b border-slate-200 pb-3">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                Contact Admin Directly
            </h3>
        </div>

        <div class="bg-white border border-slate-200 p-5 shadow-sm flex-grow overflow-y-auto">
            <p class="text-[11px] text-slate-500 leading-relaxed mb-4">
                Use this form to send a manual inquiry directly to our administrators. We will reply via the system or email.
            </p>
            
            <form action="{{ route('citizen.inquiry.manual') }}" method="POST" class="space-y-4">
                @csrf
                
                @guest
                    <div class="space-y-1.5">
                        <label for="guest_name" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Your Name</label>
                        <input type="text" name="guest_name" id="guest_name" placeholder="Juan Dela Cruz" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800" required>
                    </div>
                    <div class="space-y-1.5">
                        <label for="guest_email" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Your Email</label>
                        <input type="email" name="guest_email" id="guest_email" placeholder="juan@example.com" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800" required>
                    </div>
                @endguest

                <div class="space-y-1.5">
                    <label for="service_id" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Related Program (Optional)</label>
                    <select name="service_id" id="service_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800">
                        <option value="">General Inquiry</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->service_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="inquiry_text" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider">Your Message</label>
                    <textarea name="inquiry_text" id="inquiry_text" rows="4" placeholder="How can we help you today?" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-none focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500/20 focus:border-red-700 transition-all text-xs text-slate-800" required></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white font-bold uppercase tracking-wider text-[10px] shadow-sm transition-all active:scale-[0.98]">
                    Send Inquiry
                </button>
            </form>
        </div>
    </div>

    <!-- Right: Chat Assistant -->
    <div class="lg:col-span-2 flex flex-col h-full">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                Inquiry Assistance (GovBot)
            </h3>
        </div>

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
    </div>

</div>

<script>
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatWindow = document.getElementById('chat-window');
    const voiceBtn = document.getElementById('voice-btn');
    const micIcon = document.getElementById('mic-icon');
    const micPulse = document.getElementById('mic-pulse');

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
