@extends('dashboard.layouts.app')
@section('title', 'Tambah Reservasi Baru')

@section('content')
{{-- ================================================================
     TAMBAH RESERVASI HOTEL — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
.create-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}

.create-page-wrapper::before,
.create-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.create-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.create-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.create-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.create-header {
    margin-bottom: 32px;
}
.create-header .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    text-decoration: none;
    font-size: .8rem;
    font-weight: 600;
    transition: var(--transition);
    margin-bottom: 12px;
    padding: 5px 10px;
    border-radius: 10px;
    background: rgba(255,255,255,.7);
}
.create-header .back-link:hover {
    color: var(--navy);
    transform: translateX(-4px);
    background: #fff;
}
.create-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.create-header h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.create-header p {
    color: var(--text-muted);
    margin: 6px 0 0;
    font-size: .875rem;
    font-weight: 500;
}

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
    height: 100%;
}
.card-premium:hover { box-shadow: var(--shadow-hover); }
.card-premium-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 18px 28px;
    border: none;
}
.card-premium-header h6 {
    margin: 0;
    font-weight: 800;
    color: white;
    font-size: .85rem;
    letter-spacing: 1px;
}
.card-premium-header h6 i { margin-right: 8px; }
.card-premium-body { padding: 28px; }

/* ============================================================
   FORM ELEMENTS
   ============================================================ */
.form-label-premium {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
    display: block;
}
.form-control-premium,
.form-select-premium {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-size: .85rem;
    font-weight: 500;
    color: var(--text-primary);
    background: var(--surface);
    transition: var(--transition);
}
.form-control-premium:focus,
.form-select-premium:focus {
    outline: none;
    border-color: var(--navy);
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
.form-control-premium.is-invalid,
.form-select-premium.is-invalid {
    border-color: var(--rose);
    background-color: rgba(225,29,72,.02);
}
.invalid-feedback-premium {
    font-size: .7rem;
    color: var(--rose);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.form-text-premium {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ============================================================
   ALERT ERROR
   ============================================================ */
.alert-error-premium {
    background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
    border-left: 4px solid var(--rose);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    animation: slideInDown .5s ease;
}
.alert-error-premium ul {
    margin: 0;
    padding-left: 20px;
    color: #991b1b;
    font-weight: 500;
    font-size: .8rem;
}
@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   INFO BOX & PREVIEW
   ============================================================ */
.info-box-premium {
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    border-radius: 16px;
    padding: 16px 20px;
    margin: 24px 0 0;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid rgba(0,25,125,.1);
}
.summary-preview {
    background: var(--surface-2);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid var(--border);
}
.summary-preview-title {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.preview-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
}
.preview-row:last-child { border-bottom: none; }
.preview-label {
    font-size: .8rem;
    color: var(--text-muted);
    font-weight: 500;
}
.preview-value {
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-primary);
}
.preview-value.total {
    font-size: 1.1rem;
    color: var(--emerald);
}

/* ============================================================
   BUTTONS
   ============================================================ */
.btn-premium-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 14px 32px;
    font-weight: 800;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    cursor: pointer;
}
.btn-premium-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,25,125,.3);
    color: white;
}
.btn-premium-secondary {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 14px 32px;
    font-weight: 700;
    font-size: .85rem;
    color: var(--text-primary);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    cursor: pointer;
    text-decoration: none;
}
.btn-premium-secondary:hover {
    background: var(--border);
    transform: translateY(-2px);
}
.action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }

@media (max-width: 768px) {
    .create-page-wrapper { padding: 20px 16px; }
    .create-header h2 { font-size: 1.5rem; }
    .card-premium-body { padding: 20px; }
    .btn-premium-primary, .btn-premium-secondary { width: 100%; justify-content: center; }
    .action-buttons { flex-direction: column; gap: 10px; }
}
</style>

<div class="create-page-wrapper">

    {{-- HEADER --}}
    <div class="create-header">
        <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Reservasi
        </a>
        <h2>Tambah <span>Reservasi</span></h2>
        <p><i class="fas fa-plus-circle me-1"></i> Buat pemesanan kamar baru untuk pelanggan</p>
    </div>

    {{-- ERROR ALERTS --}}
    @if($errors->any())
    <div class="alert-error-premium">
        <ul>
            @foreach($errors->all() as $error)
                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">
        {{-- Kiri: Form --}}
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-plus-circle"></i> Form Reservasi Baru</h6>
                </div>
                <div class="card-premium-body">
                    <form action="{{ route('dashboard.hotel.reservasi.store') }}" method="POST" id="createReservasiForm">
                        @csrf

                        <div class="row g-3">
                            {{-- Pilih Pelanggan --}}
                            {{-- TIPE PEMESANAN: Pelanggan Terdaftar vs Walk-in --}}
                            <div class="col-12">
                                <label class="form-label-premium"><i class="fas fa-user-tag"></i> TIPE TAMU</label>
                                <div style="display:flex; gap:12px;">
                                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                        <input type="radio" name="tipe_tamu" id="tipe_tamu_terdaftar" value="terdaftar" 
                                            {{ old('tipe_tamu', 'terdaftar') == 'terdaftar' ? 'checked' : '' }}>
                                        <span>Pelanggan Terdaftar (Punya Akun)</span>
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                        <input type="radio" name="tipe_tamu" id="tipe_tamu_walkin" value="walkin"
                                            {{ old('tipe_tamu') == 'walkin' ? 'checked' : '' }}>
                                        <span>Tamu Walk-in (Tanpa Akun)</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Dropdown pelanggan terdaftar --}}
                            <div class="col-12" id="sectionPelangganTerdaftar">
                                <label class="form-label-premium"><i class="fas fa-users"></i> PILIH PELANGGAN TERDAFTAR</label>
                                <select name="user_id" id="user_id" class="form-select-premium">
                                    <option value="">-- Pilih Pelanggan Terdaftar --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c['id'] }}" {{ old('user_id') == $c['id'] ? 'selected' : '' }}>
                                            {{ $c['full_name'] }} ({{ $c['email'] }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id') <div class="invalid-feedback-premium"><i class="fas fa-times-circle"></i> {{ $message }}</div> @enderror
                            </div>

                            {{-- Input walk-in (hidden by default) --}}
                            <div class="col-12" id="sectionWalkin" style="display:none;">
                                <div class="p-3 rounded-3" style="background: #fff8e1; border-left: 3px solid var(--amber);">
                                    <small><i class="fas fa-info-circle text-warning"></i> <strong>Tamu Walk-in:</strong> Data tamu diambil dari field Nama & NIK di bawah. Tidak perlu pilih akun.</small>
                                </div>
                            </div>
                                                        {{-- DATA TAMU --}}
                            <div class="col-md-8">
                                <label class="form-label-premium"><i class="fas fa-id-card"></i> NAMA TAMU SESUAI KTP</label>
                                <input type="text" name="nama_tamu" id="nama_tamu"
                                    class="form-control-premium @error('nama_tamu') is-invalid @enderror"
                                    placeholder="Contoh: Budi Santoso"
                                    value="{{ old('nama_tamu') }}" required>
                                @error('nama_tamu') <div class="invalid-feedback-premium"><i class="fas fa-times-circle"></i> {{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-premium"><i class="fas fa-users"></i> JUMLAH TAMU</label>
                                <input type="number" name="jumlah_tamu" id="jumlah_tamu"
                                    class="form-control-premium @error('jumlah_tamu') is-invalid @enderror"
                                    min="1" value="{{ old('jumlah_tamu', 1) }}" required>
                                @error('jumlah_tamu') <div class="invalid-feedback-premium"><i class="fas fa-times-circle"></i> {{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label-premium"><i class="fas fa-id-badge"></i> NIK / KTP</label>
                                <input type="text" name="nik_identitas" id="nik_identitas"
                                    class="form-control-premium @error('nik_identitas') is-invalid @enderror"
                                    placeholder="16 digit NIK" maxlength="16" inputmode="numeric"
                                    value="{{ old('nik_identitas') }}" required>
                                @error('nik_identitas') <div class="invalid-feedback-premium"><i class="fas fa-times-circle"></i> {{ $message }}</div> @enderror
                            </div>

                            {{-- PROMO & VOUCHER --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-tags"></i> PROMO OTOMATIS (OPSIONAL)</label>
                                <select name="promo_id" id="promo_id" class="form-select-premium">
                                    <option value="">-- Tanpa Promo --</option>
                                    @foreach($promoOtomatis as $p)
                                        <option value="{{ $p->id }}" {{ old('promo_id') == $p->id ? 'selected' : '' }}
                                                data-tipe="{{ $p->tipe_diskon }}" data-nominal="{{ $p->nominal_potongan }}">
                                            {{ $p->nama_promo }} ({{ $p->tipe_diskon == 'persen' ? $p->nominal_potongan.'%' : 'Rp '.number_format($p->nominal_potongan,0,',','.') }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($promoOtomatis->isEmpty())
                                    <div class="form-text-premium"><i class="fas fa-info-circle text-info"></i> Tidak ada promo otomatis aktif saat ini.</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-ticket-alt"></i> KODE VOUCHER (OPSIONAL)</label>
                                <div style="display:flex; gap:8px;">
                                    <input type="text" name="kode_voucher" id="kode_voucher" class="form-control-premium"
                                        placeholder="Masukkan kode voucher" value="{{ old('kode_voucher') }}" style="text-transform:uppercase;">
                                    <button type="button" id="btnCekVoucher" class="btn-premium-secondary" style="padding:12px 18px; white-space:nowrap;">
                                        <i class="fas fa-check"></i> Cek
                                    </button>
                                </div>
                                <div class="form-text-premium" id="voucherFeedback"></div>
                                <div class="form-text-premium"><i class="fas fa-info-circle text-info"></i> Kalau voucher valid, ini menggantikan promo otomatis di atas.</div>
                            </div>
                            {{-- Tipe Kamar --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-hotel"></i> TIPE KAMAR</label>
                                <select name="tipe_kamar_id" id="tipe_kamar_id" class="form-select-premium" required>
                                    <option value="">-- Pilih Kategori Kamar --</option>
                                    @foreach($tipeKamar as $t)
                                        <option value="{{ $t->id }}" {{ old('tipe_kamar_id') == $t->id ? 'selected' : '' }}
                                                data-harga="{{ $t->harga }}" data-nama="{{ $t->nama_tipe }}">
                                            {{ $t->nama_tipe }} - Rp {{ number_format($t->harga, 0, ',', '.') }} / malam
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Unit Kamar --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-door-open"></i> NOMOR KAMAR FISIK</label>
                                <select name="kamar_id" id="kamar_id" class="form-select-premium" required>
                                    <option value="">-- Pilih Unit Kamar Tersedia --</option>
                                    @foreach($kamar as $k)
                                        <option value="{{ $k->id }}" {{ old('kamar_id') == $k->id ? 'selected' : '' }}>
                                            No. {{ $k->nomor_kamar }} - {{ $k->tipeKamar->nama_tipe ?? 'Umum' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text-premium"><i class="fas fa-check-circle text-success"></i> Hanya menampilkan kamar dengan status Tersedia</div>
                            </div>

                            {{-- Status Awal --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-tag"></i> STATUS AWAL</label>
                                <select name="status_reservasi_id" id="status_reservasi_id" class="form-select-premium" required>
                                    @foreach($statusList as $s)
                                        <option value="{{ $s->id }}" {{ old('status_reservasi_id', 1) == $s->id ? 'selected' : '' }}>
                                            {{ strtoupper($s->nama_status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-credit-card"></i> METODE PEMBAYARAN</label>
                                <select name="metode_pembayaran" class="form-select-premium" required>
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="Transfer Bank" {{ old('metode_pembayaran') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="Tunai" {{ old('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai (Check-in)</option>
                                    <option value="Kartu Kredit" {{ old('metode_pembayaran') == 'Kartu Kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                                    <option value="E-Wallet" {{ old('metode_pembayaran') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                                </select>
                            </div>

                            {{-- Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-calendar-alt"></i> TANGGAL CHECK-IN</label>
                                <input type="date" name="tgl_checkin" id="tgl_checkin" class="form-control-premium" value="{{ old('tgl_checkin', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-calendar-week"></i> TANGGAL CHECK-OUT</label>
                                <input type="date" name="tgl_checkout" id="tgl_checkout" class="form-control-premium" value="{{ old('tgl_checkout') }}" required>
                            </div>
                        </div>

                        <div class="info-box-premium">
                            <i class="fas fa-calculator"></i>
                            <span>Sistem akan menghitung otomatis total malam dan total harga berdasarkan tipe kamar yang dipilih.</span>
                        </div>

                        <div class="mt-4 pt-3 border-top action-buttons">
                            <button type="submit" class="btn-premium-primary"><i class="fas fa-save"></i> Simpan Reservasi</button>
                            <button type="button" class="btn-premium-secondary" onclick="resetForm()"><i class="fas fa-undo-alt"></i> Reset Form</button>
                            <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="btn-premium-secondary"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kanan: Preview Ringkasan --}}
        <div class="col-lg-4">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-chart-line"></i> Preview Reservasi</h6>
                </div>
                <div class="card-premium-body">
                    <div class="summary-preview">
                        <div class="summary-preview-title"><i class="fas fa-receipt"></i> RINGKASAN PEMESANAN</div>
                        <div class="preview-row"><span class="preview-label">Tipe Kamar</span><span class="preview-value" id="previewTipeKamar">Belum dipilih</span></div>
                        <div class="preview-row"><span class="preview-label">Harga per Malam</span><span class="preview-value" id="previewHarga">Rp 0</span></div>
                        <div class="preview-row"><span class="preview-label">Jumlah Malam</span><span class="preview-value" id="previewMalam">0 malam</span></div>
                        <div class="preview-row"><span class="preview-label">Tanggal</span><span class="preview-value" id="previewTanggal">-</span></div>
                        <div class="preview-row"><span class="preview-label">Subtotal</span><span class="preview-value" id="previewTotal">Rp 0</span></div>
                        <div class="preview-row" id="previewDiskonRow" style="display:none;">
                            <span class="preview-label" style="color: var(--rose);">Diskon</span>
                            <span class="preview-value" id="previewDiskon" style="color: var(--rose);">- Rp 0</span>
                        </div>
                        <div class="preview-row">
                            <span class="preview-label fw-bold">TOTAL AKHIR</span>
                            <span class="preview-value total fw-bold" id="previewTotalAkhir">Rp 0</span>
                        </div>                    
                    </div>

                    <div class="mt-3 p-3 rounded-3" style="background: #fff3e0; border-left: 3px solid var(--amber);">
                        <small class="text-muted d-block mb-1"><i class="fas fa-lightbulb text-warning"></i> <strong>Tips:</strong></small>
                        <small class="text-dark" style="font-size: .7rem;">
                            • Status PENDING adalah default untuk reservasi baru.<br>
                            • Pastikan nomor kamar yang dipilih benar-benar tersedia.<br>
                            • Notifikasi WhatsApp akan dikirim setelah reservasi tersimpan.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
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
        }, 100 + (idx * 80));
    });

    const header = document.querySelector('.create-header');
    if (header) {
        header.style.opacity = '0';
        header.style.transform = 'translateY(-10px)';
        header.style.transition = 'all 0.4s ease';
        setTimeout(() => {
            header.style.opacity = '1';
            header.style.transform = 'translateY(0)';
        }, 50);
    }

    const tipeSelect       = document.getElementById('tipe_kamar_id');
    const tglCheckin       = document.getElementById('tgl_checkin');
    const tglCheckout      = document.getElementById('tgl_checkout');
    const promoSelect      = document.getElementById('promo_id');
    const voucherInput     = document.getElementById('kode_voucher');
    const btnCekVoucher    = document.getElementById('btnCekVoucher');
    const voucherFeedback  = document.getElementById('voucherFeedback');

    const previewTipe       = document.getElementById('previewTipeKamar');
    const previewHarga      = document.getElementById('previewHarga');
    const previewMalam      = document.getElementById('previewMalam');
    const previewTanggal    = document.getElementById('previewTanggal');
    const previewTotal      = document.getElementById('previewTotal');
    const previewDiskonRow  = document.getElementById('previewDiskonRow');
    const previewDiskon     = document.getElementById('previewDiskon');
    const previewTotalAkhir = document.getElementById('previewTotalAkhir');

    let discountInfo = { value: 0, isPercent: false, source: null };
    let currentSubtotal = 0;

    // --- TOGGLE TIPE TAMU ---
    const radioTerdaftar   = document.getElementById('tipe_tamu_terdaftar');
    const radioWalkin      = document.getElementById('tipe_tamu_walkin');
    const sectionTerdaftar = document.getElementById('sectionPelangganTerdaftar');
    const sectionWalkin    = document.getElementById('sectionWalkin');
    const userIdSelect     = document.getElementById('user_id');

    function toggleTipeTamu() {
        if (radioWalkin.checked) {
            sectionTerdaftar.style.display = 'none';
            sectionWalkin.style.display = 'block';
            userIdSelect.value = '';
            userIdSelect.removeAttribute('required');
        } else {
            sectionTerdaftar.style.display = 'block';
            sectionWalkin.style.display = 'none';
            userIdSelect.setAttribute('required', 'required');
        }
    }

    radioTerdaftar.addEventListener('change', toggleTipeTamu);
    radioWalkin.addEventListener('change', toggleTipeTamu);
    toggleTipeTamu();

    function formatRp(num) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num));
    }

    function renderTotals() {
        const diskon = discountInfo.value > 0
            ? (discountInfo.isPercent
                ? currentSubtotal * (discountInfo.value / 100)
                : Math.min(discountInfo.value, currentSubtotal))
            : 0;
        const totalAkhir = Math.max(0, currentSubtotal - diskon);

        previewTotal.textContent = formatRp(currentSubtotal);

        if (diskon > 0) {
            previewDiskonRow.style.display = 'flex';
            previewDiskon.textContent = '- ' + formatRp(diskon);
        } else {
            previewDiskonRow.style.display = 'none';
        }

        previewTotalAkhir.textContent = formatRp(totalAkhir);
    }

    function updatePreview() {
        const selectedOption = tipeSelect.options[tipeSelect.selectedIndex];
        const harga = selectedOption ? parseInt(selectedOption.dataset.harga) || 0 : 0;
        const tipeNama = selectedOption ? selectedOption.dataset.nama || selectedOption.text.split(' -')[0] : 'Belum dipilih';

        previewTipe.textContent = tipeNama;
        previewHarga.textContent = formatRp(harga);

        let malam = 0;
        let subtotal = 0;
        let tanggalText = '-';

        if (tglCheckin.value && tglCheckout.value) {
            const checkin = new Date(tglCheckin.value);
            const checkout = new Date(tglCheckout.value);

            if (checkout > checkin) {
                const diffTime = Math.abs(checkout - checkin);
                malam = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                subtotal = harga * malam;
                const formatDate = (date) => date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                tanggalText = `${formatDate(checkin)} → ${formatDate(checkout)}`;
            } else if (checkout.getTime() === checkin.getTime()) {
                malam = 1;
                subtotal = harga;
                tanggalText = 'Check-in & Check-out sama hari (1 malam)';
            } else {
                tanggalText = 'Tanggal check-out harus setelah check-in';
            }
        }

        previewMalam.textContent = malam + ' malam';
        previewTanggal.textContent = tanggalText;
        currentSubtotal = subtotal;
        renderTotals();
    }

    promoSelect.addEventListener('change', function() {
        const opt = promoSelect.options[promoSelect.selectedIndex];
        discountInfo = promoSelect.value
            ? { value: parseFloat(opt.dataset.nominal) || 0, isPercent: opt.dataset.tipe === 'persen', source: 'promo' }
            : { value: 0, isPercent: false, source: null };
        renderTotals();
    });

    btnCekVoucher.addEventListener('click', function() {
        const kode = voucherInput.value.trim();
        if (!kode) {
            voucherFeedback.innerHTML = '<i class="fas fa-exclamation-circle text-warning"></i> Isi kode voucher dulu.';
            return;
        }

        voucherFeedback.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengecek voucher...';

        fetch("{{ route('dashboard.hotel.reservasi.checkVoucher') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ kode: kode })
        })
        .then(res => res.json())
        .then(data => {
            if (data.valid) {
                voucherFeedback.innerHTML = '<i class="fas fa-check-circle text-success"></i> Voucher valid: ' + data.nama_promo;
                discountInfo = { value: parseFloat(data.nominal_potongan) || 0, isPercent: data.tipe_diskon === 'persen', source: 'voucher' };
                promoSelect.value = '';
                promoSelect.disabled = true;
            } else {
                voucherFeedback.innerHTML = '<i class="fas fa-times-circle text-danger"></i> ' + data.message;
                discountInfo = { value: 0, isPercent: false, source: null };
                promoSelect.disabled = false;
            }
            renderTotals();
        })
        .catch(() => {
            voucherFeedback.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Gagal mengecek voucher, coba lagi.';
        });
    });

    voucherInput.addEventListener('input', function() {
        if (voucherInput.value.trim() === '' && discountInfo.source === 'voucher') {
            discountInfo = { value: 0, isPercent: false, source: null };
            voucherFeedback.innerHTML = '';
            promoSelect.disabled = false;
            renderTotals();
        }
    });

    tipeSelect.addEventListener('change', updatePreview);
    tglCheckin.addEventListener('change', updatePreview);
    tglCheckout.addEventListener('change', updatePreview);
    updatePreview();
});

function resetForm() {
    document.getElementById('createReservasiForm').reset();
    setTimeout(() => {
        const event = new Event('change');
        document.getElementById('tipe_kamar_id').dispatchEvent(event);
        document.getElementById('tgl_checkin').dispatchEvent(event);
        document.getElementById('tgl_checkout').dispatchEvent(event);
        document.getElementById('promo_id').dispatchEvent(event);
        document.getElementById('promo_id').disabled = false;
        document.getElementById('voucherFeedback').innerHTML = '';
        document.getElementById('tipe_tamu_terdaftar').checked = true;
        document.getElementById('tipe_tamu_terdaftar').dispatchEvent(new Event('change'));
    }, 50);
}
</script>
@endsection