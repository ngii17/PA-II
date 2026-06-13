@extends('dashboard.layouts.app')
@section('title', 'Edit Pesanan #' . $pesanan->id)

@section('content')
<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --gold:         #D4AF37;
    --amber:        #f59e0b;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #5b6e8c;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s ease;
}

*, *::before, *::after { box-sizing: border-box; }

.edit-page-wrapper {
    padding: 24px 28px;
    background: linear-gradient(135deg, #f5f7ff 0%, #fff9f0 100%);
    min-height: 100vh;
}

/* Header */
.edit-header {
    margin-bottom: 28px;
}

.breadcrumb-custom {
    margin-bottom: 8px;
}

.breadcrumb-custom a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 0.8rem;
}

.breadcrumb-custom a:hover {
    color: var(--navy);
}

.edit-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0;
}

.edit-title span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Cards */
.card-premium {
    background: var(--surface);
    border-radius: 20px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
}

.card-header-premium {
    background: var(--navy);
    padding: 14px 20px;
}

.card-header-premium h6 {
    color: white;
    font-weight: 700;
    margin: 0;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body-premium {
    padding: 20px;
}

/* Form */
.form-group {
    margin-bottom: 20px;
}

.form-label-custom {
    display: block;
    margin-bottom: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-label-custom i {
    margin-right: 5px;
    width: 14px;
}

.form-input-custom,
.form-select-custom {
    width: 100%;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 0.85rem;
    font-family: var(--font);
    transition: var(--transition);
}

.form-input-custom:focus,
.form-select-custom:focus {
    border-color: var(--navy);
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
    background: white;
}

.form-input-custom:disabled {
    background: var(--surface-2);
    color: var(--text-muted);
}

.form-hint {
    margin-top: 6px;
    font-size: 0.65rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 5px;
}

.form-hint.warning { color: var(--amber); }
.form-hint.success { color: var(--emerald); }
.form-hint.info { color: var(--navy); }

/* Button */
.btn-premium-submit {
    background: linear-gradient(135deg, var(--amber) 0%, #ea8c0b 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px 20px;
    font-weight: 700;
    font-size: 0.85rem;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: var(--transition);
    cursor: pointer;
}

.btn-premium-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245,158,11,.3);
}

/* Ringkasan Pesanan - Sederhana */
.order-summary-card {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    border-radius: 20px;
    overflow: hidden;
}

.order-summary-header {
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255,255,255,.1);
}

.order-summary-header h6 {
    color: var(--gold);
    font-weight: 700;
    margin: 0;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.order-items-list {
    padding: 12px 20px;
    max-height: 280px;
    overflow-y: auto;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,.08);
    font-size: 0.8rem;
}

.order-item-name {
    color: rgba(255,255,255,.8);
}

.order-item-name strong {
    color: white;
}

.order-item-price {
    color: var(--gold);
    font-weight: 600;
}

.order-total {
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    background: rgba(0,0,0,.2);
}

.order-total-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255,255,255,.6);
}

.order-total-value {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--gold);
}

/* Alert Sederhana */
.alert-simple {
    background: #fff3e0;
    border-left: 3px solid var(--amber);
    border-radius: 12px;
    padding: 12px 16px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.7rem;
    color: #c2410c;
}

.alert-simple i {
    font-size: 0.9rem;
}

.notif-info {
    background: #eef2ff;
    border-radius: 12px;
    padding: 12px 16px;
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.7rem;
}

.notif-info i {
    color: var(--navy);
}

/* Responsive */
@media (max-width: 768px) {
    .edit-page-wrapper { padding: 16px; }
    .edit-title { font-size: 1.3rem; }
    .card-body-premium { padding: 16px; }
}
</style>

<!-- ================================================
     MARKUP (menggunakan data dari kode asli)
     ================================================ -->
<div class="edit-page-wrapper">

    <!-- Header -->
    <div class="edit-header">
        <div class="breadcrumb-custom">
            <a href="{{ route('dashboard.restoran.pesanan.index') }}">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
        <h1 class="edit-title">Ubah Status <span>Pesanan #{{ $pesanan->id }}</span></h1>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Form (sesuai kode asli) -->
        <div class="col-lg-6">
            <div class="card-premium">
                <div class="card-header-premium">
                    <h6><i class="fas fa-sliders-h"></i> Atur Status Pesanan</h6>
                </div>
                <div class="card-body-premium">
                    <form action="{{ route('dashboard.restoran.pesanan.update', $pesanan->id) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <!-- Info Pelanggan (Read Only) - sesuai kode asli -->
                        <div class="form-group">
                            <label class="form-label-custom"><i class="fas fa-user"></i> PELANGGAN (USER ID)</label>
                            <input type="text" class="form-input-custom" value="{{ $pesanan->user_id }}" readonly disabled>
                            <div class="form-hint info">
                                <i class="fas fa-bell"></i> Notifikasi otomatis akan dikirim ke perangkat user ini.
                            </div>
                        </div>

                        <!-- Edit Nomor Meja / Lokasi (sinkron dengan DB nomor_lokasi) -->
                        <div class="form-group">
                            <label class="form-label-custom"><i class="fas fa-chair"></i> NOMOR MEJA / LOKASI</label>
                            <input type="text" name="nomor_meja" class="form-input-custom" value="{{ $pesanan->nomor_lokasi }}" required>
                        </div>

                        <!-- Edit Status Pesanan (Antrean) -->
                        <div class="form-group">
                            <label class="form-label-custom"><i class="fas fa-utensils"></i> STATUS PESANAN (PROSES DAPUR)</label>
                            <select name="status_pesanan_id" class="form-select-custom" required>
                                @foreach($statusList as $s)
                                    <option value="{{ $s->id }}" {{ $pesanan->status_pesanan_id == $s->id ? 'selected' : '' }}>
                                        {{ strtoupper($s->nama_status) }}
                                        @if($s->id == 3) (Kirim Notif Makanan Siap) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint warning">
                                <i class="fas fa-info-circle"></i> Pilih <b>DISAJIKAN</b> jika makanan sudah siap diantar ke meja/kamar.
                            </div>
                        </div>

                        <!-- Edit Status Pembayaran -->
                        <div class="form-group">
                            <label class="form-label-custom"><i class="fas fa-credit-card"></i> STATUS PEMBAYARAN (KASIR)</label>
                            <select name="status_pembayaran_id" class="form-select-custom" required>
                                @foreach($paymentStatusList as $p)
                                    <option value="{{ $p->id }}" {{ $pesanan->status_pembayaran_id == $p->id ? 'selected' : '' }}>
                                        {{ strtoupper($p->nama_status) }}
                                        @if($p->id == 2) (Kirim Notif Pembayaran Sukses) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint success">
                                <i class="fas fa-check-circle"></i> Pastikan uang tunai sudah diterima sebelum mengubah menjadi <b>LUNAS</b>.
                            </div>
                        </div>

                        <button type="submit" class="btn-premium-submit" id="submitBtn">
                            <i class="fas fa-save"></i> Simpan & Update ke HP User
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Pesanan -->
        <div class="col-lg-6">
            <div class="order-summary-card">
                <div class="order-summary-header">
                    <h6><i class="fas fa-receipt"></i> Rincian Pesanan</h6>
                </div>
                <div class="order-items-list">
                    @php $totalItems = 0; @endphp
                    @foreach($pesanan->details as $detail)
                        @php $totalItems += $detail->jumlah; @endphp
                        <div class="order-item">
                            <span class="order-item-name">
                                {{ $detail->menu->nama_menu ?? 'Menu Tidak Diketahui' }}
                                <strong>(x{{ $detail->jumlah }})</strong>
                            </span>
                            <span class="order-item-price">
                                Rp {{ number_format($detail->jumlah * $detail->harga_at_porsi, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="order-total">
                    <span class="order-total-label">TOTAL TAGIHAN</span>
                    <span class="order-total-value">
                        Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Alert sederhana sesuai kode asli -->
            <div class="alert-simple">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Pastikan uang tunai sudah diterima sebelum mengubah status pembayaran menjadi <strong>LUNAS</strong>.</span>
            </div>

            <div class="notif-info">
                <i class="fas fa-bell"></i>
                <span>Notifikasi otomatis akan dikirim ke pelanggan saat status berubah.</span>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert untuk konfirmasi simpan (sesuai template premium) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Update Status?',
            text: 'Pastikan data sudah benar sebelum menyimpan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00197D',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Menyimpan...';
                form.submit();
            }
        });
    });
});
</script>

@endsection