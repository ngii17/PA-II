@extends('dashboard.layouts.app')

@section('title', 'Laporan Bisnis & Statistik')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --gold:         #D4AF37;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e2e8f0;
    --text-primary: #0f172a;
    --text-mid:     #475569;
    --text-muted:   #94a3b8;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}
*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button { font-family: var(--font) !important; }
.fw-800 { font-weight: 800 !important; letter-spacing: -.02em; }

.laporan-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px 64px;
    position: relative;
}
.laporan-page-wrapper::before,
.laporan-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.laporan-page-wrapper::before {
    width: 560px; height: 560px;
    top: -180px; right: -130px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.laporan-page-wrapper::after {
    width: 380px; height: 380px;
    bottom: -100px; left: -90px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.laporan-page-wrapper > * { position: relative; z-index: 1; }

/* ── HEADER ── */
.laporan-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 28px;
}
.laporan-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 5px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.laporan-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.laporan-header-left p {
    color: var(--text-muted);
    font-size: .875rem;
    font-weight: 500;
    margin: 0;
}

/* ── EXPORT BUTTON ── */
.btn-export-main {
    font-family: var(--font);
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    border: none;
    border-radius: 14px;
    padding: 10px 22px;
    font-weight: 800;
    font-size: .8rem;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    box-shadow: var(--shadow-card);
}
.btn-export-main:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
    background: linear-gradient(135deg, #0025B3 0%, #001052 100%);
}
.btn-export-main .chevron {
    font-size: .7rem;
    transition: transform .25s ease;
}
.btn-export-main.open .chevron { transform: rotate(180deg); }

/* ── DROPDOWN — dipasang langsung ke body via JS ── */
#exportDropdownPortal {
    position: fixed;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18), 0 2px 8px rgba(0,25,125,0.12);
    min-width: 260px;
    border: 1px solid #e2e8f0;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity .18s ease, transform .18s ease, visibility .18s;
    z-index: 2147483647; /* max z-index */
}
#exportDropdownPortal.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.export-section { padding: 10px 0; }
.export-section + .export-section { border-top: 1px solid #e2e8f0; }
.export-section-title {
    padding: 6px 18px 4px;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #94a3b8;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.export-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 18px;
    text-decoration: none;
    color: #0f172a;
    font-size: .8rem;
    font-weight: 600;
    transition: background .15s;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.export-item:hover { background: #f8fafc; color: #00197D; text-decoration: none; }
.export-icon {
    width: 30px; height: 30px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
}
.export-icon.pdf   { background: #fee2e2; color: #dc2626; }
.export-icon.excel { background: #dcfce7; color: #15803d; }

/* ── STATS ── */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 20px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    opacity: 0;
    transform: translateY(18px);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: 4px 4px 0 0;
}
.stat-card.sc-blue::before   { background: #1D4ED8; }
.stat-card.sc-green::before  { background: #15803D; }
.stat-card.sc-amber::before  { background: #B45309; }
.stat-card.sc-teal::before   { background: #0F766E; }
.stat-card.sc-purple::before { background: #7C3AED; }
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }

.stat-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
    margin-bottom: 14px;
}
.si-blue   { background: rgba(29,78,216,.08);  color: #1D4ED8; }
.si-green  { background: rgba(21,128,61,.08);  color: #15803D; }
.si-amber  { background: rgba(180,83,9,.08);   color: #B45309; }
.si-teal   { background: rgba(15,118,110,.08); color: #0F766E; }
.si-purple { background: rgba(124,58,237,.08); color: #7C3AED; }

.stat-label {
    font-size: .62rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 5px;
}
.stat-number {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.03em;
    margin-bottom: 2px;
}
.stat-sub { font-size: .68rem; color: var(--text-muted); font-weight: 600; }

/* ── TABS ── */
.laporan-tabs-wrap {
    background: #ffffff;
    border-radius: 20px;
    padding: 7px;
    margin-bottom: 16px;
    display: inline-flex;
    gap: 4px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
}
.laporan-tab-btn {
    padding: 10px 22px;
    border-radius: 14px;
    border: none;
    background: transparent;
    font-family: var(--font);
    font-size: .78rem;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: all .25s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}
.laporan-tab-btn:hover { color: var(--navy); background: var(--surface-2); }
.laporan-tab-btn.active {
    background: var(--navy);
    color: #fff;
    box-shadow: 0 4px 14px rgba(0,25,125,.25);
}

/* ── CARDS ── */
.card-premium-pad {
    background: #ffffff;
    border-radius: 24px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    padding: 28px;
}
.card-title {
    font-size: .88rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 9px;
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }
.charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}
@media (max-width: 768px) { .charts-row { grid-template-columns: 1fr; } }
.chart-container { position: relative; height: 220px; }

/* ── TABLE ── */
.p-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
.p-table thead th {
    background: var(--surface-2);
    color: var(--text-muted);
    padding: 12px 18px;
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
    border-bottom: 1px solid var(--border);
}
.p-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
.p-table tbody tr:last-child { border-bottom: none; }
.p-table tbody tr:hover { background: #fdf6e3; }
.p-table tbody td { padding: 13px 18px; color: var(--text-primary); vertical-align: middle; }

.tbl-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .65rem; font-weight: 700;
    padding: 4px 11px; border-radius: 99px;
}
.tbl-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.badge-success { background: #ecfdf5; color: #065f46; }
.badge-success::before { background: #10b981; }
.badge-warn { background: #fef9c3; color: #854d0e; }
.badge-warn::before { background: #eab308; }
.badge-low { background: #fef2f2; color: #991b1b; }
.badge-low::before { background: #ef4444; }

.mini-bar-wrap { width: 80px; height: 7px; background: var(--border); border-radius: 99px; overflow: hidden; display: inline-block; }
.mini-bar-fill { height: 100%; border-radius: 99px; }

/* ── PROGRESS ── */
.progress-item { margin-bottom: 16px; }
.progress-item:last-child { margin-bottom: 0; }
.progress-meta { display: flex; justify-content: space-between; font-size: .75rem; color: var(--text-mid); font-weight: 700; margin-bottom: 6px; }
.progress-track { height: 8px; background: var(--surface-2); border-radius: 99px; overflow: hidden; border: 1px solid var(--border); }
.progress-fill { height: 100%; border-radius: 99px; transition: width .8s cubic-bezier(.34,1.56,.64,1); }

@media (max-width: 768px) {
    .laporan-header-left h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
    .laporan-tabs-wrap { display: flex; width: 100%; }
    .laporan-tab-btn { flex: 1; justify-content: center; }
}
</style>

<div class="laporan-page-wrapper">

    {{-- HEADER --}}
    <div class="laporan-header">
        <div class="laporan-header-left">
            <h2 class="fw-800">Laporan <span>Bisnis & Keuangan</span></h2>
            <p>Pantau performa bisnis hotel dan restoran secara real-time — <strong style="color:var(--navy);">Purnama Hotel &amp; Resto</strong>.</p>
        </div>
        <div>
            <button class="btn-export-main" type="button" id="exportMainBtn">
                <i class="fas fa-download"></i>
                Export Laporan
                <i class="fas fa-chevron-down chevron"></i>
            </button>
        </div>
    </div>

    {{-- STATISTIK --}}
    @php
        $totalGabungan  = $totalHotel + $totalRestoran;
        $pctHotel       = $totalGabungan > 0 ? ($totalHotel / $totalGabungan) * 100 : 0;
        $pctResto       = $totalGabungan > 0 ? ($totalRestoran / $totalGabungan) * 100 : 0;
        $reservasiAktif = $reservasiAktif ?? 0;
        $totalTxn       = ($totalTransaksiHotel ?? 0) + ($totalTransaksiRestoran ?? 0);
        $pctTxnHotel    = $totalTxn > 0 ? (($totalTransaksiHotel ?? 0) / $totalTxn) * 100 : 0;
        $pctTxnResto    = $totalTxn > 0 ? (($totalTransaksiRestoran ?? 0) / $totalTxn) * 100 : 0;
    @endphp
    <div class="stats-strip">
        <div class="stat-card sc-blue">
            <div class="stat-icon si-blue"><i class="fas fa-hotel"></i></div>
            <div class="stat-label">Pendapatan Hotel</div>
            <div class="stat-number">Rp {{ number_format($totalHotel / 1000000, 1) }}Jt</div>
            <div class="stat-sub">{{ $totalTransaksiHotel ?? 0 }} transaksi sukses</div>
        </div>
        <div class="stat-card sc-green">
            <div class="stat-icon si-green"><i class="fas fa-utensils"></i></div>
            <div class="stat-label">Pendapatan Restoran</div>
            <div class="stat-number">Rp {{ number_format($totalRestoran / 1000000, 1) }}Jt</div>
            <div class="stat-sub">{{ $totalTransaksiRestoran ?? 0 }} transaksi lunas</div>
        </div>
        <div class="stat-card sc-amber">
            <div class="stat-icon si-amber"><i class="fas fa-coins"></i></div>
            <div class="stat-label">Total Keseluruhan</div>
            <div class="stat-number">Rp {{ number_format($totalGabungan / 1000000, 1) }}Jt</div>
            <div class="stat-sub">Akumulasi gabungan</div>
        </div>
        <div class="stat-card sc-teal">
            <div class="stat-icon si-teal"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-label">Reservasi Aktif</div>
            <div class="stat-number">{{ $reservasiAktif }}</div>
            <div class="stat-sub">Bulan ini</div>
        </div>
        <div class="stat-card sc-purple">
            <div class="stat-icon si-purple"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Kontribusi Hotel</div>
            <div class="stat-number">{{ number_format($pctHotel, 1) }}%</div>
            <div class="stat-sub">dari total pendapatan</div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="laporan-tabs-wrap">
        <button class="laporan-tab-btn active" onclick="laporanTab('hotel', this)"><i class="fas fa-hotel"></i> Tren Hotel</button>
        <button class="laporan-tab-btn" onclick="laporanTab('resto', this)"><i class="fas fa-utensils"></i> Tren Restoran</button>
        <button class="laporan-tab-btn" onclick="laporanTab('breakdown', this)"><i class="fas fa-chart-pie"></i> Breakdown</button>
    </div>

    {{-- PANEL HOTEL --}}
    <div class="tab-panel active" id="panel-hotel">
        <div class="charts-row">
            <div class="card-premium-pad" style="grid-column: 1/-1;">
                <div class="card-title"><i class="fas fa-hotel" style="color:#1D4ED8;"></i> Tren Reservasi Hotel (12 Bulan Terakhir)</div>
                <div class="chart-container"><canvas id="chartHotel"></canvas></div>
            </div>
        </div>
        <div class="card-premium-pad" style="padding:20px 24px;">
            <div class="card-title" style="margin-bottom:16px;"><i class="fas fa-table"></i> Rincian Per Tipe Kamar</div>
            <div class="table-responsive">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Tipe Kamar</th>
                            <th class="text-center">Reservasi</th>
                            <th>Pendapatan</th><th>Occupancy</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailHotel ?? [] as $i => $item)
                        <tr>
                            <td style="color:var(--text-muted);font-weight:800;">{{ $i+1 }}</td>
                            <td style="font-weight:800;">{{ $item->nama_tipe ?? '-' }}</td>
                            <td class="text-center">{{ $item->total_reservasi ?? 0 }}</td>
                            <td style="color:var(--navy);">Rp {{ number_format($item->total_pendapatan ?? 0,0,',','.') }}</td>
                            <td>
                                <div class="mini-bar-wrap"><div class="mini-bar-fill" style="width:{{ $item->occupancy ?? 0 }}%;background:#1D4ED8;"></div></div>
                                <span style="font-size:.68rem;margin-left:6px;">{{ $item->occupancy ?? 0 }}%</span>
                            </td>
                            <td class="text-center">
                                @if(($item->occupancy ?? 0) >= 70)<span class="tbl-badge badge-success">Tinggi</span>
                                @elseif(($item->occupancy ?? 0) >= 40)<span class="tbl-badge badge-warn">Sedang</span>
                                @else<span class="tbl-badge badge-low">Rendah</span>@endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5">Tidak ada data tipe kamar</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PANEL RESTORAN --}}
    <div class="tab-panel" id="panel-resto">
        <div class="charts-row">
            <div class="card-premium-pad" style="grid-column: 1/-1;">
                <div class="card-title"><i class="fas fa-utensils" style="color:#15803D;"></i> Tren Pesanan Restoran (12 Bulan Terakhir)</div>
                <div class="chart-container"><canvas id="chartResto"></canvas></div>
            </div>
        </div>
        <div class="card-premium-pad" style="padding:20px 24px;">
            <div class="card-title" style="margin-bottom:16px;"><i class="fas fa-table"></i> Rincian Per Menu</div>
            <div class="table-responsive">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Nama Menu</th>
                            <th class="text-center">Pesanan</th>
                            <th>Pendapatan</th>
                            <th class="text-center">Rating</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailRestoran ?? [] as $i => $item)
                        <tr>
                            <td style="color:var(--text-muted);font-weight:800;">{{ $i+1 }}</td>
                            <td style="font-weight:800;">{{ $item->nama_menu ?? '-' }}</td>
                            <td class="text-center">{{ $item->total_pesanan ?? 0 }}</td>
                            <td style="color:#15803D;">Rp {{ number_format($item->total_pendapatan ?? 0,0,',','.') }}</td>
                            <td class="text-center"><span style="color:var(--gold);">★ {{ number_format($item->avg_rating ?? 0,1) }}</span></td>
                            <td class="text-center">
                                @if(($item->total_pesanan ?? 0) >= 100)<span class="tbl-badge badge-success">Terlaris</span>
                                @elseif(($item->total_pesanan ?? 0) >= 50)<span class="tbl-badge badge-warn">Normal</span>
                                @else<span class="tbl-badge badge-low">Rendah</span>@endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5">Tidak ada data menu</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PANEL BREAKDOWN --}}
    <div class="tab-panel" id="panel-breakdown">
        <div class="charts-row">
            <div class="card-premium-pad">
                <div class="card-title"><i class="fas fa-chart-pie" style="color:var(--gold);"></i> Distribusi Pendapatan</div>
                <div class="chart-container"><canvas id="chartDonut"></canvas></div>
            </div>
            <div class="card-premium-pad">
                <div class="card-title"><i class="fas fa-list-check"></i> Performa Kategori</div>
                <div class="progress-item">
                    <div class="progress-meta"><span>Pendapatan Hotel</span><span>{{ number_format($pctHotel,1) }}%</span></div>
                    <div class="progress-track"><div class="progress-fill" style="width:{{ $pctHotel }}%;background:linear-gradient(90deg,#1D4ED8,#3B82F6);"></div></div>
                </div>
                <div class="progress-item">
                    <div class="progress-meta"><span>Pendapatan Restoran</span><span>{{ number_format($pctResto,1) }}%</span></div>
                    <div class="progress-track"><div class="progress-fill" style="width:{{ $pctResto }}%;background:linear-gradient(90deg,#15803D,#22C55E);"></div></div>
                </div>
                <div class="progress-item">
                    <div class="progress-meta"><span>Transaksi Hotel</span><span>{{ $totalTransaksiHotel ?? 0 }} txn</span></div>
                    <div class="progress-track"><div class="progress-fill" style="width:{{ $pctTxnHotel }}%;background:linear-gradient(90deg,#0F766E,#2DD4BF);"></div></div>
                </div>
                <div class="progress-item">
                    <div class="progress-meta"><span>Transaksi Restoran</span><span>{{ $totalTransaksiRestoran ?? 0 }} txn</span></div>
                    <div class="progress-track"><div class="progress-fill" style="width:{{ $pctTxnResto }}%;background:linear-gradient(90deg,#B45309,#F59E0B);"></div></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- DROPDOWN PORTAL — dipasang langsung di body, di luar semua wrapper --}}
<div id="exportDropdownPortal">
    <div class="export-section">
        <div class="export-section-title"><i class="fas fa-hotel me-1"></i> Hotel</div>
        <a href="{{ route('dashboard.laporan.pdf.hotel') }}" class="export-item">
            <span class="export-icon pdf"><i class="fas fa-file-pdf"></i></span>
            <span>Unduh PDF Hotel</span>
        </a>
        <a href="{{ route('dashboard.laporan.excel.hotel') }}" class="export-item">
            <span class="export-icon excel"><i class="fas fa-file-excel"></i></span>
            <span>Unduh Excel Hotel</span>
        </a>
    </div>
    <div class="export-section">
        <div class="export-section-title"><i class="fas fa-utensils me-1"></i> Restoran</div>
        <a href="{{ route('dashboard.laporan.pdf.restoran') }}" class="export-item">
            <span class="export-icon pdf"><i class="fas fa-file-pdf"></i></span>
            <span>Unduh PDF Restoran</span>
        </a>
        <a href="{{ route('dashboard.laporan.excel.restoran') }}" class="export-item">
            <span class="export-icon excel"><i class="fas fa-file-excel"></i></span>
            <span>Unduh Excel Restoran</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Animasi stat card ── */
    document.querySelectorAll('.stat-card').forEach(function(card, i) {
        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 80 + i * 70);
    });

    /* ── Charts ── */
    var labels    = @json($bulanLabels);
    var hotelData = @json($reservasiPerBulan->values()->toArray());
    var restoData = @json($pesananPerBulan->values()->toArray());
    var font      = { family: 'Plus Jakarta Sans, sans-serif', size: 11 };
    var sharedOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: font },
            tooltip: { backgroundColor: '#000C3D', cornerRadius: 12, padding: 12 }
        },
        scales: {
            x: { grid: { display: false }, ticks: font },
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: font }
        }
    };

    new Chart(document.getElementById('chartHotel'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Reservasi', data: hotelData,
                borderColor: '#1D4ED8', backgroundColor: 'rgba(29,78,216,0.07)',
                fill: true, tension: .4, pointRadius: 4, borderWidth: 2.5
            }]
        },
        options: sharedOpts
    });

    new Chart(document.getElementById('chartResto'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pesanan', data: restoData,
                borderColor: '#15803D', backgroundColor: 'rgba(21,128,61,0.07)',
                fill: true, tension: .4, pointRadius: 4, borderWidth: 2.5
            }]
        },
        options: sharedOpts
    });

    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Hotel', 'Restoran'],
            datasets: [{
                data: [{{ $totalHotel }}, {{ $totalRestoran }}],
                backgroundColor: ['#1D4ED8', '#15803D'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: font },
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return 'Rp ' + ctx.parsed.toLocaleString('id-ID'); }
                    }
                }
            }
        }
    });

    /* ════════════════════════════════════════════
       EXPORT DROPDOWN — portal ke body
       Tidak ada parent yang bisa menutupi karena
       elemen ini anak langsung dari <body>
    ════════════════════════════════════════════ */
    var btn    = document.getElementById('exportMainBtn');
    var portal = document.getElementById('exportDropdownPortal');

    /* Pindahkan portal ke body agar benar-benar bebas dari semua stacking context */
    document.body.appendChild(portal);

    function positionPortal() {
        var r = btn.getBoundingClientRect();
        portal.style.top   = (r.bottom + window.scrollY + 8) + 'px';
        portal.style.right = (document.documentElement.clientWidth - r.right) + 'px';
        portal.style.left  = 'auto';
        /* Ubah ke fixed saat show agar tidak geser saat scroll */
        portal.style.position = 'fixed';
        portal.style.top = (r.bottom + 8) + 'px';
    }

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        var isOpen = portal.classList.contains('show');
        if (isOpen) {
            portal.classList.remove('show');
            btn.querySelector('.chevron').style.transform = '';
        } else {
            positionPortal();
            portal.classList.add('show');
            btn.querySelector('.chevron').style.transform = 'rotate(180deg)';
        }
    });

    document.addEventListener('click', function() {
        portal.classList.remove('show');
        btn.querySelector('.chevron').style.transform = '';
    });

    portal.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    window.addEventListener('scroll', function() {
        if (portal.classList.contains('show')) positionPortal();
    }, { passive: true });

    window.addEventListener('resize', function() {
        if (portal.classList.contains('show')) positionPortal();
    }, { passive: true });

});

function laporanTab(tab, btn) {
    document.querySelectorAll('.laporan-tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');
}
</script>

@endsection