<?php

namespace App\Exports;

use App\Models\hotel\Reservasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanHotelExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Ambil data reservasi yang sukses (Terbayar & Selesai)
     */
    public function collection()
    {
        return Reservasi::with(['tipeKamar', 'statusReservasi'])
            ->whereIn('status_reservasi_id', [2, 3])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Header atau Judul Kolom di Excel
     */
    public function headings(): array
    {
        return [
            'ID Reservasi',
            'User ID Pelanggan',
            'Tipe Kamar',
            'Check In',
            'Check Out',
            'Total Malam',
            'Total Harga',
            'Status'
        ];
    }

    /**
     * Memetakan data agar rapi di Excel
     */
    public function map($reservasi): array
    {
        return [
            'RES-' . $reservasi->id,
            $reservasi->user_id,
            $reservasi->tipeKamar->nama_tipe ?? '-',
            $reservasi->tgl_checkin,
            $reservasi->tgl_checkout,
            $reservasi->total_malam . ' Malam',
            $reservasi->total_harga,
            $reservasi->statusReservasi->nama_status ?? '-'
        ];
    }
}
