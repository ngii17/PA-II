@extends('dashboard.layouts.app')

@section('title', 'Kelola Promo')

@section('content')
{{-- ================================================================
     KELOLA PROMO — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;
    --gold-light:   #F5E6BE;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #94a3b8;
    --indigo:       #6366f1;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, .modal-title {
    font-family: var(--font) !important;
}
.fw-800 { font-weight: 800 !important; letter-spacing: -.02em; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.promo-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px 64px;
    position: relative;
    overflow-x: hidden;
}
.promo-wrapper::before,
.promo-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.promo-wrapper::before {
    width: 560px; height: 560px;
    top: -180px; right: -130px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.promo-wrapper::after {
    width: 380px; height: 380px;
    bottom: -100px; left: -90px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.promo-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.promo-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 28px;
}
.promo-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 5px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.promo-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.promo-header-left p {
    color: var(--text-muted);
    font-size: .875rem;
    font-weight: 500;
    margin: 0;
}
.btn-premium {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 24px;
    font-weight: 800;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    box-shadow: 0 8px 20px rgba(0,25,125,.25);
    text-decoration: none;
}
.btn-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(0,25,125,.32);
    color: white;
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
    border-radius: 20px;
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
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}
.stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.si-navy  { background: rgba(0,25,125,.08);  color: var(--navy); }
.si-gold  { background: rgba(212,175,55,.12); color: var(--gold); }
.si-green { background: rgba(16,185,129,.1); color: #10b981; }
.si-rose  { background: rgba(225,29,72,.1);  color: #e11d48; }
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
   ALERT SUCCESS
   ============================================================ */
.alert-premium {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-left: 4px solid #10b981;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #065f46;
    font-weight: 600;
    font-size: .85rem;
    animation: slideIn 0.4s ease;
}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   TABLE TOOLBAR
   ============================================================ */
.table-toolbar {
    background: var(--surface);
    border-radius: 24px;
    padding: 18px 20px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
}
.search-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.search-wrap i {
    position: absolute;
    left: 14px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .85rem;
}
.search-wrap input {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 10px 14px 10px 38px;
    font-size: .85rem;
    font-weight: 500;
    background: var(--surface-2);
    transition: .25s;
}
.search-wrap input:focus {
    border-color: var(--navy);
    background: white;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
.filter-btn {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 9px 18px;
    font-size: .78rem;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
}
.filter-btn.active-all, .filter-btn.active-hotel, .filter-btn.active-resto {
    background: var(--navy);
    color: white;
    border-color: var(--navy);
}
.filter-btn.active-hotel { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
.filter-btn.active-resto { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }

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
.p-table {
    width: 100%;
    border-collapse: collapse;
}
.p-table thead tr {
    background: var(--navy);
}
.p-table thead th {
    padding: 16px 20px;
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: 1.6px;
    font-weight: 700;
    color: rgba(255,255,255,.85);
    white-space: nowrap;
}
.p-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .2s;
    opacity: 0;
    transform: translateY(10px);
}
.p-table tbody tr:last-child { border-bottom: none; }
.p-table tbody tr:hover { background: #fdf6e3; }
.p-table tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    font-size: .82rem;
}
/* Badge & Chip */
.badge-kategori {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: .7rem;
    font-weight: 700;
}
.badge-hotel { background: #dbeafe; color: #1e40af; }
.badge-resto { background: #dcfce7; color: #15803d; }

.kode-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--surface-2);
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 5px 12px;
    font-family: monospace;
    font-weight: 800;
    font-size: .8rem;
    color: var(--navy);
    cursor: pointer;
    transition: .2s;
}
.kode-chip:hover {
    background: #e8edfa;
    transform: scale(1.02);
}
.nominal {
    font-weight: 800;
    font-size: .95rem;
    color: var(--gold);
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: .68rem;
    font-weight: 700;
}
.status-aktif { background: #dcfce7; color: #15803d; }
.status-expired { background: #fee2e2; color: #991b1b; }
.status-aktif::before, .status-expired::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.status-aktif::before { background: #10b981; }
.status-expired::before { background: #ef4444; }

/* Action Buttons */
.actions {
    display: flex;
    gap: 8px;
    justify-content: center;
}
.btn-icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: .2s;
}
.btn-icon.view { background: rgba(99,102,241,.1); color: #6366f1; }
.btn-icon.edit { background: rgba(245,158,11,.1); color: #f59e0b; }
.btn-icon.delete { background: rgba(225,29,72,.1); color: #e11d48; }
.btn-icon:hover { transform: translateY(-2px); }
.btn-icon.view:hover { background: #6366f1; color: white; }
.btn-icon.edit:hover { background: #f59e0b; color: white; }
.btn-icon.delete:hover { background: #e11d48; color: white; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-icon {
    width: 70px; height: 70px;
    background: var(--surface-2);
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--text-muted);
    margin-bottom: 16px;
    border: 2px dashed var(--border);
}

/* ============================================================
   MODAL PREMIUM
   ============================================================ */
.modal-premium .modal-content {
    border-radius: 28px;
    border: none;
    overflow: hidden;
    box-shadow: 0 30px 50px rgba(0,0,0,0.2);
}
.modal-premium .modal-header {
    background: linear-gradient(135deg, var(--navy), var(--navy-dark));
    padding: 20px 24px;
    border: none;
}
.modal-premium .modal-header .modal-title {
    font-weight: 800;
    color: white;
}
.modal-body-premium {
    padding: 28px;
    text-align: center;
}
.modal-badge {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 30px;
    font-size: .7rem;
    font-weight: 800;
    margin-bottom: 12px;
}
.modal-badge-hotel { background: #dbeafe; color: #1e40af; }
.modal-badge-resto { background: #dcfce7; color: #15803d; }
.modal-promo-name {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 6px;
}
.modal-discount {
    font-size: 2.4rem;
    font-weight: 800;
    color: var(--gold);
    margin: 12px 0 20px;
    letter-spacing: -.02em;
}
.modal-code-box {
    background: var(--surface-2);
    border: 2px dashed var(--border);
    border-radius: 16px;
    padding: 18px;
    margin-bottom: 20px;
    cursor: pointer;
    transition: .2s;
}
.modal-code-box:hover {
    border-color: var(--navy);
    background: #e8edfa;
}
.modal-code-box .code-value {
    font-family: monospace;
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--navy);
    letter-spacing: 2px;
}
.modal-date-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}
.modal-date-card {
    background: var(--surface-2);
    border-radius: 12px;
    padding: 12px;
    text-align: center;
}
.modal-date-card .date-label {
    font-size: .6rem;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 700;
}
.modal-date-card .date-value {
    font-weight: 800;
    color: var(--navy);
}
.modal-footer-custom {
    padding: 16px 24px 24px;
    border-top: 1px solid var(--border);
}
.btn-close-modal {
    width: 100%;
    padding: 12px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 14px;
    font-weight: 700;
    transition: .2s;
}
.btn-close-modal:hover {
    background: var(--navy);
    color: white;
    border-color: var(--navy);
}
.copy-toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: var(--navy-dark);
    color: white;
    padding: 10px 22px;
    border-radius: 40px;
    font-size: .8rem;
    font-weight: 700;
    z-index: 9999;
    opacity: 0;
    transition: .3s ease;
    pointer-events: none;
    white-space: nowrap;
}
.copy-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}
@media (max-width: 768px) {
    .promo-header-left h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2,1fr); }
    .modal-discount { font-size: 1.8rem; }
}
</style>

<div class="promo-wrapper">
    {{-- TOAST NOTIF --}}
    <div class="copy-toast" id="copyToast"></div>

    {{-- HEADER --}}
    <div class="promo-header">
        <div class="promo-header-left">
            <h2>Kelola <span>Promo</span></h2>
            <p>Atur diskon dan promosi untuk Hotel dan Restoran dalam satu tempat.</p>
        </div>
        <a href="{{ route('dashboard.promo.create') }}" class="btn-premium">
            <i class="fas fa-plus-circle"></i> Tambah Promo
        </a>
    </div>

    {{-- STATISTIK --}}
    @php
        $totalPromo = $promos->count();
        $hotelCount = $promos->where('kategori','hotel')->count();
        $restoCount = $promos->where('kategori','restoran')->count();
        $aktifCount = $promos->filter(fn($p) => \Carbon\Carbon::parse($p->tgl_selesai)->isFuture())->count();
    @endphp
    <div class="stats-strip">
        <div class="stat-card"><div class="stat-icon si-navy"><i class="fas fa-tags"></i></div><div class="stat-info"><div class="stat-number">{{ $totalPromo }}</div><div class="stat-label">Total Promo</div></div></div>
        <div class="stat-card"><div class="stat-icon si-gold"><i class="fas fa-hotel"></i></div><div class="stat-info"><div class="stat-number">{{ $hotelCount }}</div><div class="stat-label">Promo Hotel</div></div></div>
        <div class="stat-card"><div class="stat-icon si-green"><i class="fas fa-utensils"></i></div><div class="stat-info"><div class="stat-number">{{ $restoCount }}</div><div class="stat-label">Promo Restoran</div></div></div>
        <div class="stat-card"><div class="stat-icon si-rose"><i class="fas fa-fire"></i></div><div class="stat-info"><div class="stat-number">{{ $aktifCount }}</div><div class="stat-label">Masih Aktif</div></div></div>
    </div>

    @if(session('success'))
    <div class="alert-premium">
        <i class="fas fa-check-circle fa-lg"></i> {{ session('success') }}
    </div>
    @endif

    {{-- TOOLBAR --}}
    <div class="table-toolbar">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="searchPromo" placeholder="Cari nama promo atau kode...">
        </div>
        <div class="filter-btn active-all" data-filter="all">Semua</div>
        <div class="filter-btn" data-filter="hotel"><i class="fas fa-hotel me-1"></i> Hotel</div>
        <div class="filter-btn" data-filter="restoran"><i class="fas fa-utensils me-1"></i> Restoran</div>
    </div>

    {{-- TABLE --}}
    <div class="card-premium">
        <div class="table-responsive">
            <table class="p-table" id="promoTable">
                <thead>
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>Informasi Promo</th>
                        <th>Kode Promo</th>
                        <th>Kategori</th>
                        <th>Potongan</th>
                        <th>Status</th>
                        <th class="text-center" width="110">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promos as $i => $p)
                    @php
                        $isAktif = \Carbon\Carbon::parse($p->tgl_selesai)->isFuture();
                    @endphp
                    <tr data-kategori="{{ $p->kategori }}" data-nama="{{ strtolower($p->nama_promo) }}" data-kode="{{ strtolower($p->kode_promo) }}">
                        <td class="text-center" style="color:var(--text-muted); font-weight:800;">{{ $i+1 }}</td>
                        <td>
                            <div class="fw-800" style="font-size:.88rem;">{{ $p->nama_promo }}</div>
                            <div style="font-size:.7rem; color:var(--text-muted);"><i class="far fa-calendar-alt"></i> Exp: {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="kode-chip" onclick="copyKode('{{ $p->kode_promo }}')">
                                {{ $p->kode_promo }} <i class="far fa-copy"></i>
                            </span>
                        </td>
                        <td><span class="badge-kategori {{ $p->kategori == 'hotel' ? 'badge-hotel' : 'badge-resto' }}">{{ strtoupper($p->kategori) }}</span></td>
                        <td class="nominal">
                            {{ $p->tipe_diskon == 'persen' ? $p->nominal_potongan . '%' : 'Rp ' . number_format($p->nominal_potongan,0,',','.') }}
                        </td>
                        <td><span class="status-badge {{ $isAktif ? 'status-aktif' : 'status-expired' }}">{{ $isAktif ? 'Aktif' : 'Kadaluarsa' }}</span></td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon view" data-bs-toggle="modal" data-bs-target="#modalPromo{{ $p->id }}" title="Detail"><i class="fas fa-eye"></i></button>
                                <a href="{{ route('dashboard.promo.edit', $p->id) }}" class="btn-icon edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('dashboard.promo.destroy', $p->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-tag"></i></div>
                                <h5 class="fw-800">Belum Ada Promo</h5>
                                <p class="text-muted">Mulai tambahkan promo untuk hotel atau restoran Anda.</p>
                                <a href="{{ route('dashboard.promo.create') }}" class="btn-premium mt-2" style="display:inline-flex;">Tambah Promo Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL UNTUK SETIAP PROMO --}}
@foreach($promos as $p)
@php $isAktif = \Carbon\Carbon::parse($p->tgl_selesai)->isFuture(); @endphp
<div class="modal fade modal-premium" id="modalPromo{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-ticket-alt me-2"></i> Detail Promo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-premium">
                <div class="modal-badge {{ $p->kategori == 'hotel' ? 'modal-badge-hotel' : 'modal-badge-resto' }}">
                    <i class="fas {{ $p->kategori == 'hotel' ? 'fa-hotel' : 'fa-utensils' }} me-1"></i> {{ strtoupper($p->kategori) }}
                </div>
                <div class="modal-promo-name">{{ $p->nama_promo }}</div>
                <div class="status-badge {{ $isAktif ? 'status-aktif' : 'status-expired' }} mt-1" style="display:inline-flex;">{{ $isAktif ? 'Masih Aktif' : 'Kadaluarsa' }}</div>
                <div class="modal-discount">
                    {{ $p->tipe_diskon == 'persen' ? $p->nominal_potongan . '%' : 'Rp ' . number_format($p->nominal_potongan,0,',','.') }}
                </div>
                <div class="modal-code-box" onclick="copyKode('{{ $p->kode_promo }}')">
                    <div class="code-value">{{ $p->kode_promo }}</div>
                    <small class="text-muted"><i class="far fa-copy"></i> Klik untuk menyalin</small>
                </div>
                <div class="modal-date-grid">
                    <div class="modal-date-card">
                        <div class="date-label">Mulai</div>
                        <div class="date-value">{{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d M Y') }}</div>
                    </div>
                    <div class="modal-date-card">
                        <div class="date-label">Berakhir</div>
                        <div class="date-value">{{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="mt-2 text-start bg-light p-2 rounded" style="font-size:.75rem; border:1px solid var(--border);">
                    <strong>Tipe Diskon:</strong> {{ $p->tipe_diskon == 'persen' ? 'Persentase (%)' : 'Nominal Tetap (Rp)' }}
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animasi kartu statistik
    document.querySelectorAll('.stat-card').forEach((card, i) => {
        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 80 + i * 70);
    });

    // Animasi baris tabel
    const rows = document.querySelectorAll('#promoTable tbody tr[data-kategori]');
    rows.forEach((row, i) => {
        setTimeout(() => {
            row.style.transition = 'opacity 0.4s ease, transform 0.4s cubic-bezier(0.34,1.56,0.64,1)';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 300 + i * 50);
    });

    // Filter & Search
    const searchInput = document.getElementById('searchPromo');
    let activeFilter = 'all';

    function applyFilter() {
        const query = searchInput.value.toLowerCase().trim();
        rows.forEach(row => {
            const nama = row.dataset.nama || '';
            const kode = row.dataset.kode || '';
            const kategori = row.dataset.kategori || '';
            const matchSearch = query === '' || nama.includes(query) || kode.includes(query);
            const matchFilter = activeFilter === 'all' || kategori === activeFilter;
            row.style.display = (matchSearch && matchFilter) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilter);

    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            activeFilter = this.dataset.filter;
            filterBtns.forEach(b => {
                b.classList.remove('active-all', 'active-hotel', 'active-resto');
                b.classList.add('filter-btn');
            });
            if (activeFilter === 'all') this.classList.add('active-all');
            else if (activeFilter === 'hotel') this.classList.add('active-hotel');
            else if (activeFilter === 'restoran') this.classList.add('active-resto');
            applyFilter();
        });
    });

    // Counter animasi angka statistik
    document.querySelectorAll('.stat-number').forEach(el => {
        let target = parseInt(el.innerText);
        if (isNaN(target) || target === 0) return;
        let current = 0;
        let step = Math.ceil(target / 25);
        let iv = setInterval(() => {
            current = Math.min(current + step, target);
            el.innerText = current;
            if (current >= target) clearInterval(iv);
        }, 40);
    });

    // Konfirmasi hapus SweetAlert2
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const promoName = this.closest('tr')?.querySelector('td:nth-child(2) .fw-800')?.innerText || 'promo ini';
            Swal.fire({
                title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Hapus Promo?</span>',
                html: `<span style="font-family:Plus Jakarta Sans;">Promo <strong style="color:#e11d48;">${promoName}</strong> akan dihapus permanen dan tidak dapat dikembalikan.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then(res => {
                if (res.isConfirmed) form.submit();
            });
        });
    });
});

function copyKode(kode) {
    navigator.clipboard.writeText(kode).then(() => {
        const toast = document.getElementById('copyToast');
        toast.innerHTML = '<i class="fas fa-check-circle me-2"></i> Kode "' + kode + '" disalin!';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2200);
    });
}
</script>
@endsection