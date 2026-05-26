<?php

namespace App\Exports;

use App\Models\restoran\PesananMenu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanRestoranExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Ambil data pesanan yang lunas
     */
    public function collection()
    {
        return PesananMenu::with(['statusPembayaran'])
            ->where('status_pembayaran_id', 2)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Header Kolom Excel
     */
    public function headings(): array
    {
        return [
            'ID Pesanan',
            'User ID Pelanggan',
            'Nomor Meja',
            'Metode Pembayaran',
            'Total Bayar',
            'Status',
            'Tanggal Transaksi'
        ];
    }

    /**
     * Mapping data
     */
    public function map($pesanan): array
    {
        return [
            'ORD-' . $pesanan->id,
            $pesanan->user_id,
            $pesanan->nomor_meja ?? '-',
            $pesanan->metode_pembayaran,
            $pesanan->total_harga,
            $pesanan->statusPembayaran->nama_status ?? 'Lunas',
            $pesanan->created_at->format('d-m-Y H:i')
        ];
    }
}
