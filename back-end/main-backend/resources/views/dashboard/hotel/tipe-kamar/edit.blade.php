@extends('dashboard.layouts.app')
@section('title', 'Edit Tipe Kamar')

@section('content')
{{-- ================================================================
     EDIT TIPE KAMAR — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    --text-muted:   #94a3b8;
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
.form-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}
.form-page-wrapper::before,
.form-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.form-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.form-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.form-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   BACK LINK
   ============================================================ */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-muted);
    text-decoration: none;
    padding: 8px 14px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    transition: .25s ease;
    margin-bottom: 20px;
}
.back-link:hover {
    color: var(--navy);
    border-color: #c0ceee;
    background: var(--surface-2);
    transform: translateX(-2px);
}

/* ============================================================
   HEADER
   ============================================================ */
.form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
}
.form-header-left h2 {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.form-header-left h2 span {
    background: linear-gradient(135deg, var(--amber) 0%, #fbbf24 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.form-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}
.edit-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(245,158,11,.1);
    border: 1.5px solid rgba(245,158,11,.25);
    border-radius: 10px;
    padding: 7px 14px;
    font-size: .72rem;
    font-weight: 800;
    color: var(--amber);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* ============================================================
   FORM CARD
   ============================================================ */
.form-card {
    background: var(--surface);
    border-radius: 28px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
    opacity: 0;
    transform: translateY(24px);
    transition: opacity .5s ease, transform .5s cubic-bezier(.34,1.56,.64,1);
}
.form-card.visible {
    opacity: 1;
    transform: translateY(0);
}
.form-card-accent {
    height: 5px;
    background: linear-gradient(90deg, var(--amber), #fbbf24, var(--gold));
}
.form-section {
    padding: 28px 32px;
}
.form-section + .form-section {
    border-top: 1px solid var(--border);
}
.section-title {
    font-size: .6rem;
    text-transform: uppercase;
    letter-spacing: 2.5px;
    color: var(--text-muted);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
}
.section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}
.section-title-icon {
    width: 26px; height: 26px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .65rem;
}
.section-title-icon.navy   { background: rgba(0,25,125,.08); color: var(--navy); }
.section-title-icon.green  { background: rgba(16,185,129,.1); color: var(--emerald); }
.section-title-icon.amber  { background: rgba(245,158,11,.1); color: var(--amber); }

/* ============================================================
   FORM FIELDS
   ============================================================ */
.field-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}
.field-wrap { display: flex; flex-direction: column; gap: 7px; }
.field-wrap.full { grid-column: 1 / -1; }

.field-label {
    font-size: .62rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
.field-label .required-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--rose);
    display: inline-block;
}
.field-input {
    width: 100%;
    padding: 13px 16px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-size: .875rem;
    font-weight: 600;
    color: var(--text-primary);
    transition: .25s ease;
    outline: none;
}
.field-input:focus {
    border-color: var(--amber);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(245,158,11,.1);
}
.field-input.is-invalid {
    border-color: var(--rose);
    background: #fff5f7;
}
textarea.field-input { resize: vertical; line-height: 1.7; }
.invalid-msg {
    font-size: .72rem;
    font-weight: 700;
    color: var(--rose);
    display: flex;
    align-items: center;
    gap: 5px;
}
.input-prefix-wrap { position: relative; }
.input-prefix {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: .78rem;
    font-weight: 800;
    color: var(--text-muted);
    pointer-events: none;
}
.input-prefix-wrap .field-input { padding-left: 44px; }
.field-hint {
    font-size: .68rem;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 6px;
}

/* ============================================================
   FASILITAS LIVE PREVIEW
   ============================================================ */
.fasilitas-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 42px;
    padding: 10px 14px;
    background: var(--surface-2);
    border: 1.5px dashed var(--border);
    border-radius: 12px;
    transition: .25s;
}
.fasilitas-preview.has-chips { border-style: solid; border-color: #c0ceee; }
.preview-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: .68rem;
    font-weight: 700;
    color: var(--text-primary);
    animation: popIn .2s ease;
}
.preview-chip i { font-size: .55rem; color: var(--emerald); }
.preview-empty {
    font-size: .72rem;
    color: #c0cbe0;
    font-weight: 500;
    font-style: italic;
}
@keyframes popIn {
    from { opacity: 0; transform: scale(.8); }
    to   { opacity: 1; transform: scale(1); }
}

/* ============================================================
   CURRENT DATA INFO
   ============================================================ */
.current-strip {
    background: linear-gradient(135deg, rgba(245,158,11,.06) 0%, rgba(212,175,55,.08) 100%);
    border: 1.5px solid rgba(245,158,11,.2);
    border-radius: 16px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 22px;
}
.current-strip-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: rgba(245,158,11,.12);
    color: var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem;
}
.current-strip-label {
    font-size: .6rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #92670c;
    font-weight: 700;
    margin-bottom: 2px;
}
.current-strip-value {
    font-size: .875rem;
    font-weight: 800;
    color: #7c4e00;
}

/* ============================================================
   FOTO UPLOAD
   ============================================================ */
.foto-upload-area {
    border: 2px dashed var(--border);
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    background: var(--surface-2);
    cursor: pointer;
    transition: .25s ease;
    position: relative;
}
.foto-upload-area:hover {
    border-color: var(--amber);
    background: #fffbf0;
}
.foto-upload-area.has-preview {
    border-style: solid;
    border-color: #c0ceee;
    padding: 16px;
}
.foto-upload-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: rgba(245,158,11,.1);
    color: var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    margin: 0 auto 12px;
}
.foto-upload-text {
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-muted);
}
.foto-upload-text strong {
    color: var(--amber);
    cursor: pointer;
}
.foto-upload-text span {
    display: block;
    font-size: .68rem;
    margin-top: 4px;
}
.foto-preview-img {
    width: 100%;
    max-height: 220px;
    object-fit: cover;
    border-radius: 12px;
    display: none;
}
.foto-preview-img.visible { display: block; }
.foto-change-btn {
    margin-top: 10px;
    font-size: .72rem;
    font-weight: 700;
    color: var(--amber);
    cursor: pointer;
    display: none;
    align-items: center;
    gap: 5px;
    justify-content: center;
}
.foto-change-btn.visible { display: flex; }

/* Badge foto aktif */
.foto-aktif-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(16,185,129,.08);
    border: 1.5px solid rgba(16,185,129,.2);
    border-radius: 8px;
    padding: 5px 10px;
    font-size: .65rem;
    font-weight: 700;
    color: var(--emerald);
    margin-bottom: 10px;
}

/* ============================================================
   FOOTER BUTTONS
   ============================================================ */
.form-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 32px 28px;
    border-top: 1px solid var(--border);
    background: var(--surface-2);
    flex-wrap: wrap;
}
.btn-submit-edit {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 14px 32px;
    font-weight: 700;
    font-size: .875rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all .3s ease;
    box-shadow: 0 8px 20px rgba(180,83,9,.28);
}
.btn-submit-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(180,83,9,.36);
}
.btn-cancel {
    padding: 14px 24px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-weight: 700;
    font-size: .875rem;
    color: var(--text-primary);
    cursor: pointer;
    transition: .25s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
}
.btn-cancel:hover {
    border-color: var(--rose);
    color: var(--rose);
    background: #fff5f7;
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
    color: #991b1b;
    font-weight: 600;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}
@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .form-section { padding: 20px; }
    .form-footer   { padding: 16px 20px 22px; }
    .form-header-left h2 { font-size: 1.4rem; }
    .field-group { grid-template-columns: 1fr; }
}
</style>

<div class="form-page-wrapper">

    <!-- Back Link -->
    <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>

    <!-- Header -->
    <div class="form-header">
        <div class="form-header-left">
            <h2>Edit Tipe <span>Kamar</span></h2>
            <p>Ubah informasi tipe kamar untuk Hotel Purnama.</p>
        </div>
        <div class="edit-badge">
            <i class="fas fa-pen"></i> Mode Edit
        </div>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
    <div class="alert-error-premium">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Terdapat <strong>{{ $errors->count() }}</strong> kesalahan. Periksa kembali inputan Anda.
    </div>
    @endif

    <!-- Form Card -->
    <div class="form-card" id="formCard">
        <div class="form-card-accent"></div>

        {{-- ✅ enctype multipart agar foto bisa dikirim --}}
        <form action="{{ route('dashboard.hotel.tipe-kamar.update', $tipe->id) }}" method="POST" id="tipeForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Current Data Strip -->
            <div class="form-section" style="padding-bottom:16px;">
                <div class="current-strip">
                    <div class="current-strip-icon"><i class="fas fa-history"></i></div>
                    <div>
                        <div class="current-strip-label">Data Saat Ini</div>
                        <div class="current-strip-value">
                            {{ $tipe->nama_tipe }} &mdash;
                            Rp {{ number_format($tipe->harga, 0, ',', '.') }}/malam &mdash;
                            Kapasitas {{ $tipe->kapasitas }} Orang
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 1: Identitas -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-title-icon navy"><i class="fas fa-tag"></i></div>
                    Identitas Tipe
                </div>
                <div class="field-group">
                    <div class="field-wrap">
                        <label class="field-label"><span class="required-dot"></span> Nama Tipe Kamar</label>
                        <input type="text" name="nama_tipe"
                               class="field-input @error('nama_tipe') is-invalid @enderror"
                               placeholder="Contoh: Deluxe Room"
                               value="{{ old('nama_tipe', $tipe->nama_tipe) }}" autocomplete="off">
                        @error('nama_tipe')
                        <span class="invalid-msg"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field-wrap">
                        <label class="field-label"><span class="required-dot"></span> Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" min="1"
                               class="field-input @error('kapasitas') is-invalid @enderror"
                               placeholder="2"
                               value="{{ old('kapasitas', $tipe->kapasitas) }}">
                        @error('kapasitas')
                        <span class="invalid-msg"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Harga -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-title-icon amber"><i class="fas fa-coins"></i></div>
                    Tarif Kamar
                </div>
                <div class="field-group">
                    <div class="field-wrap">
                        <label class="field-label"><span class="required-dot"></span> Harga per Malam</label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">Rp</span>
                            <input type="number" name="harga" min="0"
                                   class="field-input @error('harga') is-invalid @enderror"
                                   placeholder="500000"
                                   value="{{ old('harga', (int)$tipe->harga) }}"
                                   id="hargaInput">
                        </div>
                        @error('harga')
                        <span class="invalid-msg"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                        @enderror
                        <span class="field-hint" id="hargaPreview" style="display:none;">
                            <i class="fas fa-info-circle"></i>
                            <span id="hargaText"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Fasilitas & Deskripsi -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-title-icon green"><i class="fas fa-concierge-bell"></i></div>
                    Fasilitas &amp; Deskripsi
                </div>
                <div class="field-group" style="margin-bottom:16px;">
                    <div class="field-wrap full">
                        <label class="field-label">Fasilitas (Pisah dengan Koma)</label>
                        <input type="text" name="fasilitas" id="fasilitasInput"
                               class="field-input"
                               placeholder="AC, WiFi, TV, Kamar Mandi Dalam, Bathtub…"
                               value="{{ old('fasilitas', $tipe->fasilitas) }}" autocomplete="off">
                        <span class="field-hint">
                            <i class="fas fa-info-circle"></i> Ketik fasilitas dipisahkan dengan koma, preview otomatis muncul di bawah.
                        </span>
                    </div>
                </div>

                <!-- Live Preview Fasilitas -->
                <div class="fasilitas-preview" id="fasilitasPreview">
                    <span class="preview-empty">Preview fasilitas akan muncul di sini…</span>
                </div>

                <div style="margin-top:20px;">
                    <div class="field-wrap full">
                        <label class="field-label">Deskripsi Lengkap</label>
                        <textarea name="deskripsi" class="field-input" rows="4"
                                  placeholder="Jelaskan keunggulan, nuansa, dan detail tipe kamar ini…">{{ old('deskripsi', $tipe->deskripsi) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ✅ Section 4: Foto Kamar — BARU -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-title-icon amber"><i class="fas fa-camera"></i></div>
                    Foto Kamar
                </div>
                <div class="field-wrap full">
                    <label class="field-label">Foto Tipe Kamar</label>

                    {{-- Badge foto aktif (foto lama yang sedang dipakai) --}}
                    @if($tipe->foto)
                        <div class="foto-aktif-badge">
                            <i class="fas fa-check-circle"></i> Foto aktif tersedia — upload baru untuk mengganti
                        </div>
                    @endif

                    {{-- Area klik untuk upload --}}
                    <div class="foto-upload-area {{ $tipe->foto ? 'has-preview' : '' }}"
                         id="fotoUploadArea"
                         onclick="document.getElementById('fotoInput').click()">

                        {{-- Placeholder (tampil jika belum ada foto sama sekali) --}}
                        <div id="fotoPlaceholder" style="{{ $tipe->foto ? 'display:none;' : '' }}">
                            <div class="foto-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="foto-upload-text">
                                <strong>Klik untuk pilih foto</strong> atau seret ke sini
                                <span>Format: JPG, PNG, WEBP &bull; Maks: 2MB</span>
                            </div>
                        </div>

                        {{-- Preview: tampilkan foto yang sudah ada, atau foto baru yang dipilih --}}
                        @if($tipe->foto && str_starts_with($tipe->foto, 'http'))
                            {{-- Foto lama dari seeder (URL internet) --}}
                            <img id="fotoPreviewImg"
                                 class="foto-preview-img visible"
                                 src="{{ $tipe->foto }}"
                                 alt="Foto {{ $tipe->nama_tipe }}">
                        @elseif($tipe->foto)
                            {{-- Foto dari storage lokal --}}
                            <img id="fotoPreviewImg"
                                 class="foto-preview-img visible"
                                 src="{{ asset('storage/' . $tipe->foto) }}"
                                 alt="Foto {{ $tipe->nama_tipe }}">
                        @else
                            {{-- Belum ada foto --}}
                            <img id="fotoPreviewImg" class="foto-preview-img" src="" alt="Preview Foto">
                        @endif
                    </div>

                    {{-- Tombol ganti foto (muncul jika sudah ada foto) --}}
                    <div class="foto-change-btn {{ $tipe->foto ? 'visible' : '' }}"
                         id="fotoChangeBtn"
                         onclick="document.getElementById('fotoInput').click()">
                        <i class="fas fa-sync-alt"></i> Ganti Foto
                    </div>

                    {{-- Input file tersembunyi --}}
                    <input type="file" name="foto" id="fotoInput"
                           accept="image/jpeg,image/png,image/webp"
                           style="display:none;">

                    @error('foto')
                    <span class="invalid-msg"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                    @enderror

                    <span class="field-hint">
                        <i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengganti foto. Foto ini ditampilkan di aplikasi mobile untuk semua kamar dengan tipe ini.
                    </span>
                </div>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <button type="submit" class="btn-submit-edit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animasi card masuk
    setTimeout(() => document.getElementById('formCard').classList.add('visible'), 80);

    // Live preview fasilitas
    const fasilitasInput = document.getElementById('fasilitasInput');
    const fasilitasPreview = document.getElementById('fasilitasPreview');

    function renderChips(val) {
        const chips = val.split(',').map(s => s.trim()).filter(Boolean);
        fasilitasPreview.innerHTML = '';
        if (!chips.length) {
            fasilitasPreview.classList.remove('has-chips');
            fasilitasPreview.innerHTML = '<span class="preview-empty">Preview fasilitas akan muncul di sini…</span>';
        } else {
            fasilitasPreview.classList.add('has-chips');
            chips.forEach(f => {
                const chip = document.createElement('span');
                chip.className = 'preview-chip';
                chip.innerHTML = `<i class="fas fa-check"></i> ${f}`;
                fasilitasPreview.appendChild(chip);
            });
        }
    }

    fasilitasInput.addEventListener('input', () => renderChips(fasilitasInput.value));
    if (fasilitasInput.value) renderChips(fasilitasInput.value);

    // Live preview harga
    const hargaInput = document.getElementById('hargaInput');
    const hargaPreview = document.getElementById('hargaPreview');
    const hargaText = document.getElementById('hargaText');

    hargaInput.addEventListener('input', function () {
        const val = parseInt(this.value);
        if (!isNaN(val) && val > 0) {
            hargaText.textContent = 'Rp ' + val.toLocaleString('id-ID') + ' per malam';
            hargaPreview.style.display = 'flex';
        } else {
            hargaPreview.style.display = 'none';
        }
    });
    if (hargaInput.value) hargaInput.dispatchEvent(new Event('input'));

    // Efek label saat focus
    document.querySelectorAll('.field-input').forEach(el => {
        el.addEventListener('focus', function () {
            const label = this.closest('.field-wrap')?.querySelector('.field-label');
            if (label) label.style.color = 'var(--amber)';
        });
        el.addEventListener('blur', function () {
            const label = this.closest('.field-wrap')?.querySelector('.field-label');
            if (label) label.style.color = '';
        });
    });

    // ✅ Preview foto baru yang dipilih
    const fotoInput       = document.getElementById('fotoInput');
    const fotoPreviewImg  = document.getElementById('fotoPreviewImg');
    const fotoUploadArea  = document.getElementById('fotoUploadArea');
    const fotoPlaceholder = document.getElementById('fotoPlaceholder');
    const fotoChangeBtn   = document.getElementById('fotoChangeBtn');

    fotoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Validasi ukuran di sisi client
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran Terlalu Besar',
                text: 'Ukuran foto maksimal 2MB.',
                confirmButtonColor: '#d97706',
            });
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            fotoPreviewImg.src = e.target.result;
            fotoPreviewImg.classList.add('visible');
            fotoPlaceholder.style.display = 'none';
            fotoUploadArea.classList.add('has-preview');
            fotoChangeBtn.classList.add('visible');
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection