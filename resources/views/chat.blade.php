<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PPU AI | Smart City Assistant</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-white: #ffffff;
            --user-msg-bg: #f0f4f9;
            --text-dark: #1f1f1f;
            --text-muted: #444746;
            --ai-bg: #f8faff;
            --ai-border: #e3eaff;
            --accent: #2563eb;
        }

        body { 
            background-color: #f5f7fc;
            font-family: 'Google Sans', sans-serif;
            color: var(--text-dark);
        }

        .ai-response-card {
            background: #ffffff;
            border: 1px solid var(--ai-border);
            border-radius: 18px;
            padding: 20px 24px;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.06);
            position: relative;
        }

        .markdown-content {
            line-height: 1.75;
            font-size: 0.95rem;
            color: #1e293b;
        }
        .markdown-content p { margin-bottom: 0.75rem; }
        .markdown-content p:last-child { margin-bottom: 0; }
        .markdown-content strong { font-weight: 600; color: #0f172a; }
        .markdown-content ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
        .markdown-content ul li { margin-bottom: 0.3rem; }
        .markdown-content ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
        .markdown-content a {
            color: var(--accent);
            text-decoration: underline;
            text-underline-offset: 2px;
            word-break: break-all;
            font-size: 0.88rem;
        }
        .markdown-content a:hover { color: #1d4ed8; }
        .markdown-content h1, .markdown-content h2, .markdown-content h3 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }
        .markdown-content code {
            background: #f1f5f9;
            border-radius: 4px;
            padding: 0.1em 0.4em;
            font-size: 0.85em;
            font-family: monospace;
        }
        .markdown-content blockquote {
            border-left: 3px solid var(--accent);
            padding-left: 1rem;
            color: #475569;
            font-style: italic;
            margin: 0.75rem 0;
        }

        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-box {
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .input-box:focus-within {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        header {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e8edf5;
        }

        .user-bubble {
            background: linear-gradient(135deg, #eff6ff, #e0ecff);
            border: 1px solid #bfdbfe;
            color: #1e3a5f;
        }

        .ai-icon {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bottom-bar {
            background: rgba(255,255,255,0.95);
            border-top: 1px solid #e8edf5;
            backdrop-filter: blur(8px);
        }
    </style>
</head>
git <body class="h-screen flex flex-col">

    <header class="flex items-center px-6 py-3 sticky top-0 z-50">
        <span class="text-xl font-medium text-gray-800">PPU <span class="text-blue-600">AI</span></span>
    </header>

    <div id="chat-container" class="flex-1 overflow-y-auto">
        <div class="max-w-3xl mx-auto px-4 py-8 space-y-6">
            
            @forelse($history as $chat)
                <div class="flex flex-col items-end animate-fade">
                    <div class="max-w-[80%] user-bubble px-5 py-3 rounded-[22px] text-sm leading-relaxed">
                        {{ $chat->user_message }}
                    </div>
                </div>

                <div class="flex items-start gap-3 animate-fade">
                    <div class="ai-icon mt-1">
                        <span class="text-white text-[10px] font-bold">AI</span>
                    </div>
                    <div class="ai-response-card flex-1">
                        <div class="markdown-content">
                            {!! Str::markdown($chat->ai_response) !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="h-[50vh] flex flex-col items-center justify-center text-center">
                    <h1 class="text-4xl font-medium mb-4 bg-gradient-to-r from-blue-600 to-purple-500 bg-clip-text text-transparent">
                        Ada yang bisa dibantu?
                    </h1>
                    <p class="text-gray-400 text-sm">Tanyakan informasi seputar PPU & IKN Nusantara.</p>
                </div>
            @endforelse

        </div>
    </div>

    <div class="bottom-bar p-4">
        <div class="max-w-3xl mx-auto">
            <div class="input-box flex items-center gap-2 px-5 py-1">
                <textarea id="userInput" rows="1" 
                    class="flex-1 bg-transparent border-none py-3.5 focus:ring-0 text-sm resize-none placeholder-gray-400 max-h-40 outline-none"
                    placeholder="Ketik pesan di sini..."
                    onkeydown="if(event.keyCode === 13 && !event.shiftKey) { event.preventDefault(); sendChat(); }"></textarea>
                
                <button onclick="sendChat()" id="sendBtn" 
                    class="p-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    disabled>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
            <p class="text-[11px] text-center text-gray-400 mt-3">
                PPU AI dapat memberikan jawaban yang tidak akurat. Verifikasi kembali informasi penting.
            </p>
        </div>
    </div>

    <script>
        const container = document.getElementById('chat-container');
        const input = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');

        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            sendBtn.disabled = !this.value.trim();
        });

        async function sendChat() {
            const message = input.value.trim();
            if (!message) return;

            input.value = '';
            input.style.height = 'auto';
            sendBtn.disabled = true;

            const userHTML = `
                <div class="flex flex-col items-end animate-fade">
                    <div class="max-w-[80%] user-bubble px-5 py-3 rounded-[22px] text-sm leading-relaxed">${escapeHtml(message)}</div>
                </div>`;
            container.querySelector('.max-w-3xl').insertAdjacentHTML('beforeend', userHTML);
            
            const loadingId = 'loading-' + Date.now();
            const loadingHTML = `
                <div id="${loadingId}" class="flex items-start gap-3 animate-fade">
                    <div class="ai-icon mt-1">
                        <span class="text-white text-[10px] font-bold animate-pulse">AI</span>
                    </div>
                    <div class="ai-response-card flex-1">
                        <div class="flex gap-1.5 py-1">
                            <span class="w-2 h-2 bg-blue-300 rounded-full animate-bounce"></span>
                            <span class="w-2 h-2 bg-blue-300 rounded-full animate-bounce" style="animation-delay:0.2s"></span>
                            <span class="w-2 h-2 bg-blue-300 rounded-full animate-bounce" style="animation-delay:0.4s"></span>
                        </div>
                    </div>
                </div>`;
            container.querySelector('.max-w-3xl').insertAdjacentHTML('beforeend', loadingHTML);
            scrollDown();

            try {
                const response = await fetch('/chat/',{
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                if (!response.ok) throw new Error(data.reply || 'Server error');

                document.getElementById(loadingId).remove();

                const aiHTML = `
                    <div class="flex items-start gap-3 animate-fade">
                        <div class="ai-icon mt-1">
                            <span class="text-white text-[10px] font-bold">AI</span>
                        </div>
                        <div class="ai-response-card flex-1">
                            <div class="markdown-content">${marked.parse(data.reply)}</div>
                        </div>
                    </div>`;
                container.querySelector('.max-w-3xl').insertAdjacentHTML('beforeend', aiHTML);
            } catch (e) {
                document.getElementById(loadingId).remove();
                const errorHTML = `
                    <div class="flex items-start gap-3 animate-fade">
                        <div class="ai-icon mt-1" style="background: linear-gradient(135deg,#ef4444,#dc2626)">
                            <span class="text-white text-[10px] font-bold">!</span>
                        </div>
                        <div class="ai-response-card flex-1 border-red-100">
                            <p class="text-red-600 text-sm">❌ ${escapeHtml(e.message || 'Error mengirim pesan. Coba lagi.')}</p>
                        </div>
                    </div>`;
                container.querySelector('.max-w-3xl').insertAdjacentHTML('beforeend', errorHTML);
            }
            scrollDown();
        }
        

        function scrollDown() {
            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
        }

        function escapeHtml(text) {
            return text.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
        }
    </script>
</body>
</html>