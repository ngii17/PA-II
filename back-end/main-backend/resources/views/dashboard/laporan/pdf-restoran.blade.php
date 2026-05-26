<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Restoran</title>
    <style>
        /* CSS Sama dengan Hotel di atas agar seragam */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 11px; color: #333; padding: 20px; line-height: 1.4; }
        .header { background: #1a1a2e; color: white; padding: 16px 20px; border-radius: 8px; margin-bottom: 16px; }
        .header .hotel-name { font-size: 18px; font-weight: bold; }
        .summary-table { width: 100%; margin-bottom: 16px; border-collapse: separate; border-spacing: 8px 0; }
        .summary-box { width: 33.33%; padding: 12px; border-radius: 6px; text-align: center; }
        .blue { background: #EEF2FF; border-left: 4px solid #0d6efd; color: #0d6efd; }
        .green { background: #ECFDF5; border-left: 4px solid #198754; color: #198754; }
        .gold { background: #FFFBEB; border-left: 4px solid #f59e0b; color: #d97706; }
        .section-title { font-size: 12px; font-weight: bold; color: #1a1a2e; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #1a1a2e; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a1a2e; color: white; padding: 8px; text-align: left; }
        td { padding: 7px; border: 1px solid #e5e7eb; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <div class="hotel-name">Purnama Hotel & Resto</div>
        <div class="subtitle">Laporan Transaksi Restoran</div>
        <div class="print-date">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
    </div>

    <table class="summary-table">
        <tr>
            <td class="summary-box blue"><div>TOTAL PESANAN</div><div class="value">{{ $totalPesanan }}</div></td>
            <td class="summary-box green"><div>PESANAN LUNAS</div><div class="value">{{ $pesananLunas }}</div></td>
            <td class="summary-box gold"><div>TOTAL PENDAPATAN</div><div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div></td>
        </tr>
    </table>

    <div class="section-title">Data Pesanan Restoran</div>
    <table>
        <thead>
            <tr>
                <th width="30">#</th>
                <th>Pelanggan</th>
                <th>Rincian Menu</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan as $i => $p)
            @php $u = $users[$p->user_id] ?? null; @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $u['full_name'] ?? 'Tamu Umum' }}</strong></td>
                <td>
                    @foreach($p->details as $d)
                        • {{ $d->menu->nama_menu ?? '-' }} ({{ $d->jumlah }}x)<br>
                    @endforeach
                </td>
                <td style="font-weight:bold;">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td>{{ $p->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
