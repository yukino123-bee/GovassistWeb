@extends('layouts.citizen')

@section('title', __('messages.bot_title'))

@section('header_title', __('messages.bot_title'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-[calc(100vh-9rem)]">

    <!-- Left: Downloadable Forms & Guide -->
    <div class="lg:col-span-1 flex flex-col space-y-6">
        <div class="border-b border-slate-200 pb-3">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-800 flex items-center">
                <span class="w-2.5 h-2.5 bg-red-700 mr-2"></span>
                Official Forms
            </h3>
        </div>

        <div class="bg-white border border-slate-200 p-5 shadow-sm space-y-4 overflow-y-auto max-h-[300px] lg:max-h-none flex-grow">
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Download the official template forms below, fill them out, and upload them via your requirements checklist.
            </p>
            
            <div class="divide-y divide-slate-100">
                @foreach($templates as $tpl)
                    @php
                        $tplName = app()->getLocale() === 'ceb' ? $tpl->name_ceb : (app()->getLocale() === 'fil' ? ($tpl->name_fil ?? $tpl->name_en) : $tpl->name_en);
                        $tplDesc = app()->getLocale() === 'ceb' ? $tpl->description_ceb : (app()->getLocale() === 'fil' ? ($tpl->description_fil ?? $tpl->description_en) : $tpl->description_en);
                    @endphp
                    <div class="py-3 first:pt-0 last:pb-0 flex items-start justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">{{ $tplName }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $tplDesc }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $tpl->file_path) }}" download class="p-2 border border-red-200 hover:border-red-700 text-red-700 hover:bg-red-50 flex-shrink-0 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
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
