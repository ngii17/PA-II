@extends('dashboard.layouts.app')
@section('title', 'Edit Informasi Menu')

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
.edit-menu-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.edit-menu-wrapper::before,
.edit-menu-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.edit-menu-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.edit-menu-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.edit-menu-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER
   ============================================================ */
.edit-header {
    margin-bottom: 32px;
}

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

.edit-header .back-link:hover {
    color: var(--navy);
    transform: translateX(-4px);
}

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

.edit-header p {
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

textarea.form-control-premium {
    resize: vertical;
    min-height: 100px;
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
    margin-bottom: 24px;
}

.image-preview-label {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 12px;
    display: block;
}

.image-preview-container {
    display: flex;
    align-items: flex-start;
    gap: 24px;
    flex-wrap: wrap;
}

.current-image {
    text-align: center;
}

.current-image img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 20px;
    border: 2px solid var(--border);
    background: var(--surface-2);
}

.image-upload-area {
    flex: 1;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--border);
}

.btn-premium-warning {
    background: linear-gradient(135deg, var(--amber) 0%, #d97706 100%);
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

.btn-premium-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245,158,11,.3);
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

.summary-value.highlight {
    color: var(--gold);
    font-size: 1rem;
}

/* Note Card */
.note-card {
    background: var(--surface-2);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border);
    margin-top: 20px;
}

.note-card h6 {
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

.note-card ul {
    padding-left: 18px;
    margin: 0;
}

.note-card li {
    font-size: .75rem;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.note-card li:last-child {
    margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .edit-menu-wrapper {
        padding: 20px 16px;
    }
    .edit-header h2 {
        font-size: 1.5rem;
    }
    .card-premium-body {
        padding: 24px;
    }
    .action-buttons {
        flex-direction: column;
    }
    .btn-premium-warning,
    .btn-premium-secondary {
        width: 100%;
        justify-content: center;
    }
    .image-preview-container {
        flex-direction: column;
    }
}
</style>

@php
    // Cek asal halaman agar navigasi kembali konsisten (SAMA PERSIS dengan kode asli)
    $isDariStok = isset($from) && $from == 'stok';

    $routeKembali = $isDariStok
        ? route('dashboard.restoran.stok')
        : route('dashboard.restoran.menu.index');

    $labelKembali = $isDariStok
        ? 'Manajemen Stok'
        : 'Daftar Menu';
@endphp

<!-- ================================================
     MARKUP (menggunakan semua field dari kode asli)
     ================================================ -->
<div class="edit-menu-wrapper">

    <!-- Header -->
    <div class="edit-header">
        <a href="{{ $routeKembali }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Kembali ke {{ $labelKembali }}
        </a>
        <h2>Edit <span>Menu</span></h2>
        <p><i class="fas fa-edit me-1"></i> Ubah informasi menu restoran</p>
    </div>

    <div class="row g-4">
        <!-- Kiri: Form Edit -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-pen-alt"></i> Form Edit Menu</h6>
                </div>
                <div class="card-premium-body">

                    <!-- Error Alerts (dari validasi Laravel) -->
                    @if($errors->any())
                    <div class="alert-error-premium">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Info Box -->
                    <div class="info-box-premium">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-text">
                            Ubah informasi menu sesuai kebutuhan. Perubahan akan langsung tampil di halaman pemesanan restoran.
                        </div>
                    </div>

                    {{-- PERBAIKAN: enctype agar file gambar bisa terkirim (SAMA PERSIS) --}}
                    <form action="{{ route('dashboard.restoran.menu.update', $menu->id) }}?from={{ $from ?? '' }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Hidden input untuk mengetahui asal halaman (SAMA PERSIS) --}}
                        @if($isDariStok)
                            <input type="hidden" name="dari_halaman_stok" value="true">
                        @endif

                        {{-- FOTO MENU (BAGIAN KRUSIAL) --}}
                        <div class="image-preview-area">
                            <label class="image-preview-label">
                                <i class="fas fa-camera"></i> Foto Produk Makanan
                            </label>
                            <div class="image-preview-container">
                                <div class="current-image">
                                    <div class="text-muted small mb-2">Foto Saat Ini:</div>
                                    <!-- Baris 378 yang diperbaiki: -->
<img id="img-preview"
     src="{{ str_starts_with($menu->foto_menu, 'http') ? $menu->foto_menu : asset('storage/' . $menu->foto_menu) }}"
     alt="Preview Menu">
                                </div>
                                <div class="image-upload-area">
                                    <div class="text-muted small mb-2">Ganti Foto Baru:</div>
                                    <input type="file" name="foto_menu" class="form-control-premium" accept="image/*" onchange="previewImage(this)">
                                    <div class="form-text-premium mt-2" style="font-size: .65rem; color: var(--text-muted);">
                                        <i class="fas fa-info-circle"></i> Format: JPG, PNG, atau JPEG. Maksimal 2MB.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Nama Menu -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-utensils"></i> Nama Menu
                                </label>
                                <input type="text"
                                       name="nama_menu"
                                       class="form-control-premium"
                                       value="{{ old('nama_menu', $menu->nama_menu) }}"
                                       required>
                            </div>

                            <!-- Harga -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-tag"></i> Harga Jual (Rp)
                                </label>
                                <input type="number"
                                       name="harga"
                                       class="form-control-premium"
                                       value="{{ old('harga', (int) $menu->harga) }}"
                                       min="0"
                                       required>
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-folder"></i> Kategori Menu
                                </label>
                                <select name="kategori_menu_id" class="form-control-premium" required>
                                    @foreach($kategori as $k)
                                        <option value="{{ $k->id }}"
                                            {{ $menu->kategori_menu_id == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-toggle-on"></i> Status Ketersediaan
                                </label>
                                <select name="status_menu_id" class="form-control-premium" required>
                                    @foreach($status as $s)
                                        <option value="{{ $s->id }}"
                                            {{ $menu->status_menu_id == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama_status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Stok -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-box"></i> Stok Menu
                                </label>
                                <input type="number"
                                       name="stok"
                                       class="form-control-premium"
                                       value="{{ old('stok', $menu->stok) }}"
                                       min="0"
                                       required>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label-premium">
                                    <i class="fas fa-align-left"></i> Deskripsi Singkat
                                </label>
                                <textarea name="deskripsi"
                                          class="form-control-premium"
                                          rows="3">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-warning">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ $routeKembali }}" class="btn-premium-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Ringkasan & Tips -->
        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="summary-card">
                <h6>
                    <i class="fas fa-chart-line"></i>
                    Informasi Menu
                </h6>
                <div class="summary-row">
                    <span class="summary-label">ID Menu</span>
                    <span class="summary-value">#{{ $menu->id }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Nama Menu</span>
                    <span class="summary-value">{{ $menu->nama_menu }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Kategori</span>
                    <span class="summary-value">{{ $menu->kategori->nama_kategori ?? '-' }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Harga Saat Ini</span>
                    <span class="summary-value highlight">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Stok Saat Ini</span>
                    <span class="summary-value">{{ $menu->stok }} stok</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Status</span>
                    <span class="summary-value">{{ $menu->status->nama_status ?? '-' }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Dibuat pada</span>
                    <span class="summary-value">{{ $menu->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Terakhir update</span>
                    <span class="summary-value">{{ $menu->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            <!-- Note Card -->
            <div class="note-card">
                <h6>
                    <i class="fas fa-lightbulb"></i>
                    Catatan Penting
                </h6>
                <ul>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Harga akan langsung diperbarui di halaman pemesanan</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Ubah stok jika terjadi penambahan atau pengurangan</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Menu dengan status "Nonaktif" tidak akan tampil</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Deskripsi membantu pelanggan memahami menu</li>
                </ul>
            </div>

            <!-- Preview Card -->
            <div class="card-premium mt-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-eye"></i> Live Preview Harga</h6>
                </div>
                <div class="card-premium-body">
                    <div class="text-center p-3 rounded-3" style="background: var(--surface-2);">
                        <div class="text-muted small mb-2">Harga akan tampil seperti ini:</div>
                        <div class="fw-bold text-success fs-4" id="previewHarga">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">/ {{ $menu->kategori->nama_kategori ?? 'menu' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT (preview gambar dan preview harga)
     ================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi Cards
    const cards = document.querySelectorAll('.card-premium, .summary-card, .note-card');
    cards.forEach((card, idx) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (idx * 80));
    });
    
    const header = document.querySelector('.edit-header');
    if (header) {
        header.style.opacity = '0';
        header.style.transform = 'translateY(-10px)';
        header.style.transition = 'all 0.4s ease';
        setTimeout(() => {
            header.style.opacity = '1';
            header.style.transform = 'translateY(0)';
        }, 50);
    }

    // Live Preview Harga
    const hargaInput = document.querySelector('input[name="harga"]');
    const previewElement = document.getElementById('previewHarga');
    
    if (hargaInput && previewElement) {
        hargaInput.addEventListener('input', function() {
            const value = parseInt(this.value) || 0;
            previewElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        });
    }

    // Live Preview Nama Menu di summary (opsional)
    const namaInput = document.querySelector('input[name="nama_menu"]');
    const summaryNama = document.querySelector('.summary-card .summary-value');
    
    if (namaInput && summaryNama && summaryNama.parentElement.previousElementSibling?.innerText === 'Nama Menu') {
        namaInput.addEventListener('input', function() {
            const value = this.value.trim();
            if (value !== '') {
                summaryNama.textContent = value;
            } else {
                summaryNama.textContent = '(kosong)';
            }
        });
    }
});

// Script untuk Live Preview Gambar (SAMA PERSIS dengan kode asli)
function previewImage(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection