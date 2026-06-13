@extends('dashboard.layouts.app')
@section('title', 'Tambah Kategori Menu')

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
.create-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
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

.create-page-wrapper > * {
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

/* Preview Card */
.preview-card {
    background: linear-gradient(145deg, var(--surface) 0%, var(--surface-2) 100%);
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

/* Responsive */
@media (max-width: 768px) {
    .create-page-wrapper {
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

.form-text-premium {
    font-size: .7rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 5px;
}
</style>

<!-- ================================================
     MARKUP (menggunakan data dan logika dari kode asli)
     ================================================ -->
<div class="create-page-wrapper">

    <!-- Header -->
    <div class="create-header">
        <a href="{{ route('dashboard.restoran.kategori.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2>Tambah <span>Kategori</span></h2>
        <p><i class="fas fa-plus-circle me-1"></i> Buat kategori menu baru untuk restoran</p>
    </div>

    <div class="row g-4">
        <!-- Kiri: Form Tambah -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-folder-plus"></i> Form Kategori Baru</h6>
                </div>
                <div class="card-premium-body">

                    <!-- Error Alerts (sama persis dengan kode asli) -->
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
                            Kategori akan membantu mengelompokkan menu restoran. Contoh: Makanan Pembuka, Menu Utama, Minuman, Dessert, dll.
                        </div>
                    </div>

                    <form action="{{ route('dashboard.restoran.kategori.store') }}" method="POST" id="createKategoriForm">
                        @csrf

                        <!-- Nama Kategori (sama persis dengan kode asli) -->
                        <div class="mb-4">
                            <label class="form-label-premium">
                                <i class="fas fa-tag"></i> Nama Kategori
                            </label>
                            <input type="text" 
                                   name="nama_kategori" 
                                   id="nama_kategori"
                                   class="form-control-premium @error('nama_kategori') is-invalid @enderror" 
                                   value="{{ old('nama_kategori') }}" 
                                   placeholder="Contoh: Makanan Pembuka, Menu Utama, Minuman..."
                                   autocomplete="off"
                                   required>
                            @error('nama_kategori')
                                <div class="invalid-feedback-premium">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Deskripsi (sama persis dengan kode asli) -->
                        <div class="mb-4">
                            <label class="form-label-premium">
                                <i class="fas fa-align-left"></i> Deskripsi (Opsional)
                            </label>
                            <textarea name="deskripsi" 
                                      class="form-control-premium" 
                                      rows="4"
                                      placeholder="Tambahkan penjelasan tentang kategori ini...">{{ old('deskripsi') }}</textarea>
                            <div class="form-text-premium mt-2">
                                <i class="fas fa-info-circle"></i> 
                                Deskripsi akan membantu pelanggan memahami jenis menu dalam kategori ini.
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-primary">
                                <i class="fas fa-save"></i> Simpan Kategori
                            </button>
                            <a href="{{ route('dashboard.restoran.kategori.index') }}" class="btn-premium-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Tips & Preview -->
        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-eye"></i> Live Preview</h6>
                </div>
                <div class="card-premium-body">
                    <div class="preview-card">
                        <div class="preview-label">Akan tampil seperti ini:</div>
                        <div class="preview-value" id="previewNamaKategori">
                            Nama Kategori
                        </div>
                        <div class="text-muted small mt-3">
                            <i class="fas fa-utensils"></i> Menu dalam kategori ini
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-lightbulb"></i> Tips Membuat Kategori</h6>
                </div>
                <div class="card-premium-body">
                    <div class="tips-card">
                        <ul>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Gunakan nama yang singkat & jelas</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Kelompokkan menu dengan jenis yang sama</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Maksimal 10-15 kategori agar tidak membingungkan</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Tambahkan deskripsi untuk membantu pelanggan</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Contoh kategori: Makanan, Minuman, Snack, Dessert</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT
     ================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi Cards
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

    // Live Preview Nama Kategori
    const namaInput = document.getElementById('nama_kategori');
    const previewElement = document.getElementById('previewNamaKategori');
    
    if (namaInput && previewElement) {
        namaInput.addEventListener('input', function() {
            const value = this.value.trim();
            if (value !== '') {
                previewElement.textContent = value;
                previewElement.style.background = 'var(--navy)';
                previewElement.style.color = 'white';
                previewElement.style.borderColor = 'var(--navy)';
            } else {
                previewElement.textContent = 'Nama Kategori';
                previewElement.style.background = 'white';
                previewElement.style.color = 'var(--navy)';
                previewElement.style.borderColor = 'var(--border)';
            }
        });
    }
});

// Validasi client-side (sama seperti kode asli)
document.getElementById('createKategoriForm')?.addEventListener('submit', function(e) {
    const namaInput = document.getElementById('nama_kategori');
    if (namaInput && !namaInput.value.trim()) {
        e.preventDefault();
        alert('Nama kategori tidak boleh kosong!');
        namaInput.focus();
    }
});
</script>

@endsection