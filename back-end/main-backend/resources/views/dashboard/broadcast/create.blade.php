@extends('dashboard.layouts.app')
@section('title', 'Buat Pengumuman Baru')

@section('content')
<!-- External Dependencies -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

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

    --radius-2xl:   32px;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);

    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }

body, input, select, textarea, button, label {
    font-family: var(--font) !important;
}

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.broadcast-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}

.broadcast-page-wrapper::before,
.broadcast-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.broadcast-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.broadcast-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.broadcast-page-wrapper > * { position: relative; z-index: 1; }

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
.broadcast-header {
    margin-bottom: 32px;
}

.broadcast-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}

.broadcast-header h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.broadcast-header p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}

/* ============================================================
   LAYOUT: MAIN + SIDEBAR
   ============================================================ */
.broadcast-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 24px;
    align-items: start;
    max-width: 1080px;
}

/* ============================================================
   FORM CARD
   ============================================================ */
.form-card {
    background: var(--surface);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
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
   FORM CONTROLS
   ============================================================ */
.field-group {
    margin-bottom: 22px;
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

.opt-badge {
    display: inline-block;
    font-size: .6rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--amber);
    background: rgba(245,158,11,.1);
    border: 1px solid rgba(245,158,11,.25);
    border-radius: 20px;
    padding: 2px 8px;
    margin-left: 8px;
    vertical-align: middle;
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
    appearance: none;
    -webkit-appearance: none;
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
.field-input.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(225,29,72,.08);
}

textarea.field-input {
    resize: vertical;
    min-height: 130px;
    line-height: 1.6;
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

/* Image URL strip hint */
.img-preview-strip {
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: var(--surface-2);
    border: 1.5px dashed var(--border);
    border-radius: 12px;
    font-size: .75rem;
    color: var(--text-muted);
    font-weight: 500;
}
.img-preview-strip i { color: var(--gold); font-size: 1rem; }

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
    position: relative;
    overflow: hidden;
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
.btn-save:hover::after { left: 100%; }

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

/* ============================================================
   SIDEBAR CARDS
   ============================================================ */
.sidebar-col {
    display: flex;
    flex-direction: column;
    gap: 16px;
    opacity: 0;
    transform: translateY(20px);
    animation: cardReveal .5s cubic-bezier(.34,1.56,.64,1) .25s forwards;
}

/* Tips Card */
.tips-card {
    background: var(--surface);
    border-radius: 24px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
}

.tips-card-header {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    padding: 16px 20px;
    border-bottom: 1px solid #fde68a;
}

.tips-card-header h6 {
    font-size: .85rem;
    font-weight: 800;
    color: #92400e;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tips-card-body {
    padding: 18px 20px;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 14px;
    font-size: .78rem;
    color: var(--text-muted);
    line-height: 1.6;
    font-weight: 500;
}
.tip-item:last-child { margin-bottom: 0; }

.tip-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem;
    flex-shrink: 0;
    margin-top: 1px;
}
.tip-icon.warn  { background: rgba(245,158,11,.1); color: var(--amber); }
.tip-icon.info  { background: rgba(0,25,125,.08);  color: var(--navy); }
.tip-icon.ok    { background: rgba(16,185,129,.1); color: var(--emerald); }

/* Preview Card */
.preview-card {
    background: var(--surface);
    border-radius: 24px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
}

.preview-card-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 16px 20px;
    position: relative;
    overflow: hidden;
}

.preview-card-header::before {
    content: '';
    position: absolute;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    top: -30px; right: -20px;
}

.preview-card-header h6 {
    font-size: .8rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    z-index: 1;
}

.preview-card-header p {
    font-size: .7rem;
    color: rgba(255,255,255,.5);
    margin: 3px 0 0;
    font-weight: 500;
    position: relative;
    z-index: 1;
}

.preview-card-body {
    padding: 16px 20px;
}

/* Notif mockup */
.notif-mockup {
    background: #f1f5f9;
    border-radius: 14px;
    padding: 14px;
}

.notif-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.notif-app-icon {
    width: 26px; height: 26px;
    background: linear-gradient(135deg, var(--navy), var(--navy-dark));
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: .6rem;
    color: #fff;
}

.notif-app-name {
    font-size: .65rem;
    font-weight: 700;
    color: #64748b;
    letter-spacing: .5px;
}

.notif-time {
    font-size: .6rem;
    color: var(--text-muted);
    margin-left: auto;
}

.notif-title {
    font-size: .82rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 4px;
    line-height: 1.3;
}

.notif-body {
    font-size: .73rem;
    color: #64748b;
    line-height: 1.55;
    font-weight: 500;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .65rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    margin-top: 12px;
}
.status-chip.draft {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 900px) {
    .broadcast-layout { grid-template-columns: 1fr; }
    .sidebar-col { order: -1; flex-direction: row; }
    .tips-card, .preview-card { flex: 1; }
}
@media (max-width: 640px) {
    .broadcast-header h2 { font-size: 1.5rem; }
    .form-card-body { padding: 24px 20px; }
    .sidebar-col { flex-direction: column; }
}
</style>

<!-- ================================================
     MARKUP
     ================================================ -->
<div class="broadcast-page-wrapper">

    <!-- Back Link -->
    <a href="{{ route('dashboard.admin.broadcast.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Daftar
    </a>

    <!-- Page Header -->
    <div class="broadcast-header">
        <h2>Buat <span>Pengumuman Baru</span></h2>
        <p>Tulis dan simpan draft pengumuman sebelum disebarkan ke pelanggan.</p>
    </div>

    <div class="broadcast-layout">

        <!-- ===== FORM CARD ===== -->
        <div class="form-card">

            <div class="form-card-header">
                <h5>
                    <i class="fas fa-bullhorn"></i>
                    Form Buat Pengumuman
                </h5>
                <p>Draft tidak akan terkirim sampai Anda menekan tombol Sebarkan di halaman daftar.</p>
            </div>

            <div class="form-card-body">
                <form action="{{ route('dashboard.admin.broadcast.store') }}" method="POST">
                    @csrf

                    <!-- SECTION: Konten Pesan -->
                    <div class="form-section-label">Konten Pesan</div>

                    <div class="field-group">
                        <label class="field-label" for="title">Judul Notifikasi (Title)</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="field-input @error('title') is-invalid @enderror"
                            placeholder="Contoh: Info Menu Baru Hari Ini!"
                            value="{{ old('title') }}"
                            required
                        >
                        @error('title')
                            <div class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        <div class="field-hint">Maksimal 60 karakter agar terbaca penuh di layar notifikasi HP pelanggan.</div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="body">Isi Pesan (Body)</label>
                        <textarea
                            id="body"
                            name="body"
                            class="field-input @error('body') is-invalid @enderror"
                            placeholder="Tuliskan deskripsi lengkap pengumuman Anda di sini..."
                            required
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        <div class="field-hint">Singkat dan padat — idealnya 1–2 kalimat agar mudah dibaca di layar notifikasi.</div>
                    </div>

                    <!-- SECTION: Periode Tampil -->
                    <div class="form-section-label" style="margin-top: 8px;">Periode Tampil</div>

                    <div class="row g-0" style="gap: 20px 0;">
                        {{-- SINKRONISASI DATABASE: Menggunakan start_date dan end_date --}}
                        <div class="col-md-6 pe-md-3">
                            <div class="field-group">
                                <label class="field-label" for="start_date">Tanggal Mulai Tampil</label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    class="field-input @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', date('Y-m-d')) }}"
                                    required
                                >
                                @error('start_date')
                                    <div class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-3">
                            <div class="field-group">
                                <label class="field-label" for="end_date">Tanggal Berakhir</label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    class="field-input @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                    required
                                >
                                @error('end_date')
                                    <div class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: Media (Opsional) -->
                    <div class="form-section-label" style="margin-top: 8px;">
                        Media <span class="opt-badge">Opsional</span>
                    </div>

                    <div class="field-group" style="margin-bottom: 0;">
                        <label class="field-label" for="image_url">URL Gambar</label>
                        <input
                            type="url"
                            id="image_url"
                            name="image_url"
                            class="field-input @error('image_url') is-invalid @enderror"
                            placeholder="https://example.com/foto.jpg"
                            value="{{ old('image_url') }}"
                        >
                        @error('image_url')
                            <div class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        <div class="img-preview-strip">
                            <i class="fas fa-image"></i>
                            Gambar akan ditampilkan sebagai banner di dalam notifikasi aplikasi pelanggan.
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i>
                            Simpan Sebagai Draft
                        </button>
                        <a href="{{ route('dashboard.admin.broadcast.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <!-- ===== SIDEBAR ===== -->
        <div class="sidebar-col">

            <!-- Tips Card -->
            <div class="tips-card">
                <div class="tips-card-header">
                    <h6><i class="fas fa-lightbulb"></i> Tips Admin</h6>
                </div>
                <div class="tips-card-body">
                    <div class="tip-item">
                        <div class="tip-icon warn"><i class="fas fa-paper-plane"></i></div>
                        <div>Pesan yang disimpan <b>tidak langsung terkirim</b> ke HP pelanggan. Tekan tombol <b>"Sebarkan"</b> pada halaman daftar untuk memicu notifikasi pop-up.</div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-icon info"><i class="fas fa-text-width"></i></div>
                        <div>Pastikan isi pesan <b>singkat dan padat</b> agar terbaca jelas pada layar notifikasi HP.</div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-icon ok"><i class="fas fa-calendar-check"></i></div>
                        <div>Atur periode tampil dengan tepat — pengumuman otomatis berhenti muncul setelah <b>tanggal berakhir</b>.</div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="preview-card">
                <div class="preview-card-header">
                    <h6><i class="fas fa-mobile-alt"></i> Preview Notifikasi</h6>
                    <p>Tampilan perkiraan di HP pelanggan</p>
                </div>
                <div class="preview-card-body">
                    <div class="notif-mockup">
                        <div class="notif-bar">
                            <div class="notif-app-icon"><i class="fas fa-hotel"></i></div>
                            <span class="notif-app-name">PURNAMA</span>
                            <span class="notif-time">Baru saja</span>
                        </div>
                        <div class="notif-title">Info Menu Baru Hari Ini!</div>
                        <div class="notif-body">Cek menu spesial terbaru kami yang sudah tersedia mulai hari ini di restoran lantai 1.</div>
                    </div>
                    <div class="status-chip draft">
                        <i class="fas fa-clock"></i> Status: Draft — Belum Disebarkan
                    </div>
                </div>
            </div>

        </div>
        <!-- end sidebar -->

    </div>
</div>
@endsection