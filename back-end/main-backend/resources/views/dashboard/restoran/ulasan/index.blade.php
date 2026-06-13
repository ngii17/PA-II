@extends('dashboard.layouts.app')
@section('title', 'Ulasan Restoran')

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
.ulasan-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.ulasan-page-wrapper::before,
.ulasan-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.ulasan-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.ulasan-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.ulasan-page-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER
   ============================================================ */
.ulasan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}

.ulasan-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}

.ulasan-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.ulasan-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
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
.stat-icon.rose  { background: rgba(225,29,72,.1);    color: var(--rose); }

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
.stat-info .stat-rating {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--amber);
    letter-spacing: -.02em;
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

.filter-wrapper {
    display: flex;
    gap: 8px;
}

.filter-select {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 48px;
    padding: 12px 20px;
    font-size: 0.8rem;
    font-weight: 500;
    font-family: var(--font);
    color: var(--text-primary);
    cursor: pointer;
    transition: .25s;
}

.filter-select:focus {
    border-color: var(--navy);
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

/* Menu badge */
.badge-menu {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fef3c7;
    color: #b45309;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.75rem;
    font-weight: 700;
}

/* Rating Stars */
.rating-stars {
    color: var(--amber);
    font-size: 0.9rem;
    letter-spacing: 2px;
}

/* Komentar Preview */
.komentar-preview {
    max-width: 280px;
}

.komentar-text {
    margin-bottom: 6px;
    font-size: 0.78rem;
    color: var(--text-muted);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.btn-lihat-komentar {
    background: none;
    border: none;
    color: var(--indigo);
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0;
    cursor: pointer;
    transition: var(--transition);
}

.btn-lihat-komentar:hover {
    color: var(--navy);
    text-decoration: underline;
}

/* Badge Status */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .68rem;
}
.badge-status.visible { background: #dcfce7; color: #15803d; }
.badge-status.hidden  { background: #fee2e2; color: #b91c1c; }

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

.btn-action-show { 
    background: rgba(16,185,129,.1);  
    color: var(--emerald); 
}
.btn-action-hide { 
    background: rgba(225,29,72,.1);   
    color: var(--rose);   
}

.btn-action-show:hover { 
    background: var(--emerald); 
    color: #fff; 
    transform: translateY(-2px); 
}
.btn-action-hide:hover { 
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
    background: linear-gradient(135deg, #b45309 0%, #d97706 100%);
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

.modal-ulasan-avatar {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #b45309 0%, #d97706 100%);
    border-radius: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.modal-ulasan-avatar span {
    font-size: 28px;
    font-weight: 800;
    color: white;
}

.modal-ulasan-nama {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--navy-dark);
    text-align: center;
    margin-bottom: 8px;
}

.modal-ulasan-email {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-align: center;
    margin-bottom: 16px;
}

.modal-rating-box {
    text-align: center;
    margin-bottom: 20px;
}

.modal-rating-stars {
    font-size: 1.3rem;
    color: var(--amber);
    letter-spacing: 4px;
}

.modal-komentar-box {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 20px;
    margin: 20px 0;
}

.modal-komentar-box p {
    font-size: 0.9rem;
    line-height: 1.7;
    color: var(--text-primary);
    font-style: italic;
    margin: 0;
}

.modal-komentar-box i.fa-quote-left {
    color: #d97706;
    opacity: 0.6;
    margin-right: 8px;
}

.modal-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 16px;
}

.modal-info-card {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px;
    text-align: center;
}

.modal-info-card .stat-label {
    font-size: .6rem;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 4px;
}
.modal-info-card .stat-value {
    font-weight: 800;
    font-size: .85rem;
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
    background: #b45309;
    color: #fff;
    border-color: #b45309;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .ulasan-page-wrapper {
        padding: 20px 16px;
    }
    .ulasan-header-left h2 {
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
    .table-toolbar-custom {
        flex-direction: column;
    }
    .filter-wrapper {
        width: 100%;
    }
    .filter-select {
        flex: 1;
    }
}
</style>

<!-- ================================================
     MARKUP
     ================================================ -->
<div class="ulasan-page-wrapper">

    <!-- Header -->
    <div class="ulasan-header">
        <div class="ulasan-header-left">
            <h2>Ulasan <span>Restoran</span></h2>
            <p><i class="fas fa-utensils me-1"></i> Kelola ulasan dan rating menu dari pelanggan restoran</p>
        </div>
    </div>

    <!-- Stats Strip -->
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="fas fa-comments"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalUlasan }}</div>
                <div class="stat-label">Total Ulasan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-star"></i></div>
            <div class="stat-info">
                <div class="stat-rating">{{ number_format($rataRating, 1) }} / 5.0</div>
                <div class="stat-label">Rata-rata Rating</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-eye"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $ulasan->where('is_hidden', false)->count() }}</div>
                <div class="stat-label">Ulasan Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose"><i class="fas fa-eye-slash"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $ulasan->where('is_hidden', true)->count() }}</div>
                <div class="stat-label">Ulasan Tersembunyi</div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
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
                <input type="text" id="searchUlasan" placeholder="Cari pelanggan, menu, atau komentar..." autocomplete="off">
            </div>
            <div class="filter-wrapper">
                <select id="filterRating" class="filter-select">
                    <option value="">Semua Rating</option>
                    <option value="5">★★★★★ (5)</option>
                    <option value="4">★★★★☆ (4)</option>
                    <option value="3">★★★☆☆ (3)</option>
                    <option value="2">★★☆☆☆ (2)</option>
                    <option value="1">★☆☆☆☆ (1)</option>
                </select>
                <select id="filterStatus" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="visible">Tampil</option>
                    <option value="hidden">Tersembunyi</option>
                </select>
            </div>
        </div>

        <div class="table-responsive-custom">
            <table class="table-premium" id="ulasanTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelanggan</th>
                        <th>Menu</th>
                        <th class="text-center">Rating</th>
                        <th>Komentar</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Status</th>
                        @if(session('user.role') === 'admin')
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="ulasanTableBody">
                    @forelse($ulasan as $i => $u)
                    @php 
                        $user = $users[$u->user_id] ?? null;
                        $statusClass = $u->is_hidden ? 'hidden' : 'visible';
                        $statusLabel = $u->is_hidden ? 'Tersembunyi' : 'Tampil';
                    @endphp
                    <tr data-rating="{{ $u->rating }}" 
                        data-status="{{ $u->is_hidden ? 'hidden' : 'visible' }}"
                        data-search="{{ strtolower($user['full_name'] ?? '') }} {{ strtolower($user['email'] ?? '') }} {{ strtolower($u->menu->nama_menu ?? '') }} {{ strtolower($u->komentar ?? '') }}">
                        <td class="text-muted fw-bold">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $user['full_name'] ?? 'Pelanggan' }}</div>
                            <div class="text-muted small">{{ $user['email'] ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge-menu">
                                <i class="fas fa-utensils"></i> {{ $u->menu->nama_menu ?? 'Menu dihapus' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="rating-stars">
                                @for($s = 1; $s <= 5; $s++)
                                    @if($s <= $u->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td>
                            <div class="komentar-preview">
                                <div class="komentar-text">{{ Str::limit($u->komentar, 60) }}</div>
                                @if(strlen($u->komentar) > 60)
                                <button class="btn-lihat-komentar" onclick="showDetailUlasan({{ $u->id }})">
                                    <i class="fas fa-eye"></i> Lihat selengkapnya
                                </button>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="text-muted small">{{ $u->created_at->format('d M Y') }}</span>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ $u->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge-status {{ $statusClass }}">
                                <i class="fas {{ $u->is_hidden ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        @if(session('user.role') === 'admin')
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn-action {{ $u->is_hidden ? 'btn-action-show' : 'btn-action-hide' }}"
                                        onclick="konfirmasiToggleUlasan({{ $u->id }}, '{{ addslashes($user['full_name'] ?? 'Pelanggan') }}', {{ $u->is_hidden ? 'true' : 'false' }})">
                                    <i class="fas {{ $u->is_hidden ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    {{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                                </button>
                            </div>
                            <form id="form-toggle-restoran-{{ $u->id }}" 
                                action="{{ route('dashboard.ulasan.toggle', ['tipe' => 'restoran', 'id' => $u->id]) }}" 
                                method="POST" style="display:none;">
                                @csrf 
                                @method('PATCH')
                            </form>
                        </td>
                        @endif
                    </tr>

                    {{-- MODAL DETAIL ULASAN PREMIUM --}}
                    <div class="modal fade modal-premium" id="detailUlasanModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-star"></i>
                                        Detail Ulasan Restoran
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body-premium">
                                    <div class="modal-ulasan-avatar">
                                        <span>{{ substr($user['full_name'] ?? 'P', 0, 1) }}</span>
                                    </div>
                                    <div class="modal-ulasan-nama">{{ $user['full_name'] ?? 'Pelanggan' }}</div>
                                    <div class="modal-ulasan-email">{{ $user['email'] ?? 'Email tidak tersedia' }}</div>

                                    <div class="modal-rating-box">
                                        <div class="modal-rating-stars">
                                            @for($s = 1; $s <= 5; $s++)
                                                @if($s <= $u->rating)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <div class="small text-muted mt-1">{{ $u->rating }} dari 5 bintang</div>
                                    </div>

                                    <div class="modal-komentar-box">
                                        <i class="fas fa-quote-left"></i>
                                        <p>{{ $u->komentar ?? 'Tidak ada komentar yang diberikan.' }}</p>
                                    </div>

                                    <div class="modal-info-grid">
                                        <div class="modal-info-card">
                                            <div class="stat-label">Menu</div>
                                            <div class="stat-value fw-bold">{{ $u->menu->nama_menu ?? 'Menu dihapus' }}</div>
                                        </div>
                                        <div class="modal-info-card">
                                            <div class="stat-label">Tanggal Ulasan</div>
                                            <div class="stat-value fw-bold">{{ $u->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer-custom">
                                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i> Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr class="empty-state-row">
                        <td colspan="8">
                            <div class="empty-state-premium">
                                <div class="empty-icon-circle">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h5>Belum Ada Ulasan Restoran</h5>
                                <p class="text-muted">Ulasan dari pelanggan restoran akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT
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

    // Search and Filter Functionality
    const searchInput = document.getElementById('searchUlasan');
    const filterRating = document.getElementById('filterRating');
    const filterStatus = document.getElementById('filterStatus');
    
    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const ratingValue = filterRating.value;
        const statusValue = filterStatus.value;
        
        const rows = document.querySelectorAll('#ulasanTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state-premium')) return;
            
            const searchText = row.dataset.search || '';
            const rowRating = row.dataset.rating || '';
            const rowStatus = row.dataset.status || '';
            
            const matchSearch = query === '' || searchText.includes(query);
            const matchRating = ratingValue === '' || rowRating === ratingValue;
            const matchStatus = statusValue === '' || rowStatus === statusValue;
            
            row.style.display = (matchSearch && matchRating && matchStatus) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterTable);
    filterRating?.addEventListener('change', filterTable);
    filterStatus?.addEventListener('change', filterTable);
});

// Show Detail Ulasan
function showDetailUlasan(id) {
    const modal = document.getElementById('detailUlasanModal' + id);
    if (modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

// Konfirmasi Toggle Ulasan dengan SweetAlert
function konfirmasiToggleUlasan(id, nama, isCurrentlyHidden) {
    const action = isCurrentlyHidden ? 'menampilkan' : 'menyembunyikan';
    const icon = isCurrentlyHidden ? 'eye' : 'eye-slash';
    
    Swal.fire({
        title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;font-size:1.2rem;color:#0f172a;">' + (isCurrentlyHidden ? 'Tampilkan Ulasan?' : 'Sembunyikan Ulasan?') + '</span>',
        html: `<span style="font-family:Plus Jakarta Sans;color:#64748b;font-size:.9rem;">Ulasan dari <strong style="color:#b45309;">${nama}</strong> akan ${action} dari halaman publik restoran.</span>`,
        icon: 'question',
        iconColor: '#f59e0b',
        showCancelButton: true,
        confirmButtonColor: '#b45309',
        cancelButtonColor: '#64748b',
        confirmButtonText: `<i class="fas fa-${icon} me-1"></i> Ya, ${isCurrentlyHidden ? 'Tampilkan' : 'Sembunyikan'}`,
        cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
        customClass: {
            popup: 'swal-premium-popup',
            confirmButton: 'swal-btn-confirm',
            cancelButton: 'swal-btn-cancel',
        },
        backdrop: 'rgba(0,0,0,.45)',
    }).then((result) => {
        if (result.isConfirmed) {
                const form = document.getElementById('form-toggle-restoran-' + id);
            if (form) form.submit();
        }
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