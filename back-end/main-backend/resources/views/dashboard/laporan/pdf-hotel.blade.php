<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Reservasi Hotel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 11px; color: #333; padding: 20px; line-height: 1.4; }
        .header { background: #1a1a2e; color: white; padding: 16px 20px; border-radius: 8px; margin-bottom: 16px; }
        .header .hotel-name { font-size: 18px; font-weight: bold; }

        .summary-table { width: 100%; margin-bottom: 16px; border-collapse: separate; border-spacing: 8px 0; }
        .summary-box { width: 33.33%; padding: 12px; border-radius: 6px; text-align: center; }
        .blue { background: #EEF2FF; border-left: 4px solid #0d6efd; color: #0d6efd; }
        .green { background: #ECFDF5; border-left: 4px solid #198754; color: #198754; }
        .gold { background: #FFFBEB; border-left: 4px solid #f59e0b; color: #d97706; }
        .label { font-size: 10px; text-transform: uppercase; margin-bottom: 4px; }
        .value { font-size: 15px; font-weight: bold; }

        .section-title { font-size: 12px; font-weight: bold; color: #1a1a2e; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #1a1a2e; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #1a1a2e; color: white; }
        th { padding: 8px; text-align: left; border: 1px solid #1a1a2e; }
        td { padding: 7px; border: 1px solid #e5e7eb; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; color: white; }
        .bg-success { background: #198754; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="hotel-name">Purnama Hotel & Resto</div>
        <div class="subtitle">Laporan Reservasi Hotel</div>
        <div class="print-date">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
    </div>

    <table class="summary-table">
        <tr>
            <td class="summary-box blue">
                <div class="label">Total Reservasi</div>
                <div class="value">{{ $reservasi->count() }}</div>
            </td>
            <td class="summary-box green">
                <div class="label">Telah Dibayar</div>
                <div class="value">{{ $terbayarCount }}</div>
            </td>
            <td class="summary-box gold">
                <div class="label">Total Pendapatan</div>
                <div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Data Reservasi</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tipe Kamar</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservasi as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r->tipeKamar->nama_tipe ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($r->tgl_checkin)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($r->tgl_checkout)->format('d/m/Y') }}</td>
                <td style="font-weight: bold;">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                <td><span class="badge bg-success">{{ $r->statusReservasi->nama_status ?? 'Selesai' }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer text-center">Dokumen otomatis sistem &bull; {{ date('Y') }}</div>
</body>
</html>
