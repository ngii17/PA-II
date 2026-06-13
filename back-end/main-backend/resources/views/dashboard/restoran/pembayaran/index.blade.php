@extends('dashboard.layouts.app')
@section('title', 'Pembayaran Restoran')

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
    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label { font-family: var(--font) !important; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.payment-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}
.payment-wrapper::before,
.payment-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.payment-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.payment-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.payment-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.payment-header {
    margin-bottom: 28px;
}
.payment-header h2 {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
}
.payment-header h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.payment-header p {
    color: var(--text-muted);
    font-size: .85rem;
    margin: 0;
}

/* ============================================================
   STATS GRID
   ============================================================ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}
.stat-card {
    background: var(--surface);
    border-radius: 20px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    transition: var(--transition);
    opacity: 0;
    transform: translateY(20px);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.stat-icon.primary { background: rgba(0,25,125,.08); color: var(--navy); }
.stat-icon.success { background: rgba(16,185,129,.1); color: var(--emerald); }
.stat-icon.warning { background: rgba(245,158,11,.1); color: var(--amber); }
.stat-info .stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 4px;
}
.stat-info .stat-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
}

/* ============================================================
   SEARCH & FILTER
   ============================================================ */
.search-filter-bar {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.search-box {
    position: relative;
    flex: 2;
    min-width: 240px;
}
.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}
.search-box input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 1.5px solid var(--border);
    border-radius: 40px;
    font-size: .8rem;
    background: var(--surface-2);
    transition: .25s;
}
.search-box input:focus {
    outline: none;
    border-color: var(--navy);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
.filter-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.filter-btn {
    padding: 8px 18px;
    border-radius: 40px;
    font-size: .7rem;
    font-weight: 700;
    background: var(--surface-2);
    border: 1px solid var(--border);
    color: var(--text-muted);
    cursor: pointer;
    transition: .3s;
}
.filter-btn i { margin-right: 6px; }
.filter-btn.active {
    background: var(--navy);
    color: white;
    border-color: var(--navy);
}
.filter-btn:hover:not(.active) {
    background: #eef2ff;
    border-color: #c0ceee;
}

/* ============================================================
   MAIN CARD & TABLE
   ============================================================ */
.main-card {
    background: var(--surface);
    border-radius: 28px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
}
.table-responsive { overflow-x: auto; }
.payment-table {
    width: 100%;
    border-collapse: collapse;
}
.payment-table thead th {
    background: var(--navy);
    color: rgba(255,255,255,.9);
    padding: 14px 20px;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.payment-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .2s;
}
.payment-table tbody tr:hover { background: #fafcff; }
.payment-table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
    font-size: .8rem;
}
.guest-name { font-weight: 800; color: var(--text-primary); }
.guest-email { font-size: .7rem; color: var(--text-muted); }
.price-total { font-weight: 700; color: var(--emerald); }
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 30px;
    font-size: .68rem;
    font-weight: 700;
}
.badge-status.pending { background: #fff3e0; color: #c2410c; }
.badge-status.lunas   { background: #dcfce7; color: #15803d; }
.badge-status.proses  { background: #e0f2fe; color: #0369a1; }
.badge-status.batal   { background: #fee2e2; color: #b91c1c; }

/* ============================================================
   DETAIL ROW (EXPANDABLE)
   ============================================================ */
.detail-row {
    background: var(--surface-2);
    border-top: 1px solid var(--border);
}
.detail-content {
    padding: 20px 24px;
}
.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}
.info-panel {
    background: white;
    border-radius: 16px;
    padding: 16px;
    border: 1px solid var(--border);
}
.info-panel h6 {
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
.info-panel p { font-size: .8rem; margin-bottom: 6px; }
.info-panel p strong { color: var(--text-primary); }
.detail-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border);
}
.detail-table th {
    background: #f1f5f9;
    padding: 12px;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted);
    text-align: left;
}
.detail-table td {
    padding: 12px;
    font-size: .8rem;
    border-bottom: 1px solid var(--border);
}
.detail-table td.text-end { text-align: right; }
.detail-table td.text-center { text-align: center; }
.detail-table tr:last-child td { border-bottom: none; }
.total-row { background: #ecfdf5; }
.total-row td { font-weight: 800; font-size: .9rem; }

/* ============================================================
   BUTTON DETAIL
   ============================================================ */
.btn-detail {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 12px;
    font-size: .7rem;
    font-weight: 700;
    background: rgba(99,102,241,.1);
    color: var(--indigo);
    border: none;
    cursor: pointer;
    transition: .3s;
}
.btn-detail:hover {
    background: var(--indigo);
    color: white;
    transform: translateY(-2px);
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.empty-state {
    text-align: center;
    padding: 70px 20px;
}
.empty-icon {
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
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .payment-wrapper { padding: 20px 16px; }
    .payment-header h2 { font-size: 1.5rem; }
    .detail-grid { grid-template-columns: 1fr; }
}
</style>

<div class="payment-wrapper">
    <!-- Header -->
    <div class="payment-header">
        <h2>Pembayaran <span>Restoran</span></h2>
        <p><i class="fas fa-credit-card me-1"></i> Rekap transaksi dan pembayaran restoran hotel</p>
    </div>

    <!-- Statistik -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-number">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-receipt"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalPesanan ?? 0 }}</div>
                <div class="stat-label">Total Pesanan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $pesananLunas ?? 0 }}</div>
                <div class="stat-label">Pesanan Lunas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $pesananPending ?? 0 }}</div>
                <div class="stat-label">Pesanan Pending</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari pelanggan, metode, atau status...">
        </div>
        <div class="filter-group" id="filterGroup">
            <button class="filter-btn active" data-filter="all"><i class="fas fa-list-ul"></i> Semua</button>
            <button class="filter-btn" data-filter="pending"><i class="fas fa-clock"></i> Pending</button>
            <button class="filter-btn" data-filter="lunas"><i class="fas fa-check-circle"></i> Lunas</button>
            <button class="filter-btn" data-filter="proses"><i class="fas fa-spinner"></i> Proses</button>
            <button class="filter-btn" data-filter="batal"><i class="fas fa-ban"></i> Batal</button>
        </div>
    </div>

    <!-- Main Table -->
    <div class="main-card">
        <div class="table-responsive">
            <table class="payment-table" id="paymentTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelanggan</th>
                        <th class="text-end">Total</th>
                        <th>Metode</th>
                        <th class="text-center">Status</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($pesanan ?? [] as $i => $p)
                    @php 
                        $user = $users[$p->user_id] ?? null;
                        $statusMap = [1 => 'pending', 2 => 'lunas', 3 => 'proses', 4 => 'batal'];
                        $statusLabel = [1 => 'PENDING', 2 => 'LUNAS', 3 => 'PROSES', 4 => 'BATAL'];
                        $statusClass = $statusMap[$p->status_pembayaran_id] ?? 'pending';
                        $statusText = $statusLabel[$p->status_pembayaran_id] ?? '-';
                    @endphp
                    <tr class="order-row" data-id="{{ $p->id }}" data-status="{{ $statusClass }}" data-search="{{ strtolower($user['full_name'] ?? '') }} {{ strtolower($user['email'] ?? '') }} {{ strtolower($p->metode_pembayaran ?? '') }}">
                        <td class="text-muted fw-bold">{{ $i + 1 }}</td>
                        <td>
                            <div class="guest-name">{{ $user['full_name'] ?? 'User #'.$p->user_id }}</div>
                            <div class="guest-email">{{ $user['email'] ?? '-' }}</div>
                        </td>
                        <td class="text-end price-total">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="text-muted">{{ $p->metode_pembayaran ?? 'Tunai' }}</td>
                        <td class="text-center">
                            <span class="badge-status {{ $statusClass }}">
                                <i class="fas {{ $statusClass == 'pending' ? 'fa-clock' : ($statusClass == 'lunas' ? 'fa-check-circle' : ($statusClass == 'proses' ? 'fa-spinner' : 'fa-ban')) }}"></i>
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="text-muted" style="font-size: .75rem;">{{ $p->created_at->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            <button class="btn-detail" onclick="toggleDetail({{ $p->id }})">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                        </td>
                    </tr>

                    <!-- Detail Row (tersembunyi, toggle) -->
                    <tr class="detail-row" id="detail-{{ $p->id }}" style="display: none;">
                        <td colspan="7">
                            <div class="detail-content">
                                <div class="detail-grid">
                                    <!-- Data Pelanggan -->
                                    <div class="info-panel">
                                        <h6><i class="fas fa-user"></i> DATA PELANGGAN</h6>
                                        <p><strong>Nama:</strong> {{ $user['full_name'] ?? '-' }}</p>
                                        <p><strong>Email:</strong> {{ $user['email'] ?? '-' }}</p>
                                        <p><strong>No. HP:</strong> {{ $user['phone'] ?? '-' }}</p>
                                    </div>
                                    <!-- Info Order -->
                                    <div class="info-panel">
                                        <h6><i class="fas fa-shopping-cart"></i> INFO ORDER</h6>
                                        <p><strong>Nomor Meja:</strong> {{ $p->nomor_meja ?? '-' }}</p>
                                        <p><strong>Metode:</strong> {{ $p->metode_pembayaran ?? 'Tunai' }}</p>
                                        <p><strong>Status:</strong> 
                                            <span class="badge-status {{ $statusClass }}" style="margin-left: 6px;">{{ $statusText }}</span>
                                        </p>
                                        <p><strong>Tanggal Order:</strong> {{ $p->created_at->format('d M Y H:i:s') }}</p>
                                    </div>
                                </div>

                                <h6 style="font-size: .7rem; font-weight: 800; letter-spacing: 1px; margin-bottom: 10px;">
                                    <i class="fas fa-utensils"></i> DAFTAR MENU
                                </h6>
                                <table class="detail-table">
                                    <thead>
                                        <tr><th>Nama Menu</th><th class="text-center">Jumlah</th><th class="text-end">Harga/Porsi</th><th class="text-end">Subtotal</th></tr>
                                    </thead>
                                    <tbody>
                                        @forelse($p->details as $item)
                                        <tr>
                                            <td>{{ $item->menu->nama_menu ?? 'Menu Tidak Ditemukan' }}</td>
                                            <td class="text-center">{{ $item->jumlah }}</td>
                                            <td class="text-end">Rp {{ number_format($item->harga_at_porsi, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($item->jumlah * $item->harga_at_porsi, 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted">Rincian menu tidak ditemukan</td></tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="3" class="text-end fw-bold">TOTAL PEMBAYARAN</td>
                                            <td class="text-end fw-bold text-success">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-credit-card"></i></div>
                                <h5>Belum Ada Data Pembayaran</h5>
                                <p class="text-muted">Belum ada transaksi pembayaran restoran.</p>
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
// Toggle Detail Row
function toggleDetail(id) {
    const detailRow = document.getElementById('detail-' + id);
    if (detailRow.style.display === 'none') {
        detailRow.style.display = 'table-row';
    } else {
        detailRow.style.display = 'none';
    }
}

// Search & Filter
const searchInput = document.getElementById('searchInput');
let currentFilter = 'all';

function filterTable() {
    const query = searchInput.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.order-row');
    rows.forEach(row => {
        const status = row.dataset.status;
        const searchText = row.dataset.search;
        const matchFilter = currentFilter === 'all' || status === currentFilter;
        const matchSearch = query === '' || searchText.includes(query);
        row.style.display = (matchFilter && matchSearch) ? '' : 'none';
        // Sembunyikan detail row jika parent disembunyikan
        const detailRow = document.getElementById('detail-' + row.dataset.id);
        if (detailRow && row.style.display === 'none') detailRow.style.display = 'none';
    });
}

searchInput?.addEventListener('input', filterTable);

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        filterTable();
    });
});

// Animasi stat cards
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, idx) => {
        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (idx * 80));
    });
});
</script>

@endsection