@extends('dashboard.layouts.app')
@section('title', 'Tambah Pesanan')

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
.tambah-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.tambah-page-wrapper::before,
.tambah-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.tambah-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.tambah-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.tambah-page-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER & BREADCRUMB
   ============================================================ */
.tambah-header {
    margin-bottom: 32px;
}

.breadcrumb-custom {
    margin-bottom: 8px;
}

.breadcrumb-custom a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: var(--transition);
}

.breadcrumb-custom a:hover {
    color: var(--navy);
}

.breadcrumb-custom .active {
    color: var(--navy);
    font-weight: 600;
}

.tambah-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0;
    letter-spacing: -.03em;
}

.tambah-title span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.tambah-subtitle {
    color: var(--text-muted);
    font-size: 0.85rem;
    margin-top: 4px;
}

/* ============================================================
   CARD STYLE
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 32px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: var(--transition);
}

.card-premium:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.card-header-premium {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 20px 28px;
    border: none;
}

.card-header-premium h6 {
    color: white;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-body-premium {
    padding: 28px;
}

/* ============================================================
   FORM STYLES
   ============================================================ */
.form-group {
    margin-bottom: 24px;
}

.form-label-custom {
    display: block;
    margin-bottom: 8px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
}

.form-label-custom i {
    margin-right: 6px;
    width: 18px;
}

.form-label-custom.required::after {
    content: '*';
    color: var(--rose);
    margin-left: 4px;
}

.form-input-custom {
    width: 100%;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 12px 16px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: var(--transition);
    font-family: var(--font);
}

.form-input-custom:focus {
    border-color: var(--navy);
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
    background: white;
}

.form-select-custom {
    width: 100%;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 12px 16px;
    font-size: 0.85rem;
    font-weight: 500;
    font-family: var(--font);
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
}

.form-select-custom:focus {
    border-color: var(--navy);
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}

/* Item Menu Row */
.item-row {
    background: var(--surface-2);
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 12px;
    transition: var(--transition);
    border: 1px solid var(--border);
}

.item-row:hover {
    border-color: var(--navy);
    background: white;
}

.btn-add-item {
    background: linear-gradient(135deg, var(--emerald) 0%, #0d9488 100%);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 10px 20px;
    font-weight: 700;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    cursor: pointer;
}

.btn-add-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16,185,129,.3);
    color: white;
}

.btn-remove-item {
    background: rgba(225,29,72,.1);
    color: var(--rose);
    border: none;
    border-radius: 12px;
    padding: 10px;
    font-weight: 700;
    font-size: 0.75rem;
    width: 100%;
    transition: var(--transition);
    cursor: pointer;
}

.btn-remove-item:hover {
    background: var(--rose);
    color: white;
}

/* Action Buttons */
.btn-premium-submit {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 14px 32px;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    cursor: pointer;
}

.btn-premium-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,25,125,.3);
    color: white;
}

.btn-premium-outline {
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--text-muted);
    border-radius: 14px;
    padding: 14px 28px;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    cursor: pointer;
    text-decoration: none;
}

.btn-premium-outline:hover {
    border-color: var(--rose);
    color: var(--rose);
    transform: translateY(-2px);
}

/* Divider */
.divider-premium {
    margin: 24px 0;
    border: none;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border), transparent);
}

.form-hint {
    font-size: 0.7rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .tambah-page-wrapper {
        padding: 20px 16px;
    }
    .tambah-title {
        font-size: 1.3rem;
    }
    .card-body-premium {
        padding: 20px;
    }
    .btn-premium-submit, .btn-premium-outline {
        padding: 12px 20px;
        font-size: 0.75rem;
    }
}
</style>

<!-- ================================================
     MARKUP (menggunakan struktur data dari kode asli)
     ================================================ -->
<div class="tambah-page-wrapper">

    <!-- Header -->
    <div class="tambah-header">
        <div class="breadcrumb-custom">
            <a href="{{ route('dashboard.restoran.pesanan.index') }}">
                <i class="fas fa-arrow-left me-1"></i> Daftar Pesanan
            </a>
            <span class="text-muted mx-1">/</span>
            <span class="active">Tambah Pesanan</span>
        </div>
        <h1 class="tambah-title">Tambah <span>Pesanan Baru</span></h1>
        <div class="tambah-subtitle">
            <i class="fas fa-plus-circle me-1"></i> Buat pesanan baru untuk pelanggan restoran
        </div>
    </div>

    <!-- Form Card -->
    <div class="card-premium">
        <div class="card-header-premium">
            <h6>
                <i class="fas fa-receipt"></i>
                Form Pesanan
            </h6>
        </div>
        <div class="card-body-premium">
            <form action="{{ route('dashboard.restoran.pesanan.store') }}" method="POST" id="tambahPesananForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom required">
                                <i class="fas fa-user"></i> Pilih Pelanggan Terdaftar
                            </label>
                            <select name="user_id" class="form-select-custom" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['full_name'] }} ({{ $c['email'] }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-custom required">
                                <i class="fas fa-chair"></i> Nomor Meja
                            </label>
                            <input type="text" name="nomor_meja" class="form-input-custom" 
                                   placeholder="Contoh: 05, A1, VIP-01" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-custom">
                                <i class="fas fa-credit-card"></i> Metode Bayar
                            </label>
                            <select name="metode_pembayaran" class="form-select-custom">
                                <option value="Tunai">💵 Tunai</option>
                                <option value="Debit">💳 Debit</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="divider-premium">

                <!-- Daftar Menu -->
                <div class="form-group">
                    <label class="form-label-custom required">
                        <i class="fas fa-utensils"></i> Daftar Menu
                    </label>
                    <div id="item-container">
                        <div class="item-row row g-2">
                            <div class="col-md-7">
                                <select name="menu_ids[]" class="form-select-custom" required>
                                    <option value="">-- Pilih Menu --</option>
                                    @foreach($menus as $m)
                                        <option value="{{ $m->id }}">
                                            {{ $m->nama_menu }} (Rp {{ number_format($m->harga, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="jumlah[]" class="form-input-custom" 
                                       placeholder="Qty" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn-remove-item remove-item">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-add-item mt-3" id="add-item">
                        <i class="fas fa-plus-circle"></i>
                        + Tambah Menu
                    </button>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Klik "Tambah Menu" untuk menambahkan lebih dari satu item
                    </div>
                </div>

                <hr class="divider-premium">

                <!-- Action Buttons -->
                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('dashboard.restoran.pesanan.index') }}" class="btn-premium-outline">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn-premium-submit" id="submitBtn">
                        <i class="fas fa-save"></i>
                        💾 Simpan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT (sama persis dengan kode asli)
     ================================================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Logika tambah/hapus item dari kode asli, dengan sedikit peningkatan UX
    document.getElementById('add-item').addEventListener('click', function() {
        let container = document.getElementById('item-container');
        let row = document.querySelector('.item-row').cloneNode(true);
        // Reset nilai select dan input
        row.querySelector('select').value = "";
        row.querySelector('input').value = "";
        container.appendChild(row);
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            let button = e.target.classList.contains('remove-item') ? e.target : e.target.closest('.remove-item');
            if (document.querySelectorAll('.item-row').length > 1) {
                button.closest('.item-row').remove();
            } else {
                Swal.fire({
                    title: 'Tidak Dapat Menghapus',
                    text: 'Minimal harus ada satu item menu dalam pesanan.',
                    icon: 'warning',
                    confirmButtonColor: '#00197D',
                    confirmButtonText: 'Mengerti'
                });
            }
        }
    });

    // Optional: Validasi sebelum submit (sama seperti kode asli, form akan submit biasa)
    // Tidak mengubah cara submit asli.
</script>
@endsection