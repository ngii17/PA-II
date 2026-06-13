@extends('dashboard.layouts.app')
@section('title', 'Manajemen Broadcast')
@section('content')

<!-- External Dependencies -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;

    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --emerald-dark: #065f46;

    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #94a3b8;

    --radius-2xl:   32px;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);

    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }

body, input, select, textarea, button, label {
    font-family: var(--font) !important;
}

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.broadcast-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}

.broadcast-page-wrapper::before,
.broadcast-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.broadcast-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.broadcast-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.broadcast-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   PAGE BAR
   ============================================================ */
.page-bar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}

.page-bar-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}

.page-bar-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-bar-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}

.btn-new {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 13px 24px;
    font-weight: 800;
    font-size: .875rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    cursor: pointer;
    transition: all .3s ease;
    box-shadow: 0 8px 20px rgba(0,25,125,.25);
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}

.btn-new::after {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
    transition: left .5s ease;
}

.btn-new:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(0,25,125,.32);
}
.btn-new:hover::after { left: 100%; }

/* ============================================================
   STATS ROW
   ============================================================ */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 18px 20px;
    box-shadow: var(--shadow-card);
    opacity: 0;
    transform: translateY(16px);
    animation: cardReveal .5s cubic-bezier(.34,1.56,.64,1) forwards;
}

.stat-card:nth-child(1) { animation-delay: .05s; }
.stat-card:nth-child(2) { animation-delay: .12s; }
.stat-card:nth-child(3) { animation-delay: .19s; }

@keyframes cardReveal {
    to { opacity: 1; transform: translateY(0); }
}

.stat-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 8px;
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    line-height: 1;
    margin-bottom: 8px;
}

.stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .68rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}

.stat-chip.sent  { background: rgba(16,185,129,.1);  color: #065f46;  border: 1px solid rgba(16,185,129,.2);  }
.stat-chip.draft { background: rgba(245,158,11,.1);  color: #92400e;  border: 1px solid rgba(245,158,11,.2);  }
.stat-chip.total { background: rgba(0,25,125,.07);   color: var(--navy); border: 1px solid rgba(0,25,125,.12); }

/* ============================================================
   ALERT BANNERS
   ============================================================ */
.alert-premium {
    display: flex;
    align-items: center;
    gap: 12px;
    border-radius: 14px;
    padding: 13px 18px;
    font-size: .85rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.alert-premium.success {
    background: #ecfdf5;
    border: 1px solid rgba(16,185,129,.25);
    color: #065f46;
}

.alert-premium.error {
    background: #fff5f7;
    border: 1px solid rgba(225,29,72,.2);
    color: #991b1b;
}

.alert-premium i { font-size: 1rem; flex-shrink: 0; }

/* ============================================================
   TABLE CARD
   ============================================================ */
.table-card {
    background: var(--surface);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: cardReveal .5s cubic-bezier(.34,1.56,.64,1) .22s forwards;
}

.table-card-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 20px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

.table-card-header::before,
.table-card-header::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.table-card-header::before { width: 160px; height: 160px; top: -60px; right: -40px; }
.table-card-header::after  { width: 80px;  height: 80px;  bottom: -30px; left: 120px; }

.table-card-header h5 {
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    z-index: 1;
}

.table-card-header p {
    color: rgba(255,255,255,.55);
    font-size: .78rem;
    font-weight: 500;
    margin: 3px 0 0;
    position: relative;
    z-index: 1;
}

/* Table */
.broadcast-table {
    width: 100%;
    border-collapse: collapse;
}

.broadcast-table thead tr {
    background: var(--surface-2);
    border-bottom: 1.5px solid var(--border);
}

.broadcast-table thead th {
    padding: 12px 20px;
    font-size: .62rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 800;
    color: var(--text-muted);
    white-space: nowrap;
}

.broadcast-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .18s;
}

.broadcast-table tbody tr:last-child {
    border-bottom: none;
}

.broadcast-table tbody tr:hover {
    background: var(--surface-2);
}

.broadcast-table tbody td {
    padding: 16px 20px;
    vertical-align: middle;
}

.row-num {
    font-size: .78rem;
    font-weight: 700;
    color: var(--text-muted);
}

.row-title {
    font-size: .9rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 3px;
    line-height: 1.3;
}

.row-body-preview {
    font-size: .75rem;
    color: var(--text-muted);
    font-weight: 500;
    line-height: 1.4;
}

.row-date {
    font-size: .8rem;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

/* Status chips */
.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: .5px;
    padding: 5px 12px;
    border-radius: 20px;
    white-space: nowrap;
}

.status-chip.sent {
    background: rgba(16,185,129,.1);
    color: var(--emerald-dark);
    border: 1px solid rgba(16,185,129,.25);
}

.status-chip.draft {
    background: rgba(245,158,11,.1);
    color: #92400e;
    border: 1px solid rgba(245,158,11,.25);
}

/* Spread button */
.btn-spread {
    background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 8px 16px;
    font-size: .75rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all .25s;
    box-shadow: 0 4px 12px rgba(16,185,129,.28);
    white-space: nowrap;
}

.btn-spread:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16,185,129,.35);
}

.btn-spread:active {
    transform: translateY(0);
}

.sent-label {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .75rem;
    font-weight: 600;
    color: var(--text-muted);
    font-style: italic;
}

/* Delete button */
.btn-delete {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 7px 11px;
    font-size: .85rem;
    color: var(--text-muted);
    cursor: pointer;
    transition: .2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.btn-delete:hover {
    background: #fff5f7;
    border-color: rgba(225,29,72,.3);
    color: var(--rose);
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    color: var(--text-muted);
    margin: 0 auto 16px;
}

.empty-state p {
    color: var(--text-muted);
    font-weight: 600;
    font-size: .9rem;
    margin: 0 0 6px;
}

.empty-state small {
    color: var(--text-muted);
    font-size: .8rem;
    font-weight: 400;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 900px) {
    .broadcast-table thead th:nth-child(3) { display: none; }
    .broadcast-table tbody td:nth-child(3) { display: none; }
}

@media (max-width: 640px) {
    .broadcast-page-wrapper { padding: 20px 16px; }
    .page-bar-left h2 { font-size: 1.5rem; }
    .stats-row { grid-template-columns: 1fr 1fr; }
}
</style>

<!-- ================================================
     MARKUP
     ================================================ -->
<div class="broadcast-page-wrapper">

    <!-- Page Bar -->
    <div class="page-bar">
        <div class="page-bar-left">
            <h2>Pengumuman &amp; <span>Broadcast</span></h2>
            <p>Kirim notifikasi langsung ke seluruh perangkat HP pelanggan.</p>
        </div>
        <a href="{{ route('dashboard.admin.broadcast.create') }}" class="btn-new">
            <i class="fas fa-plus"></i> Buat Draft Baru
        </a>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Pengumuman</div>
            <div class="stat-value">{{ $broadcasts->count() }}</div>
            <div class="stat-chip total"><i class="fas fa-layer-group"></i> Semua broadcast</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sudah Terkirim</div>
            <div class="stat-value">{{ $broadcasts->where('status', 'sent')->count() }}</div>
            <div class="stat-chip sent"><i class="fas fa-check-double"></i> Selesai disebar</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Draft Menunggu</div>
            <div class="stat-value">{{ $broadcasts->where('status', 'draft')->count() }}</div>
            <div class="stat-chip draft"><i class="fas fa-clock"></i> Belum disebar</div>
        </div>
    </div>

    {{-- Alert Berhasil --}}
    @if(session('success'))
        <div class="alert-premium success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Alert Gagal --}}
    @if(session('error'))
        <div class="alert-premium error">
            <i class="fas fa-times-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="table-card">

        <div class="table-card-header">
            <div>
                <h5>
                    <i class="fas fa-broadcast-tower"></i>
                    Daftar Pengumuman
                </h5>
                <p>Kelola dan sebarkan draft notifikasi ke seluruh pengguna.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="broadcast-table">
                <thead>
                    <tr>
                        <th style="width:50px;">NO</th>
                        <th>JUDUL PENGUMUMAN</th>
                        <th>JADWAL TAMPIL</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI SEBARKAN</th>
                        <th class="text-center">KELOLA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($broadcasts as $i => $b)
                    <tr>
                        <td class="row-num">{{ $i + 1 }}</td>

                        <td>
                            <div class="row-title">{{ $b->title }}</div>
                            <div class="row-body-preview">{{ Str::limit($b->body, 55) }}</div>
                        </td>

                        <td>
                            <div class="row-date">
                                <i class="far fa-calendar-alt"></i>
                                {{ \Carbon\Carbon::parse($b->publish_date)->format('d M Y') }}
                            </div>
                        </td>

                        <td class="text-center">
                            @if($b->status == 'sent')
                                <span class="status-chip sent">
                                    <i class="fas fa-check-double"></i> TERKIRIM
                                </span>
                            @else
                                <span class="status-chip draft">
                                    <i class="fas fa-clock"></i> DRAFT
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($b->status == 'draft')
                                <form
                                    action="{{ route('dashboard.admin.broadcast.send', $b->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menyebarkan notifikasi ini ke SELURUH HP pengguna sekarang?')"
                                >
                                    @csrf
                                    <button type="submit" class="btn-spread">
                                        <i class="fas fa-paper-plane"></i> SEBARKAN SEKARANG
                                    </button>
                                </form>
                            @else
                                <span class="sent-label">
                                    <i class="fas fa-check-double"></i> Selesai disebar
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <form
                                action="{{ route('dashboard.admin.broadcast.destroy', $b->id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus draft ini?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-broadcast-tower"></i>
                                </div>
                                <p>Belum ada draft pengumuman.</p>
                                <small>Tekan "Buat Draft Baru" untuk membuat pengumuman pertama.</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection