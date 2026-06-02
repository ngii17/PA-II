<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\PesananMenu;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PembayaranRestoController extends Controller
{
    /**
     * TAMPIL DAFTAR PEMBAYARAN RESTORAN
     * Sinkronisasi: Hanya menampilkan yang berstatus LUNAS (ID 2)
     */
    public function index()
    {
        // 1. Proteksi Keamanan Role
        if (session('user.role') === 'staff_hotel') {
            return abort(403, 'Akses ditolak.');
        }

        // 2. Ambil data User dari Port 8000 untuk mendapatkan nama asli
        $token = session('user.token');
        $users = collect();
        
        try {
            $response = Http::timeout(5)->withToken($token)->get(env('MIKRO_URL') . '/api/users');
            if ($response->successful()) {
                $users = collect($response->json('data') ?? [])->keyBy('id');
            }
        } catch (\Exception $e) {
            Log::error("PembayaranRestoController: Gagal sinkron nama user. Pesan: " . $e->getMessage());
        }

        // 3. Ambil Data Pesanan yang HANYA berstatus LUNAS (ID 2)
        $pesanan = PesananMenu::with([
            'details.menu' => function($query) {
                $query->withTrashed(); 
            },
            'statusPembayaran'
        ])
        ->where('status_pembayaran_id', 2) 
        ->orderBy('updated_at', 'desc')
        ->get();

        // 4. Hitung Statistik (Nama variabel disesuaikan dengan permintaan file Blade)
        $totalPendapatan = $pesanan->sum('total_harga');
        $totalPesanan    = $pesanan->count(); // <--- KUNCI PERBAIKAN (Sesuai error di gambar)
        
        // Tambahkan variabel ini juga untuk berjaga-jaga jika Blade membutuhkannya
        $pesananLunas    = $totalPesanan;
        $pesananPending  = 0; // Karena kita sudah filter, yang pending pasti 0 di sini

        // 5. Kirim data ke View Blade
        return view('dashboard.restoran.pembayaran.index', compact(
            'pesanan',
            'users',
            'totalPendapatan',
            'totalPesanan',
            'pesananLunas',
            'pesananPending'
        ));
    }
}