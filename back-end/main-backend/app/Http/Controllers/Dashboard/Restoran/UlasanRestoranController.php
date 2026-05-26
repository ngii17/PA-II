<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\UlasanRestoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UlasanRestoranController extends Controller
{
    public function index()
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        $ulasan = UlasanRestoran::with('menu')->orderBy('created_at', 'desc')->get();
        $totalUlasan = $ulasan->count();
        $rataRating  = $ulasan->avg('rating') ?? 0;

        return view('dashboard.restoran.ulasan.index', compact('ulasan', 'users', 'totalUlasan', 'rataRating'));
    }

    /**
     * Fitur Toggle Sembunyikan (Hanya Admin)
     */
    public function toggle($id)
    {
        // Cek Keamanan: Hanya admin yang boleh
        if (session('user.role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Admin yang dapat memoderasi ulasan.');
        }

        $ulasan = UlasanRestoran::findOrFail($id);
        $ulasan->update([
            'is_hidden' => !$ulasan->is_hidden
        ]);

        $status = $ulasan->is_hidden ? 'disembunyikan' : 'ditampilkan';
        return redirect()->back()->with('success', "Ulasan restoran berhasil $status!");
    }
}
