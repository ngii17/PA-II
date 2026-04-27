<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Pastikan import model menggunakan folder rapi hotel & restoran
use App\Models\hotel\UlasanHotel;
use App\Models\hotel\Reservasi;
use App\Models\hotel\TipeKamar;
use App\Models\restoran\UlasanRestoran;
use App\Models\restoran\PesananMenu;
use App\Models\restoran\Menu;
use Illuminate\Support\Facades\Validator;

class UlasanController extends Controller
{
    /**
     * 1. SIMPAN ULASAN HOTEL
     * Syarat: User harus sudah bayar (status_reservasi_id = 2)
     */
    public function storeHotelReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required',
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'rating'        => 'required|integer|min:1|max:5',
            'komentar'      => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid', 'errors' => $validator->errors()], 422);
        }

        // --- DEBUG: Cek status di database ---
        $cekStatus = Reservasi::where('user_id', $request->user_id)
            ->where('tipe_kamar_id', $request->tipe_kamar_id)
            ->first();

        if (!$cekStatus || $cekStatus->status_reservasi_id != 2) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal: Anda belum membayar lunas reservasi kamar ini. Status saat ini: ' . ($cekStatus->status_reservasi_id ?? 'Tidak ditemukan')
            ], 403);
        }

        // Jika lolos, simpan
        UlasanHotel::create([
            'user_id'       => $request->user_id,
            'tipe_kamar_id' => $request->tipe_kamar_id,
            'rating'        => $request->rating,
            'komentar'      => $request->komentar,
        ]);

        return response()->json(['success' => true, 'message' => 'Ulasan hotel berhasil disimpan!']);
    }

    /**
     * 2. AMBIL DAFTAR ULASAN HOTEL (Untuk Umum)
     */
    public function getHotelReviews($tipe_kamar_id)
    {
        try {
            // Mengambil ulasan berdasarkan Tipe Kamar tertentu
            $reviews = UlasanHotel::where('tipe_kamar_id', $tipe_kamar_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $reviews
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal memuat ulasan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. SIMPAN ULASAN RESTORAN
     * Syarat: User harus sudah bayar menu tersebut (status_pembayaran_id = 2)
     */
    public function storeRestoReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'menu_id' => 'required|exists:menu,id',
            'rating'  => 'required|integer|min:1|max:5',
            'komentar'=> 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Cek riwayat belanja makanan user
        $sudahPernahMakan = PesananMenu::where('user_id', $request->user_id)
            ->where('status_pembayaran_id', 2) // 2 = Lunas
            ->whereHas('details', function($query) use ($request) {
                $query->where('menu_id', $request->menu_id);
            })->exists();

        if (!$sudahPernahMakan) {
            return response()->json([
                'success' => false, 
                'message' => 'Anda hanya bisa mengulas makanan yang sudah dibayar.'
            ], 403);
        }

        UlasanRestoran::create([
            'user_id' => $request->user_id,
            'menu_id' => $request->menu_id,
            'rating'  => $request->rating,
            'komentar'=> $request->komentar,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Ulasan restoran berhasil disimpan.'
        ], 201);
    }

    /**
     * 4. AMBIL DAFTAR ULASAN RESTORAN (Untuk Umum)
     */
    public function getRestoReviews($menu_id)
    {
        try {
            $reviews = UlasanRestoran::where('menu_id', $menu_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $reviews
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal memuat ulasan: ' . $e->getMessage()
            ], 500);
        }
    }
}