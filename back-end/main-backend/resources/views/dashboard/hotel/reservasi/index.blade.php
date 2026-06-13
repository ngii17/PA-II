@extends('dashboard.layouts.app')
@section('title', 'Reservasi Hotel')

@section('content')
{{-- ================================================================
     RESERVASI HOTEL — PREMIUM UNIFIED
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
    --text-muted:   #5b6e8c;
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
.reservasi-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}
.reservasi-page-wrapper::before,
.reservasi-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.reservasi-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.reservasi-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.reservasi-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.reservasi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}
.reservasi-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.reservasi-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.reservasi-header-left p {
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
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.stat-icon.navy  { background: rgba(0,25,125,.08);   color: var(--navy); }
.stat-icon.gold  { background: rgba(212,175,55,.12); color: var(--gold); }
.stat-icon.green { background: rgba(16,185,129,.1);  color: var(--emerald); }
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
    padding: 20px 28px 0;
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
    left: 16px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .9rem;
}
.search-wrapper input {
    width: 100%;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 48px;
    padding: 12px 18px 12px 44px;
    font-size: .8rem;
    font-weight: 500;
    transition: .25s;
}
.search-wrapper input:focus {
    border-color: var(--navy);
    background: white;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
.filter-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.filter-badge-premium {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 40px;
    padding: 8px 18px;
    font-weight: 700;
    font-size: .7rem;
    cursor: pointer;
    transition: var(--transition);
    color: var(--text-muted);
}
.filter-badge-premium i { margin-right: 6px; }
.filter-badge-premium.active-filter {
    background: var(--navy);
    color: white;
    border-color: var(--navy);
    box-shadow: 0 4px 8px rgba(0,25,125,.2);
}
.table-responsive-custom {
    overflow-x: auto;
    padding: 0 8px 16px;
}
.table-premium-reservasi {
    width: 100%;
    border-collapse: collapse;
}
.table-premium-reservasi thead th {
    background: var(--navy);
    color: rgba(255,255,255,.9);
    padding: 18px 20px;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.table-premium-reservasi tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .2s;
}
.table-premium-reservasi tbody tr:hover { background: #fafcff; }
.table-premium-reservasi tbody td {
    padding: 18px 16px;
    vertical-align: middle;
    font-size: .82rem;
    font-weight: 500;
    color: #1e293b;
}
.guest-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}
.guest-avatar {
    width: 38px; height: 38px;
    background: linear-gradient(145deg, var(--navy), var(--navy-mid));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: .9rem;
}
.guest-info .guest-name {
    font-weight: 800;
    font-size: .85rem;
    color: var(--text-primary);
}
.guest-email {
    font-size: .7rem;
    color: var(--text-muted);
}
.room-badge {
    background: #eef2ff;
    color: #1e40af;
    border-radius: 12px;
    padding: 4px 12px;
    font-weight: 700;
    font-size: .7rem;
    display: inline-block;
}
.kamar-assign {
    font-size: .7rem;
    background: #e9f4e8;
    color: #2b6e3c;
    border-radius: 30px;
    padding: 3px 8px;
    display: inline-block;
    margin-top: 4px;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 60px;
    font-weight: 700;
    font-size: .68rem;
    letter-spacing: .4px;
}
.status-badge.pending   { background: #fff3e0; color: #c2410c; }
.status-badge.terbayar  { background: #e0f2fe; color: #0369a1; }
.status-badge.checkin   { background: #dcfce7; color: #15803d; }
.status-badge.selesai   { background: #e9eef3; color: #334155; }
.status-badge.batal     { background: #fee2e2; color: #b91c1c; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.reservasi-card-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
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
.btn-action-detail { background: rgba(99,102,241,.1); color: var(--indigo); }
.btn-action-edit   { background: rgba(245,158,11,.1); color: var(--amber);  }
.btn-action-delete { background: rgba(225,29,72,.1);  color: var(--rose);   }
.btn-action-detail:hover,
.btn-action-edit:hover,
.btn-action-delete:hover {
    color: #fff;
    transform: translateY(-2px);
}
.btn-action-detail:hover { background: var(--indigo); }
.btn-action-edit:hover   { background: var(--amber);  }
.btn-action-delete:hover { background: var(--rose);   }
.empty-state-premium {
    text-align: center;
    padding: 70px 20px;
}
.empty-icon-circle {
    width: 80px; height: 80px;
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
@media (max-width: 768px) {
    .reservasi-page-wrapper { padding: 20px 16px; }
    .reservasi-header h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
    .btn-action { padding: 6px 10px; font-size: .7rem; }
}
</style>

<div class="reservasi-page-wrapper">

    {{-- Header --}}
    <div class="reservasi-header">
        <div class="reservasi-header-left">
            <h2>Manajemen <span>Reservasi</span></h2>
            <p>Kelola pemesanan, status, dan ketersediaan kamar premium.</p>
        </div>
        <a href="{{ route('dashboard.hotel.reservasi.create') }}" class="btn-navy-premium">
            <i class="fas fa-plus-circle"></i> Reservasi Baru
        </a>
    </div>

    {{-- Statistik --}}
    @php
        $totalReservasi = $reservasi->count();
        $pendingCount   = $reservasi->where('status_reservasi_id', 1)->count();
        $aktifLunasCount = $reservasi->whereIn('status_reservasi_id', [2,3,4])->count();
        $totalPendapatan = $reservasi->sum('total_harga');
    @endphp
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalReservasi }}</div>
                <div class="stat-label">Total Reservasi</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $aktifLunasCount }}</div>
                <div class="stat-label">Aktif / Lunas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $pendingCount }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon navy"><i class="fas fa-coins"></i></div>
            <div class="stat-info">
                <div class="stat-number">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="stat-label">Estimasi Pendapatan</div>
            </div>
        </div>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert-success-premium"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- Tabel --}}
    <div class="card-glass">
        <div class="table-toolbar-custom">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchReservasi" placeholder="Cari nama tamu, email, atau tipe kamar...">
            </div>
            <div class="filter-group" id="filterGroup">
                <div class="filter-badge-premium active-filter" data-filter="all"><i class="fas fa-list-ul"></i> Semua</div>
                <div class="filter-badge-premium" data-filter="pending"><i class="fas fa-clock"></i> Pending</div>
                <div class="filter-badge-premium" data-filter="terbayar"><i class="fas fa-credit-card"></i> Terbayar</div>
                <div class="filter-badge-premium" data-filter="checkin"><i class="fas fa-door-open"></i> Check-in</div>
                <div class="filter-badge-premium" data-filter="selesai"><i class="fas fa-check-double"></i> Selesai</div>
                <div class="filter-badge-premium" data-filter="batal"><i class="fas fa-ban"></i> Batal</div>
            </div>
        </div>

        <div class="table-responsive-custom">
            <table class="table-premium-reservasi">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tamu</th>
                        <th>Tipe Kamar</th>
                        <th>Tanggal</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservasi as $i => $r)
                    @php
                        $user = $users[$r->user_id] ?? null;
                        $statusId = $r->status_reservasi_id;
                        switch($statusId) {
                            case 1: $statusClass = 'pending';   $statusIcon = 'fa-clock';       $statusLabel = 'PENDING'; break;
                            case 2: $statusClass = 'terbayar';  $statusIcon = 'fa-credit-card'; $statusLabel = 'TERBAYAR'; break;
                            case 3: $statusClass = 'checkin';    $statusIcon = 'fa-door-open';   $statusLabel = 'CHECK-IN'; break;
                            case 4: $statusClass = 'selesai';    $statusIcon = 'fa-check-double';$statusLabel = 'SELESAI'; break;
                            case 5: $statusClass = 'batal';      $statusIcon = 'fa-ban';         $statusLabel = 'BATAL'; break;
                            default: $statusClass = 'pending';   $statusIcon = 'fa-clock';       $statusLabel = 'PENDING';
                        }
                    @endphp
                    <tr data-status="{{ $statusClass }}" data-search="{{ strtolower($user['full_name'] ?? '') }} {{ strtolower($user['email'] ?? '') }} {{ strtolower($r->tipeKamar->nama_tipe ?? '') }}">
                        <td class="text-muted fw-bold">{{ $i+1 }}</td>
                        <td>
                            <div class="guest-cell">
                                <div class="guest-avatar">{{ strtoupper(substr($user['full_name'] ?? 'T',0,1)) }}</div>
                                <div class="guest-info">
                                    <div class="guest-name">{{ $user['full_name'] ?? 'Tamu #'.$r->user_id }}</div>
                                    <div class="guest-email">{{ $user['email'] ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="room-badge"><i class="fas fa-hotel"></i> {{ $r->tipeKamar->nama_tipe ?? '-' }}</div>
                            @if($r->kamar)
                                <div class="kamar-assign"><i class="fas fa-door-closed"></i> Kamar {{ $r->kamar->nomor_kamar }}</div>
                            @else
                                <div class="kamar-assign" style="background:#f1f5f9; color:#64748b;"><i class="fas fa-hourglass"></i> Belum assign</div>
                            @endif
                         </td>
                        <td style="font-size:.75rem;">
                            <i class="fas fa-calendar-alt text-primary"></i> {{ \Carbon\Carbon::parse($r->tgl_checkin)->format('d/m/Y') }}<br>
                            <i class="fas fa-calendar-week text-danger"></i> {{ \Carbon\Carbon::parse($r->tgl_checkout)->format('d/m/Y') }}
                        </td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($r->total_harga,0,',','.') }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $statusClass }}"><i class="fas {{ $statusIcon }}"></i> {{ $statusLabel }}</span>
                        </td>
                        <td class="text-center">
                            <div class="reservasi-card-actions">
                                <a href="{{ route('dashboard.hotel.reservasi.show', $r->id) }}" class="btn-action btn-action-detail" title="Detail"><i class="fas fa-eye"></i> Detail</a>
                                <a href="{{ route('dashboard.hotel.reservasi.edit', $r->id) }}" class="btn-action btn-action-edit" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                                <button class="btn-action btn-action-delete" onclick="konfirmasiHapusReservasi({{ $r->id }}, '{{ addslashes($user['full_name'] ?? 'Tamu') }}')" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
                            </div>
                            <form id="form-hapus-{{ $r->id }}" action="{{ route('dashboard.hotel.reservasi.destroy', $r->id) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state-premium">
                                <div class="empty-icon-circle"><i class="fas fa-bed"></i></div>
                                <h5>Belum Ada Reservasi</h5>
                                <p class="text-muted">Reservasi dari tamu hotel akan muncul di sini.</p>
                                <a href="{{ route('dashboard.hotel.reservasi.create') }}" class="btn-navy-premium" style="display:inline-flex; margin-top:12px;"><i class="fas fa-plus-circle"></i> Tambah Reservasi</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi kartu statistik
    document.querySelectorAll('.stat-card').forEach((card, i) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + i * 80);
    });
    // Animasi baris tabel
    const rows = document.querySelectorAll('.table-premium-reservasi tbody tr');
    rows.forEach((row, idx) => {
        if (row.querySelector('.empty-state-premium')) return;
        row.style.opacity = '0';
        row.style.transform = 'translateY(12px)';
        setTimeout(() => {
            row.style.transition = 'opacity 0.4s ease, transform 0.4s cubic-bezier(0.34,1.56,0.64,1)';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 150 + idx * 40);
    });

    // Filter & search
    const searchInput = document.getElementById('searchReservasi');
    const filterBadges = document.querySelectorAll('.filter-badge-premium');
    let activeFilter = 'all';
    let searchQuery = '';

    function filterTable() {
        const rowsAll = document.querySelectorAll('.table-premium-reservasi tbody tr');
        rowsAll.forEach(row => {
            if (row.querySelector('.empty-state-premium')) return;
            const status = row.dataset.status || '';
            const searchText = row.dataset.search || '';
            const matchFilter = activeFilter === 'all' || status === activeFilter;
            const matchSearch = searchQuery === '' || searchText.includes(searchQuery.toLowerCase());
            row.style.display = (matchFilter && matchSearch) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', function(e) {
        searchQuery = e.target.value;
        filterTable();
    });

    filterBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            filterBadges.forEach(b => b.classList.remove('active-filter'));
            this.classList.add('active-filter');
            activeFilter = this.dataset.filter;
            filterTable();
        });
    });
});

// Konfirmasi hapus dengan SweetAlert2
function konfirmasiHapusReservasi(id, nama) {
    Swal.fire({
        title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Hapus Reservasi?</span>',
        html: `<span style="font-family:Plus Jakarta Sans;">Reservasi atas nama <strong style="color:#e11d48;">${nama}</strong> akan dihapus secara permanen.</span>`,
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