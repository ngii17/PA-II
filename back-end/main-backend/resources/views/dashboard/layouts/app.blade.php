<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Purnama Hotel & Resto</title>

    {{-- CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background: #f5f6fa; margin: 0; }
        .sidebar { min-height: 100vh; background: #1a1a2e; width: 260px; color: white; position: fixed; z-index: 100; }
        .sidebar .brand { padding: 20px; font-size: 1.2rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-link { color: #a0a0b0; padding: 12px 20px; font-size: 14px; border-radius: 0; transition: all 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .menu-label { font-size: 11px; color: #6b6b80; font-weight: 700; text-transform: uppercase; padding: 20px 20px 5px; letter-spacing: 1px; }

        .main-content { margin-left: 260px; padding: 25px; transition: all 0.3s; }
        .topbar { background: white; padding: 10px 25px; margin-bottom: 25px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }

        /* Dropdown Profile Style */
        #profileDropdown { transition: all 0.3s; border-radius: 30px; padding: 5px 15px; }
        #profileDropdown:hover { background: #f8f9fa; }
        .dropdown-menu { border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; border: none; margin-top: 10px !important; }
        .dropdown-item { font-size: 14px; padding: 10px 20px; transition: all 0.2s; }
        .dropdown-item:hover { background: #f0f4ff; color: #0d6efd; }
    </style>
</head>
<body>

{{-- SIDEBAR --}}
<div class="sidebar p-0">
    <div class="brand">🏨 Purnama Dashboard</div>
    <nav style="padding: 0 8px;">

        {{-- LINK DASHBOARD UTAMA --}}
        <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <i class="fas fa-chart-line me-2"></i> Dashboard
        </a>

        {{-- ========================================== --}}
        {{-- MENU KHUSUS ADMIN (Management Global) --}}
        {{-- ========================================== --}}
        @if(session('user.role') === 'admin')
            <div class="menu-label">Admin Management</div>
            <a href="{{ route('dashboard.pengguna') }}" class="nav-link {{ request()->routeIs('dashboard.pengguna') ? 'active' : '' }}">
                <i class="fas fa-users me-2"></i> Data Pengguna
            </a>
            <a href="{{ route('dashboard.pembayaran') }}" class="nav-link {{ request()->routeIs('dashboard.pembayaran') ? 'active' : '' }}">
                <i class="fas fa-credit-card me-2"></i> Seluruh Pembayaran
            </a>
            <a href="{{ route('dashboard.promo.index') }}" class="nav-link {{ request()->routeIs('dashboard.promo*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt me-2"></i> Kelola Promo
            </a>
            <a href="{{ route('dashboard.ulasan') }}" class="nav-link {{ request()->routeIs('dashboard.ulasan') ? 'active' : '' }}">
                <i class="fas fa-star me-2"></i> Seluruh Ulasan
            </a>
            <a href="{{ route('dashboard.laporan') }}" class="nav-link {{ request()->routeIs('dashboard.laporan') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar me-2"></i> Laporan Sistem
            </a>
        @endif

        {{-- ========================================== --}}
        {{-- MENU KHUSUS STAFF HOTEL --}}
        {{-- ========================================== --}}
        @if(session('user.role') === 'staff_hotel')
            <div class="menu-label">Hotel Management</div>
            <a href="{{ route('dashboard.hotel.kamar.index') }}" class="nav-link {{ request()->routeIs('dashboard.hotel.kamar*') ? 'active' : '' }}">
                <i class="fas fa-bed me-2"></i> Data Kamar
            </a>
            <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}" class="nav-link {{ request()->routeIs('dashboard.hotel.tipe-kamar*') ? 'active' : '' }}">
                <i class="fas fa-tags me-2"></i> Tipe Kamar
            </a>
            <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="nav-link {{ request()->routeIs('dashboard.hotel.reservasi*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-2"></i> Reservasi Hotel
            </a>
            <a href="{{ route('dashboard.hotel.pembayaran') }}" class="nav-link {{ request()->routeIs('dashboard.hotel.pembayaran') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave me-2"></i> Pembayaran Hotel
            </a>
            <a href="{{ route('dashboard.hotel.ulasan') }}" class="nav-link {{ request()->routeIs('dashboard.hotel.ulasan') ? 'active' : '' }}">
                <i class="fas fa-comment-alt me-2"></i> Ulasan Hotel
            </a>
        @endif

        {{-- ========================================== --}}
        {{-- MENU KHUSUS STAFF RESTORAN (LENGKAP) --}}
        {{-- ========================================== --}}
        @if(session('user.role') === 'staff_restoran')
            <div class="menu-label">Restoran Management</div>

            <a href="{{ route('dashboard.restoran.kategori.index') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.kategori*') ? 'active' : '' }}">
                <i class="fas fa-folder me-2"></i> Kategori Menu
            </a>

            <a href="{{ route('dashboard.restoran.menu.index') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.menu.index') ? 'active' : '' }}">
                <i class="fas fa-utensils me-2"></i> Menu Restoran
            </a>

            <a href="{{ route('dashboard.restoran.pesanan.index') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.pesanan*') ? 'active' : '' }}">
                <i class="fas fa-shopping-basket me-2"></i> Pesanan Restoran
            </a>

            <a href="{{ route('dashboard.restoran.stok') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.stok*') ? 'active' : '' }}">
                <i class="fas fa-box-open me-2"></i> Stok Menu
            </a>

            <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.menu-event*') ? 'active' : '' }}">
                <i class="fas fa-calendar-day me-2"></i> Menu Event
            </a>

            <a href="{{ route('dashboard.restoran.event') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.event*') ? 'active' : '' }}">
                <i class="fas fa-magic me-2"></i> Event Restoran
            </a>

            <a href="{{ route('dashboard.restoran.pembayaran') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.pembayaran*') ? 'active' : '' }}">
                <i class="fas fa-receipt me-2"></i> Pembayaran Restoran
            </a>

            <a href="{{ route('dashboard.restoran.ulasan') }}" class="nav-link {{ request()->routeIs('dashboard.restoran.ulasan*') ? 'active' : '' }}">
                <i class="fas fa-comments me-2"></i> Ulasan Restoran
            </a>
        @endif

    </nav>
</div>

{{-- MAIN CONTENT --}}
<div class="main-content">
    {{-- TOPBAR --}}
    <div class="topbar d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-secondary">@yield('title', 'Dashboard')</h6>

        <div class="dropdown">
            <div class="d-flex align-items-center gap-3 pe-auto" id="profileDropdown" data-bs-toggle="dropdown" style="cursor: pointer;">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold text-dark" style="font-size: 13px; line-height: 1;">{{ session('user.name') }}</div>
                    <small class="text-muted" style="font-size: 10px; text-transform: uppercase;">{{ str_replace('_', ' ', session('user.role')) }}</small>
                </div>
                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                    style="width:38px; height:38px; font-size:14px; border: 2px solid #fff;">
                    {{ strtoupper(substr(session('user.name'), 0, 1)) }}
                </div>
            </div>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                <li class="px-3 py-2 border-bottom d-md-none">
                    <div class="fw-bold small">{{ session('user.name') }}</div>
                    <div class="text-muted" style="font-size: 10px;">{{ strtoupper(session('user.role')) }}</div>
                </li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2 text-muted"></i> Profil Saya</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2 text-muted"></i> Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('dashboard.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger fw-bold">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    {{-- PAGE CONTENT --}}
    <div class="page-content">
        @yield('content')
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('scripts')
</body>
</html>
