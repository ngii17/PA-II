@extends('dashboard.layouts.app')
@section('title', 'Edit Menu Event')

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

.edit-event-wrapper > * {
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

.form-label-premium.primary {
    color: var(--navy);
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

.form-control-premium[readonly] {
    background: var(--surface-2);
    color: var(--text-muted);
    cursor: not-allowed;
}

select.form-control-premium {
    cursor: pointer;
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

/* Price Summary Card */
.price-summary {
    background: linear-gradient(135deg, var(--surface-2) 0%, #fff9e8 100%);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid var(--border);
}

.price-summary-title {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.price-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed var(--border);
}

.price-row:last-child {
    border-bottom: none;
    padding-top: 12px;
    margin-top: 4px;
}

.price-label {
    font-size: .8rem;
    color: var(--text-muted);
}

.price-value {
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-primary);
}

.price-value.discount {
    color: var(--rose);
}

.price-value.event {
    color: var(--emerald);
    font-size: 1rem;
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

/* Info Card */
.info-card {
    background: var(--surface-2);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border);
    margin-bottom: 20px;
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

.info-card ul {
    padding-left: 18px;
    margin: 0;
}

.info-card li {
    font-size: .75rem;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.info-card li:last-child {
    margin-bottom: 0;
}

/* Badge Status */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .68rem;
}
.badge-status.aktif { background: #dcfce7; color: #15803d; }
.badge-status.nonaktif { background: #fee2e2; color: #b91c1c; }

.form-text-premium {
    margin-top: 6px;
    font-size: .7rem;
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .edit-event-wrapper {
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
}
</style>

<!-- ================================================
     MARKUP (menggunakan data dan field dari kode asli)
     ================================================ -->
<div class="edit-event-wrapper">

    <!-- Header -->
    <div class="edit-header">
        <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2>Edit <span>Menu Event</span></h2>
        <p><i class="fas fa-edit me-1"></i> Ubah harga khusus dan status promo menu event</p>
    </div>

    <div class="row g-4">
        <!-- Kiri: Form Edit -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-pen-alt"></i> Form Edit Menu Event</h6>
                </div>
                <div class="card-premium-body">

                    <!-- Info Box -->
                    <div class="info-box-premium">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-text">
                            Ubah harga khusus dan status promo untuk menu ini. Perubahan akan langsung tampil di halaman pemesanan.
                        </div>
                    </div>

                    <form action="{{ route('dashboard.restoran.menu-event.update', $menuEvent->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Nama Event (Read Only) - sama persis kode asli -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium">
                                    <i class="fas fa-calendar-alt"></i> NAMA EVENT
                                </label>
                                <input type="text" class="form-control-premium" value="{{ $menuEvent->nama_event }}" readonly>
                            </div>

                            <!-- Nama Menu (Read Only) - sama persis kode asli -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium">
                                    <i class="fas fa-utensils"></i> NAMA MENU
                                </label>
                                <input type="text" class="form-control-premium" value="{{ $menuEvent->nama_menu }}" readonly>
                            </div>

                            <!-- Input Harga Khusus (sama persis kode asli) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium primary">
                                    <i class="fas fa-star"></i> HARGA KHUSUS EVENT (RP)
                                </label>
                                <input type="number" 
                                       name="harga_khusus" 
                                       id="harga_khusus"
                                       class="form-control-premium" 
                                       value="{{ (int)$menuEvent->harga_khusus }}" 
                                       required>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Tentukan harga baru untuk menu ini selama event.
                                </div>
                            </div>

                            <!-- Input Status Aktif (sama persis kode asli) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium">
                                    <i class="fas fa-toggle-on"></i> STATUS AKTIF
                                </label>
                                <select name="is_active" class="form-control-premium" required>
                                    <option value="1" {{ $menuEvent->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$menuEvent->is_active ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons (sama persis kode asli) -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-warning">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="btn-premium-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Ringkasan & Informasi (opsional, tidak mengganggu data asli) -->
        <div class="col-lg-4">
            <!-- Price Summary Card (hanya untuk preview, tidak dikirim ke server) -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-chart-line"></i> Ringkasan Harga</h6>
                </div>
                <div class="card-premium-body">
                    @php
                        $hargaNormal = $menuEvent->menu->harga ?? 0;
                        $hargaEvent = $menuEvent->harga_khusus ?? 0;
                        $diskon = $hargaNormal - $hargaEvent;
                        $persen = $hargaNormal > 0 ? round(($diskon / $hargaNormal) * 100) : 0;
                    @endphp
                    <div class="price-summary">
                        <div class="price-summary-title">
                            <i class="fas fa-calculator"></i> Perbandingan Harga
                        </div>
                        <div class="price-row">
                            <span class="price-label">Harga Normal</span>
                            <span class="price-value">Rp {{ number_format($hargaNormal, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">Harga Event</span>
                            <span class="price-value event" id="previewHargaEvent">
                                Rp {{ number_format($hargaEvent, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">Diskon</span>
                            <span class="price-value discount" id="previewDiskon">
                                {{ $diskon >= 0 ? '-' : '+' }} Rp {{ number_format(abs($diskon), 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">Persentase Diskon</span>
                            <span class="price-value" id="previewPersen">
                                {{ $persen }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card: Informasi Event -->
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-lightbulb"></i> Informasi Event</h6>
                </div>
                <div class="card-premium-body">
                    <div class="info-card">
                        <h6><i class="fas fa-calendar-week"></i> Detail Event</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Nama Event</span>
                            <span class="fw-bold small">{{ $menuEvent->nama_event ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Menu</span>
                            <span class="fw-bold small">{{ $menuEvent->nama_menu ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Status Saat Ini</span>
                            <span class="badge-status {{ $menuEvent->is_active ? 'aktif' : 'nonaktif' }}">
                                <i class="fas {{ $menuEvent->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                {{ $menuEvent->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-card" style="margin-top: 16px; margin-bottom: 0;">
                        <h6><i class="fas fa-tips"></i> Tips Mengelola Menu Event</h6>
                        <ul>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Harga event harus lebih rendah dari harga normal</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Nonaktifkan promo jika event sudah berakhir</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Diskon besar bisa meningkatkan penjualan</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Pastikan stok menu mencukupi untuk event</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT (preview harga dinamis, tidak mengganggu submit)
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

    // Live Preview Harga Event & Diskon (hanya untuk UI, tidak mengganggu form)
    const hargaNormal = {{ $menuEvent->menu->harga ?? 0 }};
    const hargaInput = document.getElementById('harga_khusus');
    const previewHarga = document.getElementById('previewHargaEvent');
    const previewDiskon = document.getElementById('previewDiskon');
    const previewPersen = document.getElementById('previewPersen');

    if (hargaInput) {
        function updatePreview() {
            let hargaEvent = parseInt(hargaInput.value) || 0;
            let diskon = hargaNormal - hargaEvent;
            let persen = hargaNormal > 0 ? Math.round((diskon / hargaNormal) * 100) : 0;
            
            previewHarga.innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(hargaEvent);
            
            if (diskon >= 0) {
                previewDiskon.innerHTML = '- Rp ' + new Intl.NumberFormat('id-ID').format(diskon);
                previewDiskon.style.color = '#10b981';
            } else {
                previewDiskon.innerHTML = '+ Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(diskon));
                previewDiskon.style.color = '#e11d48';
            }
            
            previewPersen.innerHTML = Math.abs(persen) + '%';
            if (persen < 0) {
                previewPersen.style.color = '#e11d48';
            } else {
                previewPersen.style.color = 'var(--text-primary)';
            }
        }
        hargaInput.addEventListener('input', updatePreview);
    }
});
</script>

@endsection