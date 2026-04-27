<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\hotel\TipeKamar;
use App\Models\hotel\Promo;
use App\Models\hotel\Reservasi;
use App\Models\hotel\DetailReservasi;
use App\Models\hotel\Kamar; // Sudah tersedia
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class HotelController extends Controller
{
    /**
     * 1. AMBIL DAFTAR KAMAR & HITUNG PROMO OTOMATIS
     */
    public function getRoomTypes()
    {
        try {
            $tipeKamar = TipeKamar::all();

            $promoAktif = Promo::where('kategori', 'hotel')
                ->whereNull('kode_promo')
                ->whereDate('tgl_mulai', '<=', now())
                ->whereDate('tgl_selesai', '>=', now())
                ->first();

            $data = $tipeKamar->map(function ($item) use ($promoAktif) {
                $hargaAsli = (float) $item->harga;
                $hargaAkhir = $hargaAsli;
                $infoPromo = null;

                if ($promoAktif) {
                    $potongan = ($promoAktif->tipe_diskon == 'persen') 
                        ? $hargaAsli * ($promoAktif->nominal_potongan / 100) 
                        : (float) $promoAktif->nominal_potongan;
                    
                    $infoPromo = ($promoAktif->tipe_diskon == 'persen') 
                        ? "Diskon " . (int)$promoAktif->nominal_potongan . "%" 
                        : "Potongan Rp " . number_format($potongan, 0, ',', '.');
                    
                    $hargaAkhir = $hargaAsli - $potongan;
                }

                return [
                    'id' => $item->id,
                    'nama_tipe' => $item->nama_tipe,
                    'harga_asli' => $hargaAsli,
                    'harga_akhir' => $hargaAkhir,
                    'kapasitas' => $item->kapasitas,
                    'fasilitas' => $item->fasilitas,
                    'deskripsi' => $item->deskripsi,
                    'promo_aktif' => $infoPromo,
                ];
            });

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. SIMPAN RESERVASI DENGAN LOGIKA CEK STOK (ANTI-OVERBOOKING)
     */
    public function storeReservation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required',
            'tipe_kamar_id'     => 'required|exists:tipe_kamar,id',
            'tgl_checkin'       => 'required|date|after_or_equal:today',
            'tgl_checkout'      => 'required|date|after:tgl_checkin',
            'total_malam'       => 'required|integer|min:1',
            'total_harga'       => 'required|numeric',
            'metode_pembayaran' => 'required|string',
            'nama_tamu'         => 'required|string',
            'nik_identitas'     => 'required|string',
            'jumlah_tamu'       => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $tipeId = $request->tipe_kamar_id;
        $tglIn  = $request->tgl_checkin;
        $tglOut = $request->tgl_checkout;

        $totalUnitTersedia = Kamar::where('tipe_kamar_id', $tipeId)->count();

        $terbooking = Reservasi::where('tipe_kamar_id', $tipeId)
            ->whereIn('status_reservasi_id', [1, 2]) 
            ->where(function ($query) use ($tglIn, $tglOut) {
                $query->where('tgl_checkin', '<', $tglOut)
                      ->where('tgl_checkout', '>', $tglIn);
            })->count();

        if ($terbooking >= $totalUnitTersedia) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, kamar tipe ini sudah penuh di tanggal yang Anda pilih.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $reservasi = Reservasi::create([
                'user_id'            => $request->user_id,
                'tipe_kamar_id'      => $request->tipe_kamar_id,
                'tgl_checkin'        => $request->tgl_checkin,
                'tgl_checkout'       => $request->tgl_checkout,
                'total_malam'        => $request->total_malam,
                'total_harga'        => $request->total_harga,
                'metode_pembayaran'  => $request->metode_pembayaran,
                'status_reservasi_id' => 1, 
            ]);

            DetailReservasi::create([
                'reservasi_id'  => $reservasi->id,
                'nama_tamu'     => $request->nama_tamu,
                'nik_identitas' => $request->nik_identitas,
                'jumlah_tamu'   => $request->jumlah_tamu,
            ]);

            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $method = $request->metode_pembayaran;
            $enabledPayments = ($method == 'Transfer Bank') ? ['bank_transfer'] : ['gopay', 'shopeepay', 'qris'];

            $params = [
                'transaction_details' => [
                    'order_id' => 'INV-' . $reservasi->id . '-' . time(),
                    'gross_amount' => (int) $request->total_harga,
                ],
                'customer_details' => [
                    'first_name' => $request->nama_tamu,
                ],
                'enabled_payments' => $enabledPayments,
                'callbacks' => [
                    'finish' => 'https://demo.midtrans.com/callback_url?status_code=200',
                ],
            ];

            $midtransResponse = Snap::createTransaction($params);
            
            $snapToken = $midtransResponse->token;
            $redirectUrl = $midtransResponse->redirect_url;

            $reservasi->update(['snap_token' => $snapToken]);

            DB::commit();
            
            return response()->json([
                'success' => true, 
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
                'reservasi_id' => $reservasi->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 3. AMBIL RIWAYAT RESERVASI
     */
public function getReservationHistory(Request $request)
    {
        $userId = $request->query('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID tidak ditemukan.'], 400);
        }

        try {
            $history = Reservasi::with(['details', 'tipeKamar'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $history->map(function ($res) {
                return [
                    'id'                  => $res->id,
                    'tipe_kamar_id'       => $res->tipe_kamar_id, // <--- INI WAJIB ADA AGAR FLUTTER BISA KIRIM ULASAN
                    'tgl_checkin'         => $res->tgl_checkin,
                    'tgl_checkout'        => $res->tgl_checkout,
                    'total_malam'         => $res->total_malam,
                    'total_harga'         => $res->total_harga,
                    'metode_pembayaran'   => $res->metode_pembayaran,
                    'status_reservasi_id' => $res->status_reservasi_id,
                    'snap_token'          => $res->snap_token,
                    'nama_tipe'           => $res->tipeKamar->nama_tipe ?? 'Kamar',
                    'details'             => $res->details,
                ];
            });

            return response()->json(['success' => true, 'data' => $data], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * 4. WEBHOOK CALLBACK (Update Status & Assign Kamar Otomatis)
     */
    public function handleCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $orderId = $request->order_id; // Contoh: INV-12-xxx atau RESTO-5-xxx
            $orderIdParts = explode('-', $orderId);
            $prefix = $orderIdParts[0]; // Ambil kata depan (INV atau RESTO)
            $actualId = $orderIdParts[1]; // Ambil ID asli-nya

            // 1. JIKA INI TRANSAKSI HOTEL (INV)
            if ($prefix == 'INV') {
                $reservasi = \App\Models\hotel\Reservasi::find($actualId);
                if ($reservasi) {
                    if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                        // Cari kamar kosong & tandai terisi (Logika yang kita buat kemarin)
                        $kamar = \App\Models\hotel\Kamar::where('tipe_kamar_id', $reservasi->tipe_kamar_id)
                                             ->where('status_kamar_id', 1)->first();
                        if ($kamar) {
                            $kamar->update(['status_kamar_id' => 2]);
                            $reservasi->update(['status_reservasi_id' => 2, 'kamar_id' => $kamar->id]);
                        }
                    } else if (in_array($request->transaction_status, ['deny', 'expire', 'cancel'])) {
                        $reservasi->update(['status_reservasi_id' => 4]);
                    }
                }
            } 
            
            // 2. JIKA INI TRANSAKSI RESTORAN (RESTO)
            else if ($prefix == 'RESTO') {
                $pesanan = \App\Models\restoran\PesananMenu::find($actualId);
                if ($pesanan) {
                    if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                        $pesanan->update(['status_pembayaran_id' => 2]); // Lunas
                    } else if (in_array($request->transaction_status, ['deny', 'expire', 'cancel'])) {
                        $pesanan->update(['status_pembayaran_id' => 4]); // Batal
                    }
                }
            }
        }
        return response()->json(['message' => 'OK']);
    }

    /**
     * 5. CEK STATUS
     */
    public function checkStatus($id)
    {
        try {
            $reservasi = Reservasi::find($id);
            if (!$reservasi) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'status_id' => (int) $reservasi->status_reservasi_id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}