<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\PesananMenu;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PembayaranRestoController extends Controller
{
    public function index()
    {
        // 1. Proteksi Keamanan Role
        if (session('user.role') === 'staff_hotel') {
            return abort(403, 'Akses ditolak: Staf Hotel tidak dapat mengakses data keuangan restoran.');
        }

        // 2. Ambil data User dari Microservice (Untuk Nama Pelanggan)
        $token = session('user.token');
        try {
            $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
            $users = collect($response->json('data') ?? [])->keyBy('id');
        } catch (\Exception $e) {
            $users = collect([]);
        }

        // 3. Ambil Data Pesanan (Eager Load Relasi agar Detail Muncul)
        // Kita tampilkan semua pesanan agar Admin/Staff bisa melihat riwayat (termasuk yang belum lunas)
            $pesanan = PesananMenu::with([
            // Memaksa sistem mengambil data menu meskipun sudah di-softdelete
            'details.menu' => function($query) {
                $query->withTrashed();
            },
            'statusPembayaran'
        ])->orderBy('created_at', 'desc')->get();

        // 4. Hitung Statistik (Wajib lengkap agar View tidak error)
        $totalPendapatan = $pesanan->where('status_pembayaran_id', 2)->sum('total_harga'); // ID 2 = Lunas
        $totalPesanan    = $pesanan->count();
        $pesananLunas    = $pesanan->where('status_pembayaran_id', 2)->count();
        $pesananPending  = $pesanan->where('status_pembayaran_id', 1)->count(); // ID 1 = Pending

        // 5. Kirim SEMUA variabel ke View
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
