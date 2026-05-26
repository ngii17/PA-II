<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\UlasanHotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UlasanHotelController extends Controller
{
    /**
     * Menampilkan daftar ulasan hotel.
     * Dapat diakses oleh Admin dan Staff Hotel.
     */
    public function index()
    {
        // 1. Ambil data user dari microservice untuk mendapatkan nama pelanggan asli
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        // 2. Ambil ulasan KHUSUS Hotel saja dengan relasi tipe kamar
        $ulasan = UlasanHotel::with('tipeKamar')->orderBy('created_at', 'desc')->get();

        // 3. Hitung statistik ringkas untuk ditampilkan di card dashboard ulasan
        $totalUlasan = $ulasan->count();
        $rataRating = $ulasan->avg('rating') ?? 0;

        return view('dashboard.hotel.ulasan.index', compact('ulasan', 'users', 'totalUlasan', 'rataRating'));
    }

    /**
     * Fitur Sembunyikan/Tampilkan Ulasan (Toggle).
     * PENGAMANAN: Khusus Admin saja.
     */
    public function toggle($id)
    {
        // 1. CEK KEAMANAN ROLE
        // Jika user yang login bukan 'admin', maka tolak akses
        if (session('user.role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Admin yang memiliki wewenang memoderasi ulasan.');
        }

        // 2. PROSES TOGGLE STATUS VISIBILITAS
        $ulasan = UlasanHotel::findOrFail($id);

        // Mengubah status is_hidden menjadi kebalikannya (True ke False, atau sebaliknya)
        $ulasan->update([
            'is_hidden' => !$ulasan->is_hidden
        ]);

        // 3. REDIRECT DENGAN PESAN DINAMIS
        $status = $ulasan->is_hidden ? 'disembunyikan' : 'ditampilkan';
        return redirect()->back()->with('success', "Ulasan hotel berhasil $status!");
    }
}
