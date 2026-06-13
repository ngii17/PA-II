@extends('dashboard.layouts.app')
@section('title', 'Seluruh Ulasan')

@section('content')
{{-- ================================================================
     ULASAN PELANGGAN — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ============================================================
   ROOT VARIABLES (KONSISTEN DENGAN HALAMAN SEBELUMNYA)
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
   PAGE WRAPPER
   ============================================================ */
.ulasan-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px 64px;
    position: relative;
    overflow-x: hidden;
}
.ulasan-wrapper::before,
.ulasan-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.ulasan-wrapper::before {
    width: 560px; height: 560px;
    top: -180px; right: -130px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.ulasan-wrapper::after {
    width: 380px; height: 380px;
    bottom: -100px; left: -90px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.ulasan-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.ulasan-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 28px;
}
.ulasan-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 5px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.ulasan-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.ulasan-header-left p {
    color: var(--text-muted);
    font-size: .875rem;
    font-weight: 500;
    margin: 0;
}

/* ============================================================
   STATS STRIP
   ============================================================ */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--surface);
    border-radius: 18px;
    padding: 18px 20px;
    box-shadow: var(--shadow-card);
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid var(--border);
    transition: var(--transition);
    opacity: 0;
    transform: translateY(18px);
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
.stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.si-navy  { background: rgba(0,25,125,.08);   color: var(--navy); }
.si-gold  { background: rgba(212,175,55,.12); color: #B8960A; }
.si-rose  { background: rgba(225,29,72,.1);   color: #E11D48; }
.si-green { background: rgba(21,128,61,.1);   color: #15803D; }
.si-blue  { background: rgba(29,78,216,.1);   color: #1D4ED8; }

.stat-info .stat-number {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 2px;
}
.stat-info .stat-label {
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
}

/* ============================================================
   TAB PREMIUM
   ============================================================ */
.ulasan-tabs-wrap {
    background: var(--surface);
    border-radius: 20px;
    padding: 8px;
    margin-bottom: 16px;
    display: inline-flex;
    gap: 4px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
}
.ulasan-tab-btn {
    padding: 11px 24px;
    border-radius: 14px;
    border: none;
    background: transparent;
    font-family: var(--font) !important;
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: all .25s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}
.ulasan-tab-btn:hover { color: var(--navy); background: var(--surface-2); }
.ulasan-tab-btn.active {
    background: var(--navy);
    color: #fff;
    box-shadow: 0 4px 14px rgba(0,25,125,.25);
}
.ulasan-tab-btn .tab-count {
    background: rgba(255,255,255,.2);
    border-radius: 999px;
    padding: 2px 8px;
    font-size: .65rem;
    font-weight: 800;
}
.ulasan-tab-btn:not(.active) .tab-count {
    background: rgba(0,25,125,.07);
    color: var(--navy);
}

/* ============================================================
   TABLE CARD
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 24px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
}
.tab-panel-content { display: none; }
.tab-panel-content.active { display: block; }
.table-responsive { overflow-x: auto; }

.p-table {
    width: 100%;
    border-collapse: collapse;
}
.p-table thead tr th {
    background: var(--navy);
    color: rgba(255,255,255,.8);
    padding: 18px 20px;
    font-size: .67rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 700;
    white-space: nowrap;
    border: none;
}
.p-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .2s ease;
    opacity: 0;
    transform: translateY(10px);
}
.p-table tbody tr:last-child { border-bottom: none; }
.p-table tbody tr:hover { background: #fdf6e3; }
.p-table tbody td { padding: 16px 20px; vertical-align: middle; }

/* Avatar */
.avatar-user {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800;
    color: #fff;
    font-size: 1.05rem;
    flex-shrink: 0;
    position: relative;
    transition: transform .2s ease;
}
.avatar-user .avatar-ring {
    position: absolute;
    inset: -2px;
    border-radius: 14px;
    border: 2px solid currentColor;
    opacity: .25;
}

.user-fullname {
    font-weight: 800;
    color: var(--text-primary);
    font-size: .92rem;
    margin-bottom: 2px;
}
.user-email-sub {
    font-size: .75rem;
    color: var(--text-muted);
    font-weight: 600;
}

.unit-tag {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 8px;
    font-size: .7rem;
    font-weight: 800;
    background: rgba(0,25,125,.06);
    color: var(--navy);
    border: 1px solid rgba(0,25,125,.1);
}

.star-rating {
    display: inline-flex;
    gap: 2px;
    font-size: .82rem;
}
.star-rating .fas.fa-star { color: var(--gold); }
.star-rating .far.fa-star { color: #e2e8f0; }
.rating-val {
    font-size: .72rem;
    font-weight: 800;
    color: var(--text-muted);
    margin-top: 3px;
}

.comment-text {
    font-size: .8rem;
    color: var(--text-mid);
    line-height: 1.5;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    margin-bottom: 5px;
}
.btn-read-more {
    background: none;
    border: none;
    padding: 0;
    font-family: var(--font) !important;
    font-size: .68rem;
    font-weight: 800;
    color: var(--navy);
    cursor: pointer;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.btn-read-more:hover { color: var(--gold); }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .68rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 999px;
}
.status-badge::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
}
.status-visible { background: #ecfdf5; color: #065f46; }
.status-visible::before { background: #10b981; }
.status-hidden  { background: #fef2f2; color: #991b1b; }
.status-hidden::before  { background: #ef4444; }

.btn-toggle {
    font-family: var(--font) !important;
    font-size: .7rem;
    font-weight: 800;
    padding: 7px 16px;
    border-radius: 10px;
    cursor: pointer;
    transition: all .2s ease;
    letter-spacing: .3px;
    white-space: nowrap;
    border: none;
}
.btn-toggle-hide {
    background: #fef2f2;
    color: #b91c1c;
    border: 1.5px solid #fecaca;
}
.btn-toggle-hide:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
.btn-toggle-show {
    background: #f0fdf4;
    color: #15803d;
    border: 1.5px solid #bbf7d0;
}
.btn-toggle-show:hover { background: #22c55e; color: #fff; border-color: #22c55e; }

.empty-state {
    padding: 72px 24px;
    text-align: center;
}
.empty-icon {
    width: 72px; height: 72px;
    background: var(--surface-2);
    border-radius: 20px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    color: var(--text-muted);
    margin-bottom: 16px;
    border: 2px dashed var(--border);
}

/* ============================================================
   MODAL DETAIL
   ============================================================ */
.ulasan-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,12,61,.55);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1060;
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s ease;
}
.ulasan-modal-overlay.show { opacity: 1; pointer-events: all; }
.ulasan-modal-box {
    background: var(--surface);
    border-radius: 28px;
    width: 420px;
    max-width: 94vw;
    padding: 40px 36px 36px;
    text-align: center;
    position: relative;
    transform: translateY(24px) scale(.97);
    transition: transform .35s cubic-bezier(.34,1.56,.64,1);
    box-shadow: 0 32px 80px rgba(0,12,61,.25);
}
.ulasan-modal-overlay.show .ulasan-modal-box {
    transform: translateY(0) scale(1);
}
.modal-close-x {
    position: absolute;
    top: 16px; right: 18px;
    width: 34px; height: 34px;
    border-radius: 50%;
    background: var(--surface-2);
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    cursor: pointer;
    color: var(--text-muted);
}
.modal-avatar-lg {
    width: 72px; height: 72px;
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800;
    color: #fff;
    font-size: 1.6rem;
    margin: 0 auto 14px;
    box-shadow: 0 8px 24px rgba(0,25,125,.2);
}
.modal-user-name {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 4px;
}
.modal-user-email {
    font-size: .78rem;
    color: var(--text-muted);
    margin-bottom: 14px;
}
.modal-stars {
    font-size: 1.1rem;
    display: flex;
    gap: 4px;
    justify-content: center;
    margin-bottom: 20px;
}
.modal-quote-box {
    background: linear-gradient(135deg, #f8faff 0%, #fffdf0 100%);
    border-radius: 16px;
    padding: 22px 24px;
    text-align: left;
    margin-bottom: 26px;
    border: 1px solid #e8edf8;
    position: relative;
}
.modal-quote-box::before {
    content: '\201C';
    position: absolute;
    top: 6px; left: 16px;
    font-size: 3.5rem;
    font-weight: 900;
    color: rgba(0,25,125,.08);
    font-family: Georgia, serif;
}
.modal-quote-box p {
    font-size: .88rem;
    color: var(--text-mid);
    line-height: 1.8;
    margin: 0;
    padding-left: 16px;
    font-style: italic;
}
.modal-btn-tutup {
    font-family: var(--font) !important;
    font-size: .85rem;
    font-weight: 800;
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: 14px;
    width: 100%;
    padding: 14px;
    cursor: pointer;
}
.modal-btn-tutup:hover { background: #001052; }

@media (max-width: 768px) {
    .ulasan-header-left h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
    .ulasan-tabs-wrap { display: flex; width: 100%; }
    .ulasan-tab-btn { flex: 1; justify-content: center; }
}
</style>

<div class="ulasan-wrapper">

    {{-- HEADER --}}
    <div class="ulasan-header">
        <div class="ulasan-header-left">
            <h2 class="fw-800">Ulasan <span>Pelanggan</span></h2>
            <p>Manajemen masukan & reputasi layanan <strong style="color:var(--navy);">Purnama Hotel & Resto</strong>.</p>
        </div>
    </div>

    {{-- STATISTIK --}}
    @php
        $totalHotel = $ulasanHotel->count();
        $totalResto = $ulasanRestoran->count();
        $totalAll   = $totalHotel + $totalResto;
        $rataAll    = $totalAll > 0 ? ($ulasanHotel->sum('rating') + $ulasanRestoran->sum('rating')) / $totalAll : 0;
        $bintang5   = $ulasanHotel->where('rating', 5)->count() + $ulasanRestoran->where('rating', 5)->count();
        $hidden     = $ulasanHotel->where('is_hidden', true)->count() + $ulasanRestoran->where('is_hidden', true)->count();
    @endphp
    <div class="stats-strip">
        <div class="stat-card"><div class="stat-icon si-navy"><i class="fas fa-comments"></i></div><div class="stat-info"><div class="stat-number">{{ $totalAll }}</div><div class="stat-label">Total Ulasan</div></div></div>
        <div class="stat-card"><div class="stat-icon si-gold"><i class="fas fa-star"></i></div><div class="stat-info"><div class="stat-number" data-decimal="1">{{ number_format($rataAll, 1) }}</div><div class="stat-label">Rating Rata-rata</div></div></div>
        <div class="stat-card"><div class="stat-icon si-rose"><i class="fas fa-award"></i></div><div class="stat-info"><div class="stat-number">{{ $bintang5 }}</div><div class="stat-label">Bintang 5</div></div></div>
        <div class="stat-card"><div class="stat-icon si-green"><i class="fas fa-hotel"></i></div><div class="stat-info"><div class="stat-number">{{ $totalHotel }}</div><div class="stat-label">Ulasan Hotel</div></div></div>
        <div class="stat-card"><div class="stat-icon si-blue"><i class="fas fa-utensils"></i></div><div class="stat-info"><div class="stat-number">{{ $totalResto }}</div><div class="stat-label">Ulasan Resto</div></div></div>
    </div>

    {{-- TAB PREMIUM --}}
    <div class="ulasan-tabs-wrap">
        <button class="ulasan-tab-btn active" onclick="switchTab('hotel', this)"><i class="fas fa-hotel"></i> Layanan Hotel <span class="tab-count">{{ $totalHotel }}</span></button>
        <button class="ulasan-tab-btn" onclick="switchTab('resto', this)"><i class="fas fa-utensils"></i> Layanan Restoran <span class="tab-count">{{ $totalResto }}</span></button>
    </div>

    {{-- PANEL HOTEL --}}
    <div class="tab-panel-content active" id="panel-hotel">
        <div class="card-premium">
            <div class="table-responsive">
                <table class="p-table">
                    <thead><tr><th class="text-center">#</th><th>Profil Pelanggan</th><th>Tipe Unit</th><th class="text-center">Rating</th><th>Komentar</th><th class="text-center">Status</th><th class="text-center">Kontrol</th></tr></thead>
                    <tbody>
                        @forelse($ulasanHotel as $i => $u)
                        @php
                            $user = $users->get((int)$u->user_id);
                            $nama = $user['full_name'] ?? 'Guest #'.$u->user_id;
                            $email = $user['email'] ?? '-';
                            $colors = ['#00197D','#1D4ED8','#15803D','#B45309','#9333EA'];
                            $avatarBg = $colors[$u->user_id % 5];
                        @endphp
                            <tr>
                                <td class="text-center" style="color:var(--text-muted);font-weight:800;">{{ $i+1 }}</td>
                                <td><div style="display:flex;gap:12px;"><div class="avatar-user" style="background:{{ $avatarBg }};"><div class="avatar-ring"></div><span>{{ strtoupper(substr($nama,0,1)) }}</span></div><div><div class="user-fullname">{{ $nama }}</div><div class="user-email-sub">{{ $email }}</div></div></div></td>
                                <td><span class="unit-tag">{{ $u->tipeKamar->nama_tipe ?? 'N/A' }}</span></td>
                                <td class="text-center"><div class="star-rating">@for($s=1;$s<=5;$s++)<i class="{{ $s<=$u->rating ? 'fas' : 'far' }} fa-star"></i>@endfor</div><div class="rating-val">{{ $u->rating }}.0 / 5</div></td>
                                <td><div class="comment-text">{{ $u->komentar }}</div><button class="btn-read-more" onclick="openModal('{{ addslashes($u->komentar) }}','{{ addslashes($nama) }}','{{ addslashes($email) }}',{{ $u->rating }},'{{ $avatarBg }}')">Lihat Selengkapnya →</button></td>
                                <td class="text-center"><span class="status-badge {{ $u->is_hidden ? 'status-hidden' : 'status-visible' }}">{{ $u->is_hidden ? 'Hidden' : 'Visible' }}</span></td>
                                <td class="text-center">
                                    @php $tipe = $u instanceof \App\Models\hotel\UlasanHotel ? 'hotel' : 'restoran'; @endphp
                                    <form action="{{ route('dashboard.ulasan.toggle', ['tipe' => $tipe, 'id' => $u->id]) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="btn-toggle {{ $u->is_hidden ? 'btn-toggle-show' : 'btn-toggle-hide' }}">{{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}</button></form>
                                </td>
                            </tr>
                        @empty
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i class="fas fa-comment-slash"></i></div><h5 class="fw-bold">Belum Ada Ulasan Hotel</h5><p class="text-muted">Ulasan akan muncul setelah pelanggan memberikan penilaian.</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PANEL RESTO --}}
    <div class="tab-panel-content" id="panel-resto">
        <div class="card-premium">
            <div class="table-responsive">
                <table class="p-table">
                    <thead><tr><th class="text-center">#</th><th>Profil Pelanggan</th><th>Menu</th><th class="text-center">Rating</th><th>Komentar</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($ulasanRestoran as $i => $u)
                        @php
                            $user = $users->get((int)$u->user_id);
                            $nama = $user['full_name'] ?? 'User #'.$u->user_id;
                            $email = $user['email'] ?? '-';
                            $colors = ['#B45309','#9333EA','#0F766E','#1D4ED8','#00197D'];
                            $avatarBg = $colors[$u->user_id % 5];
                        @endphp
                        <tr>
                            <td class="text-center" style="color:var(--text-muted);font-weight:800;">{{ $i+1 }}</td>
                            <td><div style="display:flex;gap:12px;"><div class="avatar-user" style="background:{{ $avatarBg }};"><div class="avatar-ring"></div><span>{{ strtoupper(substr($nama,0,1)) }}</span></div><div><div class="user-fullname">{{ $nama }}</div><div class="user-email-sub">{{ $email }}</div></div></div></td>
                            <td><span class="unit-tag">{{ $u->menu->nama_menu ?? 'N/A' }}</span></td>
                            <td class="text-center"><div class="star-rating">@for($s=1;$s<=5;$s++)<i class="{{ $s<=$u->rating ? 'fas' : 'far' }} fa-star"></i>@endfor</div><div class="rating-val">{{ $u->rating }}.0 / 5</div></td>
                            <td><div class="comment-text">{{ $u->komentar }}</div><button class="btn-read-more" onclick="openModal('{{ addslashes($u->komentar) }}','{{ addslashes($nama) }}','{{ addslashes($email) }}',{{ $u->rating }},'{{ $avatarBg }}')">Lihat Selengkapnya →</button></td>
                            <td class="text-center"><span class="status-badge {{ $u->is_hidden ? 'status-hidden' : 'status-visible' }}">{{ $u->is_hidden ? 'Hidden' : 'Visible' }}</span></td>
                            <td class="text-center">
                                <form action="{{ route('dashboard.ulasan.toggle', ['tipe' => 'restoran', 'id' => $u->id]) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="btn-toggle {{ $u->is_hidden ? 'btn-toggle-show' : 'btn-toggle-hide' }}">{{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}</button></form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i class="fas fa-comment-slash"></i></div><h5 class="fw-bold">Belum Ada Ulasan Restoran</h5><p class="text-muted">Ulasan akan muncul setelah pelanggan memberikan penilaian.</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL DETAIL KOMENTAR --}}
<div class="ulasan-modal-overlay" id="ulasanModal">
    <div class="ulasan-modal-box">
        <button class="modal-close-x" onclick="closeModal()">✕</button>
        <div class="modal-avatar-lg" id="modalAvatar"><div class="avatar-ring"></div><span id="modalInisial"></span></div>
        <div class="modal-user-name" id="modalNama"></div>
        <div class="modal-user-email" id="modalEmail"></div>
        <div class="modal-stars" id="modalStars"></div>
        <div class="modal-quote-box"><p id="modalKomentar"></p></div>
        <button class="modal-btn-tutup" onclick="closeModal()"><i class="fas fa-times me-2"></i> Tutup Detail</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.stat-card').forEach((c, i) => setTimeout(() => { c.style.opacity = '1'; c.style.transform = 'translateY(0)'; }, 80 + i * 70));

    // Cek hash di URL untuk menentukan tab aktif setelah redirect dari toggle
    const hash = window.location.hash.replace('#', '');
    if (hash === 'resto' || hash === 'restoran') {
        const btnResto = document.querySelector('.ulasan-tab-btn[onclick*="resto"]');
        if (btnResto) {
            switchTab('resto', btnResto);
        } else {
            animateRows('panel-hotel');
        }
    } else {
        animateRows('panel-hotel');
    }

    document.querySelectorAll('.stat-number:not([data-decimal])').forEach(el => { let t=parseInt(el.textContent); if(!isNaN(t)&&t>0){ let c=0,step=Math.ceil(t/20),iv=setInterval(()=>{c=Math.min(c+step,t);el.textContent=c;if(c>=t)clearInterval(iv);},40);} });
});

function switchTab(tab, btn) {
    document.querySelectorAll('.ulasan-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel-content').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-'+tab).classList.add('active');
    animateRows('panel-'+tab);
}

function animateRows(panelId) {
    document.querySelectorAll('#'+panelId+' .p-table tbody tr').forEach((row,i) => {
        row.style.opacity = '0'; row.style.transform = 'translateY(10px)';
        setTimeout(() => { row.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(.34,1.56,.64,1)'; row.style.opacity = '1'; row.style.transform = 'translateY(0)'; }, i*50);
    });
}

function openModal(komentar, nama, email, rating, avatarColor) {
    document.getElementById('modalInisial').innerText = nama.charAt(0).toUpperCase();
    document.getElementById('modalAvatar').style.background = avatarColor;
    document.getElementById('modalNama').innerText = nama;
    document.getElementById('modalEmail').innerText = email;
    document.getElementById('modalKomentar').innerText = komentar;
    let stars = '';
    for(let i=1;i<=5;i++) stars += i<=rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
    document.getElementById('modalStars').innerHTML = stars;
    document.getElementById('ulasanModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('ulasanModal').classList.remove('show');
    document.body.style.overflow = '';
}
document.getElementById('ulasanModal').addEventListener('click', e => { if(e.target === document.getElementById('ulasanModal')) closeModal(); });
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });
</script>
@endsection