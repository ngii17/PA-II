<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\Reservasi;
use Illuminate\Support\Facades\Http;

class PembayaranHotelController extends Controller 
{
    public function index() 
    {
        // Keamanan Role
        if (session('user.role') === 'staff_restoran') return abort(403);
        
        $token = session('user.token');
        
        // Ambil data user dari Auth-Service (Sinkronisasi Nama)
        $response = Http::withToken($token)->get(env('MIKRO_URL').'/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        // --- FILTER: Hanya yang sudah Terbayar (2), Check-in (3), atau Selesai (4) ---
        $reservasi = Reservasi::with(['tipeKamar', 'kamar'])
            ->whereIn('status_reservasi_id', [2, 3, 4])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Hitung total hanya dari transaksi yang sudah lunas tersebut
        $totalPendapatanHotel = $reservasi->sum('total_harga');
        $totalTransaksiHotel = $reservasi->count();

        return view('dashboard.hotel.pembayaran.index', compact(
            'reservasi', 'users', 'totalPendapatanHotel', 'totalTransaksiHotel'
        ));
    }
}