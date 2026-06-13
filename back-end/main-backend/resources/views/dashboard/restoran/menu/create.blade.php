@extends('dashboard.layouts.app')
@section('title', 'Tambah Menu Baru')

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
    --indigo:       #6366f1;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #5b6e8c;
    --radius-2xl:   32px;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 20px 44px rgba(0,25,125,.14);
    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label { font-family: var(--font) !important; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.create-menu-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.create-menu-wrapper::before,
.create-menu-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.create-menu-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.create-menu-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.create-menu-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER
   ============================================================ */
.create-header {
    margin-bottom: 32px;
}

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

.create-header .back-link:hover {
    color: var(--navy);
    transform: translateX(-4px);
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
}

.card-premium:hover {
    box-shadow: var(--shadow-hover);
}

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

.card-premium-header h6 i {
    margin-right: 8px;
}

.card-premium-body {
    padding: 32px;
}

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

.form-label-premium i {
    margin-right: 6px;
}

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

.form-control-premium.is-invalid {
    border-color: var(--rose);
    background-color: rgba(225,29,72,.02);
}

textarea.form-control-premium {
    resize: vertical;
    min-height: 100px;
}

.invalid-feedback-premium {
    font-size: .7rem;
    color: var(--rose);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Alert Error */
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

.info-box-premium i {
    color: var(--navy);
    font-size: 1rem;
    margin-top: 2px;
}

.info-box-premium .info-text {
    font-size: .75rem;
    color: var(--navy-dark);
    line-height: 1.5;
    font-weight: 500;
}

/* Image Preview */
.image-preview-area {
    margin-bottom: 20px;
}

.preview-img {
    margin-top: 10px;
}

.preview-img img {
    max-width: 150px;
    max-height: 150px;
    object-fit: cover;
    border-radius: 14px;
    border: 2px solid var(--border);
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

.btn-premium-secondary:hover {
    background: var(--border);
    transform: translateY(-2px);
}

/* Summary Card */
.summary-card {
    background: linear-gradient(145deg, var(--navy-dark) 0%, var(--navy) 100%);
    border-radius: 24px;
    padding: 24px;
    color: white;
    margin-bottom: 20px;
}

.summary-card h6 {
    font-weight: 700;
    font-size: .75rem;
    letter-spacing: 1.5px;
    opacity: 0.8;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,.15);
}

.summary-row:last-child {
    border-bottom: none;
    padding-top: 12px;
    margin-top: 4px;
}

.summary-label {
    font-size: .75rem;
    opacity: 0.8;
}

.summary-value {
    font-weight: 700;
    font-size: .85rem;
}

/* Tips Card */
.tips-card {
    background: var(--surface-2);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border);
}

.tips-card h6 {
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

.tips-card ul {
    padding-left: 18px;
    margin: 0;
}

.tips-card li {
    font-size: .75rem;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.tips-card li:last-child {
    margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .create-menu-wrapper {
        padding: 20px 16px;
    }
    .create-header h2 {
        font-size: 1.5rem;
    }
    .card-premium-body {
        padding: 24px;
    }
    .action-buttons {
        flex-direction: column;
    }
    .btn-premium-primary,
    .btn-premium-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- ================================================
     MARKUP (menggunakan semua field dan logika dari kode asli)
     ================================================ -->
<div class="create-menu-wrapper">

    <!-- Header -->
    <div class="create-header">
        <a href="{{ route('dashboard.restoran.menu.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2>Tambah <span>Menu Baru</span></h2>
        <p><i class="fas fa-plus-circle me-1"></i> Tambahkan menu makanan atau minuman ke restoran</p>
    </div>

    <div class="row g-4">
        <!-- Kiri: Form Tambah -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-plus-circle"></i> Form Menu Baru</h6>
                </div>
                <div class="card-premium-body">

                    {{-- NOTIFIKASI ERROR VALIDASI --}}
                    @if ($errors->any())
                        <div class="alert-error-premium">
                            <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Terjadi Kesalahan Input:</div>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert-error-premium">
                            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    {{-- Info Box --}}
                    <div class="info-box-premium">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-text">
                            Tambahkan menu baru untuk restoran hotel. Stok awal akan otomatis diset ke <strong>0</strong> dan dapat diupdate nanti.
                        </div>
                    </div>

                    <form action="{{ route('dashboard.restoran.menu.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <!-- Nama Menu -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-utensils"></i> Nama Menu
                                </label>
                                <input type="text"
                                       name="nama_menu"
                                       class="form-control-premium @error('nama_menu') is-invalid @enderror"
                                       value="{{ old('nama_menu') }}"
                                       placeholder="Contoh: Ayam Bakar Madu"
                                       required>
                                @error('nama_menu')
                                    <div class="invalid-feedback-premium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Harga -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-tag"></i> Harga Jual (Rp)
                                </label>
                                <input type="number"
                                       name="harga"
                                       id="harga_input"
                                       class="form-control-premium @error('harga') is-invalid @enderror"
                                       value="{{ old('harga') }}"
                                       placeholder="0"
                                       required>
                                @error('harga')
                                    <div class="invalid-feedback-premium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-folder"></i> Pilih Kategori
                                </label>
                                <select name="kategori_menu_id" class="form-control-premium @error('kategori_menu_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategori as $k)
                                        <option value="{{ $k->id }}" {{ old('kategori_menu_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_menu_id')
                                    <div class="invalid-feedback-premium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Awal -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-toggle-on"></i> Status Awal
                                </label>
                                <select name="status_menu_id" class="form-control-premium" required>
                                    @foreach($status as $s)
                                        <option value="{{ $s->id }}" {{ old('status_menu_id', 1) == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama_status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- FOTO MENU (dengan preview gambar) -->
                            <div class="col-12">
                                <label class="form-label-premium">
                                    <i class="fas fa-camera"></i> FOTO PRODUK MAKANAN
                                </label>
                                <input type="file" name="foto_menu" class="form-control-premium" accept="image/*" onchange="previewImage(this)">
                                <div class="form-text-premium mt-2" style="font-size: .65rem; color: var(--text-muted);">
                                    <i class="fas fa-info-circle"></i> Format: JPG, PNG, atau JPEG. Maksimal 2MB.
                                </div>
                                <div class="preview-img mt-2">
                                    <img id="img-preview" src="#" alt="Preview" style="display: none; width: 150px; height: 150px; object-fit: cover; border-radius: 14px; border: 2px solid var(--border);">
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label-premium">
                                    <i class="fas fa-align-left"></i> Deskripsi Menu
                                </label>
                                <textarea name="deskripsi"
                                          class="form-control-premium"
                                          rows="3"
                                          placeholder="Jelaskan rincian bahan atau rasa menu ini...">{{ old('deskripsi') }}</textarea>
                            </div>
                        </div>

                        {{-- INFO STOK --}}
                        <div class="info-box-premium mt-4" style="background: #fff3e0;">
                            <i class="fas fa-info-circle" style="color: var(--amber);"></i>
                            <div class="info-text" style="color: #92400e;">
                                <b>Info Stok:</b> Menu baru otomatis memiliki stok <b>0</b>. Silakan masuk ke menu <b>Stok</b> untuk menambah porsi.
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-primary">
                                <i class="fas fa-save"></i> Simpan Menu Baru
                            </button>
                            <a href="{{ route('dashboard.restoran.menu.index') }}" class="btn-premium-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Preview & Tips -->
        <div class="col-lg-4">
            <!-- Live Preview Card -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-eye"></i> Live Preview</h6>
                </div>
                <div class="card-premium-body">
                    <div class="text-center p-3 rounded-3" style="background: var(--surface-2); border: 1px solid var(--border); border-radius: 16px;">
                        <div class="text-muted small mb-2">Nama Menu</div>
                        <div class="fw-bold text-dark" id="previewNamaMenu" style="font-size: 1rem;">
                            Menu Baru
                        </div>
                        <div class="mt-3 pt-2 border-top">
                            <div class="text-muted small mb-1">Harga akan tampil:</div>
                            <div class="fw-bold text-success fs-4" id="previewHarga">
                                Rp 0
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Ringkasan (DIPERBAIKI) -->
            <div class="summary-card">
                <h6>
                    <i class="fas fa-chart-simple"></i>
                    Statistik Saat Ini
                </h6>
                <div class="summary-row">
                    <span class="summary-label">Total Menu</span>
                    <span class="summary-value">{{ $kategori->sum(fn($k) => $k->menus->count()) }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total Kategori</span>
                    <span class="summary-value">{{ $kategori->count() }}</span>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="tips-card">
                <h6>
                    <i class="fas fa-lightbulb"></i>
                    Tips Menambahkan Menu
                </h6>
                <ul>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Gunakan nama yang mudah diingat pelanggan</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Tentukan harga yang kompetitif</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Pilih kategori yang tepat untuk memudahkan pencarian</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Tambahkan deskripsi yang menggugah selera</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Setelah menu tersimpan, atur stok di halaman Stok Menu</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT (preview gambar dan preview harga/nama)
     ================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi Cards
    const cards = document.querySelectorAll('.card-premium, .summary-card, .tips-card');
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

    // Live Preview Nama Menu
    const namaInput = document.querySelector('input[name="nama_menu"]');
    const previewNama = document.getElementById('previewNamaMenu');
    if (namaInput && previewNama) {
        namaInput.addEventListener('input', function() {
            const value = this.value.trim();
            previewNama.textContent = value !== '' ? value : 'Menu Baru';
        });
    }

    // Live Preview Harga
    const hargaInput = document.getElementById('harga_input');
    const previewHarga = document.getElementById('previewHarga');
    if (hargaInput && previewHarga) {
        hargaInput.addEventListener('input', function() {
            const value = parseInt(this.value) || 0;
            previewHarga.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        });
    }
});

// Script untuk Preview Gambar (SAMA PERSIS dengan kode asli)
function previewImage(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection