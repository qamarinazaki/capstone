@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root {
        --text-main: #ffffff; /* Flipped to white for dark mode readability */
        --text-muted: #cbd5e1; /* Clear readable bright slate subtext */
        --border-color: rgba(255, 255, 255, 0.1);
        --radius-lg: 16px;
        --radius-md: 12px;
        --font-sans: 'Plus Jakarta Sans', sans-serif;
    }

    /* Uses your custom chatbot-bg.png.png illustration asset as the layout background */
    .dashboard-bg-wrapper {
        position: relative;
        min-height: 100vh;
        padding: 2rem;
        background-color: #0f172a;
        background-image: url('{{ asset("images/chatbot-bg.png.png") }}'); 
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        font-family: var(--font-sans);
    }

    /* Dark translucent mask overlay to neutralize the bright white areas of the robot image */
    .dashboard-bg-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.75) 100%);
        z-index: 1;
    }

    .dashboard-relative-content {
        position: relative;
        z-index: 2;
    }

    /* Dark Mode Premium Glassmorphic Chat Shell Frame */
    .glass-chat-card {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-lg);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    /* Sectional Dark-themed Card Sub-Panels */
    .glass-chat-header {
        background: rgba(15, 23, 42, 0.85) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1.25rem 1.5rem;
    }

    .glass-chat-body {
        background: rgba(15, 23, 42, 0.3) !important;
        backdrop-filter: blur(5px);
    }

    .glass-chat-footer {
        background: rgba(15, 23, 42, 0.75) !important;
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* Messaging Bubble Custom Styles */
    .message-box {
        font-size: 0.92rem;
        line-height: 1.5;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        border-radius: var(--radius-md) !important;
    }
    
    /* System Bot Message Bubble (Dark Slate Glass Accent) */
    .msg-box-bot {
        background: rgba(30, 41, 59, 0.9) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #f8fafc !important;
    }
    
    /* Inner list links and description adjustments inside chatbot bubble replies */
    .msg-box-bot ul, .msg-box-bot em {
        color: #cbd5e1 !important;
    }

    /* User Chat Bubble Alignment */
    .msg-box-user {
        background: #dc2626 !important;
        color: #ffffff !important;
        border: none !important;
    }

    /* Glass Dark Form Input Controller Overrides */
    .glass-input-field {
        background: rgba(15, 23, 42, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: var(--radius-md) !important;
        padding: 0.75rem 1.25rem;
        font-size: 0.92rem;
        color: #ffffff !important;
        transition: all 0.2s ease;
    }
    .glass-input-field:focus {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.3) !important;
    }
    .glass-input-field::placeholder {
        color: #64748b !important;
    }

    /* System Quick Prompt Chip Selection Buttons */
    .btn-prompt-chip {
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #cbd5e1;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-prompt-chip:hover {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        border-color: #dc2626 !important;
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
            <h2 class="fw-extrabold text-white tracking-tight mb-1" style="font-size: 1.85rem;">Data Assistant ChatBot</h2>
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
                                    <span class="d-inline-block bg-success rounded-circle animate-pulse-dot" style="width: 6px; height: 6px; background-color: #16a34a;"></span> Local Database Engine Active
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
                                <ul class="mb-0 mt-2 ps-3 small" style="line-height: 1.6;">
                                    <li><em>"What is our total parcel count in Terengganu for 2023?"</em></li>
                                    <li><em>"What is our total parcel count in Pahang for 2024?"</em></li>
                                    <li><em>"Show customer survey satisfaction score summary"</em></li>
                                    <li><em>"What is the average package weight in Selangor for 2025?"</em></li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    {{-- Suggested Quick-Click Prompts Panel --}}
                    <div class="px-4 py-2 glass-chat-footer">
                        <small class="text-muted d-block mb-1.5 font-monospace fw-bold" style="font-size: 0.68rem; letter-spacing: 0.05em;"><i class="bi bi-lightning-fill text-warning"></i> SUGGESTED SYSTEM PROMPTS:</small>
                        <div class="d-flex flex-wrap gap-2 mb-1">
                            <button class="btn btn-xs rounded-pill text-start px-3 py-1 btn-prompt-chip" onclick="submitQuickPrompt('What is our total parcel count in Terengganu for 2023?')">2023 Volumes (Terengganu)</button>
                            <button class="btn btn-xs rounded-pill text-start px-3 py-1 btn-prompt-chip" onclick="submitQuickPrompt('What is our total parcel count in Pahang for 2024?')">2024 Volumes (Pahang)</button>
                            <button class="btn btn-xs rounded-pill text-start px-3 py-1 btn-prompt-chip" onclick="submitQuickPrompt('What is the average package weight in Selangor for 2025?')">2025 Standby Metrics</button>
                        </div>
                    </div>

                    {{-- Chat Input Form Area --}}
                    <div class="card-footer glass-chat-footer p-3">
                        <form id="chatForm" autocomplete="off" onsubmit="sendMessage(event)">
                            @csrf
                            <div class="input-group gap-2">
                                <input type="text" id="userInput" class="form-control glass-input-field shadow-none" placeholder="Type your data query here..." required>
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
            // Escape user input to prevent HTML injections
            formattedText = formattedText.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        } else {
            // Convert Markdown header blocks (### Title)
            formattedText = formattedText.replace(/###\s*(.*?)(?:\n|<br>|$)/g, '<h6 class="fw-bold text-white mt-3 mb-1">$1</h6>');
            
            // Convert Markdown bold elements (**text**)
            formattedText = formattedText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            // Robustly parse list blocks and assemble clean <ul> lists
            let lines = formattedText.split('\n');
            let inList = false;
            
            for (let i = 0; i < lines.length; i++) {
                let trimmed = lines[i].trim();
                if (trimmed.startsWith('* ') || trimmed.startsWith('- ')) {
                    let content = trimmed.substring(2);
                    if (!inList) {
                        lines[i] = '<ul class="mb-2 ps-3 text-muted">' + `<li>${content}</li>`;
                        inList = true;
                    } else {
                        lines[i] = `<li>${content}</li>`;
                    }
                } else {
                    if (inList) {
                        lines[i - 1] = lines[i - 1] + '</ul>';
                        inList = false;
                    }
                }
            }
            if (inList) {
                lines[lines.length - 1] = lines[lines.length - 1] + '</ul>';
            }
            
            formattedText = lines.join('<br>').replace(/<\/ul><br>/g, '</ul>').replace(/<br><ul/g, '<ul');
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
            <div class="p-3 border rounded-3 shadow-sm d-flex align-items-center gap-2 small py-2 msg-box-bot">
                <span class="spinner-border spinner-border-sm text-danger" role="status"></span> Querying system records...
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
        .then(res => {
            if (!res.ok) {
                throw new Error("Server Status Code " + res.status);
            }
            return res.json();
        })
        .then(data => {
            const el = document.getElementById(typingId);
            if(el) el.remove();
            appendMessage('bot', data.reply);
        })
        .catch(err => {
            const el = document.getElementById(typingId);
            if(el) el.remove();
            
            appendMessage('bot', `### Local Connection Error\nFailed to receive a valid response from the backend application pipelines.\n\n* **Detail Logs:** ${err.message}\n* **Action:** Double check your database connection or clear the routing cache layers.`);
            console.error(err);
        })
        .finally(() => {
            userInput.disabled = false;
            sendBtn.disabled = false;
            userInput.focus();
            chatArea.scrollTop = chatArea.scrollHeight;
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
                <div class="p-3 shadow-sm rounded-3 border text-dark message-box msg-box-bot" style="max-width: 75%;">
                    Session reset. What else can I calculate from the shipping logs for you today?
                </div>
            </div>
        `;
    }
</script>
@endsection