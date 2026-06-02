<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- IMPORT SEMUA MODEL (PASTIKAN TIDAK ADA YANG KURANG) ---
use App\Models\hotel\UlasanHotel;
use App\Models\hotel\Reservasi;
use App\Models\hotel\TipeKamar;
use App\Models\restoran\UlasanRestoran;
use App\Models\restoran\PesananMenu;
use App\Models\restoran\Menu;

// --- IMPORT TOOLS ---
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UlasanController extends Controller
{
    /**
     * ============================================================
     * BAGIAN 1: SISTEM ULASAN HOTEL (CRUD)
     * ============================================================
     */

    /**
     * 1.1 SIMPAN ULASAN HOTEL
     * Kunci: Berdasarkan reservasi_id agar 1 transaksi hanya 1 ulasan.
     */
    public function storeHotelReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required',
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'reservasi_id'  => 'required|exists:reservasi,id', // ID Transaksi Unik
            'rating'        => 'required|integer|min:1|max:5',
            'komentar'      => 'required|string|min:5',
            'is_anonymous'  => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // CEK APAKAH RESERVASI INI SUDAH PERNAH DIULAS
        $exists = UlasanHotel::where('reservasi_id', $request->reservasi_id)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Reservasi ini sudah Anda ulas sebelumnya.'], 400);
        }

        // Cek apakah reservasi sudah SELESAI (Status 4)
        $reservasi = Reservasi::where('id', $request->reservasi_id)
            ->where('user_id', $request->user_id)
            ->where('status_reservasi_id', 4)
            ->first();

        if (!$reservasi) {
            return response()->json(['success' => false, 'message' => 'Ulasan hanya bisa diberikan setelah Anda Check-out.'], 403);
        }

        UlasanHotel::create([
            'user_id'       => $request->user_id,
            'tipe_kamar_id' => $request->tipe_kamar_id,
            'reservasi_id'  => $request->reservasi_id,
            'rating'        => $request->rating,
            'komentar'      => $request->komentar,
            'is_anonymous'  => $request->is_anonymous,
            'is_hidden'     => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Terima kasih atas ulasan hotel Anda!'], 201);
    }

    /**
     * 1.2 EDIT ULASAN HOTEL
     */
    public function updateHotelReview(Request $request, $id)
    {
        $ulasan = UlasanHotel::where('id', $id)->where('user_id', $request->user_id)->first();
        if (!$ulasan) return response()->json(['success' => false, 'message' => 'Ulasan tidak ditemukan atau Anda tidak berhak.'], 403);

        $ulasan->update([
            'rating'       => $request->rating,
            'komentar'     => $request->komentar,
            'is_anonymous' => $request->is_anonymous
        ]);

        return response()->json(['success' => true, 'message' => 'Ulasan hotel berhasil diperbarui!']);
    }

    /**
     * 1.3 HAPUS ULASAN HOTEL
     */
    public function destroyHotelReview(Request $request, $id)
    {
        $ulasan = UlasanHotel::where('id', $id)->where('user_id', $request->user_id)->first();
        if (!$ulasan) return response()->json(['success' => false, 'message' => 'Gagal menghapus ulasan.'], 403);

        $ulasan->delete();
        return response()->json(['success' => true, 'message' => 'Ulasan hotel telah dihapus.']);
    }

    /**
     * 1.4 AMBIL DAFTAR ULASAN HOTEL (SINKRON NAMA USER)
     */
    public function getHotelReviews($tipeKamarId)
    {
        try {
            $ulasan = UlasanHotel::where('tipe_kamar_id', $tipeKamarId)
                ->where('is_hidden', false)
                ->orderBy('created_at', 'desc')
                ->get();

            // Ambil data user dari Auth-Service
            $users = $this->getUsersFromAuth(); 

            $data = $ulasan->map(function($u) use ($users) {
                $user = $users->get($u->user_id);
                $namaAsli = $user['full_name'] ?? ($user['username'] ?? 'Pelanggan Purnama');

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
     * ============================================================
     * BAGIAN 2: SISTEM ULASAN RESTORAN (CRUD)
     * ============================================================
     */

    /**
     * 2.1 SIMPAN ULASAN RESTORAN
     * Kunci: Berdasarkan pesanan_menu_id agar 1 menu di 1 nota hanya 1 ulasan.
     */
    public function storeRestoReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'         => 'required',
            'menu_id'         => 'required|exists:menu,id',
            'pesanan_menu_id' => 'required|exists:pesanan_menu,id', 
            'rating'          => 'required|integer|min:1|max:5',
            'komentar'        => 'required|string|min:5',
            'is_anonymous'    => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // CEK APAKAH MENU PADA NOTA INI SUDAH DIULAS
        $exists = UlasanRestoran::where('pesanan_menu_id', $request->pesanan_menu_id)
            ->where('menu_id', $request->menu_id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Menu pada pesanan ini sudah Anda ulas.'], 400);
        }

        // Cek Pembayaran Lunas (Status 2)
        $sudahBayar = PesananMenu::where('id', $request->pesanan_menu_id)
            ->where('user_id', $request->user_id)
            ->where('status_pembayaran_id', 2)
            ->exists();

        if (!$sudahBayar) {
            return response()->json(['success' => false, 'message' => 'Ulasan hanya tersedia untuk pesanan yang sudah lunas.'], 403);
        }

        UlasanRestoran::create([
            'user_id'         => $request->user_id,
            'menu_id'         => $request->menu_id,
            'pesanan_menu_id' => $request->pesanan_menu_id,
            'rating'          => $request->rating,
            'komentar'        => $request->komentar,
            'is_anonymous'    => $request->is_anonymous,
            'is_hidden'       => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Ulasan restoran berhasil disimpan!'], 201);
    }

    /**
     * 2.2 EDIT ULASAN RESTORAN
     */
    public function updateRestoReview(Request $request, $id)
    {
        $ulasan = UlasanRestoran::where('id', $id)->where('user_id', $request->user_id)->first();
        if (!$ulasan) return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);

        $ulasan->update([
            'rating'       => $request->rating,
            'komentar'     => $request->komentar,
            'is_anonymous' => $request->is_anonymous
        ]);

        return response()->json(['success' => true, 'message' => 'Ulasan berhasil diperbarui!']);
    }

    /**
     * 2.3 HAPUS ULASAN RESTORAN
     */
    public function destroyRestoReview(Request $request, $id)
    {
        $ulasan = UlasanRestoran::where('id', $id)->where('user_id', $request->user_id)->first();
        if (!$ulasan) return response()->json(['success' => false, 'message' => 'Gagal menghapus.'], 403);

        $ulasan->delete();
        return response()->json(['success' => true, 'message' => 'Ulasan telah dihapus.']);
    }

    /**
     * 2.4 AMBIL DAFTAR ULASAN RESTORAN (SINKRON NAMA USER)
     */
    public function getRestoReviews($menuId)
    {
        try {
            $reviews = UlasanRestoran::where('menu_id', $menuId)
                ->where('is_hidden', false)
                ->orderBy('created_at', 'desc')
                ->get();

            $users = $this->getUsersFromAuth();

            $data = $reviews->map(function($u) use ($users) {
                $user = $users->get($u->user_id);
                $namaAsli = $user['full_name'] ?? ($user['username'] ?? 'Pelanggan Resto');
                return [
                    'id'        => $u->id,
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
     * ============================================================
     * BAGIAN 3: FUNGSI HELPER (PROSES INTERNAL)
     * ============================================================
     */

    /**
     * Helper untuk mengambil data user dari Auth-Service (Port 8000)
     */
    /**
     * Ambil data user dari Auth-Service secara dinamis
     */
    private function getUsersFromAuth()
    {
        try {
            // Kita ambil URL dari .env, jika tidak ada default ke localhost
            $baseUrl = env('AUTH_SERVICE_URL', 'http://127.0.0.1:8000');
            
            $url = $baseUrl . "/api/internal/user-tokens";
            
            $res = Http::timeout(5)->get($url);
            
            if ($res->successful()) {
                return collect($res->json('data'))->keyBy('user_id');
            }
        } catch (\Exception $e) {
            Log::error("SINKRON_NAMA_GAGAL: Cek IP di .env kamu. Pesan: " . $e->getMessage());
        }
        return collect();
    }
    /**
     * Helper untuk sensor nama (Anonim)
     */
    private function sensorName($name) {
        $parts = explode(' ', $name);
        $censoredParts = array_map(function($part) {
            if (strlen($part) <= 1) return $part;
            return substr($part, 0, 1) . str_repeat('*', strlen($part) - 1);
        }, $parts);
        return implode(' ', $censoredParts);
    }
}