@extends('dashboard.layouts.app')
@section('title', 'Dashboard Utama')
@section('content')

{{-- ================================================================
     DASHBOARD PREMIUM — Purnama Hotel & Resto
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ============================================================
   ROOT VARIABLES & RESET
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --gold:         #D4AF37;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e2e8f0;
    --text-primary: #0f172a;
    --text-mid:     #475569;
    --text-muted:   #94a3b8;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button { font-family: var(--font) !important; }
.fw-800 { font-weight: 800 !important; letter-spacing: -.02em; }

/* ============================================================
   PAGE WRAPPER (BACKGROUND LUXURY)
   ============================================================ */
.dash-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px 64px;
    position: relative;
    overflow-x: hidden;
}
.dash-page-wrapper::before,
.dash-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.dash-page-wrapper::before {
    width: 560px; height: 560px;
    top: -180px; right: -130px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.dash-page-wrapper::after {
    width: 380px; height: 380px;
    bottom: -100px; left: -90px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.dash-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER WELCOME
   ============================================================ */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
}
.dash-header-left h2 {
    font-size: 1.9rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 5px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.dash-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.dash-header-left p {
    color: var(--text-muted);
    font-size: .875rem;
    font-weight: 500;
    margin: 0;
}
.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 18px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .5px;
    text-transform: uppercase;
    border: 1.5px solid;
}
.role-admin    { background: rgba(0,25,125,.07); color: var(--navy); border-color: rgba(0,25,125,.15); }
.role-hotel    { background: rgba(21,128,61,.07); color: #15803D; border-color: rgba(21,128,61,.2); }
.role-restoran { background: rgba(180,83,9,.07); color: #B45309; border-color: rgba(180,83,9,.2); }

/* ============================================================
   STATS STRIP (ANIMATED)
   ============================================================ */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--surface);
    border-radius: 20px;
    padding: 20px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    opacity: 0;
    transform: translateY(18px);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: 4px 4px 0 0;
}
.stat-card.sc-blue::before   { background: #1D4ED8; }
.stat-card.sc-green::before  { background: #15803D; }
.stat-card.sc-amber::before  { background: #B45309; }
.stat-card.sc-red::before    { background: #DC2626; }
.stat-card.sc-purple::before { background: #7C3AED; }
.stat-card.sc-teal::before   { background: #0F766E; }
.stat-card.sc-gray::before   { background: #475569; }
.stat-card.sc-navy::before   { background: var(--navy); }
.stat-card.sc-gold::before   { background: var(--gold); }
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }

.stat-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
    margin-bottom: 14px;
}
.si-blue   { background: rgba(29,78,216,.08);  color: #1D4ED8; }
.si-green  { background: rgba(21,128,61,.08);  color: #15803D; }
.si-amber  { background: rgba(180,83,9,.08);   color: #B45309; }
.si-red    { background: rgba(220,38,38,.08);  color: #DC2626; }
.si-purple { background: rgba(124,58,237,.08); color: #7C3AED; }
.si-teal   { background: rgba(15,118,110,.08); color: #0F766E; }
.si-gray   { background: rgba(71,85,105,.08);  color: #475569; }
.si-navy   { background: rgba(0,25,125,.08);   color: var(--navy); }
.si-gold   { background: rgba(212,175,55,.1);  color: #B8960A; }

.stat-label {
    font-size: .62rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 5px;
}
.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.04em;
    line-height: 1;
    margin-bottom: 2px;
}
.stat-number.stat-sm {
    font-size: 1.3rem;
}
.stat-sub {
    font-size: .68rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* ============================================================
   CARD PREMIUM
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 24px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 20px;
}
.card-premium-pad {
    background: var(--surface);
    border-radius: 24px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    padding: 28px;
    margin-bottom: 20px;
}
.card-title {
    font-size: .9rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 9px;
}
.chart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
}
.chart-container { position: relative; height: 280px; }

/* ============================================================
   TABLE STYLE
   ============================================================ */
.p-table {
    width: 100%;
    border-collapse: collapse;
}
.p-table thead th {
    background: var(--navy);
    color: rgba(255,255,255,.8);
    padding: 14px 20px;
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 700;
    white-space: nowrap;
    border: none;
}
.p-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
    opacity: 0;
    transform: translateY(8px);
}
.p-table tbody tr:last-child { border-bottom: none; }
.p-table tbody tr:hover { background: #fdf6e3; }
.p-table tbody td { padding: 14px 20px; vertical-align: middle; }

.avatar-user {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800;
    color: #fff;
    font-size: .9rem;
    flex-shrink: 0;
}
.tbl-name { font-weight: 800; font-size: .88rem; color: var(--text-primary); }
.tbl-sub  { font-size: .72rem; color: var(--text-muted); font-weight: 600; }
.tbl-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .7rem;
}
.badge-success { background: #dcfce7; color: #15803d; }
.badge-warn    { background: #fef3c7; color: #b45309; }

.rating-stars {
    font-size: 0.75rem;
    color: var(--gold);
    letter-spacing: 1px;
}
.komentar-preview {
    max-width: 250px;
}
.komentar-text {
    font-size: 0.75rem;
    color: var(--text-mid);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.badge-ulasan {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .65rem;
}
.badge-visible { background: #dcfce7; color: #15803d; }
.badge-hidden  { background: #fee2e2; color: #b91c1c; }
.btn-action-toggle {
    background: none;
    border: none;
    padding: 6px 12px;
    border-radius: 10px;
    font-size: .7rem;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-toggle-show { background: rgba(16,185,129,.1); color: #15803d; }
.btn-toggle-show:hover { background: #10b981; color: white; }
.btn-toggle-hide { background: rgba(225,29,72,.1); color: #b91c1c; }
.btn-toggle-hide:hover { background: #e11d48; color: white; }
.empty-state {
    padding: 56px 24px;
    text-align: center;
}
.empty-icon {
    width: 64px; height: 64px;
    background: var(--surface-2);
    border-radius: 18px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    color: var(--text-muted);
    margin-bottom: 14px;
    border: 2px dashed var(--border);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .dash-header-left h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
    .chart-grid { grid-template-columns: 1fr; }
    .komentar-preview { max-width: 180px; }
}
</style>

<!-- ================================================================
     MAIN DASHBOARD CONTENT
     ================================================================ -->
<div class="dash-page-wrapper">

    <!-- HEADER WELCOME -->
    <div class="dash-header">
        <div class="dash-header-left">
            <h2 class="fw-800">Selamat Datang, <span>{{ session('user.name') }}!</span></h2>
            <p>{{ now()->translatedFormat('l, d F Y') }} — <strong style="color:var(--navy);">Purnama Hotel &amp; Resto</strong></p>
        </div>
        <div>
            @if(session('user.role') === 'admin')
                <span class="role-badge role-admin"><i class="fas fa-shield-alt"></i> Administrator</span>
            @elseif(session('user.role') === 'staff_hotel')
                <span class="role-badge role-hotel"><i class="fas fa-hotel"></i> Staff Hotel</span>
            @elseif(session('user.role') === 'staff_restoran')
                <span class="role-badge role-restoran"><i class="fas fa-utensils"></i> Staff Restoran</span>
            @endif
        </div>
    </div>

    {{-- ============================================================
         VIEW: ADMIN (Global Management)
         ============================================================ --}}
    @if(session('user.role') === 'admin')

    <div class="stats-strip">
        <div class="stat-card sc-blue">
            <div class="stat-icon si-blue"><i class="fas fa-users"></i></div>
            <div class="stat-label">Pengguna Terdaftar</div>
            <div class="stat-number" data-count="{{ $data['total_pengguna'] ?? 0 }}">0</div>
            <div class="stat-sub">Total akun aktif</div>
        </div>
        <div class="stat-card sc-purple">
            <div class="stat-icon si-purple"><i class="fas fa-tags"></i></div>
            <div class="stat-label">Promo Aktif</div>
            <div class="stat-number" data-count="{{ $data['total_promo'] ?? 0 }}">0</div>
            <div class="stat-sub">Sedang berjalan</div>
        </div>
        <div class="stat-card sc-gold">
            <div class="stat-icon si-gold"><i class="fas fa-star"></i></div>
            <div class="stat-label">Total Ulasan</div>
            <div class="stat-number" data-count="{{ $data['total_ulasan'] ?? 0 }}">0</div>
            <div class="stat-sub">Hotel &amp; Restoran</div>
        </div>
        <div class="stat-card sc-green">
            <div class="stat-icon si-green"><i class="fas fa-coins"></i></div>
            <div class="stat-label">Pendapatan Global</div>
            <div class="stat-number stat-sm">Rp {{ number_format(($data['total_pendapatan'] ?? 0) / 1000000, 1) }}Jt</div>
            <div class="stat-sub">Akumulasi seluruh layanan</div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="card-premium-pad" style="margin-bottom:0;">
            <div class="card-title">
                <i class="fas fa-chart-bar" style="color:#1D4ED8;"></i>
                Performa Bisnis (6 Bulan Terakhir)
            </div>
            <div class="chart-container"><canvas id="chartUtama"></canvas></div>
        </div>
        <div class="card-premium-pad" style="margin-bottom:0;">
            <div class="card-title">
                <i class="fas fa-chart-donut" style="color:var(--gold);"></i>
                Status Reservasi
            </div>
            <div class="chart-container" style="display:flex;align-items:center;">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
    </div>

    <!-- KARTU ULASAN TERBARU DENGAN TOMBOL TOGGLE -->
    <div class="card-premium">
        <div style="padding:22px 24px 14px;display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;background:rgba(212,175,55,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--gold);">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <div class="card-title" style="margin-bottom:0;font-size:.88rem;">Ulasan Terbaru</div>
                <div style="font-size:.72rem;color:var(--text-muted);font-weight:600;">Kelola tampilan ulasan di halaman publik</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="p-table" aria-label="Tabel ulasan terbaru">
                <thead>
                    <tr><th class="text-center">#</th><th>Pelanggan</th><th>Rating</th><th>Komentar</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @php $ulasanList = $data['ulasan'] ?? []; @endphp
                    @php $ulasanList = $data['ulasan'] ?? []; @endphp
                    @forelse($ulasanList as $i => $u)
                    @php
                        $user = $data['users'][$u->user_id] ?? null;
                        $nama = $user['full_name'] ?? 'Tamu';
                        $colors = ['#00197D','#1D4ED8','#15803D','#B45309','#9333EA'];
                        $avatarBg = $colors[($u->user_id ?? $i) % 5];
                        $isHidden = $u->is_hidden ?? false;
                        $tipe = $u instanceof \App\Models\hotel\UlasanHotel ? 'hotel' : 'restoran';
                    @endphp
                        <tr data-ulasan-id="{{ $u->id }}">
                            <td class="text-center" style="color:var(--text-muted);font-weight:800;">{{ $i+1 }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:11px;">
                                    <div class="avatar-user" style="background:{{ $avatarBg }};">{{ strtoupper(substr($nama,0,1)) }}</div>
                                    <div><div class="tbl-name">{{ $nama }}</div><div class="tbl-sub">{{ $user['email'] ?? '-' }}</div></div>
                                </div>
                            </td>
                            <td>
                                <div class="rating-stars">
                                    @for($s=1;$s<=5;$s++)
                                        @if($s <= $u->rating) <i class="fas fa-star"></i> @else <i class="far fa-star"></i> @endif
                                    @endfor
                                    <span style="font-size:0.65rem;color:var(--text-muted);margin-left:4px;">({{ $u->rating }})</span>
                                </div>
                            </td>
                            <td><div class="komentar-text">{{ Str::limit($u->komentar ?? '-', 50) }}</div></td>
                            <td class="text-center"><span class="badge-ulasan {{ $isHidden ? 'badge-hidden' : 'badge-visible' }}"><i class="fas {{ $isHidden ? 'fa-eye-slash' : 'fa-eye' }}"></i> {{ $isHidden ? 'Tersembunyi' : 'Tampil' }}</span></td>
                            <td class="text-center">
                                <button class="btn-action-toggle {{ $isHidden ? 'btn-toggle-show' : 'btn-toggle-hide' }}" onclick="toggleUlasan({{ $u->id }}, '{{ $tipe }}', {{ $isHidden ? 'true' : 'false' }}, '{{ addslashes($nama) }}')">
                                    <i class="fas {{ $isHidden ? 'fa-eye' : 'fa-eye-slash' }}"></i> {{ $isHidden ? 'Tampilkan' : 'Sembunyikan' }}
                                </button>
                                <form id="form-toggle-{{ $tipe }}-{{ $u->id }}" action="{{ route('dashboard.ulasan.toggle', ['tipe' => $tipe, 'id' => $u->id]) }}" method="POST" style="display:none;">@csrf @method('PATCH')</form>
                            </td>
                        </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-star"></i></div><h5 style="font-weight:800;">Belum Ada Ulasan</h5><p style="font-size:.875rem;color:var(--text-muted);">Ulasan dari pelanggan akan muncul di sini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================
         VIEW: STAFF HOTEL (Operational)
         ============================================================ --}}
    @elseif(session('user.role') === 'staff_hotel')

    <div class="stats-strip">
        <div class="stat-card sc-blue">
            <div class="stat-icon si-blue"><i class="fas fa-sign-in-alt"></i></div>
            <div class="stat-label">Check-In Hari Ini</div>
            <div class="stat-number" data-count="{{ $data['checkin_today'] ?? 0 }}">0</div>
        </div>
        <div class="stat-card sc-red">
            <div class="stat-icon si-red"><i class="fas fa-sign-out-alt"></i></div>
            <div class="stat-label">Check-Out Hari Ini</div>
            <div class="stat-number" data-count="{{ $data['checkout_today'] ?? 0 }}">0</div>
        </div>
        <div class="stat-card sc-green">
            <div class="stat-icon si-green"><i class="fas fa-door-open"></i></div>
            <div class="stat-label">Kamar Tersedia</div>
            <div class="stat-number" data-count="{{ $data['kamar_tersedia'] ?? 0 }}">0</div>
        </div>
        <div class="stat-card sc-gray">
            <div class="stat-icon si-gray"><i class="fas fa-bed"></i></div>
            <div class="stat-label">Kamar Terisi</div>
            <div class="stat-number" data-count="{{ $data['kamar_terisi'] ?? 0 }}">0</div>
        </div>
    </div>

    {{-- Grid untuk dua chart (line + doughnut) --}}
    <div class="chart-grid">
        <div class="card-premium-pad" style="margin-bottom:0;">
            <div class="card-title">
                <i class="fas fa-chart-line" style="color:#1D4ED8;"></i>
                Tren Reservasi Hotel (6 Bulan Terakhir)
            </div>
            <div class="chart-container" style="height:250px;">
                <canvas id="chartHotelStaff"></canvas>
            </div>
        </div>

        <div class="card-premium-pad" style="margin-bottom:0;">
            <div class="card-title">
                <i class="fas fa-chart-donut" style="color:var(--gold);"></i>
                Distribusi Status Kamar
            </div>
            <div class="chart-container" style="height:250px;">
                <canvas id="chartKamarStatus"></canvas>
            </div>
        </div>
    </div>

    <div class="card-premium">
        <div style="padding:22px 24px 14px;display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;background:rgba(0,25,125,.08);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--navy);"><i class="fas fa-calendar-day"></i></div>
            <div><div class="card-title" style="margin-bottom:0;">Kedatangan Tamu Hari Ini</div><div style="font-size:.72rem;color:var(--text-muted);">{{ count($data['arrival_today'] ?? []) }} tamu dijadwalkan</div></div>
        </div>
        <div class="table-responsive">
            <table class="p-table">
                <thead><tr><th class="text-center">#</th><th>Profil Tamu</th><th>Tipe Kamar</th><th class="text-center">No. Kamar</th><th class="text-center">Status</th></tr></thead>
                <tbody>
                    @forelse(($data['arrival_today'] ?? []) as $i => $arrival)
                    @php
                        $u = $data['users'][$arrival->user_id] ?? null;
                        $nama = $u['full_name'] ?? 'Tamu #'.$arrival->user_id;
                        $colors = ['#00197D','#1D4ED8','#15803D','#B45309','#9333EA'];
                        $avatarBg = $colors[$arrival->user_id % 5];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:11px;">
                                <div class="avatar-user" style="background:{{ $avatarBg }};">{{ strtoupper(substr($nama,0,1)) }}</div>
                                <div><div class="tbl-name">{{ $nama }}</div><div class="tbl-sub">{{ $u['email'] ?? '-' }}</div></div>
                            </div>
                        </td>
                        <td><span style="padding:5px 12px;border-radius:8px;background:rgba(0,25,125,.06);color:var(--navy);font-weight:800;">{{ $arrival->tipeKamar->nama_tipe ?? 'N/A' }}</span></td>
                        <td class="text-center"><span style="display:inline-block;padding:5px 14px;border-radius:8px;background:rgba(29,78,216,.08);color:#1D4ED8;font-weight:800;">{{ $arrival->kamar->nomor_kamar ?? 'N/A' }}</span></td>
                        <td class="text-center"><span class="tbl-badge badge-success">Lunas</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="fas fa-calendar-times"></i></div><h5 style="font-weight:800;">Tidak Ada Kedatangan</h5><p>Belum ada tamu yang dijadwalkan tiba hari ini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================
         VIEW: STAFF RESTORAN
         ============================================================ --}}
    @elseif(session('user.role') === 'staff_restoran')

    <div class="stats-strip">
        <div class="stat-card sc-amber">
            <div class="stat-icon si-amber"><i class="fas fa-receipt"></i></div>
            <div class="stat-label">Pesanan Hari Ini</div>
            <div class="stat-number" data-count="{{ $data['total_pesanan'] ?? 0 }}">0</div>
        </div>
        <div class="stat-card sc-purple">
            <div class="stat-icon si-purple"><i class="fas fa-clock"></i></div>
            <div class="stat-label">Pesanan Pending</div>
            <div class="stat-number" data-count="{{ $data['pesanan_pending'] ?? 0 }}">0</div>
        </div>
        <div class="stat-card sc-teal">
            <div class="stat-icon si-teal"><i class="fas fa-calendar-star"></i></div>
            <div class="stat-label">Event Aktif</div>
            <div class="stat-number" data-count="{{ $data['event_aktif'] ?? 0 }}">0</div>
        </div>
        <div class="stat-card sc-green">
            <div class="stat-icon si-green"><i class="fas fa-coins"></i></div>
            <div class="stat-label">Pendapatan Resto</div>
            <div class="stat-number stat-sm">Rp {{ number_format(($data['total_pendapatan_resto'] ?? 0) / 1000000, 1) }}Jt</div>
        </div>
    </div>

    {{-- Grid untuk dua chart (line + doughnut) --}}
    <div class="chart-grid">
        <div class="card-premium-pad" style="margin-bottom:0;">
            <div class="card-title">
                <i class="fas fa-chart-line" style="color:#15803D;"></i>
                Tren Pesanan Restoran (6 Bulan Terakhir)
            </div>
            <div class="chart-container" style="height:250px;">
                <canvas id="chartRestoStaff"></canvas>
            </div>
        </div>

        <div class="card-premium-pad" style="margin-bottom:0;">
            <div class="card-title">
                <i class="fas fa-chart-donut" style="color:var(--gold);"></i>
                Distribusi Status Pesanan
            </div>
            <div class="chart-container" style="height:250px;">
                <canvas id="chartPesananStatus"></canvas>
            </div>
        </div>
    </div>

    <div class="card-premium">
        <div style="padding:22px 24px 14px;display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;background:rgba(180,83,9,.08);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#B45309;"><i class="fas fa-fire-alt"></i></div>
            <div><div class="card-title" style="margin-bottom:0;">Pesanan Terbaru</div><div style="font-size:.72rem;color:var(--text-muted);">Real-time — diperbarui otomatis</div></div>
        </div>
        <div class="table-responsive">
            <table class="p-table">
                <thead><tr><th class="text-center">#</th><th>Profil Pelanggan</th><th>Total Pesanan</th><th class="text-center">Status Bayar</th></tr></thead>
                <tbody>
                    @forelse(($data['pesanan_terbaru'] ?? []) as $i => $p)
                    @php
                        $u = $data['users'][$p->user_id] ?? null;
                        $nama = $u['full_name'] ?? 'Pelanggan #'.$p->user_id;
                        $colors = ['#B45309','#9333EA','#0F766E','#1D4ED8','#00197D'];
                        $avatarBg = $colors[$p->user_id % 5];
                        $isLunas = $p->status_pembayaran_id == 2;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:11px;">
                                <div class="avatar-user" style="background:{{ $avatarBg }};">{{ strtoupper(substr($nama,0,1)) }}</div>
                                <div><div class="tbl-name">{{ $nama }}</div><div class="tbl-sub">{{ $u['email'] ?? '-' }}</div></div>
                            </div>
                        </td>
                        <td><span style="font-weight:800;color:#15803D;">Rp {{ number_format($p->total_harga,0,',','.') }}</span></td>
                        <td class="text-center"><span class="tbl-badge {{ $isLunas ? 'badge-success' : 'badge-warn' }}">{{ $p->statusPembayaran->nama_status ?? 'Pending' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4"><div class="empty-state"><div class="empty-icon"><i class="fas fa-shopping-basket"></i></div><h5 style="font-weight:800;">Belum Ada Pesanan</h5><p>Pesanan terbaru akan muncul di sini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif

</div><!-- /.dash-page-wrapper -->

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animasi kartu statistik
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, i) => {
        setTimeout(() => {
            card.style.transition = 'all .5s cubic-bezier(.34,1.56,.64,1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 80 + i * 70);
    });

    // Animasi angka (counter)
    document.querySelectorAll('.stat-number[data-count]').forEach(el => {
        let target = parseInt(el.dataset.count || '0');
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        let current = 0;
        let step = Math.max(1, Math.ceil(target / 25));
        let timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 40);
    });

    // Animasi baris tabel
    document.querySelectorAll('.p-table tbody tr').forEach((row, i) => {
        setTimeout(() => {
            row.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(.34,1.56,.64,1)';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 350 + i * 50);
    });

    // Chart untuk Admin
    @if(session('user.role') === 'admin')
        const labels = {!! json_encode($data['bulan_labels'] ?? []) !!};
        const hotel = {!! json_encode($data['reservasi_per_bulan'] ?? []) !!};
        const resto = {!! json_encode($data['pesanan_per_bulan'] ?? []) !!};
        const statusData = {!! json_encode($data['status_reservasi'] ?? [0,0,0,0]) !!};
        const fontSetting = { family: 'Plus Jakarta Sans, sans-serif', size: 11, weight: '600' };

        const ctxUtama = document.getElementById('chartUtama');
        if (ctxUtama) {
            new Chart(ctxUtama, {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'Hotel', data: hotel, backgroundColor: '#00197D', borderRadius: 8 }, { label: 'Restoran', data: resto, backgroundColor: '#D4AF37', borderRadius: 8 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: fontSetting } }, scales: { x: { grid: { display: false }, ticks: fontSetting }, y: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: fontSetting } } }
            });
        }
        const ctxStatus = document.getElementById('chartStatus');
        if (ctxStatus) {
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: { labels: ['Terbayar', 'Pending', 'Selesai', 'Batal'], datasets: [{ data: statusData, backgroundColor: ['#10b981', '#F5E6BE', '#00197D', '#EF4444'], borderWidth: 6, borderColor: '#ffffff', hoverOffset: 15 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { position: 'bottom', labels: fontSetting } } }
            });
        }
    @endif

    // Chart untuk Staff Hotel
    @if(session('user.role') === 'staff_hotel')
        const labelsHotel = {!! json_encode($data['bulan_labels'] ?? []) !!};
        const reservasiData = {!! json_encode($data['reservasi_per_bulan'] ?? []) !!};
        const chartHotel = document.getElementById('chartHotelStaff');
        if (chartHotel && labelsHotel.length && reservasiData.length) {
            new Chart(chartHotel, {
                type: 'line',
                data: {
                    labels: labelsHotel,
                    datasets: [{
                        label: 'Jumlah Reservasi',
                        data: reservasiData,
                        borderColor: '#1D4ED8',
                        backgroundColor: 'rgba(29,78,216,0.07)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        borderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
                        tooltip: { backgroundColor: '#000C3D', cornerRadius: 12 }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
                        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } }
                    }
                }
            });
        } else if (chartHotel) {
            chartHotel.innerHTML = '<div class="text-center text-muted mt-5">Data reservasi belum tersedia</div>';
        }

        // Doughnut chart distribusi status kamar - DIPERBAIKI
        const tersedia = {{ $data['kamar_tersedia'] ?? 0 }};
        const terisi   = {{ $data['kamar_terisi'] ?? 0 }};
        // Hitung totalKamar dari nilai yang ada, bukan menggunakan konstanta PHP
        const totalKamar = tersedia + terisi + ({{ $data['kamar_nonaktif'] ?? 0 }});
        const nonaktif = totalKamar - (tersedia + terisi);
        const chartKamar = document.getElementById('chartKamarStatus');
        if (chartKamar) {
            new Chart(chartKamar, {
                type: 'doughnut',
                data: {
                    labels: ['Tersedia', 'Terisi', 'Nonaktif'],
                    datasets: [{
                        data: [tersedia, terisi, nonaktif],
                        backgroundColor: ['#10b981', '#ef4444', '#94a3b8'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
                        tooltip: { backgroundColor: '#000C3D', cornerRadius: 12 }
                    }
                }
            });
        }
    @endif

    // Chart untuk Staff Restoran
    @if(session('user.role') === 'staff_restoran')
        const labelsResto = {!! json_encode($data['bulan_labels'] ?? []) !!};
        const pesananData = {!! json_encode($data['pesanan_per_bulan'] ?? []) !!};
        const chartResto = document.getElementById('chartRestoStaff');
        if (chartResto && labelsResto.length && pesananData.length) {
            new Chart(chartResto, {
                type: 'line',
                data: {
                    labels: labelsResto,
                    datasets: [{
                        label: 'Jumlah Pesanan',
                        data: pesananData,
                        borderColor: '#15803D',
                        backgroundColor: 'rgba(21,128,61,0.07)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        borderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
                        tooltip: { backgroundColor: '#000C3D', cornerRadius: 12 }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
                        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } }
                    }
                }
            });
        } else if (chartResto) {
            chartResto.innerHTML = '<div class="text-center text-muted mt-5">Data pesanan belum tersedia</div>';
        }

        // Doughnut chart distribusi status pesanan
        const totalPesanan = {{ $data['total_pesanan'] ?? 0 }};
        const pending = {{ $data['pesanan_pending'] ?? 0 }};
        const lunas = totalPesanan - pending;
        const chartPesanan = document.getElementById('chartPesananStatus');
        if (chartPesanan) {
            new Chart(chartPesanan, {
                type: 'doughnut',
                data: {
                    labels: ['Lunas', 'Pending'],
                    datasets: [{
                        data: [lunas, pending],
                        backgroundColor: ['#10b981', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
                        tooltip: { backgroundColor: '#000C3D', cornerRadius: 12 }
                    }
                }
            });
        }
    @endif
});

// Fungsi toggle ulasan (Admin)
function toggleUlasan(id, tipe, isCurrentlyHidden, nama) {
        Swal.fire({
        title: `<span style="font-family:Plus Jakarta Sans;font-weight:800;">${isCurrentlyHidden ? 'Tampilkan Ulasan?' : 'Sembunyikan Ulasan?'}</span>`,
        html: `<span style="font-family:Plus Jakarta Sans;">Ulasan dari <strong>${nama}</strong> akan ${isCurrentlyHidden ? 'ditampilkan' : 'disembunyikan'} dari halaman publik.</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#00197D',
        cancelButtonColor: '#64748b',
        confirmButtonText: `<i class="fas ${isCurrentlyHidden ? 'fa-eye' : 'fa-eye-slash'}"></i> Ya, ${isCurrentlyHidden ? 'Tampilkan' : 'Sembunyikan'}`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-toggle-' + tipe + '-' + id).submit();
        }
    });
}
</script>
@endpush