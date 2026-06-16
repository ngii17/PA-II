@extends('dashboard.layouts.app')

@section('title', session('user.role') === 'admin' ? 'Konfigurasi Tema Aplikasi' : 'Konfigurasi Event Restoran')

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
   PAGE WRAPPER
   ============================================================ */
.edit-event-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}
.edit-event-wrapper::before,
.edit-event-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.edit-event-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.edit-event-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.edit-event-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.edit-header { margin-bottom: 32px; }
.edit-header .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    text-decoration: none;
    font-size: .8rem;
    font-weight: 600;
    transition: var(--transition);
    margin-bottom: 12px;
}
.edit-header .back-link:hover { color: var(--navy); transform: translateX(-4px); }
.edit-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.edit-header h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.edit-header p { color: var(--text-muted); margin: 6px 0 0; font-size: .875rem; font-weight: 500; }

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
.card-premium-body { padding: 32px; }

/* Section Divider */
.section-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 28px 0 20px;
}
.section-divider-line { flex: 1; height: 1px; background: var(--border); }
.section-divider-label {
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    white-space: nowrap;
}
.section-divider-label i { margin-right: 5px; }

/* Form Styles */
.form-label-premium {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
    display: block;
}
.form-label-premium i { margin-right: 6px; }
.form-control-premium {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-size: .85rem;
    font-weight: 500;
    color: var(--text-primary);
    background: var(--surface);
    transition: var(--transition);
    font-family: var(--font);
}
.form-control-premium:focus {
    outline: none;
    border-color: var(--navy);
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
textarea.form-control-premium { resize: vertical; min-height: 100px; }
select.form-control-premium { cursor: pointer; }
.form-text-premium {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Color Picker */
.color-picker-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    background: var(--surface);
    transition: var(--transition);
    cursor: pointer;
}
.color-picker-wrapper:focus-within {
    border-color: var(--navy);
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
.color-picker-wrapper input[type="color"] {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 8px;
    padding: 2px;
    cursor: pointer;
    background: none;
    flex-shrink: 0;
}
.color-hex-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: .85rem;
    font-weight: 600;
    color: var(--text-primary);
    font-family: 'Courier New', monospace;
    background: transparent;
    min-width: 0;
}
.color-swatch {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 1px solid rgba(0,0,0,.1);
    flex-shrink: 0;
    transition: background .2s;
}

/* Image Upload */
.image-upload-area {
    border: 2px dashed var(--border);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    background: var(--surface-2);
    position: relative;
    overflow: hidden;
}
.image-upload-area:hover {
    border-color: var(--navy);
    background: #f0f4ff;
}
.image-upload-area.has-current {
    border-style: solid;
    border-color: var(--border);
    padding: 12px;
    text-align: left;
}
.image-upload-area input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.upload-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}
.upload-icon i { color: white; font-size: .9rem; }
.upload-title {
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}
.upload-subtitle { font-size: .7rem; color: var(--text-muted); }

/* Current Image Preview */
.current-image-preview {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    border-radius: 10px;
    padding: 8px 12px;
    margin-bottom: 8px;
    border: 1px solid var(--border);
    position: relative;
    z-index: 1;
}
.current-image-preview .img-thumb {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    border: 1px solid var(--border);
    flex-shrink: 0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--surface-2);
}
.current-image-preview .img-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.current-image-preview .img-thumb i {
    color: var(--text-muted);
    font-size: .9rem;
}
.current-image-info { flex: 1; min-width: 0; }
.current-image-info .img-name {
    font-size: .75rem;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.current-image-info .img-hint { font-size: .65rem; color: var(--text-muted); }

/* Info Box */
.info-box-premium {
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    border-radius: 16px;
    padding: 14px 18px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.info-box-premium i { color: var(--navy); font-size: 1rem; margin-top: 2px; }
.info-box-premium .info-text {
    font-size: .75rem;
    color: var(--navy-dark);
    line-height: 1.5;
    font-weight: 500;
}

/* Preview Card */
.preview-card {
    background: linear-gradient(145deg, var(--surface-2) 0%, #fff9e8 100%);
    border-radius: 20px;
    padding: 20px;
    text-align: center;
    border: 1px solid var(--border);
}
.preview-label {
    font-size: .6rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 12px;
}
.preview-value {
    font-size: 1rem;
    font-weight: 800;
    color: var(--navy);
    background: white;
    display: inline-block;
    padding: 6px 18px;
    border-radius: 30px;
    border: 1px solid var(--border);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--border);
}
.btn-premium-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 32px;
    font-weight: 800;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    cursor: pointer;
    text-decoration: none;
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
    padding: 12px 28px;
    font-weight: 700;
    font-size: .85rem;
    color: var(--text-primary);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
}
.btn-premium-secondary:hover { background: var(--border); transform: translateY(-2px); }

/* Info Card */
.info-card {
    background: var(--surface-2);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border);
}
.info-card h6 {
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-card ul { padding-left: 18px; margin: 0; }
.info-card li { font-size: .75rem; color: var(--text-primary); margin-bottom: 8px; }

/* Badge Status */
.badge-status.aktif {
    background: #dcfce7;
    color: #15803d;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: .65rem;
    font-weight: 700;
}
.badge-status.nonaktif {
    background: #fee2e2;
    color: #b91c1c;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: .65rem;
    font-weight: 700;
}

/* Color Preview Strip */
.color-preview-strip {
    display: flex;
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 10px;
    border: 1px solid rgba(0,0,0,.06);
}
.color-preview-strip .strip-primary,
.color-preview-strip .strip-secondary { flex: 1; }

/* Image Thumbnails in sidebar */
.image-thumb-row {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}
.image-thumb {
    flex: 1;
    aspect-ratio: 1;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--surface-2);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
.image-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.image-thumb-placeholder {
    flex: 1;
    aspect-ratio: 1;
    border-radius: 10px;
    border: 1px dashed var(--border);
    background: var(--surface-2);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.image-thumb-placeholder i { font-size: .8rem; color: var(--text-muted); }
.image-thumb-placeholder span { font-size: .55rem; color: var(--text-muted); font-weight: 600; text-align: center; }

/* Responsive */
@media (max-width: 768px) {
    .edit-event-wrapper { padding: 20px 16px; }
    .edit-header h2 { font-size: 1.5rem; }
    .card-premium-body { padding: 20px; }
    .action-buttons { flex-direction: column; }
    .btn-premium-primary, .btn-premium-secondary { width: 100%; justify-content: center; }
}
</style>

@php
    $gradients = [
        'default'   => 'linear-gradient(135deg, #00197D, #1A3A9C)',
        'imlek'     => 'linear-gradient(135deg, #8D0000, #FFB300)',
        'lebaran'   => 'linear-gradient(135deg, #1B5E20, #FBC02D)',
        'valentine' => 'linear-gradient(135deg, #880E4F, #F48FB1)',
        'hut_ri'    => 'linear-gradient(135deg, #C62828, #F5F5F5)',
        'natal'     => 'linear-gradient(135deg, #0A5F38, #B71C1C)',
    ];
    $bgGradient = $gradients[$event->event_code] ?? 'linear-gradient(135deg, #434343, #000000)';

    $backRoute   = session('user.role') === 'admin'
        ? route('dashboard.event.index')
        : route('dashboard.restoran.event');
    $updateRoute = session('user.role') === 'admin'
        ? route('dashboard.event.update', $event->id)
        : route('dashboard.restoran.event.update', $event->id);

    $pageTitle = session('user.role') === 'admin' ? 'Konfigurasi Tema Aplikasi' : 'Konfigurasi Event Restoran';
    $pageIcon  = session('user.role') === 'admin' ? 'fa-palette' : 'fa-calendar-alt';
    $pageDesc  = session('user.role') === 'admin'
        ? 'Ubah pengaturan tema visual global untuk seluruh aplikasi'
        : 'Ubah pengaturan tema visual untuk event restoran';

    $primaryColor   = old('primary_color',   $event->primary_color   ?? '#00197D');
    $secondaryColor = old('secondary_color', $event->secondary_color ?? '#D4AF37');

    /*
     * HELPER: cek apakah path gambar valid (bukan path tmp Windows/Linux)
     * Path tmp biasanya mengandung: 'tmp', 'php28', 'phpXXXX', dll.
     * Jika null atau path tmp → tampilkan placeholder, JANGAN render <img>
     * Ini mencegah loop onerror yang menyebabkan kedip-kedip
     */
    $isValidImagePath = function($path) {
        if (empty($path)) return false;
        // Cek pola path tmp PHP (Windows: php2832.tmp, Linux: /tmp/phpXXXX)
        if (preg_match('/php[0-9A-Za-z]+\.tmp$/i', $path)) return false;
        if (str_contains($path, '/tmp/php')) return false;
        if (str_contains($path, 'C:\\xampp\\tmp')) return false;
        if (str_contains($path, 'C:/xampp/tmp')) return false;
        return true;
    };

    $hasHeader = $isValidImagePath($event->header_image);
    $hasBg     = $isValidImagePath($event->background_image);
    $hasDeco   = $isValidImagePath($event->decoration_image);
@endphp

<div class="edit-event-wrapper">

    <!-- Header -->
    <div class="edit-header">
        <a href="{{ $backRoute }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}
        </a>
        <h2>{{ $pageTitle }} <span>{{ $event->nama_event }}</span></h2>
        <p><i class="fas {{ $pageIcon }} me-1"></i> {{ $pageDesc }}</p>
    </div>

    <div class="row g-4">

        <!-- ===================== KIRI: FORM ===================== -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-pen-alt"></i> Form Konfigurasi</h6>
                </div>
                <div class="card-premium-body">

                    <!-- Info Box -->
                    <div class="info-box-premium">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-text">
                            @if(session('user.role') === 'admin')
                                Konfigurasikan tema aplikasi. Hanya <strong>SATU</strong> tema yang bisa aktif sebagai tema utama pada satu waktu.
                            @else
                                Konfigurasikan event/tema restoran. Hanya <strong>SATU</strong> event yang bisa aktif sebagai tema utama pada satu waktu.
                            @endif
                        </div>
                    </div>

                    <form action="{{ $updateRoute }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- ─── INFORMASI UMUM ─── --}}
                        <div class="section-divider">
                            <div class="section-divider-line"></div>
                            <span class="section-divider-label"><i class="fas fa-info-circle"></i> Informasi Umum</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div class="row g-3">
                            <!-- Nama Event -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas {{ session('user.role') === 'admin' ? 'fa-palette' : 'fa-calendar-alt' }}"></i>
                                    Nama {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}
                                </label>
                                <input type="text" name="nama_event"
                                    class="form-control-premium"
                                    value="{{ old('nama_event', $event->nama_event) }}"
                                    placeholder="Contoh: Imlek 2025"
                                    required>
                            </div>

                            <!-- Status Aktif -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-toggle-on"></i> Status Aktif
                                </label>
                                <select name="is_active" class="form-control-premium" required>
                                    <option value="1" {{ $event->is_active ? 'selected' : '' }}>✅ Aktifkan Sebagai Tema Utama</option>
                                    <option value="0" {{ !$event->is_active ? 'selected' : '' }}>❌ Nonaktifkan</option>
                                </select>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i>
                                    Hanya satu {{ session('user.role') === 'admin' ? 'tema' : 'event' }} yang bisa aktif sebagai tema utama.
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label-premium">
                                    <i class="fas fa-align-left"></i> Deskripsi / Keterangan
                                </label>
                                <textarea name="deskripsi" class="form-control-premium" rows="3"
                                    placeholder="Deskripsikan tema/event ini...">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                            </div>
                        </div>

                        {{-- ─── WARNA TEMA ─── --}}
                        <div class="section-divider">
                            <div class="section-divider-line"></div>
                            <span class="section-divider-label"><i class="fas fa-palette"></i> Warna Tema</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div class="row g-3">
                            <!-- Primary Color -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-circle" style="color: var(--navy)"></i> Warna Primer
                                </label>
                                <div class="color-picker-wrapper" id="primaryWrapper">
                                    <input type="color"
                                        id="primaryColorPicker"
                                        value="{{ $primaryColor }}"
                                        title="Pilih warna primer">
                                    <input type="text"
                                        name="primary_color"
                                        id="primaryColorHex"
                                        class="color-hex-input"
                                        value="{{ $primaryColor }}"
                                        maxlength="7"
                                        placeholder="#00197D">
                                    <div class="color-swatch" id="primarySwatch" style="background: {{ $primaryColor }}"></div>
                                </div>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Warna utama header, tombol, dan aksen.
                                </div>
                            </div>

                            <!-- Secondary Color -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-circle" style="color: var(--gold)"></i> Warna Sekunder
                                </label>
                                <div class="color-picker-wrapper" id="secondaryWrapper">
                                    <input type="color"
                                        id="secondaryColorPicker"
                                        value="{{ $secondaryColor }}"
                                        title="Pilih warna sekunder">
                                    <input type="text"
                                        name="secondary_color"
                                        id="secondaryColorHex"
                                        class="color-hex-input"
                                        value="{{ $secondaryColor }}"
                                        maxlength="7"
                                        placeholder="#D4AF37">
                                    <div class="color-swatch" id="secondarySwatch" style="background: {{ $secondaryColor }}"></div>
                                </div>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Warna aksen, highlight, dan dekorasi.
                                </div>
                            </div>
                        </div>

                        {{-- ─── GAMBAR ─── --}}
                        <div class="section-divider">
                            <div class="section-divider-line"></div>
                            <span class="section-divider-label"><i class="fas fa-images"></i> Aset Gambar</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div class="row g-3">

                            {{-- ── HEADER IMAGE ── --}}
                            <div class="col-md-4">
                                <label class="form-label-premium">
                                    <i class="fas fa-image"></i> Header Image
                                </label>
                                <div class="image-upload-area {{ $hasHeader ? 'has-current' : '' }}" id="headerImageArea">
                                    <input type="file" name="header_image" accept="image/*" id="headerImageInput">
                                    @if($hasHeader)
                                        <div class="current-image-preview">
                                            <div class="img-thumb">
                                                <img src="{{ asset('storage/' . $event->header_image) }}"
                                                     alt="Header Image"
                                                     onerror="this.onerror=null;this.style.display='none';this.parentElement.innerHTML='<i class=\'fas fa-image\'></i>'">
                                            </div>
                                            <div class="current-image-info">
                                                <div class="img-name">{{ basename($event->header_image) }}</div>
                                                <div class="img-hint">Klik untuk ganti gambar</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="upload-icon"><i class="fas fa-image"></i></div>
                                        <div class="upload-title">Upload Header</div>
                                        <div class="upload-subtitle">PNG, JPG, WEBP · Maks 2MB</div>
                                    @endif
                                </div>
                                <div class="form-text-premium mt-1">
                                    <i class="fas fa-info-circle"></i> Gambar bagian atas halaman.
                                </div>
                            </div>

                            {{-- ── BACKGROUND IMAGE ── --}}
                            <div class="col-md-4">
                                <label class="form-label-premium">
                                    <i class="fas fa-th-large"></i> Background Image
                                </label>
                                <div class="image-upload-area {{ $hasBg ? 'has-current' : '' }}" id="bgImageArea">
                                    <input type="file" name="background_image" accept="image/*" id="bgImageInput">
                                    @if($hasBg)
                                        <div class="current-image-preview">
                                            <div class="img-thumb">
                                                <img src="{{ asset('storage/' . $event->background_image) }}"
                                                     alt="Background Image"
                                                     onerror="this.onerror=null;this.style.display='none';this.parentElement.innerHTML='<i class=\'fas fa-th-large\'></i>'">
                                            </div>
                                            <div class="current-image-info">
                                                <div class="img-name">{{ basename($event->background_image) }}</div>
                                                <div class="img-hint">Klik untuk ganti gambar</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="upload-icon"><i class="fas fa-th-large"></i></div>
                                        <div class="upload-title">Upload Background</div>
                                        <div class="upload-subtitle">PNG, JPG, WEBP · Maks 2MB</div>
                                    @endif
                                </div>
                                <div class="form-text-premium mt-1">
                                    <i class="fas fa-info-circle"></i> Latar belakang halaman utama.
                                </div>
                            </div>

                            {{-- ── DECORATION IMAGE ── --}}
                            <div class="col-md-4">
                                <label class="form-label-premium">
                                    <i class="fas fa-star"></i> Decoration Image
                                </label>
                                <div class="image-upload-area {{ $hasDeco ? 'has-current' : '' }}" id="decoImageArea">
                                    <input type="file" name="decoration_image" accept="image/*" id="decoImageInput">
                                    @if($hasDeco)
                                        <div class="current-image-preview">
                                            <div class="img-thumb">
                                                <img src="{{ asset('storage/' . $event->decoration_image) }}"
                                                     alt="Decoration Image"
                                                     onerror="this.onerror=null;this.style.display='none';this.parentElement.innerHTML='<i class=\'fas fa-star\'></i>'">
                                            </div>
                                            <div class="current-image-info">
                                                <div class="img-name">{{ basename($event->decoration_image) }}</div>
                                                <div class="img-hint">Klik untuk ganti gambar</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="upload-icon"><i class="fas fa-star"></i></div>
                                        <div class="upload-title">Upload Dekorasi</div>
                                        <div class="upload-subtitle">PNG, JPG, WEBP · Maks 2MB</div>
                                    @endif
                                </div>
                                <div class="form-text-premium mt-1">
                                    <i class="fas fa-info-circle"></i> Elemen dekorasi / ornamen.
                                </div>
                            </div>

                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ $backRoute }}" class="btn-premium-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- ===================== KANAN: PREVIEW & INFO ===================== -->
        <div class="col-lg-4">

            <!-- Preview Card -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-eye"></i> Live Preview Tema</h6>
                </div>
                <div class="card-premium-body">
                    <div class="preview-card">
                        <div class="preview-label">Preview Header</div>
                        <div id="previewHeader" style="height: 80px; background: {{ $bgGradient }}; border-radius: 16px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; transition: background .4s;">
                            <span class="text-white small fw-bold" style="letter-spacing: 2px;">PREVIEW</span>
                        </div>
                        <div class="preview-value" id="previewNamaEvent">{{ $event->nama_event }}</div>
                        <div class="mt-2">
                            <span class="badge-status {{ $event->is_active ? 'aktif' : 'nonaktif' }}" id="previewStatus">
                                <i class="fas {{ $event->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                {{ $event->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </div>

                        <!-- Color Strip Preview -->
                        <div class="color-preview-strip mt-3" id="colorStrip">
                            <div class="strip-primary" id="stripPrimary" style="background: {{ $primaryColor }}"></div>
                            <div class="strip-secondary" id="stripSecondary" style="background: {{ $secondaryColor }}"></div>
                        </div>
                        <div class="form-text-premium justify-content-center mt-1">
                            <i class="fas fa-circle" style="font-size:.5rem"></i> Primer
                            <i class="fas fa-circle ms-2" style="font-size:.5rem"></i> Sekunder
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card: Detail -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-lightbulb"></i> Informasi {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}</h6>
                </div>
                <div class="card-premium-body">
                    <div class="info-card">
                        <h6><i class="fas fa-info-circle"></i> Detail</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Kode</span>
                            <span class="fw-bold small text-uppercase">{{ $event->event_code ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Warna Primer</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold small" id="infoColorPrimary">{{ $primaryColor }}</span>
                                <div id="infoSwatchPrimary" style="width:20px;height:20px;background:{{ $primaryColor }};border-radius:6px;border:1px solid #ddd;"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted small">Warna Sekunder</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold small" id="infoColorSecondary">{{ $secondaryColor }}</span>
                                <div id="infoSwatchSecondary" style="width:20px;height:20px;background:{{ $secondaryColor }};border-radius:6px;border:1px solid #ddd;"></div>
                            </div>
                        </div>

                        {{-- ── Thumbnail Gambar di Sidebar ──
                             PENTING: Gunakan $hasHeader/$hasBg/$hasDeco (sudah divalidasi di @php atas)
                             JANGAN render <img> jika path tidak valid → mencegah loop onerror
                        --}}
                        <h6 class="mt-2"><i class="fas fa-images"></i> Aset Gambar</h6>
                        <div class="image-thumb-row">

                            {{-- Header Thumb --}}
                            @if($hasHeader)
                                <div class="image-thumb">
                                    <img src="{{ asset('storage/' . $event->header_image) }}"
                                         alt="Header"
                                         onerror="this.onerror=null;this.style.display='none';">
                                </div>
                            @else
                                <div class="image-thumb-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>Header</span>
                                </div>
                            @endif

                            {{-- Background Thumb --}}
                            @if($hasBg)
                                <div class="image-thumb">
                                    <img src="{{ asset('storage/' . $event->background_image) }}"
                                         alt="Background"
                                         onerror="this.onerror=null;this.style.display='none';">
                                </div>
                            @else
                                <div class="image-thumb-placeholder">
                                    <i class="fas fa-th-large"></i>
                                    <span>BG</span>
                                </div>
                            @endif

                            {{-- Decoration Thumb --}}
                            @if($hasDeco)
                                <div class="image-thumb">
                                    <img src="{{ asset('storage/' . $event->decoration_image) }}"
                                         alt="Dekorasi"
                                         onerror="this.onerror=null;this.style.display='none';">
                                </div>
                            @else
                                <div class="image-thumb-placeholder">
                                    <i class="fas fa-star"></i>
                                    <span>Deko</span>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="info-card mt-3">
                        <h6><i class="fas fa-lightbulb"></i> Tips Mengelola</h6>
                        <ul>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Aktifkan {{ session('user.role') === 'admin' ? 'tema' : 'event' }} yang sedang berlangsung</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Hanya satu yang bisa aktif sebagai tema utama</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Gunakan PNG transparan untuk gambar dekorasi</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Perubahan tampilan akan langsung terlihat</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Animasi masuk ──
    document.querySelectorAll('.card-premium').forEach((card, idx) => {
        card.style.cssText += 'opacity:0;transform:translateY(20px);transition:all .5s cubic-bezier(.34,1.56,.64,1)';
        setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, 100 + idx * 80);
    });
    const header = document.querySelector('.edit-header');
    if (header) {
        header.style.cssText += 'opacity:0;transform:translateY(-10px);transition:all .4s ease';
        setTimeout(() => { header.style.opacity = '1'; header.style.transform = 'translateY(0)'; }, 50);
    }

    // ── Live Preview Nama Event ──
    const namaInput = document.querySelector('input[name="nama_event"]');
    const previewNama = document.getElementById('previewNamaEvent');
    if (namaInput && previewNama) {
        namaInput.addEventListener('input', function () {
            previewNama.textContent = this.value.trim() || '{{ $event->nama_event }}';
        });
    }

    // ── Live Preview Status ──
    const statusSelect = document.querySelector('select[name="is_active"]');
    const previewStatus = document.getElementById('previewStatus');
    if (statusSelect && previewStatus) {
        statusSelect.addEventListener('change', function () {
            const aktif = this.value === '1';
            previewStatus.className = 'badge-status ' + (aktif ? 'aktif' : 'nonaktif');
            previewStatus.innerHTML = `<i class="fas ${aktif ? 'fa-check-circle' : 'fa-ban'}"></i> ${aktif ? 'AKTIF' : 'NONAKTIF'}`;
        });
    }

    // ── Color Picker Sync ──
    function syncColor(pickerId, hexId, swatchId, infoTextId, infoSwatchId, stripId) {
        const picker  = document.getElementById(pickerId);
        const hex     = document.getElementById(hexId);
        const swatch  = document.getElementById(swatchId);
        const infoTxt = document.getElementById(infoTextId);
        const infoSw  = document.getElementById(infoSwatchId);
        const strip   = document.getElementById(stripId);

        function applyColor(val) {
            if (swatch)  swatch.style.background  = val;
            if (infoTxt) infoTxt.textContent       = val;
            if (infoSw)  infoSw.style.background   = val;
            if (strip)   strip.style.background    = val;
        }

        if (picker) picker.addEventListener('input', function () {
            hex.value = this.value.toUpperCase();
            applyColor(this.value);
        });
        if (hex) hex.addEventListener('input', function () {
            const val = this.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                picker.value = val;
                applyColor(val);
            }
        });
    }

    syncColor('primaryColorPicker',   'primaryColorHex',   'primarySwatch',   'infoColorPrimary',   'infoSwatchPrimary',   'stripPrimary');
    syncColor('secondaryColorPicker', 'secondaryColorHex', 'secondarySwatch', 'infoColorSecondary', 'infoSwatchSecondary', 'stripSecondary');

    // ── Image Upload Preview ──
    function bindImagePreview(inputId, areaId) {
        const input = document.getElementById(inputId);
        const area  = document.getElementById(areaId);
        if (!input || !area) return;

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                area.classList.add('has-current');
                // Hapus semua elemen lama kecuali input file
                Array.from(area.children).forEach(el => {
                    if (el.tagName !== 'INPUT') el.remove();
                });

                const preview = document.createElement('div');
                preview.className = 'current-image-preview';
                preview.innerHTML = `
                    <div class="img-thumb">
                        <img src="${e.target.result}" alt="Preview">
                    </div>
                    <div class="current-image-info">
                        <div class="img-name">${file.name}</div>
                        <div class="img-hint">Klik untuk ganti gambar</div>
                    </div>`;
                area.insertBefore(preview, input);
            };
            reader.readAsDataURL(file);
        });
    }

    bindImagePreview('headerImageInput', 'headerImageArea');
    bindImagePreview('bgImageInput',     'bgImageArea');
    bindImagePreview('decoImageInput',   'decoImageArea');
});
</script>
@endsection