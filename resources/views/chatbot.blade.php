@extends('layouts.app')

@section('content')
<!-- External Modern Typography Stack -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root {
        --text-main: #0f172a;
        --text-muted: #475569;
        --border-color: rgba(226, 232, 240, 0.4);
        --radius-lg: 16px;
        --radius-md: 12px;
        --font-sans: 'Plus Jakarta Sans', sans-serif;
    }

    /* Immersive Operations Background Overlay Container Wrapper */
    .dashboard-bg-wrapper {
        position: relative;
        min-height: 100vh;
        padding: 2rem;
        background-color: #f8fafc;
        /* Replace this URL with your local asset file directory path if preferred */
        background-image: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2070&auto=format&fit=crop'); 
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        font-family: var(--font-sans);
    }

    .dashboard-bg-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.92) 0%, rgba(241, 245, 249, 0.85) 100%);
        z-index: 1;
    }

    .dashboard-relative-content {
        position: relative;
        z-index: 2;
    }

    /* Premium Translucent Frosted Glass Architecture Shell */
    .glass-chat-card {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(20px) saturate(120%);
        -webkit-backdrop-filter: blur(20px) saturate(120%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: var(--radius-lg);
        box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    /* Card Panels Glass Subsections */
    .glass-chat-header {
        background: rgba(15, 23, 42, 0.9) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1.25rem 1.5rem;
    }

    .glass-chat-body {
        background: rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(5px);
    }

    .glass-chat-footer {
        background: rgba(255, 255, 255, 0.6) !important;
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(226, 232, 240, 0.5) !important;
    }

    /* High-contrast Messaging Bubbles */
    .message-box {
        font-size: 0.92rem;
        line-height: 1.5;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03) !important;
        border-radius: var(--radius-md) !important;
    }
    .msg-box-bot {
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        color: var(--text-main);
    }
    .msg-box-user {
        background: #dc2626 !important;
        color: #ffffff !important;
        border: none !important;
    }

    /* Form Controller Design Overrides */
    .glass-input-field {
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        border-radius: var(--radius-md) !important;
        padding: 0.75rem 1.25rem;
        font-size: 0.92rem;
        color: var(--text-main);
        transition: all 0.2s ease;
    }
    .glass-input-field:focus {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15) !important;
    }

    /* Prompt Chip Units */
    .btn-prompt-chip {
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(226, 232, 240, 0.9);
        color: #475569;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-prompt-chip:hover {
        background-color: #fff1f2 !important;
        color: #e11d48 !important;
        border-color: #fecdd3 !important;
        transform: translateY(-1px);
    }

    .animate-pulse-dot { animation: dotPulse 2s infinite ease-in-out; }
    @keyframes dotPulse { 0% { opacity: 0.4; } 50% { opacity: 1; } 100% { opacity: 0.4; } }
</style>

<div class="dashboard-bg-wrapper">
    <div class="dashboard-bg-overlay"></div>

    <div class="container-fluid p-0 dashboard-relative-content">
        {{-- Typography Dashboard Title Header Unit --}}
        <div class="mb-4">
            <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 1.85rem;">Data Assistant ChatBot</h2>
            <p class="text-muted small mb-0 fw-medium">Ask real-time analytical questions regarding your NinjaVan datasets or converse with our generative core AI system.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                
                {{-- Chat Card Shell --}}
                <div class="card glass-chat-card d-flex flex-column" style="height: calc(100vh - 220px); min-height: 520px;">
                    
                    {{-- Chat Box Header --}}
                    <div class="card-header glass-chat-header d-flex align-items-center justify-content-between border-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(220, 38, 38, 0.15) !important;">
                                <i class="bi bi-robot fs-5 text-danger"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white mb-0" style="letter-spacing: -0.01em;">NinjaVault Core Intelligence</div>
                                <div class="text-success small d-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 500;">
                                    <span class="d-inline-block bg-success rounded-circle animate-pulse-dot" style="width: 6px; height: 6px; background-color: #16a34a;"></span> Hybrid Database & Gemini Live AI Active
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-light border-0 opacity-75 hover-opacity-100 fw-semibold px-2.5" style="font-size: 0.8rem;" onclick="clearChat()" title="Reset Session">
                            <i class="bi bi-trash3 me-1"></i> Clear Session
                        </button>
                    </div>

                    {{-- Scrollable Conversation Stream Area --}}
                    <div class="card-body glass-chat-body overflow-auto p-4 flex-grow-1" id="chatArea">
                        
                        {{-- Bot Default Message --}}
                        <div class="d-flex mb-3 gap-3">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #dc2626 !important;">
                                <i class="bi bi-robot"></i>
                            </div>
                            <div class="p-3 shadow-sm rounded-3 text-dark message-box msg-box-bot" style="max-width: 75%;">
                                Hi! I am your tracking and fulfillment data assistant. Ask me questions like:
                                <ul class="mb-0 mt-2 ps-3 text-muted small" style="line-height: 1.6;">
                                    <li><em>"How many parcels do we have total?"</em></li>
                                    <li><em>"What is our average weight?"</em></li>
                                    <li><em>"What are our satisfaction scores?"</em></li>
                                </ul>
                                <hr class="my-2.5 text-muted opacity-25">
                                <span class="text-muted" style="font-size: 0.8rem;"><i class="bi bi-stars text-warning me-1"></i><strong>New Feature:</strong> You can now also type any custom question to converse with our advanced integrated Live AI generator!</span>
                            </div>
                        </div>

                    </div>

                    {{-- Suggested Quick-Click Prompts Panel --}}
                    <div class="px-4 py-2 glass-chat-footer">
                        <small class="text-muted d-block mb-1.5 font-monospace fw-bold" style="font-size: 0.68rem; letter-spacing: 0.05em;"><i class="bi bi-lightning-fill text-warning"></i> SUGGESTED SYSTEM PROMPTS:</small>
                        <div class="d-flex flex-wrap gap-2 mb-1">
                            <button class="btn btn-xs rounded-pill text-start px-3 py-1 btn-prompt-chip" onclick="submitQuickPrompt('What is our total parcel count in Selangor?')">Total parcels in Selangor</button>
                            <button class="btn btn-xs rounded-pill text-start px-3 py-1 btn-prompt-chip" onclick="submitQuickPrompt('Show customer survey satisfaction score summary')">Satisfaction Scores</button>
                            <button class="btn btn-xs rounded-pill text-start px-3 py-1 btn-prompt-chip" onclick="submitQuickPrompt('What features make the NinjaVault system secure?')">Security Architecture</button>
                        </div>
                    </div>

                    {{-- Chat Input Form Area --}}
                    <div class="card-footer glass-chat-footer p-3">
                        <form id="chatForm" autocomplete="off" onsubmit="sendMessage(event)">
                            @csrf
                            <div class="input-group gap-2">
                                <input type="text" id="userInput" class="form-control glass-input-field shadow-none" placeholder="Type your data query or custom AI question here..." required>
                                <button type="submit" id="sendBtn" class="btn btn-danger px-4 d-flex align-items-center gap-2 fw-bold" style="background-color: #dc2626 !important; border-radius: var(--radius-md) !important;">
                                    <span>Send</span> <i class="bi bi-send-fill small"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Style Overrides for Chat Layout formatting --}}
<style>
    .message-box ul { margin-top: 5px; }
    .message-box p { margin-bottom: 0px; }
</style>

<script>
    const chatArea = document.getElementById('chatArea');
    const userInput = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');

    function appendMessage(sender, text) {
        const isBot = (sender === 'bot');
        const messageRow = document.createElement('div');
        messageRow.className = `d-flex mb-3 gap-3 ${isBot ? '' : 'justify-content-end'}`;

        let formattedText = text;
        
        if (!isBot) {
            formattedText = formattedText.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }

        // Convert Markdowns to structured text components
        formattedText = formattedText
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/^\s*\*\s*(.*?)$/gm, '<li>$1</li>')
            .replace(/###\s*(.*?)(?:<br>|$)/g, '<h6 class="fw-bold text-dark mt-2 mb-1">$1</h6>');

        if (formattedText.includes('<li>')) {
            if (!formattedText.includes('<ul>')) {
                formattedText = formattedText.replace(/(<li>.*?<\/li>)/s, '<ul class="mb-0 ps-3">$1</ul>');
            }
        }

        const avatar = isBot 
            ? `<div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #dc2626 !important;"><i class="bi bi-robot"></i></div>` 
            : '';

        const bubble = `
            <div class="p-3 shadow-sm rounded-3 message-box ${isBot ? 'msg-box-bot' : 'msg-box-user'}" style="max-width: 75%;">
                <p class="mb-0">${formattedText}</p>
            </div>
        `;

        messageRow.innerHTML = isBot ? (avatar + bubble) : (bubble + avatar);
        chatArea.appendChild(messageRow);
        chatArea.scrollTop = chatArea.scrollHeight;
    }

    function sendMessage(e) {
        if(e) e.preventDefault();
        const text = userInput.value.trim();
        if (!text) return;

        appendMessage('user', text);
        userInput.value = '';

        userInput.disabled = true;
        sendBtn.disabled = true;

        const typingId = 'typing-' + Date.now();
        const typingRow = document.createElement('div');
        typingRow.id = typingId;
        typingRow.className = 'd-flex mb-3 gap-3';
        typingRow.innerHTML = `
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #dc2626 !important;"><i class="bi bi-robot"></i></div>
            <div class="p-3 bg-white border text-muted rounded-3 shadow-sm d-flex align-items-center gap-2 small py-2 msg-box-bot">
                <span class="spinner-border spinner-border-sm text-danger" role="status"></span> Analyzing system records...
            </div>
        `;
        chatArea.appendChild(typingRow);
        chatArea.scrollTop = chatArea.scrollHeight;

        fetch("{{ route('chatbot.message') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById(typingId);
            if(el) el.remove();
            appendMessage('bot', data.reply);
        })
        .catch(err => {
            const el = document.getElementById(typingId);
            if(el) el.remove();
            appendMessage('bot', "Sorry, I ran into an internal network exception trying to fetch system processing cores.");
            console.error(err);
        })
        .finally(() => {
            userInput.disabled = false;
            sendBtn.disabled = false;
            userInput.focus();
        });
    }

    function submitQuickPrompt(promptText) {
        userInput.value = promptText;
        sendMessage(null);
    }

    function clearChat() {
        chatArea.innerHTML = `
            <div class="d-flex mb-3 gap-3">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #dc2626 !important;"><i class="bi bi-robot"></i></div>
                <div class="p-3 bg-white shadow-sm rounded-3 border text-dark message-box msg-box-bot" style="max-width: 75%;">
                    Session reset. What else can I calculate from the shipping logs for you today?
                </div>
            </div>
        `;
    }
</script>
@endsection