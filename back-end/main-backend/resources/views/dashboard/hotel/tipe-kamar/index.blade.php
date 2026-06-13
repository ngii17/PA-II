@extends('dashboard.layouts.app')
@section('title', 'Tipe Kamar')

@section('content')
{{-- ================================================================
     TIPE KAMAR & HARGA — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;
    --indigo:       #6366f1;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #94a3b8;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 20px 44px rgba(0,25,125,.14);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label { font-family: var(--font) !important; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.tipe-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}
.tipe-page-wrapper::before,
.tipe-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.tipe-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.tipe-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.tipe-page-wrapper > * {
    position: relative;
    /* z-index tidak diatur agar tidak memerangkap modal */
}

/* ============================================================
   HEADER
   ============================================================ */
.tipe-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}
.tipe-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.tipe-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.tipe-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}
.btn-navy-premium {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 13px 26px;
    font-weight: 700;
    font-size: .875rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all .3s ease;
    box-shadow: 0 8px 20px rgba(0,25,125,.25);
}
.btn-navy-premium:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(0,25,125,.32);
}

/* ============================================================
   STATS STRIP
   ============================================================ */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--surface);
    border-radius: 20px;
    padding: 20px 22px;
    box-shadow: var(--shadow-card);
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid var(--border);
    transition: var(--transition);
    opacity: 0;
    transform: translateY(20px);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.stat-icon.navy  { background: rgba(0,25,125,.08); color: var(--navy); }
.stat-icon.gold  { background: rgba(212,175,55,.12); color: var(--gold); }
.stat-icon.green { background: rgba(16,185,129,.1); color: var(--emerald); }
.stat-info .stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 3px;
}
.stat-info .stat-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
}

/* ============================================================
   ALERTS
   ============================================================ */
.alert-success-premium {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-left: 4px solid #10b981;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #065f46;
    font-weight: 600;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}
.alert-error-premium {
    background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
    border-left: 4px solid var(--rose);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #991b1b;
    font-weight: 600;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}
@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   TIPE CARDS GRID
   ============================================================ */
.tipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}
.tipe-card {
    background: var(--surface);
    border-radius: 24px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: var(--transition);
    opacity: 0;
    transform: translateY(20px);
}
.tipe-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-hover);
    border-color: #c0ceee;
}
.tipe-card-accent {
    height: 5px;
    background: linear-gradient(90deg, var(--navy), var(--navy-mid), var(--gold));
}
.tipe-card-body {
    padding: 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.tipe-nama {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--navy-dark);
    letter-spacing: -.02em;
    margin-bottom: 4px;
}
.tipe-harga {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--emerald);
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 2px;
}
.tipe-harga small {
    font-size: .72rem;
    color: var(--text-muted);
    font-weight: 600;
}
.tipe-divider {
    height: 1px;
    background: var(--border);
    margin: 16px 0;
}
.tipe-meta {
    display: flex;
    gap: 16px;
    margin-bottom: 14px;
}
.tipe-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .78rem;
    font-weight: 600;
    color: var(--text-muted);
}
.tipe-meta-item i {
    width: 22px; height: 22px;
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: .65rem;
    flex-shrink: 0;
}
.tipe-meta-item.cap i  { background: rgba(0,25,125,.08); color: var(--navy); }
.tipe-meta-item.unit i { background: rgba(16,185,129,.1); color: var(--emerald); }
.tipe-meta-item strong { color: var(--text-primary); }
.fasilitas-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 20px;
    flex: 1;
}
.fasilitas-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: .68rem;
    font-weight: 700;
    color: var(--text-primary);
}
.fasilitas-chip i { font-size: .55rem; color: var(--emerald); }
.fasilitas-more {
    display: inline-flex;
    align-items: center;
    background: rgba(0,25,125,.06);
    border: 1.5px solid #c0ceee;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: .68rem;
    font-weight: 700;
    color: var(--navy);
}
.tipe-card-actions {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    padding: 16px 24px 20px;
    border-top: 1px solid var(--border);
}
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 10px 0;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: .75rem;
    font-weight: 700;
    transition: var(--transition);
    text-decoration: none;
    font-family: var(--font) !important;
}
.btn-action-detail { background: rgba(99,102,241,.1); color: var(--indigo); }
.btn-action-edit   { background: rgba(245,158,11,.1); color: var(--amber);  }
.btn-action-delete { background: rgba(225,29,72,.1); color: var(--rose);   }
.btn-action-detail:hover,
.btn-action-edit:hover,
.btn-action-delete:hover {
    color: #fff;
    transform: translateY(-2px);
}
.btn-action-detail:hover { background: var(--indigo); }
.btn-action-edit:hover   { background: var(--amber);  }
.btn-action-delete:hover { background: var(--rose);   }
.empty-state {
    grid-column: 1/-1;
    text-align: center;
    padding: 72px 24px;
}
.empty-icon {
    width: 80px; height: 80px;
    background: var(--surface-2);
    border-radius: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
    margin-bottom: 20px;
    border: 2px dashed var(--border);
}
.empty-state h5 { font-weight: 800; margin-bottom: 8px; }

/* ============================================================
   MODAL DETAIL PREMIUM
   ============================================================ */
.modal-premium .modal-content {
    border-radius: 28px;
    border: none;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0,0,0,.2);
}
.modal-premium .modal-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 24px 28px;
    border: none;
    position: relative;
}
.modal-premium .modal-header::before {
    content: '';
    position: absolute;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    top: -40px; right: -30px;
}
.modal-premium .modal-title {
    font-weight: 800;
    color: #fff;
}
.modal-body-premium { padding: 28px; }
.modal-tipe-nama {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--navy-dark);
    text-align: center;
    margin-bottom: 4px;
}
.modal-tipe-harga {
    font-size: 2rem;
    font-weight: 800;
    color: var(--emerald);
    text-align: center;
    margin-bottom: 4px;
}
.modal-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin: 20px 0;
}
.modal-stat-card {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 14px;
    text-align: center;
}
.modal-stat-card .stat-label {
    font-size: .6rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    font-weight: 700;
    display: block;
    margin-bottom: 5px;
}
.modal-stat-card .stat-value {
    font-weight: 800;
    color: var(--navy-dark);
    font-size: 1rem;
}
.modal-section-label {
    font-size: .62rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.modal-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}
.modal-fasilitas-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-bottom: 20px;
}
.modal-deskripsi {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 16px;
    font-size: .83rem;
    color: var(--text-primary);
    font-weight: 500;
    line-height: 1.7;
    font-style: italic;
}
.modal-footer-custom {
    padding: 16px 24px 24px;
    border: none;
    display: flex;
    gap: 10px;
}
.btn-close-modal {
    flex: 1;
    padding: 14px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-weight: 700;
    transition: .25s;
}
.btn-close-modal:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .tipe-header h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
    .tipe-grid { grid-template-columns: 1fr; }
}
</style>

<div class="tipe-page-wrapper">

    {{-- HEADER --}}
    <div class="tipe-header">
        <div class="tipe-header-left">
            <h2 class="fw-800">Tipe Kamar <span>&amp; Harga</span></h2>
            <p>Kelola kategori dan tarif kamar Hotel Purnama.</p>
        </div>
        <a href="{{ route('dashboard.hotel.tipe-kamar.create') }}" class="btn-navy-premium">
            <i class="fas fa-plus-circle"></i> Tambah Tipe Baru
        </a>
    </div>

    {{-- STATISTIK --}}
    @php
        $totalTipe = $tipe->count();
        $totalUnit = $tipe->sum(fn($t) => $t->kamar->count());
        $avgHarga = $totalTipe > 0 ? 'Rp '.number_format($tipe->avg('harga'), 0, ',', '.') : '—';
    @endphp
    <div class="stats-strip">
        <div class="stat-card"><div class="stat-icon navy"><i class="fas fa-layer-group"></i></div><div class="stat-info"><div class="stat-number">{{ $totalTipe }}</div><div class="stat-label">Total Tipe</div></div></div>
        <div class="stat-card"><div class="stat-icon gold"><i class="fas fa-door-open"></i></div><div class="stat-info"><div class="stat-number">{{ $totalUnit }}</div><div class="stat-label">Total Unit Kamar</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fas fa-coins"></i></div><div class="stat-info"><div class="stat-number">{{ $avgHarga }}</div><div class="stat-label">Rata-rata Harga</div></div></div>
    </div>

    {{-- ALERT SUKSES & ERROR --}}
    @if(session('success'))
    <div class="alert-success-premium"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-error-premium"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
    @endif

    {{-- GRID TIPE KAMAR --}}
    <div class="tipe-grid">
        @forelse($tipe as $t)
        @php
            $fasilitasArr = array_map('trim', explode(',', $t->fasilitas));
            $showMax = 3;
            $visible = array_slice($fasilitasArr, 0, $showMax);
            $more = count($fasilitasArr) - $showMax;
        @endphp
        <div class="tipe-card">
            <div class="tipe-card-accent"></div>
            <div class="tipe-card-body">
                <div class="tipe-nama">{{ $t->nama_tipe }}</div>
                <div class="tipe-harga">Rp {{ number_format($t->harga, 0, ',', '.') }} <small>/malam</small></div>
                <div class="tipe-divider"></div>
                <div class="tipe-meta">
                    <div class="tipe-meta-item cap"><i class="fas fa-users"></i> <span>Kapasitas: <strong>{{ $t->kapasitas }} Orang</strong></span></div>
                    <div class="tipe-meta-item unit"><i class="fas fa-door-open"></i> <span>Unit: <strong>{{ $t->kamar->count() }}</strong></span></div>
                </div>
                <div class="fasilitas-wrap">
                    @foreach($visible as $f)
                    <span class="fasilitas-chip"><i class="fas fa-check"></i> {{ $f }}</span>
                    @endforeach
                    @if($more > 0)
                    <span class="fasilitas-more">+{{ $more }} lainnya</span>
                    @endif
                </div>
            </div>
            <div class="tipe-card-actions">
                <button class="btn-action btn-action-detail" data-bs-toggle="modal" data-bs-target="#detailModal{{ $t->id }}"><i class="fas fa-eye"></i> Detail</button>
                <a href="{{ route('dashboard.hotel.tipe-kamar.edit', $t->id) }}" class="btn-action btn-action-edit"><i class="fas fa-edit"></i> Edit</a>
                <button class="btn-action btn-action-delete" onclick="konfirmasiHapusTipe({{ $t->id }}, '{{ addslashes($t->nama_tipe) }}')"><i class="fas fa-trash"></i> Hapus</button>
                <form id="form-hapus-{{ $t->id }}" action="{{ route('dashboard.hotel.tipe-kamar.destroy', $t->id) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-layer-group"></i></div>
            <h5>Belum Ada Tipe Kamar</h5>
            <p>Tambahkan tipe kamar pertama untuk Hotel Purnama.</p>
            <a href="{{ route('dashboard.hotel.tipe-kamar.create') }}" class="btn-navy-premium"><i class="fas fa-plus-circle"></i> Tambah Tipe Kamar</a>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL DETAIL UNTUK SETIAP TIPE (diletakkan di luar wrapper) --}}
@foreach($tipe as $t)
@php $fasilitasArr = array_map('trim', explode(',', $t->fasilitas)); @endphp
<div class="modal fade modal-premium" id="detailModal{{ $t->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Rincian Tipe Kamar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-premium">
                <div class="text-center mb-4">
                    <div class="modal-tipe-nama">{{ $t->nama_tipe }}</div>
                    <div class="modal-tipe-harga">Rp {{ number_format($t->harga, 0, ',', '.') }} <small>/malam</small></div>
                </div>
                <div class="modal-stats-grid">
                    <div class="modal-stat-card"><span class="stat-label">Kapasitas</span><div class="stat-value"><i class="fas fa-users me-1"></i> {{ $t->kapasitas }} Orang</div></div>
                    <div class="modal-stat-card"><span class="stat-label">Kamar Aktif</span><div class="stat-value"><i class="fas fa-door-open me-1"></i> {{ $t->kamar->count() }} Unit</div></div>
                </div>
                <div class="modal-section-label">Fasilitas Kamar</div>
                <div class="modal-fasilitas-wrap">
                    @foreach($fasilitasArr as $f)
                    <span class="fasilitas-chip"><i class="fas fa-check"></i> {{ $f }}</span>
                    @endforeach
                </div>
                <div class="modal-section-label">Deskripsi</div>
                <div class="modal-deskripsi">{{ $t->deskripsi ?? 'Tidak ada deskripsi tambahan untuk tipe kamar ini.' }}</div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animasi stat card
    document.querySelectorAll('.stat-card').forEach((card, i) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + i * 80);
    });
    // Animasi tipe card
    document.querySelectorAll('.tipe-card').forEach((card, i) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 250 + i * 80);
    });
    // Counter animasi untuk angka statistik (hanya yang bukan format Rp)
    document.querySelectorAll('.stat-number').forEach(el => {
        const raw = el.textContent.trim();
        if (raw.startsWith('Rp')) return;
        const target = parseInt(raw);
        if (isNaN(target) || target === 0) return;
        let current = 0;
        const step = Math.ceil(target / 20);
        const iv = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(iv);
        }, 40);
    });
});

function konfirmasiHapusTipe(id, nama) {
    Swal.fire({
        title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Hapus Tipe Kamar?</span>',
        html: `<span style="font-family:Plus Jakarta Sans;">Tipe <strong style="color:#e11d48;">${nama}</strong> akan dihapus. Pastikan tidak ada unit kamar aktif di dalamnya.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#00197D',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) document.getElementById('form-hapus-' + id).submit();
    });
}
</script>
@endsection