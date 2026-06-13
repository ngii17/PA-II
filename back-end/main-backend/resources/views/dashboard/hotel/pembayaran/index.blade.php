@extends('dashboard.layouts.app')
@section('title', 'Pembayaran Hotel')
@section('breadcrumb', 'Hotel Management')

@push('styles')
<style>
    /* ================================================
       DESIGN TOKENS
    ================================================ */
    :root {
        --navy:        #00197D;
        --navy-dark:   #000C3D;
        --navy-soft:   #EEF2FF;
        --gold:        #D4AF37;
        --gold-light:  #FBF5DC;
        --success:     #059669;
        --success-bg:  #ECFDF5;
        --danger:      #DC2626;
        --border:      #E8EDF5;
        --bg:          #F4F7FC;
        --text-main:   #0f172a;
        --text-muted:  #64748B;
        --text-light:  #94a3b8;
        --white:       #ffffff;
        --radius-sm:   10px;
        --radius-md:   16px;
        --radius-lg:   22px;
        --radius-xl:   28px;
    }

    /* ================================================
       PAGE HEADER
    ================================================ */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 32px;
        flex-wrap: wrap;
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
        font-size: 1.65rem;
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

    .badge-verified {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--success-bg);
        color: var(--success);
        font-weight: 700;
        font-size: 11px;
        padding: 2px 10px;
        border-radius: 20px;
        border: 1px solid rgba(5,150,105,0.2);
        margin-left: 4px;
    }

    /* ================================================
       STAT CARDS
    ================================================ */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 24px 26px;
        position: relative;
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px rgba(0,25,125,0.07);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 5px;
        border-radius: 4px 0 0 4px;
    }
    .stat-card.stat-navy::before  { background: var(--navy); }
    .stat-card.stat-green::before { background: var(--success); }

    .stat-card::after {
        content: '';
        position: absolute;
        right: -30px; top: -30px;
        width: 110px; height: 110px;
        border-radius: 50%;
        opacity: 0.04;
    }
    .stat-card.stat-navy::after  { background: var(--navy); }
    .stat-card.stat-green::after { background: var(--success); }

    .stat-icon {
        width: 42px; height: 42px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        margin-bottom: 16px;
        position: relative;
    }
    .stat-card.stat-navy .stat-icon  { background: var(--navy-soft); color: var(--navy); }
    .stat-card.stat-green .stat-icon { background: var(--success-bg); color: var(--success); }

    .stat-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-light);
        margin-bottom: 8px;
        display: block;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    .stat-card.stat-navy  .stat-value { color: var(--navy); }
    .stat-card.stat-green .stat-value { color: var(--success); }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        margin-top: 4px;
    }
    .stat-badge.badge-navy  { background: var(--navy-soft); color: var(--navy); }
    .stat-badge.badge-green { background: var(--success-bg); color: var(--success); }

    /* ================================================
       TABLE CARD
    ================================================ */
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
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        gap: 12px;
        flex-wrap: wrap;
    }

    .table-card-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-main);
    }

    .record-count {
        font-size: 11px;
        font-weight: 700;
        background: var(--navy-soft);
        color: var(--navy);
        padding: 4px 14px;
        border-radius: 20px;
    }

    .p-table {
        width: 100%;
        border-collapse: collapse;
    }

    .p-table thead tr {
        background: #FAFBFF;
        border-bottom: 1px solid var(--border);
    }

    .p-table thead th {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 14px 20px;
        white-space: nowrap;
    }

    .p-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.18s;
    }
    .p-table tbody tr:last-child { border-bottom: none; }
    .p-table tbody tr:hover { background: #FAFBFF; }

    .p-table tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        font-size: 13.5px;
    }

    .row-num {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-light);
        background: var(--bg);
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .guest-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--navy-soft);
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .guest-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-main);
    }

    .guest-email {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 2px;
    }

    .room-number {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--navy);
    }

    .room-type {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
    }

    .duration-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
    }

    .amount-value {
        font-size: 14px;
        font-weight: 800;
        color: var(--navy);
        letter-spacing: -0.3px;
    }

    .paid-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--success-bg);
        color: var(--success);
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 3px 9px;
        border-radius: 20px;
        margin-top: 4px;
        border: 1px solid rgba(5,150,105,0.15);
    }

    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 12px;
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
        box-shadow: 0 6px 18px rgba(0,25,125,0.18);
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }
    .empty-icon {
        width: 64px; height: 64px;
        background: var(--bg);
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        color: var(--text-light);
        margin: 0 auto 16px;
    }
    .empty-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }

    /* ================================================
       MODAL RECEIPT
    ================================================ */
    .modal-content {
        border: none !important;
        border-radius: var(--radius-xl) !important;
        overflow: hidden;
    }

    .modal-header-receipt {
        background: var(--navy);
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header-receipt .modal-title-text {
        font-size: 14px;
        font-weight: 800;
        color: var(--white);
    }

    .modal-header-receipt .invoice-num {
        font-size: 11px;
        font-weight: 600;
        color: rgba(255,255,255,0.5);
        letter-spacing: 1px;
        margin-top: 3px;
    }

    .modal-close-btn {
        width: 32px; height: 32px;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 8px;
        color: var(--white);
        font-size: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-close-btn:hover { background: rgba(255,255,255,0.2); }

    .receipt-body {
        padding: 28px;
        background: var(--bg);
    }

    .receipt-paper {
        background: var(--white);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .receipt-paper-header {
        padding: 22px 24px;
        text-align: center;
        border-bottom: 1px dashed var(--border);
    }

    .receipt-logo-text {
        font-size: 16px;
        font-weight: 800;
        color: var(--navy);
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .receipt-sub {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
        letter-spacing: 1px;
        margin-top: 3px;
    }

    .dashed-notch {
        display: flex;
        align-items: center;
        gap: 0;
    }
    .dashed-notch::before,
    .dashed-notch::after {
        content: '';
        width: 20px; height: 20px;
        border-radius: 50%;
        background: var(--bg);
        border: 1px solid var(--border);
    }
    .dashed-notch-line {
        flex: 1;
        border-top: 2px dashed var(--border);
    }

    .receipt-section {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
    }
    .receipt-section:last-child { border-bottom: none; }

    .receipt-section-label {
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-light);
        margin-bottom: 12px;
        display: block;
    }

    .receipt-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 13px;
        gap: 12px;
    }
    .receipt-row .rr-key {
        color: var(--text-muted);
        font-weight: 500;
    }
    .receipt-row .rr-val {
        font-weight: 700;
        color: var(--text-main);
        text-align: right;
    }

    .receipt-total {
        background: linear-gradient(135deg, #00197D 0%, #000C3D 100%);
        padding: 24px;
        text-align: center;
    }

    .receipt-total-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.55);
        margin-bottom: 8px;
        display: block;
    }

    .receipt-total-amount {
        font-size: 2rem;
        font-weight: 800;
        color: var(--gold);
        letter-spacing: -0.5px;
        line-height: 1;
        margin-bottom: 8px;
    }

    .receipt-verified-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(5,150,105,0.2);
        border: 1px solid rgba(5,150,105,0.3);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 10px;
        font-weight: 700;
        color: #6EE7B7;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .modal-footer-receipt {
        padding: 20px 28px;
        background: var(--white);
        border-top: 1px solid var(--border);
    }

    .btn-close-receipt {
        width: 100%;
        padding: 13px;
        background: var(--navy);
        color: var(--white);
        border: none;
        border-radius: var(--radius-md);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-close-receipt:hover {
        background: var(--navy-dark);
        transform: translateY(-1px);
    }

    /* ================================================
       ANIMATIONS
    ================================================ */
    .fade-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-up:nth-child(1) { animation-delay: 0.05s; }
    .fade-up:nth-child(2) { animation-delay: 0.12s; }
</style>
@endpush

@section('content')
<div class="container-fluid" style="max-width: 1200px;">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="page-header fade-up">
        <div class="page-header-left">
            <div class="page-eyebrow"><i class="fas fa-cash-register"></i> Hotel Management</div>
            <h2 class="page-title">Data Pembayaran Hotel</h2>
            <p class="page-desc">
                Rekapitulasi transaksi reservasi kamar yang telah berstatus
                <span class="badge-verified"><i class="fas fa-circle-check"></i> Lunas Terverifikasi</span>
            </p>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="stat-grid">
        <div class="stat-card stat-navy fade-up">
            <div class="stat-icon"><i class="fas fa-wallet"></i></div>
            <span class="stat-label">Total Pendapatan Hotel</span>
            <div class="stat-value" id="totalRevenueHotel" data-value="{{ $totalPendapatanHotel }}">Rp 0</div>
            <span class="stat-badge badge-navy"><i class="fas fa-arrow-trend-up"></i> Kumulatif Terverifikasi</span>
        </div>
        <div class="stat-card stat-green fade-up">
            <div class="stat-icon"><i class="fas fa-circle-check"></i></div>
            <span class="stat-label">Transaksi Terverifikasi</span>
            <div class="stat-value">{{ $totalTransaksiHotel }}</div>
            <span class="stat-badge badge-green"><i class="fas fa-check"></i> Transaksi Selesai</span>
        </div>
    </div>

    {{-- ===== TABLE CARD ===== --}}
    <div class="table-card fade-up">
        <div class="table-card-header">
            <div>
                <div class="table-card-title">Riwayat Transaksi</div>
                <div class="table-card-meta" style="font-size:12px; color:var(--text-muted);">Seluruh pembayaran yang telah lunas &amp; terverifikasi</div>
            </div>
            <div class="record-count">{{ $reservasi->count() }} Record</div>
        </div>

        <div class="table-responsive">
            <table class="p-table">
                <thead>
                    <tr>
                        <th style="width:56px; text-align:center;">#</th>
                        <th>Identitas Tamu</th>
                        <th>Info Kamar</th>
                        <th>Durasi</th>
                        <th>Total Bayar</th>
                        <th style="text-align:center; width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservasi as $i => $r)
                        @php $user = $users[$r->user_id] ?? null; @endphp
                        <tr>
                            <td style="text-align:center;"><span class="row-num">{{ $i + 1 }}</span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="guest-avatar">{{ strtoupper(substr($user['full_name'] ?? 'T', 0, 1)) }}</div>
                                    <div>
                                        <div class="guest-name">{{ $user['full_name'] ?? 'Tamu #'.$r->user_id }}</div>
                                        <div class="guest-email">{{ $user['email'] ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="room-number">Kamar {{ $r->kamar->nomor_kamar ?? 'N/A' }}</div>
                                <div class="room-type">{{ $r->tipeKamar->nama_tipe ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="duration-pill"><i class="fas fa-moon"></i> {{ $r->total_malam }} Malam</span>
                            </td>
                            <td>
                                <div class="amount-value">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</div>
                                <div class="paid-badge"><i class="fas fa-circle-check"></i> Paid</div>
                            </td>
                            <td style="text-align:center;">
                                <button class="btn-detail" data-bs-toggle="modal" data-bs-target="#modalHotel{{ $r->id }}">
                                    <i class="fas fa-file-invoice-dollar"></i> Rincian
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-receipt"></i></div>
                                    <div class="empty-title">Belum Ada Transaksi</div>
                                    <div class="empty-desc" style="color:var(--text-muted);">Transaksi yang telah lunas akan muncul di sini.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ===== MODALS ===== --}}
@foreach($reservasi as $r)
@php $user = $users[$r->user_id] ?? null; @endphp
<div class="modal fade" id="modalHotel{{ $r->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $r->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content">
            <div class="modal-header-receipt">
                <div>
                    <div class="modal-title-text" id="modalLabel{{ $r->id }}">Kwitansi Digital</div>
                    <div class="invoice-num">Invoice #PH{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Tutup"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="receipt-body">
                <div class="receipt-paper">
                    <div class="receipt-paper-header">
                        <div class="receipt-logo-text">Purnama Hotel</div>
                        <div class="receipt-sub">Hotel &amp; Resto · Sistem Manajemen</div>
                    </div>
                    <div class="dashed-notch"><div class="dashed-notch-line"></div></div>
                    <div class="receipt-section">
                        <span class="receipt-section-label">Informasi Tamu</span>
                        <div class="receipt-row"><span class="rr-key">Nama</span><span class="rr-val">{{ $user['full_name'] ?? '-' }}</span></div>
                        <div class="receipt-row"><span class="rr-key">Email</span><span class="rr-val" style="font-size:12px;">{{ $user['email'] ?? '-' }}</span></div>
                    </div>
                    <div class="receipt-section">
                        <span class="receipt-section-label">Rincian Menginap</span>
                        <div class="receipt-row"><span class="rr-key">Tipe Kamar</span><span class="rr-val">{{ $r->tipeKamar->nama_tipe ?? '-' }}</span></div>
                        <div class="receipt-row"><span class="rr-key">Nomor Kamar</span><span class="rr-val">{{ $r->kamar->nomor_kamar ?? '-' }}</span></div>
                        <div class="receipt-row"><span class="rr-key">Check-in</span><span class="rr-val">{{ \Carbon\Carbon::parse($r->tgl_checkin)->translatedFormat('d M Y') }}</span></div>
                        <div class="receipt-row"><span class="rr-key">Check-out</span><span class="rr-val">{{ \Carbon\Carbon::parse($r->tgl_checkout)->translatedFormat('d M Y') }}</span></div>
                        <div class="receipt-row"><span class="rr-key">Durasi</span><span class="rr-val">{{ $r->total_malam }} Malam</span></div>
                        <div class="receipt-row"><span class="rr-key">Metode Bayar</span><span class="rr-val" style="text-transform:uppercase;">{{ $r->metode_pembayaran }}</span></div>
                    </div>
                    <div class="receipt-total">
                        <span class="receipt-total-label">Total Dana Diterima</span>
                        <div class="receipt-total-amount">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</div>
                        <span class="receipt-verified-tag"><i class="fas fa-shield-halved"></i> Terverifikasi Sistem</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer-receipt">
                <button type="button" class="btn-close-receipt" data-bs-dismiss="modal">Tutup Kwitansi</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Count-up animation for total revenue
    function countUp(el, target, duration) {
        if (!el || target === 0) {
            if (el) el.textContent = 'Rp 0';
            return;
        }
        const fmt = new Intl.NumberFormat('id-ID');
        const startTime = performance.now();
        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(eased * target);
            el.textContent = 'Rp ' + fmt.format(current);
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    const revenueEl = document.getElementById('totalRevenueHotel');
    if (revenueEl) {
        const raw = parseInt(revenueEl.getAttribute('data-value')) || 0;
        countUp(revenueEl, raw, 1600);
    }

    // Reset modal scroll
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function () { this.scrollTop = 0; });
    });
});
</script>
@endpush