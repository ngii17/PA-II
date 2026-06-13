@extends('dashboard.layouts.app')
@section('title', 'Manajemen Kamar')

@section('content')
{{-- ================================================================
     MANAJEMEN KAMAR — PREMIUM UNIFIED
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
    --indigo:       #6366f1;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-mid:     #475569;
    --text-muted:   #94a3b8;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, .modal-title, .btn, .table, input, select, textarea { font-family: var(--font) !important; }
.fw-800 { font-weight: 800 !important; letter-spacing: -.02em; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.kamar-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}
.kamar-page-wrapper::before,
.kamar-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.kamar-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.kamar-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.kamar-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.kamar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}
.kamar-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.kamar-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.kamar-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}
.btn-navy-premium {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 13px 26px;
    font-weight: 700;
    font-size: .875rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all .3s ease;
    box-shadow: 0 8px 20px rgba(0,25,125,.25);
}
.btn-navy-premium:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(0,25,125,.32);
}

/* ============================================================
   STATS STRIP
   ============================================================ */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--surface);
    border-radius: 20px;
    padding: 20px 22px;
    box-shadow: var(--shadow-card);
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid var(--border);
    transition: var(--transition);
    opacity: 0;
    transform: translateY(20px);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.stat-icon.navy   { background: rgba(0,25,125,.08); color: var(--navy); }
.stat-icon.green  { background: rgba(16,185,129,.1); color: #10b981; }
.stat-icon.rose   { background: rgba(225,29,72,.1); color: var(--rose); }
.stat-icon.slate  { background: rgba(100,116,139,.1); color: #64748b; }
.stat-info .stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 3px;
}
.stat-info .stat-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
}

/* ============================================================
   ALERTS
   ============================================================ */
.alert-success-premium {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-left: 4px solid #10b981;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #065f46;
    font-weight: 600;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}
.alert-error-premium {
    background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
    border-left: 4px solid #e11d48;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #991b1b;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}
.alert-error-premium .alert-icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }
.alert-error-premium strong { display: block; font-weight: 800; margin-bottom: 2px; }
@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   TABLE TOOLBAR
   ============================================================ */
.table-toolbar {
    padding: 20px 24px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.search-input-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.search-input-wrap i {
    position: absolute;
    left: 14px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .85rem;
}
.search-input-wrap input {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 14px 10px 36px;
    font-size: .85rem;
    font-weight: 500;
    background: var(--surface-2);
    transition: .25s;
}
.search-input-wrap input:focus {
    border-color: var(--navy);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(0,25,125,.07);
}
.filter-badge {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 16px;
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
}
.filter-badge.active-all      { background: var(--navy); color: #fff; border-color: var(--navy); }
.filter-badge.active-tersedia { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
.filter-badge.active-terisi   { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.filter-badge.active-nonaktif { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }

/* ============================================================
   TABLE PREMIUM
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 28px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
}
.table-premium {
    width: 100%;
    border-collapse: collapse;
}
.table-premium thead th {
    background: var(--navy);
    color: rgba(255,255,255,.85);
    padding: 18px 20px;
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 700;
    white-space: nowrap;
}
.table-premium tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .2s;
    opacity: 0;
    transform: translateY(12px);
}
.table-premium tbody tr:hover { background: #f7f9ff; }
.table-premium tbody td {
    padding: 18px 20px;
    vertical-align: middle;
}
.td-num { font-weight: 800; color: var(--text-muted); text-align: center; width: 52px; }
.nomor-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    color: #fff;
    border-radius: 10px;
    padding: 7px 14px;
    font-weight: 800;
    font-size: 1rem;
    box-shadow: 0 4px 12px rgba(0,25,125,.2);
}
.tipe-name {
    font-weight: 700;
    color: var(--text-primary);
    font-size: .9rem;
    margin-bottom: 3px;
}
.tipe-harga { font-size: .73rem; color: var(--emerald); font-weight: 700; }
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 13px;
    border-radius: 999px;
    font-size: .65rem;
    font-weight: 700;
}
.status-tersedia { background: #d1fae5; color: #065f46; }
.status-terisi   { background: #fee2e2; color: #991b1b; }
.status-nonaktif { background: #f1f5f9; color: #475569; }
.badge-status::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
}
.status-tersedia::before { background: #10b981; }
.status-terisi::before   { background: #ef4444; }
.status-nonaktif::before { background: #94a3b8; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.actions-cell {
    display: flex;
    justify-content: center;
    gap: 8px;
}
.btn-action {
    width: 38px; height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}
.btn-detail { background: rgba(99,102,241,.1); color: #6366f1; }
.btn-edit   { background: rgba(245,158,11,.1); color: #f59e0b; }
.btn-delete { background: rgba(225,29,72,.1); color: #e11d48; }
.btn-detail:hover, .btn-edit:hover, .btn-delete:hover {
    color: #fff;
    transform: translateY(-3px);
}
.btn-detail:hover { background: #6366f1; }
.btn-edit:hover   { background: #f59e0b; }
.btn-delete:hover { background: #e11d48; }
.empty-state {
    text-align: center;
    padding: 72px 24px;
}
.empty-icon {
    width: 80px; height: 80px;
    background: var(--surface-2);
    border-radius: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
    margin-bottom: 20px;
    border: 2px dashed var(--border);
}

/* ============================================================
   MODAL PREMIUM
   ============================================================ */
.modal-premium .modal-content {
    border-radius: 28px;
    border: none;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0,0,0,.2);
}
.modal-premium .modal-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 24px 28px;
    border: none;
    position: relative;
}
.modal-premium .modal-title {
    font-weight: 800;
    color: white;
}
.modal-nomor-hero {
    font-size: 3.8rem;
    font-weight: 800;
    color: var(--navy-dark);
    letter-spacing: -.04em;
    line-height: 1;
    margin: 12px 0 6px;
}
.modal-tipe-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 700;
    background: #dbeafe;
    color: #1e40af;
    margin-bottom: 20px;
}
.modal-price-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}
.modal-info-card {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 14px 16px;
    text-align: center;
}
.modal-info-card .info-label {
    font-size: .6rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 5px;
}
.modal-info-card .info-value.price { color: var(--emerald); font-size: 1rem; font-weight: 800; }
.modal-fasilitas-box {
    background: var(--surface-2);
    border-radius: 14px;
    padding: 16px;
    text-align: left;
    margin-bottom: 16px;
}
.modal-fasilitas-box p {
    font-size: .82rem;
    color: var(--text-primary);
    font-weight: 500;
    margin: 0;
}
.modal-footer-custom {
    padding: 16px 24px 24px;
    border-top: none;
    display: flex;
    gap: 10px;
}
.btn-close-modal {
    flex: 1;
    padding: 14px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-weight: 700;
    transition: .25s;
}
.btn-close-modal:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
}
.btn-edit-modal {
    padding: 14px 22px;
    background: linear-gradient(135deg, var(--amber) 0%, #d97706 100%);
    border: none;
    border-radius: 14px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-edit-modal:hover { color: #fff; transform: translateY(-2px); }

@media (max-width: 768px) {
    .kamar-header h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
    .modal-nomor-hero { font-size: 2.8rem; }
}
</style>

<div class="kamar-page-wrapper">

    <!-- Header -->
    <div class="kamar-header">
        <div class="kamar-header-left">
            <h2 class="fw-800">Manajemen <span>Kamar</span></h2>
            <p>Kelola data kamar dan status ketersediaan Hotel Purnama.</p>
        </div>
        <a href="{{ route('dashboard.hotel.kamar.create') }}" class="btn-navy-premium">
            <i class="fas fa-plus-circle"></i> Tambah Kamar Baru
        </a>
    </div>

    <!-- Statistik -->
    <div class="stats-strip">
        <div class="stat-card"><div class="stat-icon navy"><i class="fas fa-door-open"></i></div><div class="stat-info"><div class="stat-number">{{ $kamar->count() }}</div><div class="stat-label">Total Kamar</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fas fa-check-circle"></i></div><div class="stat-info"><div class="stat-number">{{ $kamar->where('status_kamar_id', 1)->count() }}</div><div class="stat-label">Tersedia</div></div></div>
        <div class="stat-card"><div class="stat-icon rose"><i class="fas fa-bed"></i></div><div class="stat-info"><div class="stat-number">{{ $kamar->where('status_kamar_id', 2)->count() }}</div><div class="stat-label">Terisi</div></div></div>
        <div class="stat-card"><div class="stat-icon slate"><i class="fas fa-ban"></i></div><div class="stat-info"><div class="stat-number">{{ $kamar->where('status_kamar_id', 3)->count() }}</div><div class="stat-label">Nonaktif</div></div></div>
    </div>

    <!-- Alert Success & Error -->
    @if(session('success'))
    <div class="alert-success-premium"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-error-premium"><i class="fas fa-exclamation-triangle alert-icon"></i><div><strong>Tindakan Ditolak</strong><span>{{ session('error') }}</span></div></div>
    @endif

    <!-- Table Card -->
    <div class="card-premium">
        <div class="table-toolbar">
            <div class="search-input-wrap"><i class="fas fa-search"></i><input type="text" id="kamarSearch" placeholder="Cari nomor atau tipe kamar..."></div>
            <div class="filter-badge active-all" data-filter="all">Semua</div>
            <div class="filter-badge" data-filter="tersedia"><i class="fas fa-check-circle me-1"></i> Tersedia</div>
            <div class="filter-badge" data-filter="terisi"><i class="fas fa-bed me-1"></i> Terisi</div>
            <div class="filter-badge" data-filter="nonaktif"><i class="fas fa-ban me-1"></i> Nonaktif</div>
        </div>
        <div class="table-responsive">
            <table class="table-premium" id="kamarTable">
                <thead><tr><th class="text-center">#</th><th>Nomor Kamar</th><th>Tipe Kamar</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($kamar as $i => $k)
                    @php
                        $statusMap = [
                            1 => ['label' => 'Tersedia', 'class' => 'status-tersedia', 'filter' => 'tersedia'],
                            2 => ['label' => 'Terisi',   'class' => 'status-terisi',   'filter' => 'terisi'],
                            3 => ['label' => 'Nonaktif', 'class' => 'status-nonaktif', 'filter' => 'nonaktif'],
                        ];
                        $st = $statusMap[$k->status_kamar_id] ?? ['label' => '-', 'class' => 'status-nonaktif', 'filter' => 'nonaktif'];
                    @endphp
                    <tr data-status="{{ $st['filter'] }}" data-nomor="{{ strtolower($k->nomor_kamar) }}" data-tipe="{{ strtolower($k->tipeKamar->nama_tipe ?? '') }}">
                        <td class="td-num">{{ $i+1 }}</td>
                        <td><span class="nomor-chip"><i class="fas fa-door-open"></i> {{ $k->nomor_kamar }}</span></td>
                        <td><div class="tipe-name">{{ $k->tipeKamar->nama_tipe ?? '-' }}</div><div class="tipe-harga">Rp {{ number_format($k->tipeKamar->harga ?? 0, 0, ',', '.') }} / malam</div></td>
                        <td class="text-center"><span class="badge-status {{ $st['class'] }}">{{ $st['label'] }}</span></td>
                        <td class="text-center">
                            <div class="actions-cell">
                                <button class="btn-action btn-detail" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $k->id }}"><i class="fas fa-eye"></i></button>
                                <a href="{{ route('dashboard.hotel.kamar.edit', $k->id) }}" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
                                <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapusKamar({{ $k->id }}, '{{ $k->nomor_kamar }}')"><i class="fas fa-trash"></i></button>
                                <form id="form-hapus-kamar-{{ $k->id }}" action="{{ route('dashboard.hotel.kamar.destroy', $k->id) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="fas fa-door-open"></i></div><h5>Belum Ada Data Kamar</h5><p>Mulai tambahkan kamar untuk Hotel Purnama.</p><a href="{{ route('dashboard.hotel.kamar.create') }}" class="btn-navy-premium">Tambah Kamar Pertama</a></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL UNTUK SETIAP KAMAR --}}
@foreach($kamar as $k)
@php
    $statusMap = [1 => 'status-tersedia', 2 => 'status-terisi', 3 => 'status-nonaktif'];
    $stClass = $statusMap[$k->status_kamar_id] ?? 'status-nonaktif';
@endphp
<div class="modal fade modal-premium" id="modalDetail{{ $k->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="fas fa-door-open"></i> Detail Kamar</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0">
                <div style="padding: 28px 28px 20px; text-align:center;">
                    <div class="modal-tipe-badge"><i class="fas fa-tag"></i> {{ $k->tipeKamar->nama_tipe ?? 'Tipe Tidak Diketahui' }}</div>
                    <div class="modal-nomor-hero">{{ $k->nomor_kamar }}</div>
                    <span class="badge-status {{ $stClass }}" style="margin-bottom:20px; display:inline-flex;">{{ $statusMapLabel[$k->status_kamar_id] ?? 'Nonaktif' }}</span>
                    <div class="modal-price-grid">
                        <div class="modal-info-card"><span class="info-label">Harga / Malam</span><div class="info-value price">Rp {{ number_format($k->tipeKamar->harga ?? 0, 0, ',', '.') }}</div></div>
                        <div class="modal-info-card"><span class="info-label">Kapasitas</span><div class="info-value"><i class="fas fa-users me-1"></i>{{ $k->tipeKamar->kapasitas ?? '0' }} Orang</div></div>
                    </div>
                    <div class="modal-fasilitas-box"><span class="info-label" style="display:block; margin-bottom:8px;">Fasilitas Kamar</span><p>{{ $k->tipeKamar->fasilitas ?? 'Fasilitas standar Purnama Hotel.' }}</p></div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('dashboard.hotel.kamar.edit', $k->id) }}" class="btn-edit-modal"><i class="fas fa-edit"></i> Edit</a>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi stat card
    document.querySelectorAll('.stat-card').forEach((c, i) => {
        setTimeout(() => { c.style.opacity = '1'; c.style.transform = 'translateY(0)'; }, 100 + i * 80);
    });
    // Animasi baris tabel
    const rows = document.querySelectorAll('.table-premium tbody tr');
    rows.forEach((r, i) => {
        setTimeout(() => {
            r.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(.34,1.56,.64,1)';
            r.style.opacity = '1'; r.style.transform = 'translateY(0)';
        }, 350 + i * 55);
    });
    // Filter & search
    const search = document.getElementById('kamarSearch');
    let active = 'all';
    function applyFilter() {
        const q = search.value.toLowerCase().trim();
        rows.forEach(row => {
            const nomor = row.dataset.nomor || '';
            const tipe = row.dataset.tipe || '';
            const status = row.dataset.status || '';
            const matchSearch = q === '' || nomor.includes(q) || tipe.includes(q);
            const matchFilter = active === 'all' || status === active;
            row.style.display = (matchSearch && matchFilter) ? '' : 'none';
        });
    }
    search.addEventListener('input', applyFilter);
    const btns = document.querySelectorAll('.filter-badge');
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            active = this.dataset.filter;
            btns.forEach(b => { b.className = 'filter-badge'; });
            const cls = { 'all':'active-all', 'tersedia':'active-tersedia', 'terisi':'active-terisi', 'nonaktif':'active-nonaktif' }[active];
            this.classList.add(cls);
            applyFilter();
        });
    });
    // Counter animasi
    document.querySelectorAll('.stat-number').forEach(el => {
        let t = parseInt(el.textContent);
        if (isNaN(t) || t === 0) return;
        let cur = 0, step = Math.ceil(t / 20);
        let iv = setInterval(() => { cur = Math.min(cur + step, t); el.textContent = cur; if (cur >= t) clearInterval(iv); }, 40);
    });
});

function konfirmasiHapusKamar(id, nomor) {
    Swal.fire({
        title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Nonaktifkan Kamar?</span>',
        html: `<span style="font-family:Plus Jakarta Sans;">Kamar nomor <strong style="color:#e11d48;">${nomor}</strong> akan dinonaktifkan. Data riwayat reservasi tetap tersimpan.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#00197D',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-ban"></i> Ya, Nonaktifkan',
        cancelButtonText: 'Batal'
    }).then(res => { if (res.isConfirmed) document.getElementById('form-hapus-kamar-' + id).submit(); });
}
</script>
@endsection