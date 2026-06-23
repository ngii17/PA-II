<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard - Purnama Hotel & Resto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy-primary: #00197D;
            --navy-dark:    #000C3D;
            --gold-premium: #D4AF37;
            --soft-white:   #F8FAFC;
            --text-slate:   #475569;
            --text-muted:   #94A3B8;
            --border-color: #E2E8F0;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-white);
            margin: 0;
            height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ============================================
           LAYOUT
        ============================================ */
        .login-wrapper {
            display: flex;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* ============================================
           HERO SIDE (Kiri) - DENGAN LOGO GAMBAR & ANIMASI
        ============================================ */
        .hero-side {
            flex: 1.2;
            background: linear-gradient(140deg, var(--navy-primary) 0%, var(--navy-dark) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 72px;
            color: white;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        /* Background gelombang */
        .hero-side .wave-bg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.15;
        }

        .hero-side .wave-bg svg {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60%;
            animation: waveMove 12s ease-in-out infinite;
        }

        @keyframes waveMove {
            0% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(-3%) translateY(-2%); }
            100% { transform: translateX(0) translateY(0); }
        }

        /* Orb berputar */
        .hero-side .orb {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(212,175,55,0.2) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
            animation: rotateOrb 20s linear infinite;
        }

        @keyframes rotateOrb {
            from { transform: rotate(0deg) translateX(40px) rotate(0deg); }
            to   { transform: rotate(360deg) translateX(40px) rotate(-360deg); }
        }

        /* Logo gambar besar */
        .hero-logo {
            position: relative;
            z-index: 2;
            margin-bottom: 40px;
            animation: floatLogo 3s ease-in-out infinite;
        }

        @keyframes floatLogo {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }

        .hero-logo-img {
            width: 140px;
            height: auto;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 8px 24px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }

        .hero-logo-img:hover {
            transform: scale(1.02);
        }

        /* Tagline */
        .hero-tagline {
            display: inline-block;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold-premium);
            background: rgba(212,175,55,0.12);
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 40px;
            padding: 8px 24px;
            margin-bottom: 32px;
            backdrop-filter: blur(4px);
            animation: glowPulse 2s infinite;
            z-index: 2;
            position: relative;
        }

        @keyframes glowPulse {
            0% { box-shadow: 0 0 0 0 rgba(212,175,55,0.2); }
            70% { box-shadow: 0 0 0 12px rgba(212,175,55,0); }
            100% { box-shadow: 0 0 0 0 rgba(212,175,55,0); }
        }

        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 2;
        }

        .hero-title span {
            color: var(--gold-premium);
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.7);
            max-width: 380px;
            margin: 0 auto;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        /* Floating shapes (dekorasi) */
        .floating-shape {
            position: absolute;
            background: radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: floatSlow 15s infinite alternate;
            z-index: 0;
        }

        @keyframes floatSlow {
            0% { transform: translate(0, 0); }
            100% { transform: translate(20px, -20px); }
        }

        /* ===== BINTANG JATUH (SHOOTING STARS) ===== */
        .star {
            position: absolute;
            background-color: #fff;
            width: 2px;
            height: 2px;
            border-radius: 50%;
            opacity: 0;
            box-shadow: 0 0 4px #fff, 0 0 8px rgba(212,175,55,0.8);
            animation: shootStar 6s linear infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes shootStar {
            0% {
                transform: translateX(0) translateY(0) rotate(0deg);
                opacity: 0;
            }
            5% {
                opacity: 0.8;
            }
            15% {
                opacity: 0;
            }
            100% {
                transform: translateX(-200px) translateY(200px) rotate(45deg);
                opacity: 0;
            }
        }

        /* ============================================
           FORM SIDE (Kanan) - BERSIH TANPA GRADIENT
        ============================================ */
        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 48px;
            background: #ffffff; /* putih bersih, tanpa gradient */
            position: relative;
            overflow-y: auto;
        }

        /* Hapus noise overlay agar benar-benar bersih */
        .form-side::after {
            display: none;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 2;
            background: white;
            border-radius: 32px;
            padding: 40px 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
            animation: cardGlow 0.6s ease-out;
            border: 1px solid rgba(212,175,55,0.15);
        }

        @keyframes cardGlow {
            0% { opacity: 0; transform: scale(0.96) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .card-header-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .card-eyebrow {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2.5px;
            color: var(--gold-premium);
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .card-title {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--navy-dark);
        }

        .card-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* Alert */
        .alert-premium {
            background: #FEF2F2;
            border-left: 4px solid #dc2626;
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 24px;
            animation: shake 0.4s;
        }

        @keyframes shake {
            0%,100%{ transform:translateX(0); }
            25%{ transform:translateX(-5px); }
            75%{ transform:translateX(5px); }
        }

        /* Form fields */
        .field-group {
            margin-bottom: 24px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-slate);
            margin-bottom: 8px;
            display: block;
        }

        .field-input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid var(--border-color);
            border-radius: 16px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            background: white;
        }

        .field-input:focus {
            border-color: var(--gold-premium);
            box-shadow: 0 0 0 3px rgba(212,175,55,0.2);
            outline: none;
            transform: translateY(-1px);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-eye {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s;
        }

        .toggle-eye:hover {
            color: var(--gold-premium);
        }

        /* Tombol submit */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--navy-primary);
            color: white;
            border: none;
            border-radius: 40px;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: var(--navy-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,25,125,0.4);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            transform: none;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .form-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-side { display: none; }
            .form-side { flex: 1; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- ===== SISI KIRI: HERO DENGAN LOGO GAMBAR, BINTANG JATUH, DAN ANIMASI ===== --}}
    <div class="hero-side" id="heroSide">

        <!-- Background gelombang -->
        <div class="wave-bg">
            <svg viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="rgba(212,175,55,0.1)" fill-opacity="1" d="M0,192L48,186.7C96,181,192,171,288,176C384,181,480,203,576,213.3C672,224,768,224,864,213.3C960,203,1056,181,1152,160C1248,139,1344,117,1392,106.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
            <svg viewBox="0 0 1440 320" preserveAspectRatio="none" style="animation-delay: -3s;">
                <path fill="rgba(212,175,55,0.05)" fill-opacity="1" d="M0,256L48,245.3C96,235,192,213,288,208C384,203,480,213,576,229.3C672,245,768,267,864,256C960,245,1056,203,1152,181.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>

        <!-- Orb berputar -->
        <div class="orb" style="top: -100px; right: -100px;"></div>
        <div class="orb" style="bottom: -80px; left: -80px; width: 200px; height: 200px; animation-duration: 25s;"></div>

        <!-- Floating shapes (dekorasi) -->
        <div class="floating-shape" style="width: 120px; height: 120px; top: 20%; left: 10%;"></div>
        <div class="floating-shape" style="width: 80px; height: 80px; bottom: 15%; right: 15%; animation-duration: 18s;"></div>

        <!-- LOGO BESAR (GAMBAR) -->
        <div class="hero-logo">
            <img src="{{ asset('img/icon-purnama.png') }}" alt="Logo Purnama" class="hero-logo-img">
        </div>

        <div class="hero-tagline">Management System</div>
        <h1 class="hero-title">
            Experience <span>Excellence</span>
        </h1>
        <p class="hero-subtitle">
            Kelola hotel dan restoran dengan platform terintegrasi, real-time, dan elegan.
        </p>

        <!-- Dekorasi titik-titik -->
        <div style="margin-top: 50px; display: flex; gap: 16px; justify-content: center; position: relative; z-index: 2;">
            <div style="width: 6px; height: 6px; background: var(--gold-premium); border-radius: 50%; opacity: 0.6;"></div>
            <div style="width: 6px; height: 6px; background: var(--gold-premium); border-radius: 50%; opacity: 0.4;"></div>
            <div style="width: 6px; height: 6px; background: var(--gold-premium); border-radius: 50%; opacity: 0.2;"></div>
        </div>
    </div>

    {{-- ===== SISI KANAN: FORM (BERSIH) ===== --}}
    <div class="form-side">
        <div class="login-card">

            <div class="card-header-section">
                <p class="card-eyebrow">Selamat Datang Kembali</p>
                <h2 class="card-title">Masuk ke Akun</h2>
                <div class="card-desc">Silakan masukkan kredensial Anda</div>
            </div>

            @if($errors->has('email'))
                <div class="alert-premium">
                    <i class="fas fa-circle-exclamation me-2"></i> {{ $errors->first('email') }}
                </div>
            @endif

            @if($errors->has('password'))
                <div class="alert-premium">
                    <i class="fas fa-circle-exclamation me-2"></i> {{ $errors->first('password') }}
                </div>
            @endif

            <form action="{{ route('dashboard.login.post') }}" method="POST" id="loginForm">
                @csrf

                <div class="field-group">
                    <label class="field-label">Email</label>
                    <input type="email" name="email" class="field-input" placeholder="admin@purnama.com" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="field-group">
                    <label class="field-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="field-input" placeholder="••••••••" required>
                        <button type="button" class="toggle-eye" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="loginBtn">
                    <span id="btnContent">Masuk ke Dashboard <i class="fas fa-arrow-right"></i></span>
                </button>
            </form>

            <div class="form-footer">
                &copy; {{ date('Y') }} Purnama Hotel & Resto. All rights reserved.
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const toggle = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (toggle) {
            toggle.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }

        // Loading state on submit
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');
        const btnContent = document.getElementById('btnContent');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) return;
                btn.disabled = true;
                btnContent.innerHTML = '<span class="spinner"></span> Memproses...';
            });
        }

        // === BINTANG JATUH (SHOOTING STARS) ===
        const hero = document.getElementById('heroSide');
        if (hero) {
            const starCount = 35; // jumlah bintang jatuh
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                // posisi awal acak di area hero
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                // durasi dan delay acak
                const duration = 4 + Math.random() * 6;
                star.style.animationDuration = duration + 's';
                star.style.animationDelay = Math.random() * 8 + 's';
                // ukuran bervariasi
                const size = 1 + Math.random() * 3;
                star.style.width = size + 'px';
                star.style.height = size + 'px';
                hero.appendChild(star);
            }
        }

        // Floating shapes dinamis tambahan (agar lebih ramai)
        if (hero) {
            for (let i = 0; i < 12; i++) {
                const shape = document.createElement('div');
                shape.className = 'floating-shape';
                const size = Math.random() * 70 + 10;
                shape.style.width = size + 'px';
                shape.style.height = size + 'px';
                shape.style.top = Math.random() * 100 + '%';
                shape.style.left = Math.random() * 100 + '%';
                shape.style.animationDuration = 10 + Math.random() * 20 + 's';
                shape.style.animationDelay = Math.random() * 8 + 's';
                hero.appendChild(shape);
            }
        }
    });
</script>
</body>
</html>