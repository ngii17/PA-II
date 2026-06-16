@extends('dashboard.layouts.app')

@section('title', session('user.role') === 'admin' ? 'Tambah Tema Baru' : 'Tambah Event Restoran')

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
.create-event-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}
.create-event-wrapper::before,
.create-event-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.create-event-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.create-event-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.create-event-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.create-header { margin-bottom: 32px; }
.create-header .back-link {
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
.create-header .back-link:hover { color: var(--navy); transform: translateX(-4px); }
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
.create-header p { color: var(--text-muted); margin: 6px 0 0; font-size: .875rem; font-weight: 500; }

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

/* Event Code (auto-generate) */
.event-code-wrapper {
    position: relative;
}
.event-code-wrapper .form-control-premium {
    padding-right: 120px;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: .85rem;
    color: var(--navy);
    background: var(--surface-2);
}
.event-code-wrapper .form-control-premium:focus {
    background: var(--surface);
}
.btn-regen {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: .65rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: var(--transition);
    white-space: nowrap;
}
.btn-regen:hover { box-shadow: 0 4px 10px rgba(0,25,125,.3); }
.btn-regen i { font-size: .6rem; }

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

/* Color Presets */
.color-presets {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 8px;
}
.color-preset-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: var(--transition);
    flex-shrink: 0;
}
.color-preset-btn:hover, .color-preset-btn.active {
    border-color: var(--navy);
    transform: scale(1.2);
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
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.image-upload-area:hover { border-color: var(--navy); background: #f0f4ff; }
.image-upload-area.has-preview {
    border-style: solid;
    border-color: #86efac;
    background: #f0fdf4;
    padding: 12px;
    text-align: left;
    align-items: flex-start;
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
.upload-title { font-size: .8rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
.upload-subtitle { font-size: .7rem; color: var(--text-muted); }
.upload-preview-row {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    position: relative;
    z-index: 1;
}
.upload-preview-row img {
    width: 52px;
    height: 52px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #bbf7d0;
    flex-shrink: 0;
}
.upload-preview-info { flex: 1; min-width: 0; }
.upload-preview-info .file-name {
    font-size: .75rem;
    font-weight: 700;
    color: #15803d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.upload-preview-info .file-hint { font-size: .65rem; color: var(--text-muted); }

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
.info-box-premium .info-text { font-size: .75rem; color: var(--navy-dark); line-height: 1.5; font-weight: 500; }

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
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.preview-code {
    font-size: .65rem;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: var(--text-muted);
    margin-top: 6px;
    letter-spacing: 1px;
}

/* Color Strip Preview */
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
.btn-premium-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,25,125,.3); color: white; }
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
    background: #dcfce7; color: #15803d;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 12px; border-radius: 20px;
    font-size: .65rem; font-weight: 700;
}
.badge-status.nonaktif {
    background: #fee2e2; color: #b91c1c;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 12px; border-radius: 20px;
    font-size: .65rem; font-weight: 700;
}

/* Steps indicator */
.steps-indicator {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 28px;
}
.step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}
.step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    font-weight: 800;
    color: var(--text-muted);
    flex-shrink: 0;
    transition: var(--transition);
}
.step-circle.done {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: white;
}
.step-label { font-size: .65rem; font-weight: 700; color: var(--text-muted); }
.step-label.done { color: var(--navy); }
.step-line { flex: 1; height: 1px; background: var(--border); margin: 0 8px; }

/* Responsive */
@media (max-width: 768px) {
    .create-event-wrapper { padding: 20px 16px; }
    .create-header h2 { font-size: 1.5rem; }
    .card-premium-body { padding: 20px; }
    .action-buttons { flex-direction: column; }
    .btn-premium-primary, .btn-premium-secondary { width: 100%; justify-content: center; }
    .steps-indicator { display: none; }
}
</style>

@php
    $backRoute   = session('user.role') === 'admin'
        ? route('dashboard.event.index')
        : route('dashboard.restoran.event');
    $storeRoute  = session('user.role') === 'admin'
        ? route('dashboard.event.store')
        : route('dashboard.restoran.event.store');

    $pageTitle = session('user.role') === 'admin' ? 'Tambah Tema Baru' : 'Tambah Event Baru';
    $pageIcon  = session('user.role') === 'admin' ? 'fa-palette' : 'fa-calendar-plus';
    $pageDesc  = session('user.role') === 'admin'
        ? 'Buat tema visual baru untuk aplikasi'
        : 'Buat event/tema baru untuk restoran';

    // Preset warna event populer
    $colorPresets = [
        ['#00197D', '#D4AF37', 'Default'],
        ['#8D0000', '#FFB300', 'Imlek'],
        ['#1B5E20', '#FBC02D', 'Lebaran'],
        ['#880E4F', '#F48FB1', 'Valentine'],
        ['#C62828', '#F5F5F5', 'HUT RI'],
        ['#0A5F38', '#B71C1C', 'Natal'],
    ];
@endphp

<div class="create-event-wrapper">

    <!-- Header -->
    <div class="create-header">
        <a href="{{ $backRoute }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}
        </a>
        <h2>{{ $pageTitle }} <span>Baru</span></h2>
        <p><i class="fas {{ $pageIcon }} me-1"></i> {{ $pageDesc }}</p>
    </div>

    <div class="row g-4">

        <!-- ===================== KIRI: FORM ===================== -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-plus-circle"></i> Form Tambah {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}</h6>
                </div>
                <div class="card-premium-body">

                    <!-- Info Box -->
                    <div class="info-box-premium">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-text">
                            Isi seluruh informasi di bawah ini untuk membuat
                            <strong>{{ session('user.role') === 'admin' ? 'tema' : 'event' }} baru</strong>.
                            Kode event akan di-generate otomatis dari nama yang kamu isi.
                        </div>
                    </div>

                    <form action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- ─── INFORMASI UMUM ─── --}}
                        <div class="section-divider" style="margin-top:0">
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
                                    <span style="color:#e53e3e">*</span>
                                </label>
                                <input type="text" name="nama_event" id="namaEventInput"
                                    class="form-control-premium"
                                    value="{{ old('nama_event') }}"
                                    placeholder="Contoh: Imlek 2025"
                                    required>
                                @error('nama_event')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Aktif -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-toggle-on"></i> Status Aktif
                                    <span style="color:#e53e3e">*</span>
                                </label>
                                <select name="is_active" id="isActiveSelect" class="form-control-premium" required>
                                    <option value="0" {{ old('is_active', '0') == '0' ? 'selected' : '' }}>❌ Simpan sebagai Draft (Nonaktif)</option>
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>✅ Aktifkan Langsung Sebagai Tema Utama</option>
                                </select>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Hanya satu {{ session('user.role') === 'admin' ? 'tema' : 'event' }} yang bisa aktif sekaligus.
                                </div>
                            </div>

                            <!-- Kode Event (Auto-generate) -->
                            <div class="col-12">
                                <label class="form-label-premium">
                                    <i class="fas fa-code"></i> Kode Event
                                    <span style="color:#e53e3e">*</span>
                                </label>
                                <div class="event-code-wrapper">
                                    <input type="text" name="event_code" id="eventCodeInput"
                                        class="form-control-premium"
                                        value="{{ old('event_code') }}"
                                        placeholder="Auto-generate dari nama..."
                                        readonly>
                                    <button type="button" class="btn-regen" id="regenCodeBtn">
                                        <i class="fas fa-sync-alt"></i> Generate Ulang
                                    </button>
                                </div>
                                <div class="form-text-premium">
                                    <i class="fas fa-magic"></i> Kode di-generate otomatis dari nama. Harus unik & lowercase, hanya huruf, angka, dan underscore.
                                </div>
                                @error('event_code')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label-premium">
                                    <i class="fas fa-align-left"></i> Deskripsi / Keterangan
                                </label>
                                <textarea name="deskripsi" class="form-control-premium" rows="3"
                                    placeholder="Deskripsikan tema/event ini...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ─── WARNA TEMA ─── --}}
                        <div class="section-divider">
                            <div class="section-divider-line"></div>
                            <span class="section-divider-label"><i class="fas fa-palette"></i> Warna Tema</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <!-- Preset Warna Cepat -->
                        <div class="mb-3">
                            <div class="form-text-premium mb-2"><i class="fas fa-magic"></i> Preset Warna Cepat — Klik untuk terapkan</div>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                @foreach($colorPresets as $preset)
                                    <button type="button"
                                        class="preset-combo-btn"
                                        data-primary="{{ $preset[0] }}"
                                        data-secondary="{{ $preset[1] }}"
                                        title="{{ $preset[2] }}"
                                        style="
                                            display: flex;
                                            align-items: center;
                                            gap: 0;
                                            border: 1.5px solid var(--border);
                                            border-radius: 8px;
                                            overflow: hidden;
                                            cursor: pointer;
                                            width: 48px;
                                            height: 28px;
                                            padding: 0;
                                            background: none;
                                            transition: var(--transition);
                                        ">
                                        <div style="flex:1;height:100%;background:{{ $preset[0] }}"></div>
                                        <div style="flex:1;height:100%;background:{{ $preset[1] }}"></div>
                                    </button>
                                @endforeach
                            </div>
                            <div class="form-text-premium mt-1"><i class="fas fa-info-circle"></i> Setiap pasangan warna diambil dari tema populer.</div>
                        </div>

                        <div class="row g-3">
                            <!-- Primary Color -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-circle" style="color: var(--navy)"></i> Warna Primer
                                    <span style="color:#e53e3e">*</span>
                                </label>
                                <div class="color-picker-wrapper">
                                    <input type="color" id="primaryColorPicker" value="#00197D" title="Pilih warna primer">
                                    <input type="text" name="primary_color" id="primaryColorHex"
                                        class="color-hex-input"
                                        value="{{ old('primary_color', '#00197D') }}"
                                        maxlength="7" placeholder="#00197D" required>
                                    <div class="color-swatch" id="primarySwatch" style="background: {{ old('primary_color', '#00197D') }}"></div>
                                </div>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Warna utama header, tombol, dan aksen.
                                </div>
                                @error('primary_color')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Secondary Color -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-circle" style="color: var(--gold)"></i> Warna Sekunder
                                    <span style="color:#e53e3e">*</span>
                                </label>
                                <div class="color-picker-wrapper">
                                    <input type="color" id="secondaryColorPicker" value="#D4AF37" title="Pilih warna sekunder">
                                    <input type="text" name="secondary_color" id="secondaryColorHex"
                                        class="color-hex-input"
                                        value="{{ old('secondary_color', '#D4AF37') }}"
                                        maxlength="7" placeholder="#D4AF37" required>
                                    <div class="color-swatch" id="secondarySwatch" style="background: {{ old('secondary_color', '#D4AF37') }}"></div>
                                </div>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Warna aksen, highlight, dan dekorasi.
                                </div>
                                @error('secondary_color')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ─── ASET GAMBAR ─── --}}
                        <div class="section-divider">
                            <div class="section-divider-line"></div>
                            <span class="section-divider-label"><i class="fas fa-images"></i> Aset Gambar</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div class="row g-3">
                            <!-- Header Image -->
                            <div class="col-md-4">
                                <label class="form-label-premium">
                                    <i class="fas fa-image"></i> Header Image
                                </label>
                                <div class="image-upload-area" id="headerImageArea">
                                    <input type="file" name="header_image" accept="image/*" id="headerImageInput">
                                    <div class="upload-icon"><i class="fas fa-image"></i></div>
                                    <div class="upload-title">Upload Header</div>
                                    <div class="upload-subtitle">PNG, JPG, WEBP · Maks 2MB</div>
                                </div>
                                <div class="form-text-premium mt-1">
                                    <i class="fas fa-info-circle"></i> Gambar bagian atas halaman.
                                </div>
                                @error('header_image')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Background Image -->
                            <div class="col-md-4">
                                <label class="form-label-premium">
                                    <i class="fas fa-th-large"></i> Background Image
                                </label>
                                <div class="image-upload-area" id="bgImageArea">
                                    <input type="file" name="background_image" accept="image/*" id="bgImageInput">
                                    <div class="upload-icon"><i class="fas fa-th-large"></i></div>
                                    <div class="upload-title">Upload Background</div>
                                    <div class="upload-subtitle">PNG, JPG, WEBP · Maks 2MB</div>
                                </div>
                                <div class="form-text-premium mt-1">
                                    <i class="fas fa-info-circle"></i> Latar belakang halaman utama.
                                </div>
                                @error('background_image')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Decoration Image -->
                            <div class="col-md-4">
                                <label class="form-label-premium">
                                    <i class="fas fa-star"></i> Decoration Image
                                </label>
                                <div class="image-upload-area" id="decoImageArea">
                                    <input type="file" name="decoration_image" accept="image/*" id="decoImageInput">
                                    <div class="upload-icon"><i class="fas fa-star"></i></div>
                                    <div class="upload-title">Upload Dekorasi</div>
                                    <div class="upload-subtitle">PNG, JPG, WEBP · Maks 2MB</div>
                                </div>
                                <div class="form-text-premium mt-1">
                                    <i class="fas fa-info-circle"></i> Elemen dekorasi / ornamen (PNG transparan ideal).
                                </div>
                                @error('decoration_image')
                                    <div class="form-text-premium" style="color:#e53e3e"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-primary">
                                <i class="fas fa-plus-circle"></i> Buat {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}
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

            <!-- Live Preview -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-eye"></i> Live Preview</h6>
                </div>
                <div class="card-premium-body">
                    <div class="preview-card">
                        <div class="preview-label">Preview Header</div>
                        <div id="previewHeader" style="height: 80px; background: linear-gradient(135deg, #00197D, #000C3D); border-radius: 16px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; transition: background .4s;">
                            <span class="text-white small fw-bold" style="letter-spacing: 2px;">PREVIEW</span>
                        </div>
                        <div class="preview-value" id="previewNamaEvent">Nama Event</div>
                        <div class="preview-code" id="previewCode">event_code</div>
                        <div class="mt-2">
                            <span class="badge-status nonaktif" id="previewStatus">
                                <i class="fas fa-ban"></i> DRAFT
                            </span>
                        </div>
                        <!-- Color Strip -->
                        <div class="color-preview-strip mt-3">
                            <div class="strip-primary" id="stripPrimary" style="background: #00197D"></div>
                            <div class="strip-secondary" id="stripSecondary" style="background: #D4AF37"></div>
                        </div>
                        <div class="form-text-premium justify-content-center mt-1">
                            <i class="fas fa-circle" style="font-size:.5rem"></i> Primer
                            <i class="fas fa-circle ms-2" style="font-size:.5rem"></i> Sekunder
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-lightbulb"></i> Panduan Pengisian</h6>
                </div>
                <div class="card-premium-body">
                    <div class="info-card">
                        <h6><i class="fas fa-list-ol"></i> Langkah-Langkah</h6>
                        <ul>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Isi nama — kode otomatis ter-generate</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Pilih status: draft atau langsung aktif</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Pilih warna dari preset atau custom</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Upload gambar (opsional, bisa diisi nanti)</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Klik <strong>Buat {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}</strong></li>
                        </ul>
                    </div>

                    <div class="info-card mt-3">
                        <h6><i class="fas fa-images"></i> Tips Gambar</h6>
                        <ul>
                            <li><strong>Header Image</strong> — Rasio 16:9, min 1200px lebar</li>
                            <li><strong>Background</strong> — Resolusi tinggi, bisa diulang (tileable)</li>
                            <li><strong>Dekorasi</strong> — Gunakan PNG transparan untuk overlay</li>
                            <li>Format: JPG, PNG, WEBP · Maks 2MB per file</li>
                        </ul>
                    </div>

                    <div class="info-card mt-3">
                        <h6><i class="fas fa-code"></i> Format Kode Event</h6>
                        <ul>
                            <li>Lowercase, tanpa spasi</li>
                            <li>Hanya huruf, angka, underscore (<code>_</code>)</li>
                            <li>Contoh: <code>imlek_2025</code>, <code>lebaran</code>, <code>natal_2024</code></li>
                            <li>Harus unik — tidak boleh sama dengan yang sudah ada</li>
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
    const header = document.querySelector('.create-header');
    if (header) {
        header.style.cssText += 'opacity:0;transform:translateY(-10px);transition:all .4s ease';
        setTimeout(() => { header.style.opacity = '1'; header.style.transform = 'translateY(0)'; }, 50);
    }

    // ── Auto-generate event_code dari nama ──
    const namaInput     = document.getElementById('namaEventInput');
    const codeInput     = document.getElementById('eventCodeInput');
    const previewNama   = document.getElementById('previewNamaEvent');
    const previewCode   = document.getElementById('previewCode');

    function generateCode(nama) {
        return nama
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s_]/g, '')   // hapus karakter non-alfanumerik kecuali spasi & _
            .replace(/\s+/g, '_')            // spasi → underscore
            .replace(/_+/g, '_')             // underscore berlebih
            .replace(/^_|_$/g, '');          // trim underscore di awal/akhir
    }

    if (namaInput) {
        namaInput.addEventListener('input', function () {
            const nama = this.value;
            const code = generateCode(nama);
            codeInput.value = code;
            previewNama.textContent = nama || 'Nama Event';
            previewCode.textContent = code || 'event_code';
        });
    }

    // ── Tombol Generate Ulang (tambah timestamp) ──
    const regenBtn = document.getElementById('regenCodeBtn');
    if (regenBtn) {
        regenBtn.addEventListener('click', function () {
            const base   = generateCode(namaInput.value || 'event');
            const suffix = '_' + Date.now().toString().slice(-4);
            const newCode = (base + suffix).slice(0, 50);
            codeInput.value = newCode;
            previewCode.textContent = newCode;

            // Animasi tombol
            const icon = this.querySelector('i');
            icon.style.transform = 'rotate(360deg)';
            icon.style.transition = 'transform .4s';
            setTimeout(() => { icon.style.transform = ''; }, 400);
        });
    }

    // ── Live Preview Status ──
    const statusSelect  = document.getElementById('isActiveSelect');
    const previewStatus = document.getElementById('previewStatus');
    if (statusSelect && previewStatus) {
        statusSelect.addEventListener('change', function () {
            const aktif = this.value === '1';
            previewStatus.className = 'badge-status ' + (aktif ? 'aktif' : 'nonaktif');
            previewStatus.innerHTML = `<i class="fas ${aktif ? 'fa-check-circle' : 'fa-ban'}"></i> ${aktif ? 'AKTIF' : 'DRAFT'}`;
        });
    }

    // ── Color Picker Sync ──
    function syncColor(pickerId, hexId, swatchId, stripId) {
        const picker = document.getElementById(pickerId);
        const hex    = document.getElementById(hexId);
        const swatch = document.getElementById(swatchId);
        const strip  = document.getElementById(stripId);

        function applyColor(val) {
            if (swatch) swatch.style.background = val;
            if (strip)  strip.style.background  = val;
            updatePreviewGradient();
        }

        if (picker) picker.addEventListener('input', function () {
            const val = this.value.toUpperCase();
            if (hex) hex.value = val;
            applyColor(val);
        });
        if (hex) hex.addEventListener('input', function () {
            const val = this.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                if (picker) picker.value = val;
                applyColor(val);
            }
        });
    }

    syncColor('primaryColorPicker',   'primaryColorHex',   'primarySwatch',   'stripPrimary');
    syncColor('secondaryColorPicker', 'secondaryColorHex', 'secondarySwatch', 'stripSecondary');

    function updatePreviewGradient() {
        const p = document.getElementById('primaryColorHex')?.value   || '#00197D';
        const s = document.getElementById('secondaryColorHex')?.value || '#D4AF37';
        const header = document.getElementById('previewHeader');
        if (header) header.style.background = `linear-gradient(135deg, ${p}, ${s})`;
    }

    // ── Preset Warna ──
    document.querySelectorAll('.preset-combo-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const p = this.dataset.primary;
            const s = this.dataset.secondary;

            // Primary
            const pPicker = document.getElementById('primaryColorPicker');
            const pHex    = document.getElementById('primaryColorHex');
            const pSwatch = document.getElementById('primarySwatch');
            if (pPicker) pPicker.value = p;
            if (pHex)    pHex.value    = p.toUpperCase();
            if (pSwatch) pSwatch.style.background = p;
            const pStrip = document.getElementById('stripPrimary');
            if (pStrip) pStrip.style.background = p;

            // Secondary
            const sPicker = document.getElementById('secondaryColorPicker');
            const sHex    = document.getElementById('secondaryColorHex');
            const sSwatch = document.getElementById('secondarySwatch');
            if (sPicker) sPicker.value = s;
            if (sHex)    sHex.value    = s.toUpperCase();
            if (sSwatch) sSwatch.style.background = s;
            const sStrip = document.getElementById('stripSecondary');
            if (sStrip) sStrip.style.background = s;

            updatePreviewGradient();

            // Highlight tombol aktif
            document.querySelectorAll('.preset-combo-btn').forEach(b => b.style.borderColor = 'var(--border)');
            this.style.borderColor = 'var(--navy)';
        });
    });

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
                area.classList.add('has-preview');
                // Hapus konten lama
                area.querySelectorAll('.upload-icon, .upload-title, .upload-subtitle, .upload-preview-row')
                    .forEach(el => el.remove());

                const row = document.createElement('div');
                row.className = 'upload-preview-row';
                row.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <div class="upload-preview-info">
                        <div class="file-name"><i class="fas fa-check-circle me-1"></i>${file.name}</div>
                        <div class="file-hint">Klik untuk ganti gambar</div>
                    </div>`;
                area.insertBefore(row, input);
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