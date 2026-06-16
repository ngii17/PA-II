@extends('dashboard.layouts.app')

@section('title', 'Edit Promo')

@section('content')
{{-- ================================================================
     EDIT PROMO — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:       #00197D;
    --navy-dark:  #000C3D;
    --navy-mid:   #0025B3;
    --gold:       #D4AF37;
    --gold-light: #F5E6BE;
    --rose:       #e11d48;
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
.edit-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 55%, #fffdf0 100%);
    padding: 36px 24px 64px;
    position: relative;
    overflow-x: hidden;
}
.edit-page-wrapper::before,
.edit-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.edit-page-wrapper::before {
    width: 500px; height: 500px;
    top: -160px; right: -120px;
    background: radial-gradient(circle, rgba(0,25,125,.045) 0%, transparent 70%);
}
.edit-page-wrapper::after {
    width: 360px; height: 360px;
    bottom: -80px; left: -80px;
    background: radial-gradient(circle, rgba(212,175,55,.07) 0%, transparent 70%);
}
.edit-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.edit-header { margin-bottom: 32px; }

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

.edit-title-block { margin-top: 20px; }
.edit-title-block h2 {
    font-size: 1.9rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 6px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.edit-title-block h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.edit-title-block p {
    color: var(--text-muted);
    font-size: .85rem;
    font-weight: 500;
    margin: 0;
}
.edit-title-block p strong {
    color: var(--navy);
    font-weight: 700;
}

/* ============================================================
   LIVE PREVIEW STRIP
   ============================================================ */
.preview-strip {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    border-radius: 20px;
    padding: 20px 24px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 32px rgba(0,25,125,.22);
}
.preview-strip::before {
    content: '';
    position: absolute;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
    top: -80px; right: -50px;
}
.preview-label {
    font-size: .6rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: rgba(255,255,255,.5);
    font-weight: 700;
    margin-bottom: 4px;
}
.preview-item { flex: 1; min-width: 120px; }
.preview-item .preview-val {
    color: #fff;
    font-weight: 800;
    font-size: 1rem;
    letter-spacing: -.01em;
}
.preview-discount {
    font-size: 2.2rem !important;
    color: var(--gold) !important;
    letter-spacing: -.04em !important;
    line-height: 1 !important;
}
.preview-code-chip {
    background: rgba(255,255,255,.12);
    border: 1.5px dashed rgba(255,255,255,.3);
    border-radius: 10px;
    padding: 8px 16px;
    font-family: monospace;
    font-weight: 800;
    font-size: .9rem;
    color: #fff;
    letter-spacing: 2px;
}
.preview-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: .6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.preview-badge-hotel { background: rgba(147,197,253,.2); color: #93c5fd; }
.preview-badge-resto { background: rgba(110,231,183,.2); color: #6ee7b7; }
.preview-badge-semua { background: rgba(245,158,11,.2); color: #fbbf24; }

/* ============================================================
   FORM CARD
   ============================================================ */
.card-edit {
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
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
}
.form-section-title span {
    font-weight: 800;
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--navy);
}

/* Form elements */
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
.form-label-premium .required-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--rose);
    display: inline-block;
}
.form-control-premium, .form-select-premium {
    width: 100%;
    border-radius: 13px;
    padding: 13px 16px;
    border: 1.5px solid var(--border);
    font-size: .88rem;
    font-weight: 500;
    color: var(--text-primary);
    background: var(--surface-2);
    outline: none;
    transition: all .25s;
}
.form-control-premium:focus, .form-select-premium:focus {
    border-color: var(--navy);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(0,25,125,.08);
}
.form-control-premium.is-edited, .form-select-premium.is-edited {
    border-color: var(--gold) !important;
    box-shadow: 0 0 0 4px rgba(212,175,55,.1) !important;
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
    font-size: .7rem;
    pointer-events: none;
}
.kode-input-wrap { position: relative; }
.kode-input-wrap input { padding-right: 56px !important; }
.kode-upper-badge {
    position: absolute;
    right: 12px; top: 50%;
    transform: translateY(-50%);
    font-size: .55rem;
    font-weight: 800;
    text-transform: uppercase;
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
    gap: 8px;
    margin-top: 8px;
    font-size: .8rem;
    font-weight: 700;
    color: var(--gold);
}
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
.changes-indicator {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: .75rem;
    font-weight: 600;
    color: var(--text-muted);
}
.changes-indicator .changes-count {
    background: var(--gold);
    color: var(--navy-dark);
    font-weight: 800;
    font-size: .65rem;
    min-width: 20px; height: 20px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
}
.changes-indicator.has-changes { color: var(--navy); }

@media (max-width: 768px) {
    .edit-title-block h2 { font-size: 1.5rem; }
    .form-section { padding: 24px 20px 0; }
    .form-footer { padding: 24px 20px 28px; }
    .preview-strip { gap: 12px; }
    .preview-discount { font-size: 1.7rem !important; }
}
</style>

<div class="edit-page-wrapper">

    <!-- Header -->
    <div class="edit-header">
        <a href="{{ route('dashboard.promo.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Promo
        </a>
        <div class="edit-title-block">
            <h2 class="fw-800">Perbarui Data <span>Promo</span></h2>
            <p>Sedang mengubah campaign: <strong>{{ $promo->nama_promo }}</strong></p>
        </div>
    </div>

    <!-- Live Preview Strip -->
    <div class="preview-strip" id="previewStrip">
        <div class="preview-item">
            <div class="preview-label">Campaign</div>
            <div class="preview-val" id="previewNama">{{ $promo->nama_promo }}</div>
        </div>
        <div class="preview-item">
            <div class="preview-label">Potongan</div>
            <div class="preview-val preview-discount" id="previewDiskon">
                {{ $promo->tipe_diskon == 'persen' ? $promo->nominal_potongan.'%' : 'Rp '.number_format($promo->nominal_potongan,0,',','.') }}
            </div>
        </div>
        <div class="preview-item">
            <div class="preview-label">Kode Voucher</div>
            <div class="preview-code-chip" id="previewKode">{{ $promo->kode_promo ?: '—' }}</div>
        </div>
        <div class="preview-item">
            <div class="preview-label">Kategori</div>
            <div>
                <span class="preview-badge preview-badge-{{ $promo->kategori }}" id="previewKategoriBadge">
                    {{ strtoupper($promo->kategori) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card-edit">
        <form action="{{ route('dashboard.promo.update', $promo->id) }}" method="POST" id="mainForm">
            @csrf
            @method('PUT')

            <!-- SECTION 1 -->
            <div class="form-section">
                <div class="form-section-title">
                    <div class="section-icon"><i class="fas fa-tag"></i></div>
                    <span>Identitas Promo</span>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-premium">Nama Campaign <span class="required-dot"></span></label>
                        <input type="text" name="nama_promo" id="inputNama"
                               class="form-control-premium"
                               value="{{ $promo->nama_promo }}"
                               placeholder="Contoh: Promo Member Baru" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">Kode Voucher</label>
                        <div class="kode-input-wrap">
                            <input type="text" name="kode_promo" id="inputKode"
                                   class="form-control-premium"
                                   value="{{ $promo->kode_promo }}"
                                   placeholder="Contoh: PURNAMA30"
                                   autocomplete="off">
                            <span class="kode-upper-badge">AUTO CAPS</span>
                        </div>
                        <span class="helper-text"><i class="fas fa-info-circle"></i> Kosongkan kode jika promo ingin muncul otomatis sebagai pop-up.</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 2 -->
            <div class="form-section" style="padding-top: 28px;">
                <div class="form-section-title">
                    <div class="section-icon"><i class="fas fa-percent"></i></div>
                    <span>Konfigurasi Diskon</span>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label-premium">Kategori Layanan <span class="required-dot"></span></label>
                        <div class="select-wrap">
                            <select name="kategori" id="inputKategori" class="form-select-premium" required>
                                <option value="hotel"    {{ $promo->kategori == 'hotel'    ? 'selected' : '' }}>🏨 Layanan Hotel</option>
                                <option value="restoran" {{ $promo->kategori == 'restoran' ? 'selected' : '' }}>🍽️ Layanan Restoran</option>
                                <option value="semua"    {{ $promo->kategori == 'semua'    ? 'selected' : '' }}>✨ Semua Unit Bisnis</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">Tipe Pemotongan <span class="required-dot"></span></label>
                        <div class="select-wrap">
                            <select name="tipe_diskon" id="inputTipe" class="form-select-premium" required>
                                <option value="persen" {{ $promo->tipe_diskon == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                                @if($promo->kode_promo)
                                    <option value="nominal" {{ $promo->tipe_diskon == 'nominal' ? 'selected' : '' }}>Potongan Harga (Rp)</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">Besaran Potongan <span class="required-dot"></span></label>
                        @if($promo->tipe_diskon == 'persen')
                            <div class="select-wrap">
                                <select name="nominal_potongan" id="inputNominal" class="form-select-premium" required>
                                    @for($i = 1; $i <= 100; $i++)
                                        <option value="{{ $i }}" {{ (int)$promo->nominal_potongan == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        @else
                            <input type="number" name="nominal_potongan" id="inputNominal"
                                class="form-control-premium"
                                value="{{ (int)$promo->nominal_potongan }}"
                                min="0" required>
                        @endif
                        <div class="diskon-preview-inline" id="diskonPreviewInline">
                            <i class="fas fa-bolt"></i>
                            <span id="diskonPreviewText">
                                {{ $promo->tipe_diskon == 'persen' ? $promo->nominal_potongan.'% potongan' : 'Rp '.number_format($promo->nominal_potongan,0,',','.').' potongan' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3 -->
            <div class="form-section" style="padding-top: 28px;">
                <div class="form-section-title">
                    <div class="section-icon"><i class="fas fa-calendar-alt"></i></div>
                    <span>Periode & Visibilitas</span>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label-premium">Tanggal Aktivasi <span class="required-dot"></span></label>
                        <input type="date" name="tgl_mulai" class="form-control-premium" value="{{ $promo->tgl_mulai }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">Tanggal Kadaluarsa <span class="required-dot"></span></label>
                        <input type="date" name="tgl_selesai" class="form-control-premium" value="{{ $promo->tgl_selesai }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">Status Visibilitas <span class="required-dot"></span></label>
                        <div class="select-wrap">
                            <select name="is_active" id="inputStatus" class="form-select-premium" required>
                                <option value="1" {{ $promo->is_active ? 'selected' : '' }}>✅ Aktif (Terlihat)</option>
                                <option value="0" {{ !$promo->is_active ? 'selected' : '' }}>🗄️ Non-Aktif (Diarsipkan)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <div class="changes-indicator" id="changesIndicator">
                    <span class="changes-count" id="changesCount">0</span>
                    <span id="changesText">Belum ada perubahan</span>
                </div>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <a href="{{ route('dashboard.promo.index') }}" class="btn-cancel-soft"><i class="fas fa-times"></i> Batal</a>
                    <button type="submit" class="btn-save-premium" id="btnSave">
                        <i class="fas fa-save" id="btnIcon"></i>
                        <span id="btnText">Update Data Promo</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const inputNama     = document.getElementById('inputNama');
    const inputKode     = document.getElementById('inputKode');
    const inputKategori = document.getElementById('inputKategori');
    const inputTipe     = document.getElementById('inputTipe');
    const inputNominal  = document.getElementById('inputNominal');
    const previewNama   = document.getElementById('previewNama');
    const previewDiskon = document.getElementById('previewDiskon');
    const previewKode   = document.getElementById('previewKode');
    const previewKategoriBadge = document.getElementById('previewKategoriBadge');
    const diskonPreviewText = document.getElementById('diskonPreviewText');
    const changesCountSpan = document.getElementById('changesCount');
    const changesTextSpan = document.getElementById('changesText');
    const changesIndicator = document.getElementById('changesIndicator');
    const btnSave = document.getElementById('btnSave');
    const btnIcon = document.getElementById('btnIcon');
    const btnText = document.getElementById('btnText');
    const mainForm = document.getElementById('mainForm');


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
        inputTipe.value = 'persen';
    }

    syncBesaranDiskon();
}

function syncBesaranDiskon() {
    const isPersen = inputTipe.value === 'persen';
    const current = document.getElementById('inputNominal');
    const currentVal = current.value;

    if (isPersen && current.tagName !== 'SELECT') {
        const selectEl = document.createElement('select');
        selectEl.name = 'nominal_potongan';
        selectEl.id = 'inputNominal';
        selectEl.className = 'form-select-premium';
        selectEl.required = true;

        for (let i = 1; i <= 100; i++) {
            const opt = document.createElement('option');
            opt.value = i;
            opt.textContent = i;
            if (parseInt(currentVal) === i) opt.selected = true;
            selectEl.appendChild(opt);
        }

        current.replaceWith(selectEl);
        document.getElementById('inputNominal').addEventListener('change', () => {
            updatePreview();
            markEdited(document.getElementById('inputNominal'));
        });

    } else if (!isPersen && current.tagName === 'SELECT') {
        const inputEl = document.createElement('input');
        inputEl.type = 'number';
        inputEl.name = 'nominal_potongan';
        inputEl.id = 'inputNominal';
        inputEl.className = 'form-control-premium';
        inputEl.placeholder = '0';
        inputEl.min = '0';
        inputEl.required = true;
        inputEl.value = currentVal;
        current.replaceWith(inputEl);

        document.getElementById('inputNominal').addEventListener('input', () => {
            updatePreview();
            markEdited(document.getElementById('inputNominal'));
        });
    }
}

    // Original values for change detection
    const originals = {};
    document.querySelectorAll('.form-control-premium, .form-select-premium').forEach(el => {
        if (el.name) originals[el.name] = el.value;
    });

    // Helper: format diskon
    function formatDiskon() {
        const tipe = inputTipe.value;
        const val = parseFloat(inputNominal.value) || 0;
        if (tipe === 'persen') return val + '%';
        return 'Rp ' + val.toLocaleString('id-ID');
    }

    // Update live preview
    function updatePreview() {
        previewNama.textContent = inputNama.value || '—';
        const diskonFormatted = formatDiskon();
        previewDiskon.textContent = diskonFormatted;
        diskonPreviewText.textContent = diskonFormatted + ' potongan';
        previewKode.textContent = inputKode.value || '—';

        const kat = inputKategori.value;
        let badgeClass = 'preview-badge ';
        if (kat === 'hotel') badgeClass += 'preview-badge-hotel';
        else if (kat === 'restoran') badgeClass += 'preview-badge-resto';
        else badgeClass += 'preview-badge-semua';
        previewKategoriBadge.className = badgeClass;
        previewKategoriBadge.textContent = kat.toUpperCase();
    }

    // Mark field as edited (gold border)
    function markEdited(el) {
        if (el.value !== originals[el.name]) el.classList.add('is-edited');
        else el.classList.remove('is-edited');
        countChanges();
    }

    // Count changed fields
    function countChanges() {
        let count = 0;
        document.querySelectorAll('.form-control-premium, .form-select-premium').forEach(el => {
            if (el.name && el.value !== originals[el.name]) count++;
        });
        changesCountSpan.textContent = count;
        if (count > 0) {
            changesIndicator.classList.add('has-changes');
            changesTextSpan.textContent = count + ' kolom diubah';
            changesCountSpan.style.transform = 'scale(1.3)';
            setTimeout(() => changesCountSpan.style.transform = 'scale(1)', 200);
        } else {
            changesIndicator.classList.remove('has-changes');
            changesTextSpan.textContent = 'Belum ada perubahan';
        }
    }

    // Auto uppercase for kode promo
    inputKode.addEventListener('input', function () {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase().replace(/\s/g, '');
    this.setSelectionRange(pos, pos);
    syncTipeOptions(); // <-- tambah ini
    updatePreview();
    markEdited(this);
    });

    // Attach listeners
    [inputNama, inputKategori, inputTipe, inputNominal].forEach(el => {
        el.addEventListener('input', function() { updatePreview(); markEdited(this); });
        el.addEventListener('change', function() { 
            updatePreview(); 
            markEdited(this);
            if (el === inputTipe) syncBesaranDiskon(); // <-- tambah ini
        });
    });
    document.querySelectorAll('input[type="date"]').forEach(el => {
        el.addEventListener('change', function() { markEdited(this); });
    });
    const statusSelect = document.getElementById('inputStatus');
    if (statusSelect) statusSelect.addEventListener('change', function() { markEdited(this); });

    // Form submit loading state
    mainForm.addEventListener('submit', function() {
        btnSave.classList.add('loading');
        btnIcon.className = 'fas fa-spinner fa-spin';
        btnText.textContent = 'Menyimpan...';
    });

    // Initial preview & animation
    updatePreview();
    [document.querySelector('.edit-header'), document.querySelector('.preview-strip'), document.querySelector('.card-edit')].forEach((el, i) => {
        if (!el) return;
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'opacity .5s ease, transform .5s cubic-bezier(.34,1.56,.64,1)';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 80 + i * 120);
    });
});
</script>
@endsection