<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\UlasanHotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UlasanHotelController extends Controller
{
    /**
     * 1. MENAMPILKAN DAFTAR ULASAN HOTEL
     * Sinkronisasi: Mengambil nama asli pengulas dari Port 8000
     */
    public function index()
    {
        // A. Ambil data user dari Auth-Service (Port 8000) dengan aman
        $users = collect();
        try {
            $token = session('user.token');
            $response = Http::timeout(5)->withToken($token)->get(env('MIKRO_URL') . '/api/users');
            
            if ($response->successful()) {
                $users = collect($response->json('data') ?? [])->keyBy('id');
            }
        } catch (\Exception $e) {
            Log::error("Dashboard Ulasan Hotel: Gagal mengambil data user dari Port 8000. Pesan: " . $e->getMessage());
            // Tetap lanjut meskipun Port 8000 down, nama user akan ditangani fallback
        }

        // B. Ambil ulasan Hotel dengan relasi tipe kamar
        $ulasan = UlasanHotel::with('tipeKamar')->orderBy('created_at', 'desc')->get();

        // C. Hitung statistik untuk Dashboard
        $totalUlasan = $ulasan->count();
        $rataRating = $ulasan->avg('rating') ?? 0;

        return view('dashboard.hotel.ulasan.index', compact('ulasan', 'users', 'totalUlasan', 'rataRating'));
    }

    /**
     * 2. FITUR SEMBUNYIKAN/TAMPILKAN ULASAN (TOGGLE)
     * PENGAMANAN DOSEN: Hanya ROLE ADMIN yang boleh mengeksekusi ini.
     */
    public function toggle($id)
    {
        // --- VALIDASI HAK AKSES ADMIN ---
        // Sesuai permintaan dosen, Staff Hotel dilarang memoderasi ulasan.
        if (session('user.role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses Ditolak! Hanya Administrator yang diperbolehkan memoderasi ulasan pelanggan.');
        }

        try {
            $ulasan = UlasanHotel::findOrFail($id);

            // Balikkan status (jika hidden jadi tampil, jika tampil jadi hidden)
            $ulasan->update([
                'is_hidden' => !$ulasan->is_hidden
            ]);

            $pesan = $ulasan->is_hidden ? 'Ulasan disembunyikan dari publik.' : 'Ulasan ditampilkan kembali ke publik.';
            return redirect()->back()->with('success', $pesan);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses moderasi: ' . $e->getMessage());
        }
    }
}