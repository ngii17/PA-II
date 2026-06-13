<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Purnama Hotel & Resto</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    {{-- Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <style>
        /* ================================================
           DESIGN TOKENS
        ================================================ */
        :root {
            --navy:        #0f172a;
            --navy-mid:    #1e293b;
            --navy-active: #00197D;
            --gold:        #D4AF37;
            --gold-light:  #f3e9b5;
            --bg:          #F1F5F9;
            --white:       #ffffff;
            --border:      rgba(0,0,0,0.07);
            --text-main:   #0f172a;
            --text-muted:  #64748B;
            --text-light:  #94a3b8;
            --sidebar-w:   260px;
            --topbar-h:    68px;
            --radius-sm:   10px;
            --radius-md:   14px;
            --radius-lg:   20px;
            --transition:  0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ================================================
           RESET & BASE
        ================================================ */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            margin: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            color: var(--text-main);
        }

        /* ================================================
           LAYOUT SHELL
        ================================================ */
        .layout-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ================================================
           SIDEBAR
        ================================================ */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--navy);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1100;
            transition: transform var(--transition);
            overflow: hidden;
        }

        /* Branding - DIBUAT BISA DI-KLIK RELOAD */
        .sidebar-brand {
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 13px;
            flex-shrink: 0;
            cursor: pointer;
            transition: opacity var(--transition);
        }
        .sidebar-brand:hover {
            opacity: 0.85;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: var(--radius-sm);
            flex-shrink: 0;
        }

        .sidebar-brand-text .brand-name {
            font-size: 13px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .sidebar-brand-text .brand-sub {
            font-size: 9px;
            font-weight: 700;
            color: var(--gold);
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-top: 2px;
            display: block;
        }

        /* Nav scroll area */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 14px 12px 20px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.08) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.08);
            border-radius: 10px;
        }

        /* Section label */
        .nav-section-label {
            font-size: 9.5px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.8px;
            padding: 20px 10px 8px;
            display: block;
        }

        /* Nav link */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,0.48);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 2px;
            border: 1px solid transparent;
            transition: all var(--transition);
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-item i {
            font-size: 17px;
            width: 22px;
            flex-shrink: 0;
            transition: color var(--transition);
        }

        .nav-item span {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-item:hover {
            color: rgba(255,255,255,0.85);
            background: rgba(255,255,255,0.05);
            transform: translateX(3px);
        }

        .nav-item.active {
            color: #fff;
            background: var(--navy-active);
            border-color: rgba(255,255,255,0.08);
            box-shadow: 0 6px 20px rgba(0,25,125,0.35);
        }

        .nav-item.active i {
            color: var(--gold);
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.05);
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: rgba(255,255,255,0.04);
        }

        .sidebar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--navy-active);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255,255,255,0.85);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 9px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ================================================
           MAIN CONTENT AREA
        ================================================ */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            transition: margin-left var(--transition);
        }

        /* ================================================
           TOPBAR
        ================================================ */
        .topbar {
            height: var(--topbar-h);
            min-height: var(--topbar-h);
            background: rgba(255,255,255,0.90);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .topbar-toggle {
            display: none;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            background: var(--bg);
            border: 1px solid var(--border);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-main);
            font-size: 18px;
            flex-shrink: 0;
        }

        .topbar-page-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--navy-active);
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .topbar-breadcrumb {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-divider {
            width: 1px;
            height: 18px;
            background: var(--border);
            flex-shrink: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 6px 14px 6px 8px;
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }

        .profile-trigger:hover {
            background: var(--bg);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        }

        .profile-avatar {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--navy-active);
            color: var(--white);
            font-size: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            letter-spacing: 0.5px;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.3;
            white-space: nowrap;
        }

        .profile-role {
            font-size: 9.5px;
            color: var(--text-light);
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .profile-chevron {
            font-size: 13px;
            color: var(--text-light);
            margin-left: 2px;
        }

        /* Dropdown */
        .dropdown-menu {
            border-radius: var(--radius-lg) !important;
            box-shadow: 0 20px 50px rgba(0,0,0,0.10) !important;
            border: 1px solid var(--border) !important;
            padding: 10px !important;
            min-width: 200px;
            margin-top: 10px !important;
        }

        .dropdown-item {
            border-radius: var(--radius-sm) !important;
            padding: 10px 14px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: var(--text-muted) !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            transition: all 0.2s !important;
        }

        .dropdown-item i { font-size: 16px; width: 18px; }

        .dropdown-item:hover {
            background: var(--bg) !important;
            color: var(--navy-active) !important;
        }

        .dropdown-item.text-danger { color: #ef4444 !important; }
        .dropdown-item.text-danger:hover {
            background: #fef2f2 !important;
            color: #dc2626 !important;
        }

        .dropdown-divider {
            margin: 6px 0 !important;
            border-color: var(--border) !important;
        }

        /* ================================================
           PAGE CONTENT
        ================================================ */
        .page-wrapper {
            flex: 1;
            padding: 28px 32px;
            overflow-x: hidden;
        }

        /* ================================================
           MOBILE OVERLAY
        ================================================ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1050;
            backdrop-filter: blur(2px);
        }

        /* ================================================
           RESPONSIVE
        ================================================ */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.sidebar-open {
                transform: translateX(0);
                box-shadow: 20px 0 60px rgba(0,0,0,0.25);
            }
            .sidebar-overlay.overlay-open { display: block; }
            .main-wrapper { margin-left: 0; }
            .topbar-toggle { display: flex; }
            .profile-info { display: none; }
            .profile-trigger { padding: 6px 8px; }
            .page-wrapper { padding: 20px 16px; }
        }

        @media (max-width: 576px) {
            .topbar { padding: 0 16px; }
            .topbar-breadcrumb { display: none; }
            .topbar-divider { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="layout-shell">

    {{-- ======================================
         SIDEBAR
    ====================================== --}}
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="Navigasi Utama">

        {{-- BRAND AREA - KLIK UNTUK RELOAD HALAMAN --}}
        <div class="sidebar-brand" id="reloadButton">
            <img src="{{ asset('img/icon-purnama.png') }}" class="sidebar-logo" alt="Logo Purnama">
            <div class="sidebar-brand-text">
                <div class="brand-name">PURNAMA</div>
                <span class="brand-sub">Hotel &amp; Resto</span>
            </div>
        </div>

        <nav class="sidebar-nav">

            {{-- Dashboard (semua role) --}}
            <a href="{{ route('dashboard.index') }}"
               class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>

            {{-- ============================================
                 MENU KHUSUS ADMIN
            ============================================ --}}
            @if(session('user.role') === 'admin')
                <span class="nav-section-label">Admin Control</span>

                <a href="{{ route('dashboard.pengguna') }}"
                   class="nav-item {{ request()->routeIs('dashboard.pengguna') ? 'active' : '' }}">
                    <i class="ti ti-shield-check" aria-hidden="true"></i>
                    <span>Data Pengguna</span>
                </a>

                <a href="{{ route('dashboard.pembayaran') }}"
                   class="nav-item {{ request()->routeIs('dashboard.pembayaran') ? 'active' : '' }}">
                    <i class="ti ti-wallet" aria-hidden="true"></i>
                    <span>Seluruh Pembayaran</span>
                </a>

                <a href="{{ route('dashboard.promo.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.promo*') ? 'active' : '' }}">
                    <i class="ti ti-tag" aria-hidden="true"></i>
                    <span>Kelola Promo</span>
                </a>

                <a href="{{ route('dashboard.ulasan') }}"
                   class="nav-item {{ request()->routeIs('dashboard.ulasan') ? 'active' : '' }}">
                    <i class="ti ti-message-dots" aria-hidden="true"></i>
                    <span>Seluruh Ulasan</span>
                </a>

                <a href="{{ route('dashboard.laporan') }}"
                   class="nav-item {{ request()->routeIs('dashboard.laporan') ? 'active' : '' }}">
                    <i class="ti ti-file-invoice" aria-hidden="true"></i>
                    <span>Laporan Sistem</span>
                </a>

                <a href="{{ route('dashboard.event.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.event*') ? 'active' : '' }}">
                    <i class="ti ti-sparkles" aria-hidden="true"></i>
                    <span>Tema Aplikasi</span>
                </a>

                <a href="{{ route('dashboard.admin.broadcast.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.broadcast*') ? 'active' : '' }}">
                    <i class="ti ti-speakerphone" aria-hidden="true"></i>
                    <span>Broadcast Notif</span>
                </a>
            @endif

            {{-- ============================================
                 MENU KHUSUS STAFF HOTEL
            ============================================ --}}
            @if(session('user.role') === 'staff_hotel')
                <span class="nav-section-label">Hotel Management</span>

                <a href="{{ route('dashboard.hotel.kamar.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.hotel.kamar*') ? 'active' : '' }}">
                    <i class="ti ti-bed" aria-hidden="true"></i>
                    <span>Data Kamar</span>
                </a>

                <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.hotel.tipe-kamar*') ? 'active' : '' }}">
                    <i class="ti ti-key" aria-hidden="true"></i>
                    <span>Tipe Kamar</span>
                </a>

                <a href="{{ route('dashboard.hotel.reservasi.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.hotel.reservasi*') ? 'active' : '' }}">
                    <i class="ti ti-calendar-check" aria-hidden="true"></i>
                    <span>Reservasi Hotel</span>
                </a>

                <a href="{{ route('dashboard.hotel.pembayaran') }}"
                   class="nav-item {{ request()->routeIs('dashboard.hotel.pembayaran') ? 'active' : '' }}">
                    <i class="ti ti-cash-register" aria-hidden="true"></i>
                    <span>Pembayaran Hotel</span>
                </a>

                <a href="{{ route('dashboard.hotel.ulasan') }}"
                   class="nav-item {{ request()->routeIs('dashboard.hotel.ulasan') ? 'active' : '' }}">
                    <i class="ti ti-star-half" aria-hidden="true"></i>
                    <span>Ulasan Hotel</span>
                </a>
            @endif

            {{-- ============================================
                 MENU KHUSUS STAFF RESTORAN
            ============================================ --}}
            @if(session('user.role') === 'staff_restoran')
                <span class="nav-section-label">Restoran Management</span>

                <a href="{{ route('dashboard.restoran.kategori.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.restoran.kategori*') ? 'active' : '' }}">
                    <i class="ti ti-folder" aria-hidden="true"></i>
                    <span>Kategori Menu</span>
                </a>

                <a href="{{ route('dashboard.restoran.menu.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.restoran.menu.index') ? 'active' : '' }}">
                    <i class="ti ti-tools-kitchen-2" aria-hidden="true"></i>
                    <span>Menu Restoran</span>
                </a>

                <a href="{{ route('dashboard.restoran.pesanan.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.restoran.pesanan*') ? 'active' : '' }}">
                    <i class="ti ti-shopping-cart" aria-hidden="true"></i>
                    <span>Pesanan Restoran</span>
                </a>

                <a href="{{ route('dashboard.restoran.stok') }}"
                   class="nav-item {{ request()->routeIs('dashboard.restoran.stok*') ? 'active' : '' }}">
                    <i class="ti ti-package" aria-hidden="true"></i>
                    <span>Stok Menu</span>
                </a>

                <a href="{{ route('dashboard.restoran.menu-event.index') }}"
                   class="nav-item {{ request()->routeIs('dashboard.restoran.menu-event*') ? 'active' : '' }}">
                    <i class="ti ti-calendar-event" aria-hidden="true"></i>
                    <span>Menu Event</span>
                </a>

                <a href="{{ route('dashboard.restoran.pembayaran') }}"
                   class="nav-item {{ request()->routeIs('dashboard.restoran.pembayaran*') ? 'active' : '' }}">
                    <i class="ti ti-receipt" aria-hidden="true"></i>
                    <span>Pembayaran Restoran</span>
                </a>

                <a href="{{ route('dashboard.restoran.ulasan') }}"
                   class="nav-item {{ request()->routeIs('dashboard.restoran.ulasan*') ? 'active' : '' }}">
                    <i class="ti ti-message-2" aria-hidden="true"></i>
                    <span>Ulasan Restoran</span>
                </a>
            @endif

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(session('user.name', 'U'), 0, 1)) }}
                </div>
                <div style="overflow: hidden; min-width: 0;">
                    <div class="sidebar-user-name">{{ session('user.name') }}</div>
                    <div class="sidebar-user-role">{{ str_replace('_', ' ', session('user.role')) }}</div>
                </div>
            </div>
        </div>

    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-wrapper">

        <header class="topbar" role="banner">
            <div class="topbar-left">
                <button class="topbar-toggle" id="sidebarToggle" aria-label="Buka navigasi">
                    <i class="ti ti-menu-2" aria-hidden="true"></i>
                </button>
                <span class="topbar-page-title">@yield('title', 'Dashboard')</span>
                @hasSection('breadcrumb')
                    <div class="topbar-divider" aria-hidden="true"></div>
                    <span class="topbar-breadcrumb">@yield('breadcrumb')</span>
                @endif
            </div>

            <div class="topbar-right">
                <div class="dropdown">
                    <div class="profile-trigger"
                         id="profileDropdown"
                         data-bs-toggle="dropdown"
                         aria-expanded="false"
                         role="button"
                         tabindex="0"
                         aria-haspopup="true">
                        <div class="profile-avatar">
                            {{ strtoupper(substr(session('user.name', 'U'), 0, 1)) }}
                        </div>
                        <div class="profile-info">
                            <span class="profile-name">{{ session('user.name') }}</span>
                            <span class="profile-role">{{ str_replace('_', ' ', session('user.role')) }}</span>
                        </div>
                        <i class="ti ti-chevron-down profile-chevron" aria-hidden="true"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 border-bottom d-md-none">
                            <div class="fw-bold small" style="font-size:13px; color: var(--text-main);">{{ session('user.name') }}</div>
                            <div style="font-size:10px; color: var(--text-muted); text-transform:uppercase; letter-spacing:1px;">{{ session('user.role') }}</div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard.profile') }}">
                                <i class="ti ti-user-circle"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('dashboard.logout') }}" method="POST" id="logoutForm">
                                @csrf
                                <button type="button"
                                        class="dropdown-item text-danger"
                                        onclick="confirmLogout()">
                                    <i class="ti ti-power" aria-hidden="true"></i>
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="page-wrapper" id="pageContent">
            @yield('content')
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    // Tombol reload pada brand area
    const reloadBtn = document.getElementById('reloadButton');
    if (reloadBtn) {
        reloadBtn.addEventListener('click', function() {
            window.location.reload();
        });
    }

    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        overlay.classList.add('overlay-open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        overlay.classList.remove('overlay-open');
        document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
    });

    overlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    function confirmLogout() {
        Swal.fire({
            title: 'Keluar dari sistem?',
            text: 'Sesi Anda akan diakhiri.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#00197D',
            cancelButtonColor: '#64748B',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        background: '#ffffff',
        color: '#0f172a',
        customClass: { popup: 'rounded-4 shadow border' },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: @json(session('success')) });
    @endif

    @if(session('error'))
        Toast.fire({ icon: 'error', title: @json(session('error')) });
    @endif

    @if(session('warning'))
        Toast.fire({ icon: 'warning', title: @json(session('warning')) });
    @endif

    @if(session('info'))
        Toast.fire({ icon: 'info', title: @json(session('info')) });
    @endif
</script>

@stack('scripts')
</body>
</html>