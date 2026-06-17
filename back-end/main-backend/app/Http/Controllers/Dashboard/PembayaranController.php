<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Hotel\Reservasi;
use App\Models\Restoran\PesananMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PembayaranController extends Controller
{
    public function index()
    {
        // 1. Ambil Token & Data User dari Microservice
        $token = session('user.token');
        try {
            $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
            $users = collect($response->json('data') ?? [])->keyBy('id');
        } catch (\Exception $e) {
            $users = collect([]);
        }

        // 2. Ambil Data Pembayaran Hotel (Reservasi yang Terbayar/Selesai)
        $pembayaranHotel = Reservasi::with(['tipeKamar', 'statusReservasi'])
            ->whereIn('status_reservasi_id', [2, 3]) // 2:Terbayar, 3:Selesai
            ->orderBy('updated_at', 'desc')
            ->get();

        // 3. Ambil Data Pembayaran Restoran (Pesanan yang Lunas)
        $pembayaranResto = PesananMenu::with([
        'details.menu' => function($query) {
            $query->withTrashed();
        },
        'statusPembayaran'
    ])->where('status_pembayaran_id', 2)->get();

        // 4. Hitung Statistik untuk Admin
        $stats = [
            'total_hotel' => $pembayaranHotel->sum('total_harga'),
            'total_resto' => $pembayaranResto->sum('total_harga'),
            'count_hotel' => $pembayaranHotel->count(),
            'count_resto' => $pembayaranResto->count(),
        ];

        return view('dashboard.pembayaran.index', compact('pembayaranHotel', 'pembayaranResto', 'users', 'stats'));
    }
}
