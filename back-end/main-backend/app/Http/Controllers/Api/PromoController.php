<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\hotel\Promo; // Import model Promo dari subfolder hotel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{
    /**
     * FUNGSI CEK VALIDITAS KODE PROMO (MANUAL INPUT)
     */
    public function checkPromo(Request $request)
    {
        // 1. Validasi input dari Flutter
        $validator = Validator::make($request->all(), [
            'kode_promo' => 'required|string',
            'kategori'   => 'required|in:hotel,restoran', // Memastikan kategori hanya hotel/restoran
        ], [
            'kode_promo.required' => 'Silakan masukkan kode promo.',
            'kategori.in'         => 'Kategori tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => $validator->errors()->first()
            ], 422);
        }

        // 2. Cari kode promo yang sesuai kriteria
        $promo = Promo::where('kode_promo', $request->kode_promo)
            ->whereNotNull('kode_promo') // Hanya mencari promo yang punya kode (bukan otomatis)
            ->where(function($q) use ($request) {
                $q->where('kategori', $request->kategori)
                  ->orWhere('kategori', 'semua');
            })
            ->whereDate('tgl_mulai', '<=', now())
            ->whereDate('tgl_selesai', '>=', now())
            ->first();

        // 3. Jika kode promo tidak ditemukan atau tidak aktif
        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak valid, sudah kadaluwarsa, atau tidak berlaku untuk layanan ini.'
            ], 404);
        }

        // 4. Jika ditemukan, kirimkan data potongan ke Flutter
        return response()->json([
            'success' => true,
            'message' => 'Selamat! Kode promo "' . $promo->nama_promo . '" berhasil dipasang.',
            'data'    => [
                'nama_promo'       => $promo->nama_promo,
                'tipe_diskon'      => $promo->tipe_diskon, // 'persen' atau 'nominal'
                'nominal_potongan' => (float) $promo->nominal_potongan,
            ]
        ], 200);
    }
}