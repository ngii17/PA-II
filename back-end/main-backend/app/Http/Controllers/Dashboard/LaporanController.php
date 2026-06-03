<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\hotel\Reservasi;
use App\Models\restoran\PesananMenu;
use App\Exports\LaporanHotelExport;
use App\Exports\LaporanRestoranExport;
use Illuminate\Support\Facades\DB; // WAJIB ADA untuk selectRaw
use Barryvdh\DomPDF\Facade\Pdf as PDF; 
use Maatwebsite\Excel\Facades\Excel as Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * 1. TAMPILAN UTAMA LAPORAN (STATISTIK & GRAFIK)
     */
    public function index()
    {
        // PENGAMANAN: Hanya Admin yang bisa masuk
        if (session('user.role') !== 'admin') {
            return abort(403, 'Akses Terbatas: Laporan hanya dapat diakses oleh Admin.');
        }

        // SINKRONISASI: Status sukses (Hotel: Terbayar, Check-in, Selesai | Resto: Lunas)
        $statusLunasHotel = [2, 3, 4];
        $statusLunasResto = [2];

        // 1. Hitung Total Pendapatan
        $totalHotel = Reservasi::whereIn('status_reservasi_id', $statusLunasHotel)->sum('total_harga');
        $totalRestoran = PesananMenu::whereIn('status_pembayaran_id', $statusLunasResto)->sum('total_harga');

        // 2. Statistik Transaksi
        $totalTransaksiHotel = Reservasi::whereIn('status_reservasi_id', $statusLunasHotel)->count();
        $totalTransaksiRestoran = PesananMenu::whereIn('status_pembayaran_id', $statusLunasResto)->count();
        
        // FIX ERROR VIEW: Sediakan variabel yang diminta oleh Dashboard utama kamu
        $totalPesanan = $totalTransaksiRestoran;

        // 3. Logika Grafik (6 Bulan Terakhir)
        $bulanLabels = collect(range(5, 0))->map(function($i) {
            return now()->subMonths($i)->format('M Y');
        })->values();

        // Data Grafik Hotel (PostgreSQL format)
        $rawHotel = Reservasi::selectRaw("TO_CHAR(created_at, 'Mon YYYY') as bulan, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->whereIn('status_reservasi_id', $statusLunasHotel)
            ->groupByRaw("TO_CHAR(created_at, 'Mon YYYY')")
            ->get();

        $reservasiPerBulan = $bulanLabels->map(function($label) use ($rawHotel) {
            return $rawHotel->where('bulan', $label)->first()->total ?? 0;
        });

        // Data Grafik Restoran
        $rawResto = PesananMenu::selectRaw("TO_CHAR(created_at, 'Mon YYYY') as bulan, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->whereIn('status_pembayaran_id', $statusLunasResto)
            ->groupByRaw("TO_CHAR(created_at, 'Mon YYYY')")
            ->get();

        $pesananPerBulan = $bulanLabels->map(function($label) use ($rawResto) {
            return $rawResto->where('bulan', $label)->first()->total ?? 0;
        });

        return view('dashboard.laporan.index', compact(
            'totalHotel', 'totalRestoran', 'reservasiPerBulan',
            'pesananPerBulan', 'totalTransaksiHotel', 'totalTransaksiRestoran', 
            'totalPesanan', 'bulanLabels'
        ));
    }

    /**
     * 2. EXPORT EXCEL
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
     * 3. EXPORT PDF HOTEL
     */
    public function exportPdfHotel()
    {
        $reservasi = Reservasi::with(['tipeKamar', 'statusReservasi'])
            ->whereIn('status_reservasi_id', [2, 3, 4])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPendapatan = $reservasi->sum('total_harga');
        $terbayarCount = $reservasi->count();

        $pdf = PDF::loadView('dashboard.laporan.pdf-hotel', compact(
            'reservasi', 'totalPendapatan', 'terbayarCount'
        ));

        return $pdf->download('laporan-hotel-purnama.pdf');
    }

    /**
     * 4. EXPORT PDF RESTORAN (SINKRON NAMA DARI PORT 8000)
     */
    public function exportPdfRestoran()
    {
        $pesanan = PesananMenu::with(['details.menu'])
            ->whereIn('status_pembayaran_id', [2])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil data user dari Auth-Service secara dinamis
        $users = collect();
        try {
            $token = session('user.token');
            $baseUrl = env('MIKRO_URL', 'http://127.0.0.1:8000');
            $response = Http::timeout(5)->withToken($token)->get($baseUrl . '/api/internal/user-tokens');
            if ($response->successful()) {
                $users = collect($response->json('data'))->keyBy('user_id');
            }
        } catch (\Exception $e) {
            Log::error("Gagal sinkron nama untuk PDF Laporan: " . $e->getMessage());
        }

        $totalPesanan = $pesanan->count();
        $totalPendapatan = $pesanan->sum('total_harga');

        $pdf = PDF::loadView('dashboard.laporan.pdf-restoran', compact(
            'pesanan', 'users', 'totalPesanan', 'totalPendapatan'
        ));

        return $pdf->download('laporan-restoran-purnama.pdf');
    }
}