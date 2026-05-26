<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\hotel\Reservasi;
use App\Models\restoran\PesananMenu;
use App\Exports\LaporanHotelExport;
use App\Exports\LaporanRestoranExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * TAMPILAN UTAMA LAPORAN (STATISTIK & GRAFIK)
     */
    public function index()
    {
        // ID Status Sukses (2: Terbayar, 3: Selesai)
        $statusSuksesHotel = [2, 3];

        // 1. Hitung Total Pendapatan
        $totalHotel = Reservasi::whereIn('status_reservasi_id', $statusSuksesHotel)->sum('total_harga');
        $totalRestoran = PesananMenu::where('status_pembayaran_id', 2)->sum('total_harga');

        // 2. Statistik Transaksi
        $totalTransaksiHotel = Reservasi::whereIn('status_reservasi_id', $statusSuksesHotel)->count();
        $totalTransaksiRestoran = PesananMenu::where('status_pembayaran_id', 2)->count();

        // 3. Logika Grafik (6 Bulan Terakhir)
        // Kita buat label bulan dulu agar grafik urut dan tidak kosong jika ada bulan yang 0 transaksi
        $bulanLabels = collect(range(5, 0))->map(function($i) {
            return now()->subMonths($i)->format('M Y');
        })->values();

        // Data Grafik Hotel
        $rawHotel = Reservasi::selectRaw("TO_CHAR(created_at, 'Mon YYYY') as bulan, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->whereIn('status_reservasi_id', $statusSuksesHotel)
            ->groupByRaw("TO_CHAR(created_at, 'Mon YYYY')")
            ->get();

        $reservasiPerBulan = $bulanLabels->map(function($label) use ($rawHotel) {
            return $rawHotel->where('bulan', $label)->first()->total ?? 0;
        });

        // Data Grafik Restoran
        $rawResto = PesananMenu::selectRaw("TO_CHAR(created_at, 'Mon YYYY') as bulan, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->where('status_pembayaran_id', 2)
            ->groupByRaw("TO_CHAR(created_at, 'Mon YYYY')")
            ->get();

        $pesananPerBulan = $bulanLabels->map(function($label) use ($rawResto) {
            return $rawResto->where('bulan', $label)->first()->total ?? 0;
        });

        return view('dashboard.laporan.index', compact(
            'totalHotel', 'totalRestoran', 'reservasiPerBulan',
            'pesananPerBulan', 'totalTransaksiHotel', 'totalTransaksiRestoran', 'bulanLabels'
        ));
    }

    /**
     * EXPORT EXCEL
     */
    public function exportExcelHotel()
    {
        return Excel::download(new LaporanHotelExport, 'laporan-hotel-' . date('Ymd') . '.xlsx');
    }

    public function exportExcelRestoran()
    {
        return Excel::download(new LaporanRestoranExport, 'laporan-restoran-' . date('Ymd') . '.xlsx');
    }

    /**
     * EXPORT PDF HOTEL
     */
    public function exportPdfHotel()
    {
        $reservasi = Reservasi::with(['tipeKamar', 'statusReservasi'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPendapatan = $reservasi->whereIn('status_reservasi_id', [2, 3])->sum('total_harga');
        $terbayarCount = $reservasi->whereIn('status_reservasi_id', [2, 3])->count();

        $pdf = Pdf::loadView('dashboard.laporan.pdf-hotel', compact(
            'reservasi', 'totalPendapatan', 'terbayarCount'
        ));

        return $pdf->download('laporan-hotel.pdf');
    }

    /**
     * EXPORT PDF RESTORAN
     */
    public function exportPdfRestoran()
    {
        // 1. Ambil data pesanan
        $pesanan = PesananMenu::with(['details.menu'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Ambil data user dari microservice agar nama pelanggan muncul di PDF
        $token = session('user.token');
        try {
            $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
            $users = collect($response->json('data') ?? [])->keyBy('id');
        } catch (\Exception $e) {
            $users = collect([]);
        }

        // 3. Hitung Statistik untuk Header PDF
        $totalPesanan = $pesanan->count();
        $pesananLunas = $pesanan->where('status_pembayaran_id', 2)->count();
        $totalPendapatan = $pesanan->where('status_pembayaran_id', 2)->sum('total_harga');

        $pdf = Pdf::loadView('dashboard.laporan.pdf-restoran', compact(
            'pesanan', 'users', 'totalPesanan', 'pesananLunas', 'totalPendapatan'
        ));

        return $pdf->download('laporan-restoran.pdf');
    }
}
