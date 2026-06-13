@extends('dashboard.layouts.app')
@section('title', 'Detail Reservasi #RES-' . $reservasi->id)

@section('content')
{{-- ================================================================
     DETAIL RESERVASI HOTEL — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;
    --indigo:       #6366f1;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #5b6e8c;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 20px 44px rgba(0,25,125,.14);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label { font-family: var(--font) !important; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.detail-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
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
.detail-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}
.detail-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.detail-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.detail-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.btn-premium-outline {
    background: transparent;
    border: 1.5px solid var(--navy);
    border-radius: 14px;
    padding: 12px 24px;
    font-weight: 700;
    font-size: .85rem;
    color: var(--navy);
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
    box-shadow: 0 8px 20px rgba(0,25,125,.2);
}
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

/* ============================================================
   STATUS BADGE LARGE
   ============================================================ */
.status-badge-large {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: 40px;
    font-weight: 800;
    font-size: .85rem;
    letter-spacing: 0.5px;
}
.status-badge-large.pending   { background: #fff3e0; color: #c2410c; }
.status-badge-large.terbayar  { background: #e0f2fe; color: #0369a1; }
.status-badge-large.checkin   { background: #dcfce7; color: #15803d; }
.status-badge-large.selesai   { background: #e9eef3; color: #334155; }
.status-badge-large.batal     { background: #fee2e2; color: #b91c1c; }

/* ============================================================
   CARD PREMIUM
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 28px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
    transition: var(--transition);
}
.card-premium:hover { box-shadow: var(--shadow-hover); }
.card-premium-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 20px 28px;
    border: none;
}
.card-premium-header h6 {
    margin: 0;
    font-weight: 800;
    color: white;
    font-size: .9rem;
    letter-spacing: 0.5px;
}
.card-premium-body { padding: 28px; }

/* Avatar Premium */
.avatar-premium {
    width: 80px;
    height: 80px;
    background: linear-gradient(145deg, var(--navy), var(--navy-mid));
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 2rem;
    margin-bottom: 16px;
    box-shadow: 0 8px 20px rgba(0,25,125,.2);
}

/* Info Table */
.info-table {
    width: 100%;
}
.info-table tr td {
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
    font-size: .85rem;
}
.info-table tr:last-child td { border-bottom: none; }
.info-table .label {
    color: var(--text-muted);
    font-weight: 600;
    width: 40%;
}
.info-table .value {
    color: var(--text-primary);
    font-weight: 700;
    text-align: right;
}

/* Date Box */
.date-box-premium {
    background: var(--surface-2);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border);
    margin-bottom: 24px;
}

/* Price Summary */
.price-summary {
    background: linear-gradient(135deg, #f8faff 0%, #fef9e6 100%);
    border-radius: 20px;
    padding: 20px;
    margin-top: 20px;
}
.price-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid rgba(0,25,125,.1);
}
.price-row.total {
    border-bottom: none;
    padding-top: 16px;
    margin-top: 8px;
    font-size: 1.1rem;
}
.price-label {
    font-weight: 600;
    color: var(--text-muted);
}
.price-value {
    font-weight: 700;
    color: var(--text-primary);
}
.price-value.total-price {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--emerald);
}

/* Catatan Tamu */
.note-box {
    background: #fff3e0;
    border-left: 4px solid var(--amber);
    border-radius: 12px;
    padding: 12px 16px;
    margin-top: 16px;
}
.note-box small { color: var(--text-muted); font-size: .7rem; letter-spacing: 1px; }

/* Footer Note */
.card-footer-note {
    background: var(--surface-2);
    border-top: 1px solid var(--border);
    padding: 16px 28px;
    text-align: center;
}
.card-footer-note small {
    color: var(--text-muted);
    font-size: .75rem;
}

/* ============================================================
   RESPONSIVE & PRINT
   ============================================================ */
@media (max-width: 768px) {
    .detail-page-wrapper { padding: 20px 16px; }
    .detail-header-left h2 { font-size: 1.5rem; }
    .card-premium-body { padding: 20px; }
    .avatar-premium { width: 60px; height: 60px; font-size: 1.5rem; border-radius: 20px; }
}
@media print {
    .no-print { display: none !important; }
    .detail-page-wrapper {
        padding: 0;
        background: white;
    }
    .detail-page-wrapper::before,
    .detail-page-wrapper::after { display: none; }
    .card-premium {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
    }
    .btn-premium-outline, .btn-premium-primary { display: none; }
}
</style>

<div class="detail-page-wrapper">

    {{-- Header dengan tombol --}}
    <div class="detail-header">
        <div class="detail-header-left">
            <h2>Detail <span>Reservasi</span></h2>
            <p><i class="fas fa-receipt me-1"></i> Informasi lengkap pemesanan kamar tamu</p>
        </div>
        <div class="d-flex gap-2 no-print">
            <button onclick="window.print()" class="btn-premium-outline">
                <i class="fas fa-print"></i> Cetak Struk
            </button>
            <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="btn-premium-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kiri: Status & Pelanggan --}}
        <div class="col-lg-5">
            <!-- Status Card -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-info-circle me-2"></i> Status Reservasi</h6>
                </div>
                <div class="card-premium-body text-center">
                    @php
                        $sid = $reservasi->status_reservasi_id;
                        switch($sid) {
                            case 1: $statusClass = 'pending'; $statusIcon = 'fa-clock'; $statusLabel = 'PENDING'; break;
                            case 2: $statusClass = 'terbayar'; $statusIcon = 'fa-credit-card'; $statusLabel = 'TERBAYAR'; break;
                            case 3: $statusClass = 'checkin'; $statusIcon = 'fa-door-open'; $statusLabel = 'CHECK-IN'; break;
                            case 4: $statusClass = 'selesai'; $statusIcon = 'fa-check-double'; $statusLabel = 'SELESAI'; break;
                            case 5: $statusClass = 'batal'; $statusIcon = 'fa-ban'; $statusLabel = 'BATAL'; break;
                            default: $statusClass = 'pending'; $statusIcon = 'fa-clock'; $statusLabel = 'PENDING';
                        }
                    @endphp
                    <div class="status-badge-large {{ $statusClass }} mb-3">
                        <i class="fas {{ $statusIcon }}"></i> {{ $statusLabel }}
                    </div>
                    <h4 class="fw-bold text-dark mb-1">#RES-{{ $reservasi->id }}</h4>
                    <p class="text-muted small mb-0">
                        <i class="far fa-calendar-alt me-1"></i> Dibuat: {{ $reservasi->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            <!-- Pelanggan Card -->
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-user-circle me-2"></i> Data Pelanggan</h6>
                </div>
                <div class="card-premium-body">
                    @php $user = $users[$reservasi->user_id] ?? null; @endphp
                    <div class="text-center mb-4">
                        <div class="avatar-premium">
                            {{ strtoupper(substr($user['full_name'] ?? 'T', 0, 1)) }}
                        </div>
                        <h5 class="fw-bold text-dark mb-0">{{ $user['full_name'] ?? 'Tamu Umum' }}</h5>
                        <p class="text-muted small mb-0">{{ $user['email'] ?? '-' }}</p>
                    </div>
                    <table class="info-table">
                        <tr><td class="label"><i class="fas fa-phone-alt me-2"></i> No. Handphone</td><td class="value">{{ $user['phone'] ?? '-' }}</td></tr>
                        <tr><td class="label"><i class="fas fa-credit-card me-2"></i> Metode Pembayaran</td><td class="value text-success">{{ $reservasi->metode_pembayaran }}</td></tr>
                        <tr><td class="label"><i class="fas fa-calendar-check me-2"></i> Reservasi via</td><td class="value">{{ $reservasi->created_via ?? 'Website' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kanan: Detail Kamar & Biaya --}}
        <div class="col-lg-7">
            <div class="card-premium h-100">
                <div class="card-premium-header">
                    <h6><i class="fas fa-bed me-2"></i> Rincian Kamar & Biaya</h6>
                </div>
                <div class="card-premium-body">

                    {{-- Tipe & Nomor Kamar --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: var(--surface-2); border: 1px solid var(--border);">
                                <span class="text-muted d-block mb-1" style="font-size: .7rem; letter-spacing: 1px;">
                                    <i class="fas fa-tag"></i> TIPE KAMAR
                                </span>
                                <h5 class="fw-bold text-dark mb-0">{{ $reservasi->tipeKamar->nama_tipe ?? '-' }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: var(--surface-2); border: 1px solid var(--border);">
                                <span class="text-muted d-block mb-1" style="font-size: .7rem; letter-spacing: 1px;">
                                    <i class="fas fa-door-open"></i> NOMOR KAMAR
                                </span>
                                <h5 class="fw-bold text-dark mb-0">
                                    {{ $reservasi->kamar->nomor_kamar ?? 'Menunggu Penempatan' }}
                                    @if(!$reservasi->kamar)
                                        <span class="badge bg-warning text-dark ms-2" style="font-size: .65rem;">Belum Assign</span>
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Menginap --}}
                    <div class="date-box-premium">
                        <div class="row align-items-center text-center">
                            <div class="col-md-5">
                                <span class="text-muted d-block mb-1" style="font-size: .7rem; letter-spacing: 1px;">
                                    <i class="fas fa-calendar-alt text-primary"></i> CHECK IN
                                </span>
                                <div class="fw-bold text-dark fs-5">{{ \Carbon\Carbon::parse($reservasi->tgl_checkin)->format('d M Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($reservasi->tgl_checkin)->format('H:i') }} WIB</small>
                            </div>
                            <div class="col-md-2">
                                <div class="my-2 my-md-0">
                                    <i class="fas fa-long-arrow-alt-right fa-2x text-muted"></i>
                                    <div class="mt-1"><span class="badge bg-primary">{{ $reservasi->total_malam }} Malam</span></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <span class="text-muted d-block mb-1" style="font-size: .7rem; letter-spacing: 1px;">
                                    <i class="fas fa-calendar-week text-danger"></i> CHECK OUT
                                </span>
                                <div class="fw-bold text-dark fs-5">{{ \Carbon\Carbon::parse($reservasi->tgl_checkout)->format('d M Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($reservasi->tgl_checkout)->format('H:i') }} WIB</small>
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan Biaya --}}
                    <div class="price-summary">
                        <div class="price-row">
                            <span class="price-label"><i class="fas fa-hotel me-1"></i> Harga per Malam</span>
                            <span class="price-value">Rp {{ number_format($reservasi->tipeKamar->harga ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-row">
                            <span class="price-label"><i class="fas fa-moon me-1"></i> Jumlah Malam</span>
                            <span class="price-value">x {{ $reservasi->total_malam }}</span>
                        </div>
                        <div class="price-row">
                            <span class="price-label"><i class="fas fa-user-friends me-1"></i> Kapasitas Tamu</span>
                            <span class="price-value">{{ $reservasi->tipeKamar->kapasitas ?? 1 }} orang</span>
                        </div>
                        @if(($reservasi->total_diskon ?? 0) > 0)
                        <div class="price-row">
                            <span class="price-label"><i class="fas fa-tags me-1"></i> Diskon</span>
                            <span class="price-value text-danger">- Rp {{ number_format($reservasi->total_diskon ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="price-row total">
                            <span class="price-label fw-bold">TOTAL PEMBAYARAN</span>
                            <span class="price-value total-price">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Catatan Tamu (jika ada) --}}
                    @if($reservasi->catatan)
                    <div class="note-box">
                        <small class="text-muted d-block mb-1"><i class="fas fa-pen me-1"></i> Catatan Tamu</small>
                        <p class="mb-0 small fw-medium">{{ $reservasi->catatan }}</p>
                    </div>
                    @endif
                </div>

                <div class="card-footer-note no-print">
                    <small><i class="fas fa-info-circle me-1"></i> Harap tunjukkan invoice ini saat melakukan Check-in di resepsionis.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi kartu
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
    // Animasi header
    const header = document.querySelector('.detail-header');
    if (header) {
        header.style.opacity = '0';
        header.style.transform = 'translateY(-10px)';
        header.style.transition = 'all 0.4s ease';
        setTimeout(() => {
            header.style.opacity = '1';
            header.style.transform = 'translateY(0)';
        }, 50);
    }
});
</script>
@endsection