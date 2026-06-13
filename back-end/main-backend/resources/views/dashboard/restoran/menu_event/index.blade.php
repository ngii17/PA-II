@extends('dashboard.layouts.app')

@section('title', 'Daftar Menu Event')

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
.menuevent-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.menuevent-page-wrapper::before,
.menuevent-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.menuevent-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.menuevent-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.menuevent-page-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER
   ============================================================ */
.menuevent-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}

.menuevent-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}

.menuevent-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.menuevent-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}

/* Button Premium */
.btn-premium-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 12px 26px;
    font-weight: 700;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
}

.btn-premium-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,25,125,.3);
    color: white;
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
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }

.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.stat-icon.navy  { background: rgba(0,25,125,.08);   color: var(--navy); }
.stat-icon.gold  { background: rgba(212,175,55,.12);  color: var(--gold); }
.stat-icon.green { background: rgba(16,185,129,.1);   color: var(--emerald); }
.stat-icon.amber { background: rgba(245,158,11,.1);   color: var(--amber); }

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
   ALERT SUCCESS
   ============================================================ */
.alert-success-premium {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-left: 4px solid #10b981;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #065f46;
    font-weight: 600;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}

@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   TABLE CARD
   ============================================================ */
.card-glass {
    background: var(--surface);
    border-radius: 36px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
}

.table-toolbar-custom {
    padding: 20px 28px 0px 28px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.search-wrapper {
    position: relative;
    flex: 2;
    min-width: 240px;
}

.search-wrapper i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.9rem;
}

.search-wrapper input {
    width: 100%;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 48px;
    padding: 12px 18px 12px 44px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: .25s;
    font-family: var(--font);
}

.search-wrapper input:focus {
    border-color: var(--navy);
    background: white;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}

/* Table Styles */
.table-responsive-custom {
    overflow-x: auto;
    padding: 0 8px 16px 8px;
}

.table-premium {
    width: 100%;
    border-collapse: collapse;
}

.table-premium thead tr th {
    background: var(--navy);
    color: rgba(255,255,255,.9);
    padding: 18px 20px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    border: none;
}

.table-premium tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.2s;
}

.table-premium tbody tr:hover {
    background: #fafcff;
}

.table-premium tbody td {
    padding: 18px 20px;
    vertical-align: middle;
    font-size: 0.82rem;
    font-weight: 500;
    color: #1e293b;
}

/* Badge Styles */
.badge-event {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    color: white;
    border-radius: 30px;
    padding: 5px 14px;
    font-weight: 700;
    font-size: .7rem;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .68rem;
}
.badge-status.aktif { background: #dcfce7; color: #15803d; }
.badge-status.nonaktif { background: #fee2e2; color: #b91c1c; }

/* Harga Coret */
.price-strike {
    text-decoration: line-through;
    color: var(--rose);
    font-size: .75rem;
}

.price-event {
    font-weight: 800;
    color: var(--emerald);
    font-size: .9rem;
}

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: .75rem;
    font-weight: 700;
    transition: var(--transition);
    text-decoration: none;
    font-family: var(--font) !important;
}

.btn-action-detail { 
    background: rgba(99,102,241,.1);  
    color: var(--indigo); 
}
.btn-action-edit   { 
    background: rgba(245,158,11,.1);  
    color: var(--amber);  
}
.btn-action-delete { 
    background: rgba(225,29,72,.1);   
    color: var(--rose);   
}

.btn-action-detail:hover { 
    background: var(--indigo); 
    color: #fff; 
    transform: translateY(-2px); 
}
.btn-action-edit:hover   { 
    background: var(--amber);  
    color: #fff; 
    transform: translateY(-2px); 
}
.btn-action-delete:hover { 
    background: var(--rose);   
    color: #fff; 
    transform: translateY(-2px); 
}

/* Empty State */
.empty-state-premium {
    padding: 70px 20px;
    text-align: center;
}

.empty-icon-circle {
    width: 80px;
    height: 80px;
    background: var(--surface-2);
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
    margin-bottom: 20px;
    border: 2px dashed var(--border);
}

/* ============================================================
   MODAL DETAIL PREMIUM
   ============================================================ */
.modal-premium .modal-content {
    border-radius: 28px;
    border: none;
    box-shadow: 0 30px 60px rgba(0,0,0,.2);
    overflow: hidden;
}

.modal-premium .modal-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 20px 28px;
    border: none;
}

.modal-premium .modal-title {
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-body-premium {
    padding: 28px;
}

.modal-footer-custom {
    padding: 16px 24px 24px;
    border: none;
    background: #fff;
}

.btn-close-modal {
    width: 100%;
    padding: 14px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-weight: 700;
    font-size: .875rem;
    color: var(--text-primary);
    cursor: pointer;
    transition: .25s;
    font-family: var(--font) !important;
}

.btn-close-modal:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .menuevent-page-wrapper {
        padding: 20px 16px;
    }
    .menuevent-header-left h2 {
        font-size: 1.5rem;
    }
    .stats-strip {
        grid-template-columns: repeat(2, 1fr);
    }
    .action-buttons {
        flex-wrap: wrap;
    }
    .btn-action {
        padding: 6px 12px;
        font-size: .7rem;
    }
}
</style>

<!-- ================================================
     MARKUP (menggunakan data dari kode asli)
     ================================================ -->
<div class="menuevent-page-wrapper">

    <!-- Header -->
    <div class="menuevent-header">
        <div class="menuevent-header-left">
            <h2>Menu <span>Event</span></h2>
            <p><i class="fas fa-tags me-1"></i> Kelola daftar menu promo yang tersedia pada event restoran</p>
        </div>
        <a href="{{ route('dashboard.restoran.menu-event.create') }}" class="btn-premium-primary">
            <i class="fas fa-plus-circle"></i>
            Tambah Menu Event
        </a>
    </div>

    <!-- Stats Strip (dihitung otomatis dari data $menuEvents) -->
    @php
        $totalMenuEvents = $menuEvents->count();
        $uniqueEvents = $menuEvents->pluck('nama_event')->unique()->count();
        $activePromo = $menuEvents->where('is_active', true)->count();
        $totalDiskon = $menuEvents->sum(function($item) {
            return $item->harga - $item->harga_khusus;
        });
    @endphp
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="fas fa-tags"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalMenuEvents }}</div>
                <div class="stat-label">Total Menu Event</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $uniqueEvents }}</div>
                <div class="stat-label">Event Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $activePromo }}</div>
                <div class="stat-label">Promo Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-number">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</div>
                <div class="stat-label">Total Diskon</div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi (sama persis dengan kode asli) -->
    @if(session('success'))
        <div class="alert-success-premium">
            <i class="fas fa-check-circle fa-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Card -->
    <div class="card-glass">
        <div class="table-toolbar-custom">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchMenuEvent" placeholder="Cari nama event, menu, atau deskripsi..." autocomplete="off">
            </div>
        </div>

        <div class="table-responsive-custom">
            <table class="table-premium" id="menuEventTable">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Event</th>
                        <th>Nama Menu</th>
                        <th class="text-end">Harga Normal</th>
                        <th class="text-end">Harga Event</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="menuEventTableBody">
                    @forelse($menuEvents as $i => $me)
                    @php
                        $statusClass = $me->is_active ? 'aktif' : 'nonaktif';
                        $statusLabel = $me->is_active ? 'AKTIF' : 'NONAKTIF';
                    @endphp
                    <tr data-search="{{ strtolower($me->nama_event) }} {{ strtolower($me->nama_menu) }} {{ strtolower($me->deskripsi ?? '') }}">
                        <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                        <td>
                            <span class="badge-event">
                                <i class="fas fa-calendar-alt"></i> {{ $me->nama_event }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">{{ $me->nama_menu }}</td>
                        <td class="text-end">
                            <span class="price-strike">Rp {{ number_format($me->harga, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-end">
                            <span class="price-event">Rp {{ number_format($me->harga_khusus, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge-status {{ $statusClass }}">
                                <i class="fas {{ $me->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn-action btn-action-detail"
                                        onclick="tampilDetailEvent({{ $me->id }})">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <a href="{{ route('dashboard.restoran.menu-event.edit', $me->id) }}" 
                                   class="btn-action btn-action-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn-action btn-action-delete"
                                        onclick="konfirmasiHapusMenuEvent({{ $me->id }}, '{{ addslashes($me->nama_menu) }}')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                            <form id="form-hapus-{{ $me->id }}" 
                                  action="{{ route('dashboard.restoran.menu-event.destroy', $me->id) }}" 
                                  method="POST" style="display:none;">
                                @csrf 
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-state-row">
                        <td colspan="7">
                            <div class="empty-state-premium">
                                <div class="empty-icon-circle">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <h5>Belum Ada Menu Event</h5>
                                <p class="text-muted">Tambahkan menu promo pertama untuk event restoran.</p>
                                <a href="{{ route('dashboard.restoran.menu-event.create') }}" class="btn-premium-primary" style="display:inline-flex; margin-top:12px;">
                                    <i class="fas fa-plus-circle"></i> Tambah Menu Event
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL POPUP DETAIL PREMIUM (sama persis dengan kode asli tapi style premium) --}}
<div class="modal fade modal-premium" id="modalDetailEvent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-tags"></i>
                    Rincian Promo Menu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-premium">
                <div class="text-center mb-4">
                    <span id="det-event" class="badge-event d-inline-flex mb-2"></span>
                    <h3 id="det-menu" class="fw-bold text-dark mb-0"></h3>
                </div>

                <div class="modal-stats-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                    <div class="modal-stat-card" style="background: var(--surface-2); border: 1px solid var(--border); border-radius: 14px; padding: 14px; text-align: center;">
                        <span class="stat-label" style="font-size: .6rem; text-transform: uppercase; color: var(--text-muted);">Harga Normal</span>
                        <div class="stat-value" style="text-decoration: line-through; color: var(--rose); font-weight: 800;">
                            <span id="det-harga-asli"></span>
                        </div>
                    </div>
                    <div class="modal-stat-card" style="background: var(--surface-2); border: 1px solid var(--border); border-radius: 14px; padding: 14px; text-align: center;">
                        <span class="stat-label" style="font-size: .6rem; text-transform: uppercase; color: var(--text-muted);">Harga Event</span>
                        <div class="stat-value" style="color: var(--emerald); font-weight: 800; font-size: 1.1rem;">
                            <span id="det-harga-event"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-section-label" style="font-size: .65rem; text-transform: uppercase; letter-spacing: 2px; color: var(--text-muted); margin-bottom: 8px;">
                    <i class="fas fa-align-left me-1"></i> Deskripsi Menu
                </div>
                <div class="modal-deskripsi" style="background: var(--surface-2); border: 1px solid var(--border); border-radius: 14px; padding: 16px; font-size: .85rem; line-height: 1.6; margin-bottom: 20px;">
                    <p id="det-deskripsi" class="mb-0"></p>
                </div>

                <div id="det-status-box" class="text-center p-3 rounded-3" style="background: var(--surface-2); border: 1px solid var(--border);">
                    <span class="small text-muted">Status Promo: </span>
                    <span id="det-status-label" class="badge-status"></span>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup Rincian
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT (fungsi tampilDetailEvent dari kode asli, 
     ditambah konfirmasi hapus SweetAlert dan search)
     ================================================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stat Cards Animation
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, idx) => {
        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (idx * 80));
    });

    // Table Row Stagger Animation
    const rows = document.querySelectorAll('.table-premium tbody tr');
    rows.forEach((row, index) => {
        if (!row.querySelector('.empty-state-premium')) {
            row.style.opacity = '0';
            row.style.transform = 'translateY(12px)';
            row.style.transition = 'opacity 0.4s ease, transform 0.4s cubic-bezier(0.34,1.56,0.64,1)';
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 150 + (index * 40));
        }
    });

    // Search Functionality
    const searchInput = document.getElementById('searchMenuEvent');
    
    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#menuEventTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state-premium')) return;
            
            const searchText = row.dataset.search || '';
            const matchSearch = query === '' || searchText.includes(query);
            
            row.style.display = matchSearch ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterTable);
});

// Konfirmasi Hapus dengan SweetAlert (menggantikan confirm biasa dari kode asli)
function konfirmasiHapusMenuEvent(id, nama) {
    Swal.fire({
        title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;font-size:1.2rem;color:#0f172a;">Arsipkan Menu Event?</span>',
        html: `<span style="font-family:Plus Jakarta Sans;color:#64748b;font-size:.9rem;">Menu <strong style="color:#e11d48;">${nama}</strong> akan diarsipkan dan tidak akan tampil di halaman promo.</span>`,
        icon: 'warning',
        iconColor: '#f59e0b',
        showCancelButton: true,
        confirmButtonColor: '#00197D',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-archive me-1"></i> Ya, Arsipkan',
        cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
        customClass: {
            popup: 'swal-premium-popup',
            confirmButton: 'swal-btn-confirm',
            cancelButton: 'swal-btn-cancel',
        },
        backdrop: 'rgba(0,0,0,.45)',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('form-hapus-' + id);
            if (form) form.submit();
        }
    });
}

// Fungsi tampilDetailEvent (SAMA PERSIS dengan kode asli, hanya menggunakan SweetAlert untuk error)
function tampilDetailEvent(id) {
    fetch(`/dashboard/restoran/menu-event/${id}`)
        .then(response => {
            if (!response.ok) throw new Error('Data tidak ditemukan');
            return response.json();
        })
        .then(data => {
            // Mapping data ke elemen Modal
            document.getElementById('det-event').innerHTML = `<i class="fas fa-calendar-alt me-1"></i> ${data.nama_event}`;
            document.getElementById('det-menu').innerText = data.nama_menu;
            document.getElementById('det-deskripsi').innerText = data.deskripsi || 'Tidak ada keterangan tambahan untuk menu ini.';

            // Format Mata Uang Rupiah
            const rupiah = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);

            document.getElementById('det-harga-asli').innerHTML = rupiah(data.harga_asli || data.harga);
            document.getElementById('det-harga-event').innerHTML = rupiah(data.harga_khusus);

            // Pengaturan Badge Status
            const statusSpan = document.getElementById('det-status-label');
            if(data.is_active) {
                statusSpan.innerHTML = '<i class="fas fa-check-circle me-1"></i> PROMO AKTIF';
                statusSpan.className = 'badge-status aktif';
            } else {
                statusSpan.innerHTML = '<i class="fas fa-ban me-1"></i> PROMO NONAKTIF';
                statusSpan.className = 'badge-status nonaktif';
            }

            // Tampilkan Modal
            new bootstrap.Modal(document.getElementById('modalDetailEvent')).show();
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Gagal memuat rincian: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#00197D'
            });
        });
}

// Additional SweetAlert styling
const style = document.createElement('style');
style.textContent = `
    .swal-premium-popup {
        border-radius: 28px !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        padding: 28px !important;
    }
    .swal-btn-confirm, .swal-btn-cancel {
        border-radius: 12px !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 700 !important;
        padding: 12px 24px !important;
    }
`;
document.head.appendChild(style);
</script>

@endsection