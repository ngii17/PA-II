<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Hotel\Reservasi;
use App\Models\Hotel\Kamar;
use App\Models\restoran\PesananMenu; // Gunakan huruf kecil sesuai folder kamu
use App\Models\restoran\Menu;
use App\Models\event\Event;
use App\Models\Hotel\Promo;
use App\Models\Hotel\UlasanHotel;
use App\Models\restoran\UlasanRestoran;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. SINKRONISASI ROLE
        $user = session('user');
        $role = str_replace('_', ' ', strtolower($user['role'] ?? ''));
        $token = session('user.token');
        $data = [];

        // 2. Ambil data User dari Port 8000
        try {
            $baseUrl = env('MIKRO_URL', 'http://10.187.82.132:8000');
            $response = Http::timeout(5)->withToken($token)->get($baseUrl . '/api/internal/user-tokens');
            $data['users'] = collect($response->json('data') ?? [])->keyBy('user_id');
        } catch (\Exception $e) {
            $data['users'] = collect([]);
        }

        // ============================================================
        // 🛡️ BAGIAN 1: DASHBOARD ADMIN (Laporan & Grafik)
        // ============================================================
        if ($role === 'admin') {
            $data['total_pengguna'] = $data['users']->count();
            $data['total_promo']    = Promo::count();
            $data['total_ulasan']   = UlasanHotel::count() + UlasanRestoran::count();

            // --- TAMBAHAN: Data untuk widget "Ulasan Terbaru" ---
            $ulasanHotelTerbaru = UlasanHotel::latest()->take(10)->get();
            $ulasanRestoTerbaru = UlasanRestoran::latest()->take(10)->get();

            $data['ulasan'] = $ulasanHotelTerbaru->concat($ulasanRestoTerbaru)
                ->sortByDesc('created_at')
                ->take(5)
                ->values();
            // --- AKHIR TAMBAHAN ---

            // Status yang dianggap SAH sebagai uang masuk
            $statusLunasHotel = [2, 3, 4]; // Terbayar, Check-in, Selesai
            $statusLunasResto = [2];       // Lunas

            $data['total_pendapatan'] = Reservasi::whereIn('status_reservasi_id', $statusLunasHotel)->sum('total_harga') +
                                        PesananMenu::whereIn('status_pembayaran_id', $statusLunasResto)->sum('total_harga');

            // --- PERBAIKAN GRAFIK: LOGIKA PHP (LEBIH AKURAT) ---
            $labels = [];
            $hotelData = [];
            $restoData = [];

            for ($i = 5; $i >= 0; $i--) {
                $monthDate = now()->subMonths($i);
                $monthName = $monthDate->format('M Y');
                $labels[] = $monthName;

                // Ambil data Hotel di bulan tersebut
                $countHotel = Reservasi::whereIn('status_reservasi_id', $statusLunasHotel)
                    ->whereMonth('created_at', $monthDate->month)
                    ->whereYear('created_at', $monthDate->year)
                    ->count();
                $hotelData[] = $countHotel;

                // Ambil data Restoran di bulan tersebut
                $countResto = PesananMenu::whereIn('status_pembayaran_id', $statusLunasResto)
                    ->whereMonth('created_at', $monthDate->month)
                    ->whereYear('created_at', $monthDate->year)
                    ->count();
                $restoData[] = $countResto;
            }

            $data['bulan_labels'] = $labels;
            $data['reservasi_per_bulan'] = $hotelData;
            $data['pesanan_per_bulan'] = $restoData;

            // Statistik Pie Chart
            $data['status_reservasi'] = [
                Reservasi::where('status_reservasi_id', 1)->count(),
                Reservasi::where('status_reservasi_id', 2)->count(),
                Reservasi::where('status_reservasi_id', 3)->count(),
                Reservasi::where('status_reservasi_id', 4)->count(),
                Reservasi::where('status_reservasi_id', 5)->count(),
            ];
        }

        // ============================================================
        // 🏨 BAGIAN 2: DASHBOARD STAFF HOTEL
        // ============================================================
        if ($role === 'staff hotel') {
            $today = Carbon::today();
            $data['total_reservasi']   = Reservasi::count();
            $data['reservasi_pending'] = Reservasi::where('status_reservasi_id', 1)->count();
            $data['checkin_today']     = Reservasi::whereDate('tgl_checkin', $today)->where('status_reservasi_id', '!=', 5)->count();
            $data['checkout_today']    = Reservasi::whereDate('tgl_checkout', $today)->where('status_reservasi_id', '!=', 5)->count();
            $data['kamar_tersedia']    = Kamar::where('status_kamar_id', 1)->count();
            $data['kamar_terisi']      = Kamar::where('status_kamar_id', 2)->count();

            $data['arrival_today'] = Reservasi::with(['tipeKamar', 'kamar'])
                ->whereDate('tgl_checkin', $today)
                ->where('status_reservasi_id', '!=', 5)->get();
                
                // TAMBAHAN: Tren reservasi 6 bulan terakhir
                    $statusLunasHotel = [2, 3, 4];
                    $labelsHotel = [];
                    $hotelData = [];

                    for ($i = 5; $i >= 0; $i--) {
                        $monthDate = now()->subMonths($i);
                        $labelsHotel[] = $monthDate->format('M Y');

                        $hotelData[] = Reservasi::whereIn('status_reservasi_id', $statusLunasHotel)
                            ->whereMonth('created_at', $monthDate->month)
                            ->whereYear('created_at', $monthDate->year)
                            ->count();
                    

                    $data['bulan_labels'] = $labelsHotel;
                    $data['reservasi_per_bulan'] = $hotelData;
                }
        }

        // ============================================================
        // 🍽️ BAGIAN 3: DASHBOARD STAFF RESTORAN
        // ============================================================

        if ($role === 'staff restoran') {
            $data['total_pesanan']   = PesananMenu::count();
            $data['pesanan_pending'] = PesananMenu::where('status_pembayaran_id', 1)->count();
            $data['total_menu']      = Menu::count();
            $data['menu_habis']      = Menu::where('stok', 0)->count();
            $data['event_aktif']     = Event::where('is_active', true)->count();
            $data['total_pendapatan_resto'] = PesananMenu::where('status_pembayaran_id', 2)->sum('total_harga');

            $data['pesanan_terbaru'] = PesananMenu::with(['statusPembayaran'])
                ->orderBy('created_at', 'desc')->limit(5)->get();

            // TAMBAHAN: Tren pesanan resto 6 bulan terakhir
            $statusLunasResto = [2]; // Lunas
            $labelsResto = [];
            $restoData = [];

            for ($i = 5; $i >= 0; $i--) {
                $monthDate = now()->subMonths($i);
                $labelsResto[] = $monthDate->format('M Y');

                $restoData[] = PesananMenu::whereIn('status_pembayaran_id', $statusLunasResto)
                    ->whereMonth('created_at', $monthDate->month)
                    ->whereYear('created_at', $monthDate->year)
                    ->count();
            }

            $data['bulan_labels'] = $labelsResto;
            $data['pesanan_per_bulan'] = $restoData;
        }

        return view('dashboard.index', compact('user', 'data'));
    }
}