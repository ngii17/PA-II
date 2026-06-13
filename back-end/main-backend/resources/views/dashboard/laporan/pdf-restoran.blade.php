<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Restoran — Purnama Hotel & Resto</title>
    <style>
        /* ============================================================
           RESET & BASE (Kompatibel DOMPDF)
           ============================================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #fff;
            padding: 28px 32px;
            line-height: 1.5;
        }

        /* ============================================================
           HEADER BRAND
           ============================================================ */
        .doc-header {
            background: #000C3D;
            border-radius: 10px;
            padding: 22px 28px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .doc-header::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 180px; height: 100%;
            background: linear-gradient(135deg, transparent 40%, rgba(212,175,55,.12) 100%);
        }
        .doc-header-inner {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand-name {
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: -.3px;
            margin-bottom: 3px;
        }
        .brand-tagline {
            font-size: 9px;
            color: rgba(255,255,255,.55);
            text-transform: uppercase;
            letter-spacing: 1.8px;
            font-weight: bold;
        }
        .doc-meta {
            text-align: right;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
            color: #D4AF37;
            margin-bottom: 4px;
        }
        .doc-date {
            font-size: 9px;
            color: rgba(255,255,255,.5);
        }
        .gold-bar {
            height: 3px;
            background: linear-gradient(90deg, #D4AF37 0%, #e8c84a 50%, transparent 100%);
            border-radius: 0 0 3px 3px;
            margin-top: 14px;
        }

        /* ============================================================
           SUMMARY CARDS
           ============================================================ */
        .summary-row {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 20px;
        }
        .summary-cell {
            width: 33.33%;
            padding: 14px 16px;
            border-radius: 8px;
            vertical-align: top;
        }
        .sc-blue   { background: #EEF2FF; border-left: 4px solid #1D4ED8; }
        .sc-green  { background: #ECFDF5; border-left: 4px solid #15803D; }
        .sc-gold   { background: #FFFBEB; border-left: 4px solid #D4AF37; }

        .s-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .sc-blue  .s-label  { color: #1D4ED8; }
        .sc-green .s-label  { color: #15803D; }
        .sc-gold  .s-label  { color: #B45309; }

        .s-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 2px;
        }
        .s-sub {
            font-size: 8px;
            color: #94a3b8;
        }

        /* ============================================================
           SECTION TITLE
           ============================================================ */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #000C3D;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #00197D;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .section-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #D4AF37;
            flex-shrink: 0;
        }

        /* ============================================================
           DATA TABLE
           ============================================================ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table thead tr {
            background: #00197D;
        }
        .data-table thead th {
            padding: 9px 11px;
            text-align: left;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: bold;
            color: rgba(255,255,255,.85);
            border: none;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .data-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }
        .data-table tbody td {
            padding: 8px 11px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5px;
            color: #334155;
            vertical-align: top;
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .td-num {
            color: #94a3b8;
            font-weight: bold;
            text-align: center;
        }
        .td-bold {
            font-weight: bold;
            color: #0f172a;
        }
        .td-money {
            font-weight: bold;
            color: #15803D;
        }
        .td-center {
            text-align: center;
        }
        .td-muted {
            color: #64748b;
            font-size: 9px;
        }

        /* ============================================================
           MENU LIST INSIDE CELL
           ============================================================ */
        .menu-list {
            line-height: 1.8;
        }
        .menu-item {
            display: block;
            font-size: 9px;
            color: #475569;
            padding: 1px 0;
        }
        .menu-item::before {
            content: '• ';
            color: #D4AF37;
            font-weight: bold;
        }

        /* ============================================================
           BADGE STATUS
           ============================================================ */
        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 99px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success { background: #dcfce7; color: #065f46; }
        .badge-warn    { background: #fef9c3; color: #854d0e; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }

        /* ============================================================
           FOOTER
           ============================================================ */
        .doc-footer {
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-left {
            font-size: 8px;
            color: #94a3b8;
        }
        .footer-brand {
            font-size: 8px;
            font-weight: bold;
            color: #00197D;
        }
        .footer-right {
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="doc-header">
        <div class="doc-header-inner">
            <div>
                <div class="brand-name">Purnama Hotel &amp; Resto</div>
                <div class="brand-tagline">Laporan Manajemen Internal</div>
            </div>
            <div class="doc-meta">
                <div class="doc-title">Laporan Transaksi Restoran</div>
                <div class="doc-date">Dicetak: {{ now()->format('d F Y, H:i') }} WIB</div>
            </div>
        </div>
        <div class="gold-bar"></div>
    </div>

    <!-- SUMMARY CARDS -->
    <table class="summary-row">
        <tr>
            <td class="summary-cell sc-blue">
                <div class="s-label">Total Pesanan</div>
                <div class="s-value">{{ $totalPesanan }}</div>
                <div class="s-sub">Semua status</div>
            </td>
            <td class="summary-cell sc-green">
                <div class="s-label">Pesanan Lunas</div>
                <div class="s-value">{{ $pesananLunas }}</div>
                <div class="s-sub">Transaksi sukses</div>
            </td>
            <td class="summary-cell sc-gold">
                <div class="s-label">Total Pendapatan</div>
                <div class="s-value" style="font-size:13px;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="s-sub">Akumulasi lunas</div>
             </td>
        </tr>
    </table>

    <!-- DATA TABLE -->
    <div class="section-title">
        <span class="section-dot"></span> Data Pesanan Restoran
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:24px;" class="td-center">#</th>
                <th>Pelanggan</th>
                <th>Rincian Menu</th>
                <th>Total Harga</th>
                <th>Tanggal</th>
                <th class="td-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesanan as $i => $p)
            @php
                $user = $users[$p->user_id] ?? null;
                $nama = $user['full_name'] ?? 'Tamu Umum';
                $email = $user['email'] ?? '-';
                $statusLabel = $p->statusPembayaran->nama_status ?? 'Pending';
                $statusRaw = strtolower($statusLabel);
                $badgeClass = match(true) {
                    in_array($statusRaw, ['lunas', 'terbayar', 'selesai']) => 'badge-success',
                    $statusRaw === 'pending' => 'badge-warn',
                    in_array($statusRaw, ['batal', 'dibatalkan']) => 'badge-danger',
                    default => 'badge-info',
                };
            @endphp
            <tr>
                <td class="td-num">{{ $i + 1 }}</td>
                <td>
                    <span class="td-bold">{{ $nama }}</span><br>
                    <span class="td-muted">{{ $email }}</span>
                </td>
                <td>
                    <div class="menu-list">
                        @foreach($p->details as $d)
                            <span class="menu-item">{{ $d->menu->nama_menu ?? 'Menu dihapus' }} &times;{{ $d->jumlah }}</span>
                        @endforeach
                    </div>
                </td>
                <td class="td-money">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td>{{ $p->created_at->format('d M Y') }}</td>
                <td class="td-center">
                    <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">
                    Tidak ada data pesanan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="doc-footer">
        <div class="footer-left">
            <span class="footer-brand">Purnama Hotel &amp; Resto</span><br>
            Dokumen ini digenerate otomatis oleh sistem manajemen.
        </div>
        <div class="footer-right">
            Halaman 1 &bull; {{ now()->format('Y') }}
        </div>
    </div>

</body>
</html>