<?php

namespace App\Exports;

use App\Models\Hotel\Reservasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;            // Tambahan
use Maatwebsite\Excel\Concerns\ShouldAutoSize;        // Tambahan
use Maatwebsite\Excel\Concerns\WithEvents;            // Tambahan
use Maatwebsite\Excel\Events\AfterSheet;              // Tambahan
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanHotelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return Reservasi::with(['tipeKamar', 'statusReservasi'])
            ->whereIn('status_reservasi_id', [2, 3, 4]) 
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Reservasi',
            'User ID',
            'Tipe Kamar',
            'Check In',
            'Check Out',
            'Durasi',
            'Total Harga (Rp)',
            'Status Transaksi'
        ];
    }

    public function map($reservasi): array
    {
        return [
            'RES-' . $reservasi->id,
            $reservasi->user_id,
            $reservasi->tipeKamar->nama_tipe ?? 'Tipe Dihapus',
            $reservasi->tgl_checkin,
            $reservasi->tgl_checkout,
            $reservasi->total_malam . ' Malam',
            $reservasi->total_harga,
            $reservasi->statusReservasi->nama_status ?? 'LUNAS'
        ];
    }

    /**
     * STYLING HEADER: Bold & Background Kuning/Emas
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Header) dibuat Bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * MEMBUAT GARIS TABEL (BORDER) OTOMATIS
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Tentukan jangkauan tabel (dari A1 sampai kolom H dan baris terakhir data)
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $cellRange = 'A1:H' . $highestRow; 

                // 1. Beri Border ke seluruh tabel
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // 2. Beri Warna Background Kuning pada Header (Baris 1)
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFF00'); // Warna Kuning sesuai gambarmu
            },
        ];
    }
}