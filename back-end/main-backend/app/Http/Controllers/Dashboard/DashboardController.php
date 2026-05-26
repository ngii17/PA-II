<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\hotel\Reservasi;
use App\Models\hotel\Kamar;
use App\Models\restoran\PesananMenu;
use App\Models\restoran\Menu;
use App\Models\event\Event;
use App\Models\hotel\Promo;
use App\Models\hotel\UlasanHotel;
use App\Models\restoran\UlasanRestoran;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = session('user');
        $role = $user['role'];
        $token = session('user.token');
        $data = [];

        // Ambil data User dari Microservice
        try {
            $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
            $data['users'] = collect($response->json('data') ?? [])->keyBy('id');
        } catch (\Exception $e) {
            $data['users'] = collect([]);
        }

        // ==========================================
        // KHUSUS ADMIN (Management View)
        // ==========================================
        if ($role === 'admin') {
            $data['total_pengguna'] = $data['users']->count();
            $data['total_promo']    = Promo::count();
            $data['total_ulasan']   = UlasanHotel::count() + UlasanRestoran::count();

            // Pendapatan Gabungan (Hotel Selesai/Lunas + Resto Lunas)
            $data['total_pendapatan'] = Reservasi::whereIn('status_reservasi_id', [2, 3])->sum('total_harga') +
                                        PesananMenu::where('status_pembayaran_id', 2)->sum('total_harga');

            // Data Grafik 6 Bulan
            $bulan = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M Y'));
            $data['bulan_labels'] = $bulan->values();
            $data['reservasi_per_bulan'] = $bulan->map(fn($b) => Reservasi::whereRaw("TO_CHAR(created_at, 'Mon YYYY') = ?", [$b])->whereIn('status_reservasi_id', [2,3])->count())->values();
            $data['pesanan_per_bulan'] = $bulan->map(fn($b) => PesananMenu::whereRaw("TO_CHAR(created_at, 'Mon YYYY') = ?", [$b])->where('status_pembayaran_id', 2)->count())->values();

            $data['status_reservasi'] = [
                Reservasi::where('status_reservasi_id', 2)->count(), // Terbayar
                Reservasi::where('status_reservasi_id', 1)->count(), // Pending
                Reservasi::where('status_reservasi_id', 3)->count(), // Selesai
                Reservasi::where('status_reservasi_id', 4)->count(), // Batal
            ];
        }

        // ==========================================
        // KHUSUS STAFF HOTEL (Operational View)
        // ==========================================
        if ($role === 'staff_hotel') {
            $today = Carbon::today();
            $data['total_reservasi'] = Reservasi::count();
            $data['reservasi_pending'] = Reservasi::where('status_reservasi_id', 1)->count();
            $data['checkin_today'] = Reservasi::whereDate('tgl_checkin', $today)->where('status_reservasi_id', '!=', 4)->count();
            $data['checkout_today'] = Reservasi::whereDate('tgl_checkout', $today)->where('status_reservasi_id', '!=', 4)->count();
            $data['kamar_tersedia'] = Kamar::where('status_kamar_id', 1)->count();
            $data['kamar_terisi'] = Kamar::where('status_kamar_id', 2)->count();

            $data['arrival_today'] = Reservasi::with(['tipeKamar', 'kamar'])
                ->whereDate('tgl_checkin', $today)
                ->where('status_reservasi_id', '!=', 4)->get();
        }

        // ==========================================
        // KHUSUS STAFF RESTORAN (Kitchen/Stok View)
        // ==========================================
        if ($role === 'staff_restoran') {
            $data['total_pesanan'] = PesananMenu::count();
            $data['pesanan_pending'] = PesananMenu::where('status_pembayaran_id', 1)->count();
            $data['total_menu'] = Menu::count();
            $data['menu_habis'] = Menu::where('stok', 0)->count();
            $data['event_aktif'] = Event::where('is_active', true)->count();
            $data['total_pendapatan_resto'] = PesananMenu::where('status_pembayaran_id', 2)->sum('total_harga');

            $data['pesanan_terbaru'] = PesananMenu::with(['statusPembayaran'])
                ->orderBy('created_at', 'desc')->limit(5)->get();
        }

        return view('dashboard.index', compact('user', 'data'));
    }
}
