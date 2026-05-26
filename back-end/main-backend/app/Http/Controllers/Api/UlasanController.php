<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- IMPORT MODEL HOTEL ---
use App\Models\hotel\UlasanHotel;
use App\Models\hotel\Reservasi;
use App\Models\hotel\TipeKamar;

// --- IMPORT MODEL RESTORAN ---
use App\Models\restoran\UlasanRestoran;
use App\Models\restoran\PesananMenu;
use App\Models\restoran\Menu;

// --- IMPORT TOOLS ---
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class UlasanController extends Controller
{
    /**
     * 1. SIMPAN ULASAN HOTEL
     * Syarat: User harus sudah bayar lunas (status_reservasi_id = 2)
     */
    public function storeHotelReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required',
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'rating'        => 'required|integer|min:1|max:5',
            'komentar'      => 'required|string|min:5',
            'is_anonymous'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Cek apakah user benar-benar sudah pernah menginap dan bayar lunas
        $reservasi = Reservasi::where('user_id', $request->user_id)
            ->where('tipe_kamar_id', $request->tipe_kamar_id)
            ->where('status_reservasi_id', 2)
            ->first();

        if (!$reservasi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya bisa mengulas kamar yang sudah dibayar lunas.'
            ], 403);
        }

        UlasanHotel::create([
            'user_id'       => $request->user_id,
            'tipe_kamar_id' => $request->tipe_kamar_id,
            'rating'        => $request->rating,
            'komentar'      => $request->komentar,
            'is_anonymous'  => $request->is_anonymous ?? false,
            'is_hidden'     => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Ulasan hotel berhasil disimpan!'], 201);
    }

    /**
     * 2. AMBIL DAFTAR ULASAN HOTEL (Untuk Katalog Flutter)
     * Dilengkapi sensor nama jika user memilih anonim
     */
    public function getHotelReviews($tipeKamarId)
    {
        try {
            // Ambil data user dari mikro service agar kita tahu nama aslinya
            $token = request()->bearerToken();
            $res = Http::withToken($token)->get(env('MIKRO_URL').'/api/users');
            $users = collect($res->json('data') ?? [])->keyBy('id');

            $ulasan = UlasanHotel::where('tipe_kamar_id', $tipeKamarId)
                ->where('is_hidden', false)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $ulasan->map(function($u) use ($users) {
                $user = $users->get($u->user_id);
                $namaAsli = $user['full_name'] ?? 'Pelanggan Purnama';

                return [
                    'rating'    => $u->rating,
                    'komentar'  => $u->komentar,
                    'nama_user' => $u->is_anonymous ? $this->sensorName($namaAsli) : $namaAsli,
                    'tanggal'   => $u->created_at->format('d M Y')
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 3. SIMPAN ULASAN RESTORAN
     * Syarat: User harus sudah bayar lunas menu tersebut
     */
    public function storeRestoReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required',
            'menu_id'      => 'required|exists:menu,id',
            'rating'       => 'required|integer|min:1|max:5',
            'komentar'     => 'required|string|min:5',
            'is_anonymous' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Cek apakah user sudah bayar lunas pesanan yang berisi menu tersebut
        $sudahBayar = PesananMenu::where('user_id', $request->user_id)
            ->where('status_pembayaran_id', 2)
            ->whereHas('details', function($q) use ($request) {
                $q->where('menu_id', $request->menu_id);
            })->exists();

        if (!$sudahBayar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya bisa mengulas menu yang sudah dibayar lunas.'
            ], 403);
        }

        UlasanRestoran::create([
            'user_id'      => $request->user_id,
            'menu_id'      => $request->menu_id,
            'rating'       => $request->rating,
            'komentar'     => $request->komentar,
            'is_anonymous' => $request->is_anonymous ?? false,
            'is_hidden'    => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Ulasan restoran berhasil disimpan!'], 201);
    }

    /**
     * 4. AMBIL DAFTAR ULASAN RESTORAN (Untuk Katalog Flutter)
     */
    public function getRestoReviews($menuId)
    {
        try {
            $token = request()->bearerToken();
            $res = Http::withToken($token)->get(env('MIKRO_URL').'/api/users');
            $users = collect($res->json('data') ?? [])->keyBy('id');

            $reviews = UlasanRestoran::where('menu_id', $menuId)
                ->where('is_hidden', false)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $reviews->map(function($u) use ($users) {
                $user = $users->get($u->user_id);
                $namaAsli = $user['full_name'] ?? 'Pelanggan Resto';

                return [
                    'rating'    => $u->rating,
                    'komentar'  => $u->komentar,
                    'nama_user' => $u->is_anonymous ? $this->sensorName($namaAsli) : $namaAsli,
                    'tanggal'   => $u->created_at->format('d M Y')
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * FUNGSI HELPER: SENSOR NAMA
     * Contoh: "Laura Aurelia" -> "L**** A******"
     */
    private function sensorName($name) {
        $parts = explode(' ', $name);
        $censoredParts = array_map(function($part) {
            if (strlen($part) <= 1) return $part;
            // Ambil huruf pertama, sisanya ganti bintang
            return substr($part, 0, 1) . str_repeat('*', strlen($part) - 1);
        }, $parts);
        return implode(' ', $censoredParts);
    }
}
