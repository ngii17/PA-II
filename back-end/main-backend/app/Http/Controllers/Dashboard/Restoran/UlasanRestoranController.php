<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\UlasanRestoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Import Log untuk debugging

class UlasanRestoranController extends Controller
{
    /**
     * 1. MENAMPILKAN DAFTAR ULASAN RESTORAN
     * Sinkronisasi: Mengambil nama asli pengulas dari Auth-Service (Port 8000)
     */
    public function index()
    {
        // A. Ambil data user dari Auth-Service dengan aman
        $users = collect();
        try {
            $token = session('user.token');
            // Gunakan variabel MIKRO_URL sesuai .env Anda
            $baseUrl = env('MIKRO_URL', 'http://10.187.82.132:8000');
            $response = Http::timeout(5)->withToken($token)->get($baseUrl . '/api/internal/user-tokens');
            
            if ($response->successful()) {
                // Gunakan user_id sebagai key agar sinkron saat mapping
                $users = collect($response->json('data') ?? [])->keyBy('user_id');
            }
        } catch (\Exception $e) {
            Log::error("Dashboard Ulasan Resto: Gagal sinkron data user. Pesan: " . $e->getMessage());
        }

        // B. Ambil ulasan Restoran beserta data Menu-nya
        $ulasan = UlasanRestoran::with(['menu' => function($q) {
            $q->withTrashed(); // Tetap tampilkan meskipun menu sudah dihapus
        }])->orderBy('created_at', 'desc')->get();

        // C. Hitung statistik ringkas
        $totalUlasan = $ulasan->count();
        $rataRating  = $ulasan->avg('rating') ?? 0;

        return view('dashboard.restoran.ulasan.index', compact(
            'ulasan', 
            'users', 
            'totalUlasan', 
            'rataRating'
        ));
    }

    /**
     * 2. FITUR SEMBUNYIKAN/TAMPILKAN (TOGGLE)
     * KEAMANAN: Hanya boleh diakses oleh ROLE ADMIN
     */
    public function toggle($id)
    {
        // Sesuai permintaan dosen: Staff Resto dilarang memoderasi ulasan
        if (session('user.role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Administrator yang berhak memoderasi ulasan.');
        }

        try {
            $ulasan = UlasanRestoran::findOrFail($id);
            
            // Balikkan status is_hidden
            $ulasan->update([
                'is_hidden' => !$ulasan->is_hidden
            ]);

            $status = $ulasan->is_hidden ? 'disembunyikan dari publik' : 'ditampilkan kembali';
            return redirect()->back()->with('success', "Ulasan berhasil $status.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status ulasan.');
        }
    }
}