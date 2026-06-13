@extends('dashboard.layouts.app')
@section('title', 'Tambah Kamar Baru')

@section('content')
{{-- ================================================================
     TAMBAH KAMAR — PREMIUM UNIFIED
     ================================================================ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #94a3b8;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label {
    font-family: var(--font) !important;
}

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
    position: fixed;
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
   BACK LINK
   ============================================================ */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: var(--text-muted);
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    margin-bottom: 20px;
    transition: .2s;
    padding: 7px 14px;
    border-radius: 10px;
    background: rgba(255,255,255,.7);
    border: 1.5px solid var(--border);
    backdrop-filter: blur(6px);
}
.back-link:hover {
    color: var(--navy);
    background: #fff;
    border-color: #c0ceee;
    transform: translateX(-3px);
}

/* ============================================================
   PAGE HEADER
   ============================================================ */
.create-header {
    margin-bottom: 32px;
}
.create-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
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
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}

/* ============================================================
   FORM CARD
   ============================================================ */
.form-card {
    background: var(--surface);
    border-radius: 28px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
    max-width: 720px;
    opacity: 0;
    transform: translateY(20px);
    animation: cardReveal .5s cubic-bezier(.34,1.56,.64,1) .1s forwards;
}
@keyframes cardReveal {
    to { opacity: 1; transform: translateY(0); }
}
.form-card-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 22px 28px;
    position: relative;
    overflow: hidden;
}
.form-card-header::before,
.form-card-header::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.form-card-header::before { width: 160px; height: 160px; top: -60px; right: -40px; }
.form-card-header::after  { width: 80px;  height: 80px;  bottom: -30px; left: 80px; }
.form-card-header h5 {
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    z-index: 1;
}
.form-card-header p {
    color: rgba(255,255,255,.6);
    font-size: .78rem;
    font-weight: 500;
    margin: 4px 0 0;
    position: relative;
    z-index: 1;
}
.form-card-body {
    padding: 32px 28px;
}

/* ============================================================
   SECTION LABEL
   ============================================================ */
.form-section-label {
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.form-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* ============================================================
   FORM FIELDS
   ============================================================ */
.field-group {
    margin-bottom: 24px;
}
.field-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 8px;
    display: block;
}
.field-input {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 13px 16px;
    font-size: .9rem;
    font-weight: 600;
    color: var(--text-primary);
    background: var(--surface-2);
    outline: none;
    transition: .25s;
}
.field-input:focus {
    border-color: var(--navy);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(0,25,125,.07);
}
.field-input.is-invalid {
    border-color: var(--rose);
    background: #fff5f7;
}
.select-wrap {
    position: relative;
}
.select-wrap::after {
    content: '\f107';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
    font-size: .85rem;
}
.invalid-msg {
    color: var(--rose);
    font-size: .75rem;
    font-weight: 600;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.field-hint {
    font-size: .72rem;
    color: var(--text-muted);
    font-weight: 500;
    margin-top: 7px;
    line-height: 1.5;
}

/* ============================================================
   STATUS PICKER (VISUAL RADIO CARDS)
   ============================================================ */
.status-picker {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-top: 4px;
}
.status-option {
    position: relative;
}
.status-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}
.status-option label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 12px;
    border: 2px solid var(--border);
    border-radius: 16px;
    cursor: pointer;
    background: var(--surface-2);
    transition: var(--transition);
    text-align: center;
    font-size: .78rem;
    font-weight: 700;
    color: var(--text-muted);
}
.status-option label .status-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    transition: .25s;
}
/* Tersedia */
.status-option.opt-tersedia input:checked ~ label {
    border-color: var(--emerald);
    background: #ecfdf5;
    color: #065f46;
    box-shadow: 0 0 0 4px rgba(16,185,129,.1);
}
.status-option.opt-tersedia label .status-icon { background: rgba(16,185,129,.1); color: var(--emerald); }
.status-option.opt-tersedia input:checked ~ label .status-icon { background: var(--emerald); color: #fff; }
/* Terisi */
.status-option.opt-terisi input:checked ~ label {
    border-color: var(--rose);
    background: #fff5f7;
    color: #991b1b;
    box-shadow: 0 0 0 4px rgba(225,29,72,.08);
}
.status-option.opt-terisi label .status-icon { background: rgba(225,29,72,.1); color: var(--rose); }
.status-option.opt-terisi input:checked ~ label .status-icon { background: var(--rose); color: #fff; }
/* Nonaktif */
.status-option.opt-nonaktif input:checked ~ label {
    border-color: #94a3b8;
    background: #f1f5f9;
    color: #334155;
    box-shadow: 0 0 0 4px rgba(100,116,139,.1);
}
.status-option.opt-nonaktif label .status-icon { background: rgba(100,116,139,.1); color: #64748b; }
.status-option.opt-nonaktif input:checked ~ label .status-icon { background: #64748b; color: #fff; }
.status-option label:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
    border-color: #c0ceee;
}
.status-hint-box {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 14px 16px;
    margin-top: 14px;
    font-size: .75rem;
    color: var(--text-muted);
    line-height: 1.7;
}
.status-hint-box b { color: var(--text-primary); }

/* ============================================================
   FORM ACTIONS
   ============================================================ */
.form-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-top: 28px;
    border-top: 1.5px solid var(--border);
    margin-top: 8px;
    flex-wrap: wrap;
}
.btn-save {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 14px 32px;
    font-weight: 800;
    font-size: .9rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all .3s ease;
    box-shadow: 0 8px 20px rgba(0,25,125,.25);
}
.btn-save::after {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
    transition: left .5s ease;
}
.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(0,25,125,.32);
}
.btn-cancel {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 14px 24px;
    font-weight: 700;
    font-size: .875rem;
    color: var(--text-primary);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: .25s;
}
.btn-cancel:hover {
    color: var(--navy);
    background: #fff;
    border-color: #c0ceee;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .create-header h2 { font-size: 1.5rem; }
    .form-card-body { padding: 24px 20px; }
    .status-picker { grid-template-columns: repeat(3, 1fr); }
}
</style>

<div class="create-page-wrapper">

    <!-- Back Link -->
    <a href="{{ route('dashboard.hotel.kamar.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kamar
    </a>

    <!-- Page Header -->
    <div class="create-header">
        <h2 class="fw-800">Tambah <span>Kamar Baru</span></h2>
        <p>Isi detail unit kamar yang akan ditambahkan ke sistem.</p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-card-header">
            <h5><i class="fas fa-plus-circle"></i> Form Tambah Unit Kamar</h5>
            <p>Semua field wajib diisi dengan benar.</p>
        </div>
        <div class="form-card-body">
            <form action="{{ route('dashboard.hotel.kamar.store') }}" method="POST">
                @csrf

                <!-- SECTION: Info Kamar -->
                <div class="form-section-label">Informasi Unit</div>

                <div class="row g-0" style="gap: 20px 0;">
                    <!-- Nomor Kamar -->
                    <div class="col-md-6 pe-md-3">
                        <div class="field-group">
                            <label class="field-label" for="nomor_kamar">Nomor Unit Kamar</label>
                            <input
                                type="text"
                                id="nomor_kamar"
                                name="nomor_kamar"
                                class="field-input @error('nomor_kamar') is-invalid @enderror"
                                placeholder="Contoh: 101"
                                value="{{ old('nomor_kamar') }}"
                                required
                            >
                            @error('nomor_kamar')
                                <div class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                            <div class="field-hint">Pastikan nomor kamar unik dan tidak duplikat.</div>
                        </div>
                    </div>

                    <!-- Tipe Kamar -->
                    <div class="col-md-6 ps-md-3">
                        <div class="field-group">
                            <label class="field-label" for="tipe_kamar_id">Tipe Layanan Kamar</label>
                            <div class="select-wrap">
                                <select
                                    id="tipe_kamar_id"
                                    name="tipe_kamar_id"
                                    class="field-input @error('tipe_kamar_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">— Pilih Tipe —</option>
                                    @foreach($tipeKamar as $tipe)
                                        <option value="{{ $tipe->id }}" {{ old('tipe_kamar_id') == $tipe->id ? 'selected' : '' }}>
                                            {{ $tipe->nama_tipe }} — Rp {{ number_format($tipe->harga) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('tipe_kamar_id')
                                <div class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION: Status -->
                <div class="form-section-label" style="margin-top: 8px;">Status Awal</div>

                <div class="field-group">
                    <div class="status-picker">
                        @foreach($statusKamar as $status)
                        @php
                            $isChecked = old('status_kamar_id', 1) == $status->id;
                            $optClass = match($status->id) {
                                1 => 'opt-tersedia',
                                2 => 'opt-terisi',
                                3 => 'opt-nonaktif',
                                default => 'opt-nonaktif',
                            };
                            $icon = match($status->id) {
                                1 => 'fa-check-circle',
                                2 => 'fa-bed',
                                3 => 'fa-ban',
                                default => 'fa-question-circle',
                            };
                        @endphp
                        <div class="status-option {{ $optClass }}">
                            <input
                                type="radio"
                                id="status_{{ $status->id }}"
                                name="status_kamar_id"
                                value="{{ $status->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                                required
                            >
                            <label for="status_{{ $status->id }}">
                                <div class="status-icon"><i class="fas {{ $icon }}"></i></div>
                                {{ strtoupper($status->nama_status) }}
                            </label>
                        </div>
                        @endforeach
                    </div>

                    @error('status_kamar_id')
                        <div class="invalid-msg mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror

                    <div class="status-hint-box">
                        <b>Tersedia:</b> Kamar siap dijual ke pelanggan. &nbsp;·&nbsp;
                        <b>Terisi:</b> Kamar sedang digunakan oleh tamu. &nbsp;·&nbsp;
                        <b>Nonaktif:</b> Kamar tidak dapat dipesan sementara.
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan Data Kamar</button>
                    <a href="{{ route('dashboard.hotel.kamar.index') }}" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection