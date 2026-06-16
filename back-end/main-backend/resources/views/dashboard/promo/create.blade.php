@extends('dashboard.layouts.app')

@section('title', 'Tambah Promo Baru')

@section('content')
{{-- ================================================================
     CREATE PROMO — PREMIUM UNIFIED (DENGAN VALIDASI TANGGAL YANG BENAR)
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ============================================================
   ROOT VARIABLES (Konsisten dengan halaman lain)
   ============================================================ */
:root {
    --navy:       #00197D;
    --navy-dark:  #000C3D;
    --navy-mid:   #0025B3;
    --gold:       #D4AF37;
    --gold-light: #F5E6BE;
    --rose:       #e11d48;
    --amber:      #f59e0b;
    --green:      #10b981;
    --surface:    #ffffff;
    --surface-2:  #f8fafc;
    --border:     #e2e8f0;
    --text-primary: #0f172a;
    --text-mid:     #475569;
    --text-muted:   #94a3b8;
    --radius-xl:  20px;
    --radius-2xl: 28px;
    --shadow-card: 0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-form: 0 20px 48px rgba(0,25,125,.06);
    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button {
    font-family: var(--font) !important;
}
.fw-800 { font-weight: 800 !important; letter-spacing: -.02em; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.create-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 55%, #fffdf0 100%);
    padding: 36px 24px 64px;
    position: relative;
    overflow-x: hidden;
}
.create-page-wrapper::before,
.create-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.create-page-wrapper::before {
    width: 500px; height: 500px;
    top: -160px; right: -120px;
    background: radial-gradient(circle, rgba(0,25,125,.045) 0%, transparent 70%);
}
.create-page-wrapper::after {
    width: 360px; height: 360px;
    bottom: -80px; left: -80px;
    background: radial-gradient(circle, rgba(212,175,55,.07) 0%, transparent 70%);
}
.create-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.create-header { margin-bottom: 32px; }

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    font-weight: 600;
    font-size: .82rem;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 10px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    transition: all .25s ease;
}
.back-link:hover {
    color: var(--navy);
    border-color: var(--navy);
    background: #eef0ff;
    transform: translateX(-3px);
}

.create-title-block { margin-top: 20px; }
.create-title-block h2 {
    font-size: 1.9rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 6px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.create-title-block h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.create-title-block p {
    color: var(--text-muted);
    font-size: .85rem;
    font-weight: 500;
    margin: 0;
}

/* ============================================================
   STEPS INDICATOR
   ============================================================ */
.steps-bar {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 24px;
}
.step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}
.step-item:last-child { flex: none; }
.step-dot {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem;
    font-weight: 800;
    flex-shrink: 0;
    transition: all .4s cubic-bezier(.34,1.56,.64,1);
    border: 2px solid var(--border);
    background: var(--surface);
    color: var(--text-muted);
}
.step-dot.active { background: var(--navy); color: #fff; border-color: var(--navy); box-shadow: 0 4px 12px rgba(0,25,125,.25); }
.step-dot.done   { background: var(--green); color: #fff; border-color: var(--green); }
.step-label {
    font-size: .7rem;
    font-weight: 600;
    color: var(--text-muted);
    white-space: nowrap;
}
.step-label.active { color: var(--navy); font-weight: 700; }
.step-connector {
    flex: 1;
    height: 2px;
    background: var(--border);
    margin: 0 8px;
    border-radius: 999px;
    transition: background .4s;
}
.step-connector.done { background: var(--green); }

/* ============================================================
   LIVE VOUCHER PREVIEW
   ============================================================ */
.voucher-preview {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    border-radius: 24px;
    padding: 28px 28px 24px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 36px rgba(0,25,125,.24);
    display: flex;
    gap: 0;
    align-items: stretch;
    min-height: 140px;
}
.voucher-preview::before {
    content: '';
    position: absolute;
    width: 240px; height: 240px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    top: -80px; right: -60px;
}
.voucher-left {
    flex: 1;
    padding-right: 24px;
    position: relative;
    z-index: 1;
}
.voucher-right {
    width: 140px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-left: 2px dashed rgba(255,255,255,.18);
    padding-left: 24px;
    position: relative;
    z-index: 1;
}
.voucher-notch {
    position: absolute;
    width: 20px; height: 20px;
    background: #f0f4ff;
    border-radius: 50%;
    left: -10px;
}
.voucher-notch.top    { top: -10px; }
.voucher-notch.bottom { bottom: -10px; }
.v-label {
    font-size: .58rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: rgba(255,255,255,.45);
    font-weight: 700;
    margin-bottom: 4px;
    display: block;
}
.v-value {
    color: #fff;
    font-weight: 800;
    font-size: 1rem;
    letter-spacing: -.01em;
    line-height: 1.2;
}
.v-discount {
    font-size: 2.6rem !important;
    color: var(--gold) !important;
    letter-spacing: -.04em !important;
    line-height: 1 !important;
}
.v-code-chip {
    display: inline-block;
    margin-top: 10px;
    background: rgba(255,255,255,.1);
    border: 1.5px dashed rgba(255,255,255,.25);
    border-radius: 8px;
    padding: 6px 14px;
    font-family: monospace;
    font-weight: 800;
    font-size: .82rem;
    color: #fff;
    letter-spacing: 2px;
}
.v-badge {
    display: inline-block;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: .6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 8px;
}
.v-badge-hotel { background: rgba(147,197,253,.18); color: #93c5fd; }
.v-badge-resto { background: rgba(110,231,183,.18); color: #6ee7b7; }
.v-badge-semua { background: rgba(212,175,55,.18);  color: var(--gold); }
.v-badge-empty { background: rgba(255,255,255,.08); color: rgba(255,255,255,.3); }

/* ============================================================
   PROGRESS BAR
   ============================================================ */
.form-progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.form-progress-label span {
    font-size: .68rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.form-progress-label .pct {
    font-weight: 800;
    color: var(--navy);
}
.form-progress-bar {
    height: 3px;
    background: var(--border);
    border-radius: 999px;
    overflow: hidden;
    margin-bottom: 24px;
}
.form-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--navy), var(--gold));
    border-radius: 999px;
    transition: width .4s cubic-bezier(.34,1.56,.64,1);
    width: 0%;
}

/* ============================================================
   FORM CARD
   ============================================================ */
.card-create {
    background: var(--surface);
    border-radius: var(--radius-2xl);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-form);
    overflow: hidden;
}
.form-section {
    padding: 32px 36px 0;
}
.form-section:first-of-type { padding-top: 36px; }
.form-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 24px;
    padding-bottom: 14px;
    border-bottom: 1.5px solid var(--border);
}
.form-section-title .section-icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: rgba(0,25,125,.08);
    color: var(--navy);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem;
}
.form-section-title span {
    font-weight: 800;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--navy);
}

/* ============================================================
   FORM FIELDS
   ============================================================ */
.form-label-premium {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.required-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--rose);
    display: inline-block;
}
.form-control-premium,
.form-select-premium {
    width: 100%;
    border-radius: 13px !important;
    padding: 13px 16px !important;
    border: 1.5px solid var(--border) !important;
    font-size: .88rem !important;
    font-weight: 500 !important;
    color: var(--text-primary) !important;
    background: var(--surface-2) !important;
    outline: none !important;
    transition: border-color .25s, box-shadow .25s, background .25s !important;
}
.form-control-premium:focus,
.form-select-premium:focus {
    border-color: var(--navy) !important;
    background: #fff !important;
    box-shadow: 0 0 0 4px rgba(0,25,125,.08) !important;
}
.form-control-premium.is-filled,
.form-select-premium.is-filled {
    border-color: rgba(0,25,125,.3) !important;
    background: #fff !important;
}
.select-wrap { position: relative; }
.select-wrap::after {
    content: '\f078';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 16px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .68rem;
    pointer-events: none;
}
.kode-input-wrap { position: relative; }
.kode-input-wrap input { padding-right: 60px !important; letter-spacing: 1.5px !important; }
.kode-auto-badge {
    position: absolute;
    right: 12px; top: 50%;
    transform: translateY(-50%);
    font-size: .52rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    background: var(--border);
    padding: 3px 7px;
    border-radius: 6px;
}
.helper-text {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 7px;
    display: flex;
    align-items: flex-start;
    gap: 5px;
}
.diskon-preview-inline {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 7px;
    font-size: .78rem;
    font-weight: 700;
    color: var(--gold);
    min-height: 18px;
    opacity: 0;
    transition: opacity .3s;
}
.diskon-preview-inline.show { opacity: 1; }
.date-range-visual {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    font-size: .72rem;
    font-weight: 600;
    color: var(--text-muted);
    min-height: 18px;
    opacity: 0;
    transition: opacity .3s;
}
.date-range-visual.show { opacity: 1; }
.date-range-visual .duration {
    background: rgba(0,25,125,.07);
    color: var(--navy);
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 700;
}

/* ============================================================
   FORM FOOTER
   ============================================================ */
.form-footer {
    padding: 28px 36px 36px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    border-top: 1.5px solid var(--border);
    margin-top: 32px;
    flex-wrap: wrap;
}
.footer-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .78rem;
    color: var(--text-muted);
}
.btn-cancel-soft {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 22px;
    border-radius: 13px;
    border: 1.5px solid var(--border);
    background: var(--surface-2);
    color: var(--text-mid);
    font-weight: 700;
    font-size: .875rem;
    text-decoration: none;
    transition: all .25s;
}
.btn-cancel-soft:hover {
    border-color: var(--rose);
    color: var(--rose);
    background: #fff1f3;
}
.btn-save-premium {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    border-radius: 13px;
    border: none;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    font-weight: 800;
    font-size: .9rem;
    cursor: pointer;
    transition: all .3s;
    box-shadow: 0 8px 20px rgba(0,25,125,.25);
}
.btn-save-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(0,25,125,.32);
}
.btn-save-premium.loading {
    pointer-events: none;
    opacity: .8;
}

@media (max-width: 768px) {
    .create-title-block h2 { font-size: 1.5rem; }
    .form-section { padding: 24px 20px 0; }
    .form-footer { padding: 24px 20px 28px; }
    .voucher-preview { flex-direction: column; }
    .voucher-right {
        border-left: none;
        border-top: 2px dashed rgba(255,255,255,.18);
        padding-left: 0;
        padding-top: 16px;
        width: 100%;
        flex-direction: row;
        justify-content: flex-start;
        gap: 16px;
    }
    .voucher-notch { display: none; }
    .steps-bar { display: none; }
}
</style>

<div class="create-page-wrapper">

    <!-- Header -->
    <div class="create-header">
        <a href="{{ route('dashboard.promo.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Promo
        </a>
        <div class="create-title-block">
            <h2 class="fw-800">Buat Campaign <span>Baru</span></h2>
            <p>Tambahkan strategi promosi baru untuk layanan Hotel dan Restoran.</p>
        </div>
    </div>

    <!-- Steps Bar -->
    <div class="steps-bar" id="stepsBar">
        <div class="step-item"><div class="step-dot active" id="step1Dot">1</div><span class="step-label active" id="step1Label">Identitas</span></div>
        <div class="step-connector" id="conn1"></div>
        <div class="step-item"><div class="step-dot" id="step2Dot">2</div><span class="step-label" id="step2Label">Diskon</span></div>
        <div class="step-connector" id="conn2"></div>
        <div class="step-item"><div class="step-dot" id="step3Dot">3</div><span class="step-label" id="step3Label">Periode</span></div>
        <div class="step-connector" id="conn3"></div>
        <div class="step-item"><div class="step-dot" id="step4Dot"><i class="fas fa-check" style="font-size:.6rem;"></i></div><span class="step-label" id="step4Label">Selesai</span></div>
    </div>

    <!-- Live Preview Voucher -->
    <div class="voucher-preview" id="voucherPreview">
        <div class="voucher-left">
            <span class="v-label">Campaign</span>
            <div class="v-value" id="vNama" style="opacity:.3;">Nama promo akan muncul di sini</div>
            <div id="vCodeWrap" style="display:none;"><div class="v-code-chip" id="vKode"></div></div>
            <div style="margin-top:10px;"><span class="v-badge v-badge-empty" id="vKategori">— Kategori —</span></div>
        </div>
        <div class="voucher-right">
            <span class="voucher-notch top"></span>
            <span class="voucher-notch bottom"></span>
            <div id="vDiscountWrap">
                <span class="v-label" style="text-align:center;">Diskon</span>
                <div class="v-value v-discount" id="vDiskon" style="opacity:.25; font-size:1.8rem !important;">—</div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div>
        <div class="form-progress-label"><span>Kelengkapan Form</span><span class="pct" id="progressPct">0%</span></div>
        <div class="form-progress-bar"><div class="form-progress-fill" id="progressFill"></div></div>
    </div>

    <!-- Form Card -->
    <div class="card-create">
        <form action="{{ route('dashboard.promo.store') }}" method="POST" id="mainForm">
            @csrf

            <!-- Section 1: Identitas -->
            <div class="form-section">
                <div class="form-section-title"><div class="section-icon"><i class="fas fa-tag"></i></div><span>Identitas Promo</span></div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-premium">Nama Campaign <span class="required-dot"></span></label>
                        <input type="text" name="nama_promo" id="inputNama" class="form-control-premium" placeholder="Misal: Promo Akhir Tahun Eksklusif" required autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">Kode Voucher</label>
                        <div class="kode-input-wrap">
                            <input type="text" name="kode_promo" id="inputKode" class="form-control-premium" placeholder="Misal: PURNAMA2024" autocomplete="off">
                            <span class="kode-auto-badge">AUTO CAPS</span>
                        </div>
                        <span class="helper-text"><i class="fas fa-info-circle"></i> Kosongkan jika ingin promo muncul otomatis sebagai pop-up tanpa perlu input kode.</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Diskon -->
            <div class="form-section" style="padding-top:28px;">
                <div class="form-section-title"><div class="section-icon"><i class="fas fa-percent"></i></div><span>Konfigurasi Diskon</span></div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label-premium">Unit Layanan <span class="required-dot"></span></label>
                        <div class="select-wrap">
                            <select name="kategori" id="inputKategori" class="form-select-premium" required>
                                <option value="" selected disabled>Pilih Unit...</option>
                                <option value="hotel">🏨 Layanan Hotel</option>
                                <option value="restoran">🍽️ Layanan Restoran</option>
                                <option value="semua">✨ Semua Unit Bisnis</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">Mekanisme Potongan <span class="required-dot"></span></label>
                        <div class="select-wrap">
                            <select name="tipe_diskon" id="inputTipe" class="form-select-premium" required>
                                <option value="persen">Persentase (%)</option>
                                {{-- opsi nominal muncul via JS saat kode voucher berisi --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">Besaran Diskon <span class="required-dot"></span></label>
                        <input type="number" name="nominal_potongan" id="inputNominal" class="form-control-premium" placeholder="0" min="0" required>
                        <div class="diskon-preview-inline" id="diskonInline"><i class="fas fa-bolt"></i> <span id="diskonInlineText"></span></div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Periode -->
            <div class="form-section" style="padding-top:28px;">
                <div class="form-section-title"><div class="section-icon"><i class="fas fa-calendar-alt"></i></div><span>Periode Campaign</span></div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-premium">Tanggal Mulai Berlaku <span class="required-dot"></span></label>
                        <input type="date" name="tgl_mulai" id="inputMulai" class="form-control-premium" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">Tanggal Berakhir <span class="required-dot"></span></label>
                        <input type="date" name="tgl_selesai" id="inputSelesai" class="form-control-premium" required>
                        <div class="date-range-visual" id="dateRangeVisual">
                            <i class="fas fa-calendar-check" style="color:var(--green);"></i>
                            <span id="dateRangeText"></span>
                            <span class="duration" id="dateDuration"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Notifikasi -->
<div class="form-section" style="padding-top:28px; padding-bottom:28px;">
    <div class="form-section-title">
        <div class="section-icon"><i class="fas fa-bell"></i></div>
        <span>Notifikasi Pengguna</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <input type="checkbox" name="send_notification" id="sendNotif" value="1"
               style="width:20px;height:20px;cursor:pointer;accent-color:#00197D;">
        <label for="sendNotif" style="cursor:pointer;font-weight:600;color:#0f172a;margin:0;">
            Kirim notifikasi pop-up ke semua pengguna saat promo ini dibuat
        </label>
    </div>
    <span class="helper-text" style="margin-top:8px;">
        <i class="fas fa-info-circle"></i> 
        Notifikasi akan dikirim ke semua HP pengguna yang sudah terdaftar.
    </span>
</div>

            <!-- Footer -->
            <div class="form-footer">
                <div class="footer-info"><i class="fas fa-shield-alt"></i> <span>Data tersimpan aman di sistem.</span></div>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <a href="{{ route('dashboard.promo.index') }}" class="btn-cancel-soft"><i class="fas fa-times"></i> Batal</a>
                    <button type="submit" class="btn-save-premium" id="btnSave"><i class="fas fa-check-circle" id="btnIcon"></i> <span id="btnText">Simpan Campaign</span></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const inputNama    = document.getElementById('inputNama');
    const inputKode    = document.getElementById('inputKode');
    const inputKategori= document.getElementById('inputKategori');
    const inputTipe    = document.getElementById('inputTipe');
    const inputNominal = document.getElementById('inputNominal');
    const inputMulai   = document.getElementById('inputMulai');
    const inputSelesai = document.getElementById('inputSelesai');
    const mainForm     = document.getElementById('mainForm');

    // Preview elements
    const vNama     = document.getElementById('vNama');
    const vKode     = document.getElementById('vKode');
    const vCodeWrap = document.getElementById('vCodeWrap');
    const vKategori = document.getElementById('vKategori');
    const vDiskon   = document.getElementById('vDiskon');

    // Progress elements
    const progressFill = document.getElementById('progressFill');
    const progressPct  = document.getElementById('progressPct');
    const diskonInline = document.getElementById('diskonInline');
    const diskonInlineText = document.getElementById('diskonInlineText');
    const dateRangeVisual  = document.getElementById('dateRangeVisual');
    const dateRangeText    = document.getElementById('dateRangeText');
    const dateDuration     = document.getElementById('dateDuration');

    const requiredFields = [inputNama, inputKategori, inputNominal, inputMulai, inputSelesai];


    // ==================================================
// LOGIKA KODE VOUCHER → MEKANISME & BESARAN DISKON
// ==================================================
function syncTipeOptions() {
    const hasKode = inputKode.value.trim() !== '';
    const sudahAdaNominal = inputTipe.querySelector('option[value="nominal"]');

    if (hasKode && !sudahAdaNominal) {
        const opt = document.createElement('option');
        opt.value = 'nominal';
        opt.textContent = 'Potongan Harga (Rp)';
        inputTipe.appendChild(opt);
    } else if (!hasKode && sudahAdaNominal) {
        sudahAdaNominal.remove();
        inputTipe.value = 'persen'; // reset ke persen jika nominal sedang dipilih
    }

    syncBesaranDiskon();
}

function syncBesaranDiskon() {
    const isPersen = inputTipe.value === 'persen';

    if (isPersen) {
        // Ganti jadi dropdown 1-100
        const selectEl = document.createElement('select');
        selectEl.name = 'nominal_potongan';
        selectEl.id = 'inputNominal';
        selectEl.className = 'form-select-premium';
        selectEl.required = true;

        for (let i = 1; i <= 100; i++) {
            const opt = document.createElement('option');
            opt.value = i;
            opt.textContent = i;
            selectEl.appendChild(opt);
        }

        const current = document.getElementById('inputNominal');
        current.replaceWith(selectEl);

        // Re-bind event
        document.getElementById('inputNominal').addEventListener('change', () => {
            updatePreview();
            updateProgress();
        });
    } else {
        // Ganti jadi input number biasa
        const current = document.getElementById('inputNominal');
        if (current.tagName === 'SELECT') {
            const inputEl = document.createElement('input');
            inputEl.type = 'number';
            inputEl.name = 'nominal_potongan';
            inputEl.id = 'inputNominal';
            inputEl.className = 'form-control-premium';
            inputEl.placeholder = '0';
            inputEl.min = '0';
            inputEl.required = true;
            current.replaceWith(inputEl);

            // Re-bind event
            document.getElementById('inputNominal').addEventListener('input', () => {
                updatePreview();
                updateProgress();
            });
        }
    }
}

    // ==================================================
    // VALIDASI TANGGAL DENGAN STRING LANGSUNG (Y-m-d)
    // ==================================================
    function isDateRangeValid() {
        const mulai = inputMulai.value;
        const selesai = inputSelesai.value;
        if (!mulai || !selesai) return false;
        // Bandingkan string format YYYY-MM-DD secara langsung (leksikografis)
        return selesai >= mulai;
    }

    // ==================================================
    // UPDATE PROGRESS, STEPS, DAN VISUAL RENTANG TANGGAL
    // ==================================================
    function updateDateRangeVisual() {
        const mulai = inputMulai.value;
        const selesai = inputSelesai.value;
        if (!mulai || !selesai) {
            dateRangeVisual.classList.remove('show');
            return;
        }
        
        if (!isDateRangeValid()) {
            // Tampilkan visual merah (invalid)
            dateRangeText.innerHTML = `<span style="color:#e11d48;"><i class="fas fa-exclamation-triangle"></i> Tanggal berakhir tidak boleh lebih awal dari tanggal mulai</span>`;
            dateDuration.textContent = '';
            dateRangeVisual.classList.add('show');
            // Warna merah pada ikon
            dateRangeVisual.style.color = '#e11d48';
            return;
        }
        
        // Format tanggal Indonesia
        const fmt = (dateStr) => {
            const [y, m, d] = dateStr.split('-');
            return new Date(y, m-1, d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
        };
        const diff = Math.round((new Date(selesai) - new Date(mulai)) / (1000 * 60 * 60 * 24));
        dateRangeText.innerHTML = `${fmt(mulai)} → ${fmt(selesai)}`;
        dateDuration.textContent = diff + ' hari';
        dateRangeVisual.classList.add('show');
        dateRangeVisual.style.color = ''; // reset warna
    }

    function updateProgress() {
        // Hitung field yang terisi (tidak kosong)
        let filled = requiredFields.filter(el => el.value && el.value.trim() !== '').length;
        // Jika kedua tanggal terisi tapi tidak valid, kurangi 1 agar progress tidak penuh
        if (inputMulai.value && inputSelesai.value && !isDateRangeValid()) {
            filled = filled - 1; // periode tidak dianggap lengkap
        }
        const pct = Math.min(100, Math.round((filled / requiredFields.length) * 100));
        progressFill.style.width = pct + '%';
        progressPct.textContent = pct + '%';

        const s1 = inputNama.value.trim() !== '';
        const s2 = inputNominal.value.trim() !== '' && inputKategori.value !== '';
        const s3 = isDateRangeValid();
        
        setStep(1, s1, true);
        setStep(2, s2, s1);
        setStep(3, s3, s2);
        setStep(4, pct === 100 && s3, s3);
    }

    function setStep(n, done, active) {
        const dot   = document.getElementById('step' + n + 'Dot');
        const label = document.getElementById('step' + n + 'Label');
        const conn  = document.getElementById('conn' + (n - 1));
        if (!dot) return;
        dot.classList.remove('active', 'done');
        if (label) label.classList.remove('active');
        if (conn) conn.classList.remove('done');
        if (done) {
            dot.classList.add('done');
            dot.innerHTML = '<i class="fas fa-check" style="font-size:.6rem;"></i>';
            if (conn) conn.classList.add('done');
        } else if (active) {
            dot.classList.add('active');
            dot.textContent = n;
            if (label) label.classList.add('active');
        } else {
            dot.textContent = n;
        }
    }

    // ==================================================
    // LIVE PREVIEW VOUCHER
    // ==================================================
    function updatePreview() {
        const nama = inputNama.value.trim();
        vNama.textContent = nama || 'Nama promo akan muncul di sini';
        vNama.style.opacity = nama ? '1' : '0.3';

        const kode = inputKode.value.trim();
        if (kode) {
            vKode.textContent = kode;
            vCodeWrap.style.display = 'block';
        } else {
            vCodeWrap.style.display = 'none';
        }

        const kat = inputKategori.value;
        vKategori.className = 'v-badge';
        if (kat === 'hotel') { vKategori.textContent = 'Hotel'; vKategori.classList.add('v-badge-hotel'); }
        else if (kat === 'restoran') { vKategori.textContent = 'Restoran'; vKategori.classList.add('v-badge-resto'); }
        else if (kat === 'semua') { vKategori.textContent = 'Semua'; vKategori.classList.add('v-badge-semua'); }
        else { vKategori.textContent = '— Kategori —'; vKategori.classList.add('v-badge-empty'); }

        const tipe = inputTipe.value;
        const val = parseFloat(inputNominal.value) || 0;
        if (val > 0) {
            const formatted = tipe === 'persen' ? val + '%' : 'Rp ' + val.toLocaleString('id-ID');
            vDiskon.textContent = formatted;
            vDiskon.style.opacity = '1';
            vDiskon.style.fontSize = tipe === 'persen' ? '2.6rem' : '1.8rem';
            diskonInlineText.textContent = formatted + ' potongan';
            diskonInline.classList.add('show');
        } else {
            vDiskon.textContent = '—';
            vDiskon.style.opacity = '0.25';
            diskonInline.classList.remove('show');
        }
    }

    // ==================================================
    // HANDLER UNTUK PERUBAHAN TANGGAL (tanpa menghapus input)
    // ==================================================
    function handleDateChange() {
        updateDateRangeVisual();
        updateProgress();
        // Jika tanggal tidak valid, tampilkan peringatan tapi jangan hapus input
        if (inputMulai.value && inputSelesai.value && !isDateRangeValid()) {
            // Hanya tampilkan peringatan sekali (tanpa menghapus field)
            // Gunakan timeout agar tidak terlalu mengganggu saat mengetik
            if (window.dateWarningTimeout) clearTimeout(window.dateWarningTimeout);
            window.dateWarningTimeout = setTimeout(() => {
                Swal.fire({
                    icon: 'warning',
                    title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Perhatian</span>',
                    html: '<span style="font-family:Plus Jakarta Sans;">Tanggal berakhir seharusnya tidak lebih awal dari tanggal mulai.<br>Silakan perbaiki manual.</span>',
                    confirmButtonColor: '#00197D',
                    confirmButtonText: 'Mengerti',
                    timer: 3000,
                    timerProgressBar: true
                });
            }, 300);
        }
    }

    // Mark field as filled (gaya visual)
    function checkFilled(el) {
        if (el.value && el.value.trim() !== '') el.classList.add('is-filled');
        else el.classList.remove('is-filled');
    }

    // ==================================================
    // AUTO UPPERCASE KODE PROMO
    // ==================================================
    inputKode.addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase().replace(/\s/g, '');
        this.setSelectionRange(pos, pos);
        syncTipeOptions();
        updatePreview();
    });

    // ==================================================
    // EVENT LISTENER UNTUK SEMUA FIELD
    // ==================================================
    [inputNama, inputNominal].forEach(el => {
        el.addEventListener('input', () => { checkFilled(el); updatePreview(); updateProgress(); });
    });
    [inputKategori, inputTipe].forEach(el => {
        el.addEventListener('change', () => { 
            checkFilled(el); 
            updatePreview(); 
            updateProgress();
            if (el === inputTipe) syncBesaranDiskon(); // <-- tambah ini
        });
    });

    // Event untuk tanggal: pakai 'input' dan 'change' agar selalu terdeteksi (baik picker maupun manual)
    inputMulai.addEventListener('input', () => { checkFilled(inputMulai); handleDateChange(); });
    inputMulai.addEventListener('change', () => { checkFilled(inputMulai); handleDateChange(); });
    inputSelesai.addEventListener('input', () => { checkFilled(inputSelesai); handleDateChange(); });
    inputSelesai.addEventListener('change', () => { checkFilled(inputSelesai); handleDateChange(); });

    // ==================================================
    // VALIDASI SAAT SUBMIT (CEK LAGI)
    // ==================================================
    mainForm.addEventListener('submit', function(e) {
        if (!inputMulai.value || !inputSelesai.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Periode Belum Lengkap</span>',
                html: '<span style="font-family:Plus Jakarta Sans;">Harap isi tanggal mulai dan tanggal berakhir.</span>',
                confirmButtonColor: '#00197D',
                confirmButtonText: 'Mengerti'
            });
            return;
        }
        if (!isDateRangeValid()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Tanggal Tidak Valid</span>',
                html: '<span style="font-family:Plus Jakarta Sans;">Tanggal berakhir harus lebih besar atau sama dengan tanggal mulai.</span>',
                confirmButtonColor: '#00197D',
                confirmButtonText: 'Mengerti'
            });
            return;
        }
        // Loading state
        const btn = document.getElementById('btnSave');
        btn.classList.add('loading');
        document.getElementById('btnIcon').className = 'fas fa-spinner fa-spin';
        document.getElementById('btnText').textContent = 'Menyimpan...';
    });

    // ==================================================
    // ANIMASI HALAMAN
    // ==================================================
    [document.querySelector('.create-header'), document.querySelector('.steps-bar'), document.querySelector('.voucher-preview'), document.querySelector('.form-progress-label')?.closest('div'), document.querySelector('.card-create')].forEach((el, i) => {
        if (!el) return;
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'opacity .5s ease, transform .5s cubic-bezier(.34,1.56,.64,1)';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 80 + i * 100);
    });

    // Inisialisasi
    updateProgress();
    updatePreview();
    updateDateRangeVisual();
    syncTipeOptions();
});
</script>

@endsection