@extends('dashboard.layouts.app')

@section('title', session('user.role') === 'admin' ? 'Konfigurasi Tema Aplikasi' : 'Konfigurasi Event Restoran')

@section('content')
<style>
/* ============================================================
   ROOT VARIABLES (KONSISTEN DENGAN HALAMAN SEBELUMNYA)
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
select.form-control-premium {
    cursor: pointer;
}
.form-text-premium {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
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
    .btn-premium-primary,
    .btn-premium-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>

@php
    // Gradients untuk preview
    $gradients = [
        'default'  => 'linear-gradient(135deg, #00197D, #1A3A9C)',
        'imlek'    => 'linear-gradient(135deg, #8D0000, #FFB300)',
        'lebaran'  => 'linear-gradient(135deg, #1B5E20, #FBC02D)',
        'valentine'=> 'linear-gradient(135deg, #880E4F, #F48FB1)',
        'hut_ri'   => 'linear-gradient(135deg, #C62828, #F5F5F5)',
        'natal'    => 'linear-gradient(135deg, #0A5F38, #B71C1C)',
    ];
    $bgGradient = $gradients[$event->event_code] ?? 'linear-gradient(135deg, #434343, #000000)';
    
    // Tentukan route kembali dan route update berdasarkan role
    $backRoute = session('user.role') === 'admin' 
        ? route('dashboard.event.index') 
        : route('dashboard.restoran.event');
    $updateRoute = session('user.role') === 'admin'
        ? route('dashboard.event.update', $event->id)
        : route('dashboard.restoran.event.update', $event->id);
    
    // Judul dan deskripsi
    $pageTitle = session('user.role') === 'admin' ? 'Konfigurasi Tema Aplikasi' : 'Konfigurasi Event Restoran';
    $pageIcon = session('user.role') === 'admin' ? 'fa-palette' : 'fa-calendar-alt';
    $pageDesc = session('user.role') === 'admin' 
        ? 'Ubah pengaturan tema visual global untuk seluruh aplikasi' 
        : 'Ubah pengaturan tema visual untuk event restoran';
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
        <!-- Kiri: Form -->
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
                                Konfigurasikan tema aplikasi. Hanya SATU tema yang bisa aktif sebagai tema utama pada satu waktu.
                            @else
                                Konfigurasikan event/tema restoran. Hanya SATU event yang bisa aktif sebagai tema utama pada satu waktu.
                            @endif
                        </div>
                    </div>

                    <form action="{{ $updateRoute }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Nama -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas {{ session('user.role') === 'admin' ? 'fa-palette' : 'fa-calendar-alt' }}"></i> 
                                    Nama {{ session('user.role') === 'admin' ? 'Tema' : 'Event' }}
                                </label>
                                <input type="text" name="nama_event" class="form-control-premium" value="{{ old('nama_event', $event->nama_event) }}" required>
                            </div>

                            <!-- Status Aktif -->
                            <div class="col-md-6">
                                <label class="form-label-premium">
                                    <i class="fas fa-toggle-on"></i> Status Aktif
                                </label>
                                <select name="is_active" class="form-control-premium" required>
                                    <option value="1" {{ $event->is_active ? 'selected' : '' }}>
                                        ✅ Aktifkan Sebagai Tema Utama
                                    </option>
                                    <option value="0" {{ !$event->is_active ? 'selected' : '' }}>
                                        ❌ Nonaktifkan
                                    </option>
                                </select>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Hanya satu {{ session('user.role') === 'admin' ? 'tema' : 'event' }} yang bisa aktif sebagai tema utama.
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label-premium">
                                    <i class="fas fa-align-left"></i> Deskripsi / Keterangan
                                </label>
                                <textarea name="deskripsi" class="form-control-premium" rows="4">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-primary">
                                <i class="fas fa-save"></i> Simpan Konfigurasi
                            </button>
                            <a href="{{ $backRoute }}" class="btn-premium-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Preview & Info -->
        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card-premium mb-4">
                <div class="card-premium-header">
                    <h6><i class="fas fa-eye"></i> Live Preview Tema</h6>
                </div>
                <div class="card-premium-body">
                    <div class="preview-card">
                        <div class="preview-label">Preview Header</div>
                        <div style="height: 80px; background: {{ $bgGradient }}; border-radius: 16px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center;">
                            <span class="text-white small fw-bold" style="letter-spacing: 2px;">PREVIEW</span>
                        </div>
                        <div class="preview-value" id="previewNamaEvent">
                            {{ $event->nama_event }}
                        </div>
                        <div class="mt-2">
                            <span class="badge-status {{ $event->is_active ? 'aktif' : 'nonaktif' }}">
                                <i class="fas {{ $event->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                {{ $event->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card-premium">
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
                                <span class="fw-bold small">{{ $event->primary_color ?? '#00197D' }}</span>
                                <div style="width: 20px; height: 20px; background: {{ $event->primary_color ?? '#00197D' }}; border-radius: 6px; border: 1px solid #ddd;"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Warna Sekunder</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold small">{{ $event->secondary_color ?? '#D4AF37' }}</span>
                                <div style="width: 20px; height: 20px; background: {{ $event->secondary_color ?? '#D4AF37' }}; border-radius: 6px; border: 1px solid #ddd;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card" style="margin-top: 16px;">
                        <h6><i class="fas fa-tips"></i> Tips Mengelola</h6>
                        <ul>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Aktifkan {{ session('user.role') === 'admin' ? 'tema' : 'event' }} yang sedang berlangsung</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Hanya satu yang bisa aktif sebagai tema utama</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Yang nonaktif masih bisa diaktifkan kembali nanti</li>
                            <li><i class="fas fa-check-circle text-success me-1"></i> Perubahan tampilan akan langsung terlihat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    
    // Header animation
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

    // Live Preview Nama Event
    const namaInput = document.querySelector('input[name="nama_event"]');
    const previewElement = document.getElementById('previewNamaEvent');
    if (namaInput && previewElement) {
        namaInput.addEventListener('input', function() {
            const value = this.value.trim();
            previewElement.textContent = value !== '' ? value : '{{ $event->nama_event }}';
        });
    }
});
</script>
@endsection