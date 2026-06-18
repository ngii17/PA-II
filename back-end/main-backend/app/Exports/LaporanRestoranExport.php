<?php

namespace App\Exports;

use App\Models\Restoran\PesananMenu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanRestoranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return PesananMenu::with(['statusPembayaran'])
            ->where('status_pembayaran_id', 2)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Pesanan',
            'User ID',
            'Lokasi (Meja/Kamar)',
            'Metode Pembayaran',
            'Total Bayar (Rp)',
            'Status',
            'Tanggal Transaksi'
        ];
    }

    public function map($pesanan): array
    {
        return [
            'ORD-' . $pesanan->id,
            $pesanan->user_id,
            $pesanan->nomor_lokasi ?? '-', 
            $pesanan->metode_pembayaran,
            $pesanan->total_harga,
            $pesanan->statusPembayaran->nama_status ?? 'LUNAS',
            $pesanan->created_at->format('d-m-Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $cellRange = 'A1:G' . $highestRow; 

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Warna Oranye untuk Header Restoran (biar beda dengan Hotel)
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFCC00'); 
            },
        ];
    }
}