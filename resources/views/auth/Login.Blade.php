<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PPU AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #090e1a;
            --card: rgba(17, 25, 40, 0.75);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.5);
            --text-main: #ffffff;
            --text-dim: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 58, 138, 0.2) 0px, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeIn 0.8s ease-out;
        }

        .card {
            background: var(--card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-circle {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--accent), #1d4ed8);
            border-radius: 16px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .header p {
            color: var(--text-dim);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-dim);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: white;
            font-size: 15px;
            transition: all 0.2s;
            outline: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Sembunyikan placeholder asli, gunakan custom placeholder */
        .input-wrapper input::placeholder {
            color: transparent;
        }

        /* Custom animated placeholder lewat data attribute + pseudo */
        .input-wrapper .custom-placeholder {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #4a5568;
            font-size: 15px;
            pointer-events: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Cursor blink di akhir placeholder */
        .input-wrapper .custom-placeholder::after {
            content: '|';
            color: var(--accent);
            animation: blink 0.8s step-end infinite;
            margin-left: 1px;
        }

        /* Sembunyikan cursor & placeholder saat input ada isi atau fokus dengan value */
        .input-wrapper input:not(:placeholder-shown) + .custom-placeholder,
        .input-wrapper input:focus + .custom-placeholder {
            display: none;
        }

        .input-wrapper input:focus {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-dim);
            cursor: pointer;
        }

        .checkbox-group input {
            accent-color: var(--accent);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-login:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .btn-login:active { transform: translateY(0); }

        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-dim);
            opacity: 0.5;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        /* Alert styling */
        .alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card">
        <div class="header">
            <div class="logo-circle">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <h1>PPU AI</h1>
            <p>Portal Admin</p>
        </div>

        @if($errors->any() || session('error'))
            <div class="alert">
                {{ $errors->first() ?? session('error') }}
            </div>
        @endif

        {{-- autocomplete="off" dan autocomplete="new-password" mencegah browser auto-fill --}}
        <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
            @csrf

            {{-- Input dummy tersembunyi untuk mengelabui autofill browser --}}
            <input type="text" name="fakeusernameremembered" style="display:none;">
            <input type="password" name="fakepasswordremembered" style="display:none;">

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="input-wrapper">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder=" "
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        autocomplete="new-password"
                        readonly
                        onfocus="this.removeAttribute('readonly')"
                    >
                    <span class="custom-placeholder" id="placeholder-email"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder=" "
                        required
                        autocomplete="new-password"
                        readonly
                        onfocus="this.removeAttribute('readonly')"
                    >
                    <span class="custom-placeholder" id="placeholder-password"></span>
                </div>
            </div>

            <div class="options">
                <label class="checkbox-group">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login">
                Masuk ke Dashboard
            </button>
        </form>
    </div>
    
    <p class="footer-text">© 2026 Pemkab Penajam Paser Utara</p>
</div>

<script>
    /**
     * Typewriter animation untuk placeholder
     * @param {HTMLElement} el - elemen span placeholder
     * @param {string} text - teks yang akan diketik
     * @param {number} speed - kecepatan ketik (ms per karakter)
     * @param {number} delay - jeda sebelum mulai (ms)
     */
    function typewriterPlaceholder(el, text, speed = 80, delay = 0) {
        let i = 0;
        el.textContent = '';

        setTimeout(() => {
            const interval = setInterval(() => {
                if (i < text.length) {
                    el.textContent += text[i];
                    i++;
                } else {
                    clearInterval(interval);
                    // Setelah selesai, tunggu lalu hapus dan ulangi (loop)
                    setTimeout(() => {
                        eraseText(el, text, speed, delay);
                    }, 2000);
                }
            }, speed);
        }, delay);
    }

    function eraseText(el, text, speed, delay) {
        let i = text.length;
        const interval = setInterval(() => {
            if (i > 0) {
                el.textContent = text.substring(0, i - 1);
                i--;
            } else {
                clearInterval(interval);
                // Mulai ulang animasi ketik
                setTimeout(() => {
                    typewriterPlaceholder(el, text, speed, 300);
                }, 500);
            }
        }, speed / 2); // hapus lebih cepat dari ketik
    }

    // Sembunyikan placeholder saat input diisi atau difokus
    function bindInputVisibility(inputEl, placeholderEl) {
        const hide = () => {
            if (inputEl.value.length > 0 || document.activeElement === inputEl) {
                placeholderEl.style.display = 'none';
            } else {
                placeholderEl.style.display = 'block';
            }
        };

        inputEl.addEventListener('focus', () => { placeholderEl.style.display = 'none'; });
        inputEl.addEventListener('blur', () => {
            if (inputEl.value.length === 0) {
                placeholderEl.style.display = 'block';
            }
        });
        inputEl.addEventListener('input', hide);
    }

    // Jalankan saat halaman siap
    window.addEventListener('DOMContentLoaded', () => {
        const emailInput       = document.getElementById('email');
        const passwordInput    = document.getElementById('password');
        const placeholderEmail = document.getElementById('placeholder-email');
        const placeholderPass  = document.getElementById('placeholder-password');

        // Sembunyikan placeholder jika field sudah terisi (misal dari old() Laravel)
        if (emailInput.value.length > 0) placeholderEmail.style.display = 'none';

        // Mulai animasi typewriter
        typewriterPlaceholder(placeholderEmail, 'admin@ppu.go.id', 90, 500);
        typewriterPlaceholder(placeholderPass,  '••••••••',         120, 1200);

        // Bind visibilitas
        bindInputVisibility(emailInput, placeholderEmail);
        bindInputVisibility(passwordInput, placeholderPass);
    });
</script>

</body>
</html>