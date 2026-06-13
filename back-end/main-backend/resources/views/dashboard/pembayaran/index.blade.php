@extends('dashboard.layouts.app')
@section('title', 'Seluruh Pembayaran')
@section('breadcrumb', 'Admin Control')

@push('styles')
<style>
/* ================================================
   DESIGN TOKENS (Premium)
   ================================================ */
:root {
    --navy:          #00197D;
    --navy-dark:     #000C3D;
    --navy-soft:     #EEF2FF;
    --navy-xsoft:    #F5F7FF;
    --gold:          #D4AF37;
    --gold-bg:       #FFFBEB;
    --success:       #059669;
    --success-bg:    #ECFDF5;
    --border:        #E8EDF5;
    --bg:            #F4F7FC;
    --white:         #ffffff;
    --text-main:     #0f172a;
    --text-sub:      #334155;
    --text-muted:    #64748B;
    --text-light:    #94a3b8;
    --radius-sm:     10px;
    --radius-md:     16px;
    --radius-lg:     22px;
    --radius-xl:     28px;
}

.page-header {
    margin-bottom: 30px;
}
.page-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--navy);
    background: var(--navy-soft);
    border: 1px solid rgba(0,25,125,0.12);
    border-radius: 20px;
    padding: 5px 14px;
    margin-bottom: 12px;
}
.page-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.2;
    margin: 0 0 6px;
    letter-spacing: -0.3px;
}
.page-desc {
    font-size: 13.5px;
    color: var(--text-muted);
    font-weight: 500;
    margin: 0;
}

/* Tab Navigation */
.tab-nav-wrapper {
    margin-bottom: 24px;
}
.tab-nav {
    display: inline-flex;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 5px;
    gap: 4px;
    box-shadow: 0 4px 16px rgba(0,25,125,0.05);
}
.tab-btn {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 11px 26px;
    border-radius: var(--radius-md);
    border: none;
    background: transparent;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.tab-btn i { font-size: 14px; }
.tab-btn:hover:not(.active) {
    background: var(--navy-xsoft);
    color: var(--navy);
}
.tab-btn.active {
    background: var(--navy);
    color: var(--white);
    box-shadow: 0 6px 18px rgba(0,25,125,0.22);
}
.tab-btn.active i { color: var(--gold); }
.tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    border-radius: 7px;
    font-size: 10px;
    font-weight: 800;
    padding: 0 5px;
}
.tab-btn.active .tab-count {
    background: rgba(255,255,255,0.15);
    color: var(--white);
}
.tab-btn:not(.active) .tab-count {
    background: var(--navy-soft);
    color: var(--navy);
}

/* Tab Panels */
.tab-panel {
    display: none;
    animation: panelFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.tab-panel.active { display: block; }
@keyframes panelFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Table Card */
.table-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    overflow: hidden;
}
.table-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    gap: 12px;
    flex-wrap: wrap;
    background: #FAFBFF;
}
.table-card-title {
    font-size: 13px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 2px;
}
.table-card-desc {
    font-size: 11.5px;
    color: var(--text-muted);
    font-weight: 500;
}
.record-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    background: var(--navy-soft);
    color: var(--navy);
    padding: 5px 14px;
    border-radius: 20px;
}

.p-table {
    width: 100%;
    border-collapse: collapse;
}
.p-table thead tr {
    background: #F8FAFF;
    border-bottom: 1.5px solid var(--border);
}
.p-table thead th {
    font-size: 9.5px;
    font-weight: 800;
    letter-spacing: 1.8px;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 13px 20px;
    white-space: nowrap;
}
.p-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.p-table tbody tr:hover { background: var(--navy-xsoft); }
.p-table tbody td {
    padding: 15px 20px;
    vertical-align: middle;
    font-size: 13px;
}
.row-num {
    width: 28px; height: 28px;
    background: var(--bg);
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-light);
}
.guest-avatar {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: var(--navy-soft);
    color: var(--navy);
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
}
.guest-name {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
}
.guest-email {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
    margin-top: 1px;
}
.room-type {
    font-size: 13px;
    font-weight: 700;
    color: var(--navy);
}
.room-unit {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
}
.period-range {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-sub);
}
.night-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--success-bg);
    color: var(--success);
    font-size: 10px;
    font-weight: 700;
    padding: 2px 9px;
    border-radius: 20px;
    margin-top: 3px;
}
.invoice-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--navy-soft);
    color: var(--navy);
    font-size: 12px;
    font-weight: 800;
    padding: 5px 12px;
    border-radius: var(--radius-sm);
}
.date-text {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-sub);
}
.amount-navy {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--navy);
}
.amount-green {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--success);
}
.btn-detail {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 7px 14px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--navy);
    cursor: pointer;
    transition: all 0.2s;
}
.btn-detail:hover {
    background: var(--navy);
    color: var(--white);
    border-color: var(--navy);
    transform: translateY(-1px);
}
.empty-state {
    padding: 55px 20px;
    text-align: center;
}
.empty-icon {
    width: 58px; height: 58px;
    background: var(--bg);
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--text-light);
    margin-bottom: 14px;
}

/* Modal Styles */
.modal-content {
    border: none !important;
    border-radius: var(--radius-xl) !important;
    overflow: hidden;
}
.modal-navy-header {
    background: var(--navy);
    padding: 22px 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-title-main {
    font-size: 14px;
    font-weight: 800;
    color: var(--white);
    margin-bottom: 3px;
}
.modal-title-sub {
    font-size: 10px;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    letter-spacing: 1px;
}
.modal-x-btn {
    width: 30px; height: 30px;
    background: rgba(255,255,255,0.1);
    border: none;
    border-radius: 8px;
    color: var(--white);
    cursor: pointer;
}
.hotel-receipt-body {
    padding: 26px;
    background: var(--bg);
}
.receipt-paper {
    background: var(--white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    overflow: hidden;
}
.receipt-paper-top {
    padding: 20px 22px;
    text-align: center;
    border-bottom: 1px dashed var(--border);
}
.receipt-brand {
    font-size: 15px;
    font-weight: 800;
    color: var(--navy);
}
.receipt-brand-sub {
    font-size: 10.5px;
    color: var(--text-muted);
}
.receipt-section {
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
}
.receipt-section-label {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--text-light);
    margin-bottom: 11px;
    display: block;
}
.receipt-row {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    margin-bottom: 7px;
}
.rr-key { color: var(--text-muted); font-weight: 500; }
.rr-val { font-weight: 700; color: var(--text-main); text-align: right; }
.receipt-total-section {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 22px;
    text-align: center;
}
.receipt-total-label {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 2px;
    color: rgba(255,255,255,0.5);
    margin-bottom: 8px;
}
.receipt-total-amount {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--gold);
}
.verified-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(5,150,105,0.18);
    border: 1px solid rgba(5,150,105,0.28);
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 9.5px;
    font-weight: 700;
    color: #6EE7B7;
}
.resto-modal-body {
    padding: 26px;
    background: var(--bg);
}
.customer-strip {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 18px 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    margin-bottom: 16px;
}
.cs-col:first-child {
    border-right: 1px solid var(--border);
    padding-right: 16px;
}
.cs-col:last-child { padding-left: 16px; }
.cs-label {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 2px;
    color: var(--text-light);
    margin-bottom: 5px;
}
.cs-value {
    font-size: 14px;
    font-weight: 800;
    color: var(--navy);
}
.menu-table-wrap {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: 16px;
}
.menu-table {
    width: 100%;
    border-collapse: collapse;
}
.menu-table thead tr {
    background: #F8FAFF;
    border-bottom: 1px solid var(--border);
}
.menu-table thead th {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.8px;
    padding: 12px 16px;
}
.menu-table tbody td {
    padding: 13px 16px;
    font-size: 12.5px;
    border-bottom: 1px solid var(--border);
}
.menu-name { font-weight: 700; color: var(--navy); }
.qty-badge {
    display: inline-block;
    min-width: 30px;
    background: var(--navy-soft);
    color: var(--navy);
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 6px;
    text-align: center;
}
.total-box {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
}
.total-box-label {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: var(--text-muted);
}
.total-box-amount {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--success);
}
.modal-footer-custom {
    padding: 18px 26px;
    background: var(--white);
    border-top: 1px solid var(--border);
}
.btn-modal-close {
    width: 100%;
    padding: 13px;
    background: var(--navy);
    color: var(--white);
    border: none;
    border-radius: var(--radius-md);
    font-weight: 700;
}
.fade-up {
    opacity: 0;
    transform: translateY(18px);
    animation: fadeUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
}
.fade-up:nth-child(1) { animation-delay: 0.04s; }
.fade-up:nth-child(2) { animation-delay: 0.1s;  }
</style>
@endpush

@section('content')
<div class="container-fluid" style="max-width: 1200px;">

    {{-- Header --}}
    <div class="page-header fade-up">
        <div class="page-eyebrow"><i class="fas fa-layer-group"></i> Admin Control</div>
        <h2 class="page-title">Seluruh Transaksi Pembayaran</h2>
        <p class="page-desc">Laporan konsolidasi pendapatan dari unit bisnis Hotel dan Restoran.</p>
    </div>

    {{-- Tab Navigation --}}
    <div class="tab-nav-wrapper fade-up">
        <div class="tab-nav">
            <button class="tab-btn active" data-target="panel-hotel">
                <i class="fas fa-hotel"></i> Pembayaran Hotel
                <span class="tab-count">{{ count($pembayaranHotel) }}</span>
            </button>
            <button class="tab-btn" data-target="panel-resto">
                <i class="fas fa-utensils"></i> Pembayaran Restoran
                <span class="tab-count">{{ count($pembayaranResto) }}</span>
            </button>
        </div>
    </div>

    {{-- Panel Hotel --}}
    <div class="tab-panel active" id="panel-hotel">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Transaksi Hotel</div>
                    <div class="table-card-desc">Reservasi kamar yang telah lunas & terverifikasi</div>
                </div>
                <div class="record-chip"><i class="fas fa-receipt"></i> {{ count($pembayaranHotel) }} Transaksi</div>
            </div>
            <div class="table-responsive">
                <table class="p-table">
                    <thead>
                        <tr><th style="width:52px; text-align:center;">#</th><th>Identitas Tamu</th><th>Informasi Kamar</th><th>Periode</th><th>Total Penerimaan</th><th style="width:110px; text-align:center;">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaranHotel as $i => $h)
                        @php $user = $users[$h->user_id] ?? null; @endphp
                        <tr>
                            <td class="text-center"><span class="row-num">{{ $i+1 }}</span></td>
                            <td>
                                <div style="display:flex; gap:11px; align-items:center;">
                                    <div class="guest-avatar">{{ strtoupper(substr($user['full_name'] ?? 'T',0,1)) }}</div>
                                    <div><div class="guest-name">{{ $user['full_name'] ?? 'Tamu #'.$h->user_id }}</div><div class="guest-email">{{ $user['email'] ?? '-' }}</div></div>
                                </div>
                            </td>
                            <td>
                                <div class="room-type">{{ $h->tipeKamar->nama_tipe ?? '-' }}</div>
                                <div class="room-unit">Unit {{ $h->kamar->nomor_kamar ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="period-range">{{ \Carbon\Carbon::parse($h->tgl_checkin)->format('d/m/y') }} – {{ \Carbon\Carbon::parse($h->tgl_checkout)->format('d/m/y') }}</div>
                                <span class="night-badge"><i class="fas fa-moon"></i> {{ $h->total_malam }} Malam</span>
                             </td>
                            <td class="amount-navy">Rp {{ number_format($h->total_harga,0,',','.') }}</td>
                            <td class="text-center"><button class="btn-detail" data-bs-toggle="modal" data-bs-target="#modalHotel{{ $h->id }}"><i class="fas fa-file-invoice-dollar"></i> Detail</button></td>
                        </tr>
                        @empty
                        <td><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-hotel"></i></div><div class="fw-bold mb-1">Belum Ada Transaksi Hotel</div><div class="text-muted small">Pembayaran hotel yang lunas akan muncul di sini.</div></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Panel Restoran --}}
    <div class="tab-panel" id="panel-resto">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Transaksi Restoran</div>
                    <div class="table-card-desc">Pesanan restoran yang telah selesai & dibayar</div>
                </div>
                <div class="record-chip"><i class="fas fa-receipt"></i> {{ count($pembayaranResto) }} Transaksi</div>
            </div>
            <div class="table-responsive">
                <table class="p-table">
                    <thead>
                        <tr><th style="width:52px; text-align:center;">#</th><th>Data Pelanggan</th><th>Nomor Invoice</th><th>Tanggal Transaksi</th><th>Total Penerimaan</th><th style="width:110px; text-align:center;">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaranResto as $i => $r)
                        @php $user = $users[$r->user_id] ?? null; @endphp
                        <tr>
                            <td class="text-center"><span class="row-num">{{ $i+1 }}</span></td>
                            <td>
                                <div style="display:flex; gap:11px; align-items:center;">
                                    <div class="guest-avatar">{{ strtoupper(substr($user['full_name'] ?? 'U',0,1)) }}</div>
                                    <div><div class="guest-name">{{ $user['full_name'] ?? 'User #'.$r->user_id }}</div><div class="guest-email">{{ $user['email'] ?? '-' }}</div></div>
                                </div>
                             </td>
                            <td><span class="invoice-tag"><i class="fas fa-hashtag"></i> ORD-{{ str_pad($r->id,5,'0',STR_PAD_LEFT) }}</span></td>
                            <td><div class="date-text">{{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d M Y') }}</div><div style="font-size:11px; color:var(--text-light);">{{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }} WIB</div></td>
                            <td class="amount-green">Rp {{ number_format($r->total_harga,0,',','.') }}</td>
                            <td class="text-center"><button class="btn-detail" data-bs-toggle="modal" data-bs-target="#modalResto{{ $r->id }}"><i class="fas fa-list-ul"></i> Detail</button></td>
                        </tr>
                        @empty
                        <td><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-utensils"></i></div><div class="fw-bold mb-1">Belum Ada Transaksi Restoran</div><div class="text-muted small">Pesanan restoran yang selesai akan muncul di sini.</div></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Modal Hotel --}}
@foreach($pembayaranHotel as $h)
@php $user = $users[$h->user_id] ?? null; @endphp
<div class="modal fade" id="modalHotel{{ $h->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-navy-header">
                <div><div class="modal-title-main">Kwitansi Hotel</div><div class="modal-title-sub">Invoice #PH{{ str_pad($h->id,5,'0',STR_PAD_LEFT) }}</div></div>
                <button type="button" class="modal-x-btn" data-bs-dismiss="modal"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="hotel-receipt-body">
                <div class="receipt-paper">
                    <div class="receipt-paper-top"><div class="receipt-brand">Purnama Hotel</div><div class="receipt-brand-sub">Sistem Manajemen Terpadu</div></div>
                    <div class="receipt-section"><span class="receipt-section-label">Informasi Tamu</span><div class="receipt-row"><span class="rr-key">Nama</span><span class="rr-val">{{ $user['full_name'] ?? '-' }}</span></div><div class="receipt-row"><span class="rr-key">Email</span><span class="rr-val">{{ $user['email'] ?? '-' }}</span></div></div>
                    <div class="receipt-section"><span class="receipt-section-label">Detail Menginap</span><div class="receipt-row"><span class="rr-key">Tipe Kamar</span><span class="rr-val">{{ $h->tipeKamar->nama_tipe ?? '-' }}</span></div><div class="receipt-row"><span class="rr-key">Unit</span><span class="rr-val">{{ $h->kamar->nomor_kamar ?? '-' }}</span></div><div class="receipt-row"><span class="rr-key">Check-in</span><span class="rr-val">{{ \Carbon\Carbon::parse($h->tgl_checkin)->translatedFormat('d M Y') }}</span></div><div class="receipt-row"><span class="rr-key">Check-out</span><span class="rr-val">{{ \Carbon\Carbon::parse($h->tgl_checkout)->translatedFormat('d M Y') }}</span></div><div class="receipt-row"><span class="rr-key">Durasi</span><span class="rr-val">{{ $h->total_malam }} Malam</span></div></div>
                    <div class="receipt-total-section"><span class="receipt-total-label">Total Dana Diterima</span><div class="receipt-total-amount">Rp {{ number_format($h->total_harga,0,',','.') }}</div><span class="verified-chip"><i class="fas fa-shield-halved"></i> Terverifikasi Sistem</span></div>
                </div>
            </div>
            <div class="modal-footer-custom"><button type="button" class="btn-modal-close" data-bs-dismiss="modal">Tutup Kwitansi</button></div>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Restoran --}}
@foreach($pembayaranResto as $r)
@php $user = $users[$r->user_id] ?? null; @endphp
<div class="modal fade" id="modalResto{{ $r->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 560px;">
        <div class="modal-content">
            <div class="modal-navy-header">
                <div><div class="modal-title-main">Rincian Pesanan Restoran</div><div class="modal-title-sub">ORD-{{ str_pad($r->id,5,'0',STR_PAD_LEFT) }}</div></div>
                <button type="button" class="modal-x-btn" data-bs-dismiss="modal"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="resto-modal-body">
                <div class="customer-strip">
                    <div class="cs-col"><span class="cs-label">Pelanggan</span><div class="cs-value">{{ $user['full_name'] ?? 'Guest' }}</div><div style="font-size:11px; color:var(--text-muted);">{{ $user['email'] ?? '-' }}</div></div>
                    <div class="cs-col"><span class="cs-label">Tanggal Transaksi</span><div class="cs-value">{{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d M Y') }}</div><div style="font-size:11px; color:var(--text-muted);">{{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }} WIB</div></div>
                </div>
                <div class="menu-table-wrap">
                    <table class="menu-table">
                        <thead><tr><th>Item Menu</th><th style="text-align:center;">Qty</th><th style="text-align:right;">Harga</th><th style="text-align:right;">Subtotal</th></tr></thead>
                        <tbody>
                            @foreach($r->details as $item)
                            <tr><td class="menu-name">{{ $item->menu->nama_menu ?? 'Menu' }}</td><td class="text-center"><span class="qty-badge">{{ $item->jumlah }}</span></td><td class="text-end">Rp {{ number_format($item->harga_at_porsi,0,',','.') }}</td><td class="text-end fw-bold">Rp {{ number_format($item->jumlah * $item->harga_at_porsi,0,',','.') }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="total-box"><div class="total-box-label">Total Penerimaan Kas</div><div class="total-box-amount">Rp {{ number_format($r->total_harga,0,',','.') }}</div></div>
            </div>
            <div class="modal-footer-custom"><button type="button" class="btn-modal-close" data-bs-dismiss="modal">Tutup Rincian</button></div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switcher
    const btns = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.dataset.target;
            btns.forEach(b => b.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });
});
</script>
@endpush