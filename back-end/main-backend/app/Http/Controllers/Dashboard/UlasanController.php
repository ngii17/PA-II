<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\hotel\UlasanHotel;
use App\Models\restoran\UlasanRestoran;
use Illuminate\Support\Facades\Http;

class UlasanController extends Controller
{
    public function index()
    {
        $token = session('user.token');

        // Ambil data dari Microservice
        $response = Http::withToken($token)->get(env('MIKRO_URL').'/api/users');

        // FIX: Paksa ID menjadi Integer agar sinkron saat dipanggil di Blade
        $users = collect($response->json('data') ?? [])->mapWithKeys(function ($item) {
            return [(int)$item['id'] => $item];
        });

        // Ambil ulasan, tambahkan withTrashed pada relasi master data (tipeKamar/menu)
        // agar jika kamar/menu dihapus, ulasannya tidak error (Tetap Ngetrack)
        $ulasanHotel = UlasanHotel::with(['tipeKamar' => function($q){ $q->withTrashed(); }])
            ->orderBy('created_at', 'desc')->get();

        $ulasanRestoran = UlasanRestoran::with(['menu' => function($q){ $q->withTrashed(); }])
            ->orderBy('created_at', 'desc')->get();

        // Statistik
        $totalUlasan = $ulasanHotel->count() + $ulasanRestoran->count();
        $totalRatingSemua = $ulasanHotel->sum('rating') + $ulasanRestoran->sum('rating');
        $rataRating = $totalUlasan > 0 ? $totalRatingSemua / $totalUlasan : 0;

        return view('dashboard.ulasan.index', compact(
            'ulasanHotel',
            'ulasanRestoran',
            'users',
            'totalUlasan',
            'rataRating'
        ));
    }

        public function toggle($tipe, $id)
    {
        if ($tipe === 'hotel') {
            $u = UlasanHotel::findOrFail($id);
        } elseif ($tipe === 'restoran') {
            $u = UlasanRestoran::findOrFail($id);
        } else {
            abort(404);
        }

        $u->update(['is_hidden' => !$u->is_hidden]);

       return redirect()->back()
        ->with('success', 'Status visibilitas ulasan berhasil diubah.')
        ->withFragment($tipe === 'hotel' ? 'hotel' : 'resto');
        }
}
