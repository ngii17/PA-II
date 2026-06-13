@extends('dashboard.layouts.app')
@section('title', 'Pesanan Restoran')

@section('content')
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
    --text-muted:   #5b6e8c;
    --radius-2xl:   32px;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 20px 44px rgba(0,25,125,.14);
    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label { font-family: var(--font) !important; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.pesanan-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.pesanan-page-wrapper::before,
.pesanan-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.pesanan-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.pesanan-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.pesanan-page-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER
   ============================================================ */
.pesanan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}

.pesanan-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.pesanan-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.pesanan-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}

/* Button Premium */
.btn-premium-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 12px 26px;
    font-weight: 700;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
}
.btn-premium-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,25,125,.3);
    color: white;
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
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }

.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.stat-icon.navy  { background: rgba(0,25,125,.08);   color: var(--navy); }
.stat-icon.green { background: rgba(16,185,129,.1);   color: var(--emerald); }
.stat-icon.amber { background: rgba(245,158,11,.1);   color: var(--amber); }
.stat-icon.gold  { background: rgba(212,175,55,.12);  color: var(--gold); }

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
   ALERT SUCCESS
   ============================================================ */
.alert-success-premium {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-left: 4px solid #10b981;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #065f46;
    font-weight: 600;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}
@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   TABLE CARD
   ============================================================ */
.card-glass {
    background: var(--surface);
    border-radius: 36px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
}

.table-toolbar-custom {
    padding: 20px 28px 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.search-wrapper {
    position: relative;
    flex: 2;
    min-width: 240px;
}
.search-wrapper i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.9rem;
}
.search-wrapper input {
    width: 100%;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 48px;
    padding: 12px 18px 12px 44px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: .25s;
}
.search-wrapper input:focus {
    border-color: var(--navy);
    background: white;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
.filter-wrapper {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.filter-select {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 48px;
    padding: 12px 20px;
    font-size: 0.8rem;
    font-weight: 500;
    font-family: var(--font);
    color: var(--text-primary);
    cursor: pointer;
    transition: .25s;
}
.filter-select:focus {
    border-color: var(--navy);
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}

/* Table Styles */
.table-responsive-custom {
    overflow-x: auto;
    padding: 0 8px 16px;
}
.table-premium {
    width: 100%;
    border-collapse: collapse;
}
.table-premium thead th {
    background: var(--navy);
    color: rgba(255,255,255,.9);
    padding: 18px 20px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.table-premium tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.2s;
}
.table-premium tbody tr:hover { background: #fafcff; }
.table-premium tbody td {
    padding: 18px 20px;
    vertical-align: middle;
    font-size: 0.82rem;
    font-weight: 500;
    color: #1e293b;
}

/* Badge Meja */
.badge-meja {
    background: #eef2ff;
    color: var(--navy);
    border-radius: 12px;
    padding: 5px 12px;
    font-weight: 700;
    font-size: .7rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Badge Status Pembayaran */
.badge-payment {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .68rem;
}
.badge-payment.pending   { background: #fff3e0; color: #c2410c; }
.badge-payment.lunas     { background: #dcfce7; color: #15803d; }
.badge-payment.batal     { background: #fee2e2; color: #b91c1c; }

/* Badge Status Antrean */
.badge-queue {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .68rem;
}
.badge-queue.waiting    { background: #eef2ff; color: var(--navy); }
.badge-queue.processing { background: #fff3e0; color: #c2410c; }
.badge-queue.completed  { background: #dcfce7; color: #15803d; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
}
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: .75rem;
    font-weight: 700;
    transition: var(--transition);
    text-decoration: none;
}
.btn-action-detail { background: rgba(99,102,241,.1); color: #6366f1; }
.btn-action-edit   { background: rgba(245,158,11,.1); color: #f59e0b; }
.btn-action-delete { background: rgba(225,29,72,.1); color: #e11d48; }
.btn-action-cancel { background: rgba(225,29,72,.1); color: #e11d48; }
.btn-action-detail:hover,
.btn-action-edit:hover,
.btn-action-delete:hover,
.btn-action-cancel:hover {
    color: #fff;
    transform: translateY(-2px);
}
.btn-action-detail:hover { background: #6366f1; }
.btn-action-edit:hover   { background: #f59e0b; }
.btn-action-delete:hover,
.btn-action-cancel:hover { background: #e11d48; }

/* ============================================================
   MODAL DETAIL PREMIUM
   ============================================================ */
.modal-premium .modal-content {
    border-radius: 28px;
    border: none;
    box-shadow: 0 30px 60px rgba(0,0,0,.2);
    overflow: hidden;
}
.modal-premium .modal-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 20px 28px;
    border: none;
}
.modal-premium .modal-title {
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.modal-body-premium {
    padding: 28px;
}
.modal-pesanan-header {
    text-align: center;
    margin-bottom: 20px;
}
.modal-pesanan-nomor {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 4px;
}
.modal-pesanan-meja {
    font-size: 0.8rem;
    color: var(--text-muted);
}
.modal-items-list {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 16px;
    margin: 20px 0;
    max-height: 300px;
    overflow-y: auto;
}
.modal-item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
}
.modal-item-row:last-child { border-bottom: none; }
.modal-item-name {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-primary);
}
.modal-item-qty {
    font-size: 0.75rem;
    color: var(--text-muted);
}
.modal-item-price {
    font-weight: 700;
    color: var(--emerald);
}
.modal-total-box {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    border-radius: 16px;
    padding: 16px;
    margin: 16px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-total-label {
    color: rgba(255,255,255,.7);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}
.modal-total-value {
    color: var(--gold);
    font-size: 1.3rem;
    font-weight: 800;
}
.modal-status-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin: 16px 0;
}
.modal-status-card {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px;
    text-align: center;
}
.modal-footer-custom {
    padding: 16px 24px 24px;
    border: none;
}
.btn-close-modal {
    width: 100%;
    padding: 14px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-weight: 700;
    font-size: .875rem;
    color: var(--text-primary);
    cursor: pointer;
    transition: .25s;
}
.btn-close-modal:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
    transform: translateY(-1px);
}

/* Empty State */
.empty-state-premium {
    padding: 70px 20px;
    text-align: center;
}
.empty-icon-circle {
    width: 80px;
    height: 80px;
    background: var(--surface-2);
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
    margin-bottom: 20px;
    border: 2px dashed var(--border);
}

/* Responsive */
@media (max-width: 768px) {
    .pesanan-page-wrapper { padding: 20px 16px; }
    .pesanan-header-left h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
    .table-toolbar-custom { flex-direction: column; }
    .filter-wrapper { width: 100%; }
    .filter-select { flex: 1; }
    .action-buttons { flex-wrap: wrap; }
    .btn-action { padding: 6px 12px; font-size: .7rem; }
}
</style>

<div class="pesanan-page-wrapper">

    <!-- Header -->
    <div class="pesanan-header">
        <div class="pesanan-header-left">
            <h2>Pesanan <span>Restoran</span></h2>
            <p><i class="fas fa-receipt me-1"></i> Kelola pesanan makanan dan minuman dari pelanggan</p>
        </div>
        <a href="{{ route('dashboard.restoran.pesanan.create') }}" class="btn-premium-primary">
            <i class="fas fa-plus-circle"></i> Tambah Pesanan
        </a>
    </div>

    <!-- Stats Strip -->
    @php
        $totalPesanan = $pesanan->count();
        $pendingCount = $pesanan->where('status_pembayaran_id', 1)->count();
        $lunasCount   = $pesanan->where('status_pembayaran_id', 2)->count();
        $totalOmzet   = $pesanan->sum('total_harga');
    @endphp
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="fas fa-receipt"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalPesanan }}</div>
                <div class="stat-label">Total Pesanan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $pendingCount }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $lunasCount }}</div>
                <div class="stat-label">Lunas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-number">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                <div class="stat-label">Total Omzet</div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="alert-success-premium">
        <i class="fas fa-check-circle fa-lg"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Main Card -->
    <div class="card-glass">
        <div class="table-toolbar-custom">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchPesanan" placeholder="Cari pelanggan, meja, atau nomor pesanan...">
            </div>
            <div class="filter-wrapper">
                <select id="filterPayment" class="filter-select">
                    <option value="">Semua Status Bayar</option>
                    <option value="1">Pending</option>
                    <option value="2">Lunas</option>
                    <option value="3">Batal</option>
                </select>
                <select id="filterQueue" class="filter-select">
                    <option value="">Semua Status Antrean</option>
                    <option value="1">Menunggu</option>
                    <option value="2">Diproses</option>
                    <option value="3">Selesai</option>
                </select>
            </div>
        </div>

        <div class="table-responsive-custom">
            <table class="table-premium" id="pesananTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Meja</th>
                        <th class="text-end">Total Harga</th>
                        <th class="text-center">Status Bayar</th>
                        <th class="text-center">Status Antrean</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pesananTableBody">
                    @forelse($pesanan as $i => $p)
                    @php 
                        $user = $users[$p->user_id] ?? null;
                        
                        $paymentStatus = $p->status_pembayaran_id ?? 1;
                        $paymentClass = $paymentStatus == 1 ? 'pending' : ($paymentStatus == 2 ? 'lunas' : 'batal');
                        $paymentLabel = $p->statusPembayaran->nama_status ?? ($paymentStatus == 1 ? 'Pending' : ($paymentStatus == 2 ? 'Lunas' : 'Batal'));
                        
                        $queueStatus = $p->status_pesanan_id ?? 1;
                        $queueClass = $queueStatus == 1 ? 'waiting' : ($queueStatus == 2 ? 'processing' : 'completed');
                        $queueLabel = $p->statusPesanan->nama_status ?? ($queueStatus == 1 ? 'Menunggu' : ($queueStatus == 2 ? 'Diproses' : 'Selesai'));
                        
                        $canDelete = ($paymentStatus == 2 || $queueStatus == 3);
                        $deleteAction = $canDelete ? 'Hapus' : 'Batal';
                        $deleteClass = $canDelete ? 'btn-action-delete' : 'btn-action-cancel';
                        $deleteIcon = $canDelete ? 'fa-trash-alt' : 'fa-times';
                    @endphp
                    <tr data-payment="{{ $paymentStatus }}" 
                        data-queue="{{ $queueStatus }}"
                        data-search="{{ strtolower($user['full_name'] ?? '') }} {{ strtolower($user['email'] ?? '') }} meja{{ $p->nomor_meja }} {{ $p->id }}">
                        <td class="text-muted fw-bold">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">#{{ $p->id }}</div>
                            <div class="text-muted small">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td>
                            @if($user)
                                <div class="fw-bold" style="font-size:0.82rem;">{{ $user['full_name'] }}</div>
                                <div class="text-muted" style="font-size:0.7rem;">{{ $user['email'] }}</div>
                            @else
                                <div class="fw-bold text-danger">User #{{ $p->user_id }}</div>
                                <div class="text-muted">Data tidak tersedia</div>
                            @endif
                        </td>
                        <td><span class="badge-meja"><i class="fas fa-chair"></i> Meja {{ $p->nomor_meja }}</span></td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="text-center"><span class="badge-payment {{ $paymentClass }}"><i class="fas {{ $paymentStatus == 1 ? 'fa-clock' : ($paymentStatus == 2 ? 'fa-check-circle' : 'fa-ban') }}"></i> {{ $paymentLabel }}</span></td>
                        <td class="text-center"><span class="badge-queue {{ $queueClass }}"><i class="fas {{ $queueStatus == 1 ? 'fa-hourglass-half' : ($queueStatus == 2 ? 'fa-spinner fa-pulse' : 'fa-check-double') }}"></i> {{ $queueLabel }}</span></td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn-action btn-action-detail" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}"><i class="fas fa-eye"></i> Detail</button>
                                <a href="{{ route('dashboard.restoran.pesanan.edit', $p->id) }}" class="btn-action btn-action-edit"><i class="fas fa-edit"></i> Edit</a>
                                <button class="btn-action {{ $deleteClass }}" onclick="konfirmasiHapusPesanan({{ $p->id }}, '{{ addslashes('#' . $p->id . ' - ' . ($user['full_name'] ?? 'Tamu')) }}', {{ $canDelete ? 'true' : 'false' }})"><i class="fas {{ $deleteIcon }}"></i> {{ $deleteAction }}</button>
                            </div>
                            <form id="form-hapus-{{ $p->id }}" action="{{ route('dashboard.restoran.pesanan.destroy', $p->id) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
                        </td>
                    </tr>

                    <!-- MODAL DETAIL PESANAN PREMIUM -->
                    <div class="modal fade modal-premium" id="detailModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-receipt"></i> Detail Pesanan</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body-premium">
                                    <div class="modal-pesanan-header">
                                        <div class="modal-pesanan-nomor">Pesanan #{{ $p->id }}</div>
                                        <div class="modal-pesanan-meja"><i class="fas fa-chair"></i> Meja {{ $p->nomor_meja }} | <i class="fas fa-calendar"></i> {{ $p->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                    <div class="modal-items-list">
                                        <div class="fw-bold text-muted mb-2" style="font-size:0.7rem;">DETAIL PESANAN</div>
                                        @if($p->details && count($p->details) > 0)
                                            @foreach($p->details as $item)
                                            <div class="modal-item-row">
                                                <div>
                                                    <div class="modal-item-name">{{ $item->menu->nama_menu ?? 'Menu tidak tersedia' }}</div>
                                                    <div class="modal-item-qty">{{ $item->jumlah }} x Rp {{ number_format($item->harga_at_porsi ?? 0, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="modal-item-price">Rp {{ number_format(($item->jumlah ?? 0) * ($item->harga_at_porsi ?? 0), 0, ',', '.') }}</div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="text-center text-muted py-3"><i class="fas fa-box-open"></i> Tidak ada detail pesanan</div>
                                        @endif
                                    </div>
                                    <div class="modal-total-box"><span class="modal-total-label">TOTAL PEMBAYARAN</span><span class="modal-total-value">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span></div>
                                    <div class="modal-status-grid">
                                        <div class="modal-status-card"><div class="stat-label">Status Pembayaran</div><div class="mt-1"><span class="badge-payment {{ $paymentClass }}">{{ $paymentLabel }}</span></div></div>
                                        <div class="modal-status-card"><div class="stat-label">Status Antrean</div><div class="mt-1"><span class="badge-queue {{ $queueClass }}">{{ $queueLabel }}</span></div></div>
                                    </div>
                                </div>
                                <div class="modal-footer-custom"><button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Tutup Detail</button></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state-premium">
                                <div class="empty-icon-circle"><i class="fas fa-receipt"></i></div>
                                <h5>Belum Ada Pesanan</h5>
                                <p class="text-muted">Tambahkan pesanan pertama untuk restoran hotel.</p>
                                <a href="{{ route('dashboard.restoran.pesanan.create') }}" class="btn-premium-primary" style="display:inline-flex; margin-top:12px;"><i class="fas fa-plus-circle"></i> Tambah Pesanan</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi stat cards
    document.querySelectorAll('.stat-card').forEach((card, idx) => {
        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (idx * 80));
    });
    // Animasi baris tabel
    const rows = document.querySelectorAll('.table-premium tbody tr');
    rows.forEach((row, index) => {
        if (!row.querySelector('.empty-state-premium')) {
            row.style.opacity = '0';
            row.style.transform = 'translateY(12px)';
            row.style.transition = 'opacity 0.4s ease, transform 0.4s cubic-bezier(0.34,1.56,0.64,1)';
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 150 + (index * 40));
        }
    });

    // Filter & Search
    const searchInput = document.getElementById('searchPesanan');
    const filterPayment = document.getElementById('filterPayment');
    const filterQueue = document.getElementById('filterQueue');
    
    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const paymentValue = filterPayment.value;
        const queueValue = filterQueue.value;
        const rows = document.querySelectorAll('#pesananTableBody tr');
        rows.forEach(row => {
            if (row.querySelector('.empty-state-premium')) return;
            const searchText = row.dataset.search || '';
            const rowPayment = row.dataset.payment || '';
            const rowQueue = row.dataset.queue || '';
            const matchSearch = query === '' || searchText.includes(query);
            const matchPayment = paymentValue === '' || rowPayment === paymentValue;
            const matchQueue = queueValue === '' || rowQueue === queueValue;
            row.style.display = (matchSearch && matchPayment && matchQueue) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterTable);
    filterPayment?.addEventListener('change', filterTable);
    filterQueue?.addEventListener('change', filterTable);
});

function konfirmasiHapusPesanan(id, nama, isDelete) {
    const title = isDelete ? 'Hapus Pesanan?' : 'Batalkan Pesanan?';
    const message = isDelete 
        ? `Pesanan <strong>${nama}</strong> akan dihapus permanen dari sistem.`
        : `Pesanan <strong>${nama}</strong> akan dibatalkan dan tidak dapat dikembalikan.`;
    const confirmText = isDelete ? 'Ya, Hapus Permanen' : 'Ya, Batalkan Pesanan';
    Swal.fire({
        title: `<span style="font-family:Plus Jakarta Sans;font-weight:800;font-size:1.2rem;color:#0f172a;">${title}</span>`,
        html: `<span style="font-family:Plus Jakarta Sans;color:#64748b;font-size:.9rem;">${message}</span>`,
        icon: isDelete ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: '#00197D',
        cancelButtonColor: '#64748b',
        confirmButtonText: `<i class="fas ${isDelete ? 'fa-trash-alt' : 'fa-times'} me-1"></i> ${confirmText}`,
        cancelButtonText: '<i class="fas fa-arrow-left me-1"></i> Kembali'
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('form-hapus-' + id).submit();
    });
}
// Style for SweetAlert
const style = document.createElement('style');
style.textContent = `.swal2-popup { border-radius: 28px !important; font-family: 'Plus Jakarta Sans', sans-serif !important; padding: 28px !important; } .swal2-confirm, .swal2-cancel { border-radius: 12px !important; font-weight: 700 !important; padding: 12px 24px !important; }`;
document.head.appendChild(style);
</script>
@endsection