@extends('dashboard.layouts.app')
@section('title', 'Detail Pesanan #' . $pesanan->id)

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
.detail-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.detail-page-wrapper::before,
.detail-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.detail-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.detail-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.detail-page-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER & BREADCRUMB
   ============================================================ */
.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 16px;
}

.breadcrumb-custom {
    margin-bottom: 4px;
}

.breadcrumb-custom a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: var(--transition);
}

.breadcrumb-custom a:hover {
    color: var(--navy);
}

.breadcrumb-custom .active {
    color: var(--navy);
    font-weight: 600;
}

.detail-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0;
    letter-spacing: -.03em;
}

.detail-title span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Button Premium */
.btn-premium-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 12px 24px;
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

.btn-premium-outline {
    background: transparent;
    border: 1.5px solid var(--navy);
    color: var(--navy);
    border-radius: 14px;
    padding: 12px 24px;
    font-weight: 700;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
}

.btn-premium-outline:hover {
    background: var(--navy);
    color: white;
    transform: translateY(-2px);
}

.btn-group-custom {
    display: flex;
    gap: 12px;
}

/* ============================================================
   CARDS STYLE
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 28px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: var(--transition);
    height: 100%;
}

.card-premium:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.card-header-premium {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 18px 24px;
    border: none;
}

.card-header-premium h6 {
    color: white;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-body-premium {
    padding: 24px;
}

/* Badge Status */
.badge-status-large {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: 40px;
    font-weight: 700;
    font-size: 0.85rem;
}
.badge-status-large.pending { background: #fff3e0; color: #c2410c; }
.badge-status-large.lunas { background: #dcfce7; color: #15803d; }
.badge-status-large.batal { background: #fee2e2; color: #b91c1c; }

/* Order Info */
.order-number {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 4px;
}

.order-date {
    font-size: 0.75rem;
    color: var(--text-muted);
}

/* Avatar Section */
.avatar-circle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.customer-name {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.customer-email {
    font-size: 0.7rem;
    color: var(--text-muted);
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin: 20px 0;
}

.info-card {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 14px;
    text-align: center;
}

.info-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 6px;
}

.info-value {
    font-weight: 800;
    font-size: 0.9rem;
    color: var(--text-primary);
}

.info-value i {
    margin-right: 6px;
    color: var(--navy);
}

/* Table Styles */
.table-responsive-custom {
    overflow-x: auto;
}

.table-detail {
    width: 100%;
    border-collapse: collapse;
}

.table-detail thead tr th {
    background: var(--surface-2);
    padding: 16px 20px;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    border-bottom: 1.5px solid var(--border);
}

.table-detail tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.2s;
}

.table-detail tbody tr:hover {
    background: #fafcff;
}

.table-detail tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    font-size: 0.82rem;
    font-weight: 500;
    color: #1e293b;
}

/* Badge Kategori */
.badge-kategori-sm {
    background: #eef2ff;
    color: var(--navy);
    border-radius: 10px;
    padding: 3px 10px;
    font-weight: 700;
    font-size: .6rem;
    display: inline-block;
}

/* Total Box */
.total-box {
    background: linear-gradient(135deg, var(--surface-2) 0%, #ffffff 100%);
    border: 2px dashed var(--gold);
    border-radius: 20px;
    padding: 20px;
    margin-top: 16px;
}

.total-item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.total-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.total-value {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-primary);
}

.grand-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 2px solid var(--border);
}

.grand-total-label {
    font-size: 1rem;
    font-weight: 800;
    color: var(--navy-dark);
}

.grand-total-value {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Print Styles */
@media print {
    .sidebar, 
    .topbar, 
    .btn-group-custom, 
    .btn-premium-primary,
    .btn-premium-outline,
    .breadcrumb-custom {
        display: none !important;
    }
    .detail-page-wrapper {
        padding: 0 !important;
        background: white !important;
    }
    .card-premium {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
    .card-header-premium {
        background: #f0f0f0 !important;
    }
    .card-header-premium h6 {
        color: #333 !important;
    }
    body {
        background: white !important;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .detail-page-wrapper {
        padding: 20px 16px;
    }
    .detail-title {
        font-size: 1.3rem;
    }
    .btn-group-custom {
        flex-wrap: wrap;
    }
    .info-grid {
        grid-template-columns: 1fr;
    }
    .grand-total-value {
        font-size: 1.1rem;
    }
}
</style>

<!-- ================================================
     MARKUP (menggunakan data dari kode asli)
     ================================================ -->
<div class="detail-page-wrapper">

    <!-- Header -->
    <div class="detail-header">
        <div>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard.restoran.pesanan.index') }}">Daftar Pesanan</a>
                <span class="text-muted mx-1">/</span>
                <span class="active">Detail #{{ $pesanan->id }}</span>
            </div>
            <h1 class="detail-title">Invoice <span>Digital</span></h1>
        </div>
        <div class="btn-group-custom">
            <button onclick="window.print()" class="btn-premium-outline">
                <i class="fas fa-print"></i>
                Cetak Struk
            </button>
            <a href="{{ route('dashboard.restoran.pesanan.index') }}" class="btn-premium-primary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sisi Kiri: Informasi Order & Pelanggan -->
        <div class="col-lg-4">
            <!-- Card Status -->
            <div class="card-premium mb-4">
                <div class="card-body-premium text-center">
                    @php
                        $statusPay = $pesanan->status_pembayaran_id;
                        $bgPay = ($statusPay == 2) ? 'lunas' : (($statusPay == 1) ? 'pending' : 'batal');
                        $payIcon = ($statusPay == 2) ? 'fa-check-circle' : (($statusPay == 1) ? 'fa-clock' : 'fa-ban');
                        $payLabel = $pesanan->statusPembayaran->nama_status ?? ($statusPay == 1 ? 'Pending' : ($statusPay == 2 ? 'Lunas' : 'Batal'));
                    @endphp
                    <div class="mb-3">
                        <span class="badge-status-large {{ $bgPay }}">
                            <i class="fas {{ $payIcon }}"></i>
                            {{ $payLabel }}
                        </span>
                    </div>
                    <div class="order-number">ORD-{{ $pesanan->id }}</div>
                    <div class="order-date">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $pesanan->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                </div>
            </div>

            <!-- Card Pelanggan -->
            <div class="card-premium">
                <div class="card-header-premium">
                    <h6>
                        <i class="fas fa-user-circle"></i>
                        Data Pelanggan
                    </h6>
                </div>
                <div class="card-body-premium">
                    @php $user = $users[$pesanan->user_id] ?? null; @endphp
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="avatar-circle">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="customer-name">{{ $user['full_name'] ?? 'Tamu Umum' }}</div>
                            <div class="customer-email">{{ $user['email'] ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-card">
                            <div class="info-label">Nomor Meja</div>
                            <div class="info-value">
                                <i class="fas fa-chair"></i> Meja {{ $pesanan->nomor_meja }}
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">Metode Bayar</div>
                            <div class="info-value">
                                <i class="fas fa-credit-card"></i> {{ $pesanan->metode_pembayaran ?? 'Tunai' }}
                            </div>
                        </div>
                    </div>

                    <hr class="my-3" style="border-color: var(--border);">

                    @php
                        $queueStatus = $pesanan->status_pesanan_id ?? 1;
                        $queueIcon = $queueStatus == 1 ? 'fa-hourglass-half' : ($queueStatus == 2 ? 'fa-spinner fa-pulse' : 'fa-check-double');
                        $queueLabel = $pesanan->statusPesanan->nama_status ?? ($queueStatus == 1 ? 'Dalam Antrean' : ($queueStatus == 2 ? 'Diproses' : 'Selesai'));
                    @endphp
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold">Status Antrean:</span>
                        <strong class="text-primary">
                            <i class="fas {{ $queueIcon }} me-1"></i> {{ $queueLabel }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Rincian Menu -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-header-premium">
                    <h6>
                        <i class="fas fa-utensils"></i>
                        Rincian Menu yang Dipesan
                    </h6>
                </div>
                <div class="card-body-premium p-0">
                    <div class="table-responsive-custom">
                        <table class="table-detail">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">NAMA ITEM</th>
                                    <th class="text-center" style="width: 15%;">QTY</th>
                                    <th class="text-end" style="width: 20%;">HARGA</th>
                                    <th class="text-end" style="width: 20%;">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalItems = 0; @endphp
                                @foreach($pesanan->details as $item)
                                @php $totalItems += $item->jumlah; @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            {{ $item->menu->nama_menu ?? 'Menu Terhapus' }}
                                        </div>
                                        <span class="badge-kategori-sm mt-1 d-inline-block">
                                            <i class="fas fa-tag"></i> {{ $item->menu->kategori->nama_kategori ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold">{{ $item->jumlah }}</td>
                                    <td class="text-end text-muted">
                                        Rp {{ number_format($item->harga_at_porsi, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        Rp {{ number_format($item->jumlah * $item->harga_at_porsi, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-body-premium" style="border-top: 1px solid var(--border);">
                    <div class="total-box">
                        <div class="total-item-row">
                            <span class="total-label">TOTAL ITEM</span>
                            <span class="total-value">{{ $totalItems }} Item</span>
                        </div>
                        <div class="grand-total-row">
                            <span class="grand-total-label">TOTAL BAYAR</span>
                            <span class="grand-total-value">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Catatan Tambahan (jika ada) – menggunakan field yang sama seperti kode asli jika tersedia --}}
                    @if(!empty($pesanan->catatan))
                    <div class="mt-3 p-3" style="background: #fff3e0; border-radius: 16px;">
                        <div class="d-flex gap-2">
                            <i class="fas fa-pencil-alt text-warning"></i>
                            <div>
                                <div class="small fw-bold text-muted mb-1">Catatan Pesanan:</div>
                                <div class="small">{{ $pesanan->catatan }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Animasi sederhana (sama seperti template backup) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card-premium');
    cards.forEach((card, idx) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (idx * 100));
    });
});
</script>

@endsection