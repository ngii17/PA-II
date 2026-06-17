<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel\TipeKamar;
use App\Models\Hotel\Promo;
use App\Models\Hotel\Reservasi;
use App\Models\Hotel\DetailReservasi;
use App\Models\Hotel\Kamar;
use App\Models\Hotel\StatusReservasi;
use App\Services\NotificationClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log; // <--- SUDAH BENAR
use Illuminate\Support\Facades\Storage;
use App\Helpers\AuthServiceHelper;


class HotelController extends Controller
{
    /**
     * 2. INJECT SERVICE VIA CONSTRUCTOR
     */
    public function __construct(protected NotificationClientService $notifService)
    {
    }

    /**
     * 1. AMBIL DAFTAR KAMAR & HITUNG PROMO OTOMATIS
     * (Versi Sinkron dengan Fitur Saklar is_active)
     */
    public function getRoomTypes()
    {
        try {
            $tipeKamar = TipeKamar::all();

            // --- PERBAIKAN: Ditambahkan pengecekan is_active ---
            $promoAktif = Promo::where('kategori', 'hotel')
                ->where('is_active', true) // <--- Hanya ambil jika saklar aktif (true)
                ->whereNull('kode_promo') // Hanya promo otomatis (tanpa kode)
                ->whereDate('tgl_mulai', '<=', now())
                ->whereDate('tgl_selesai', '>=', now())
                ->first();

            $data = $tipeKamar->map(function ($item) use ($promoAktif) {
                $hargaAsli = (float) $item->harga;
                $hargaAkhir = $hargaAsli;
                $infoPromo = null;

                // Hitung potongan harga HANYA JIKA promo ditemukan (is_active == true)
                if ($promoAktif) {
                    $potongan = ($promoAktif->tipe_diskon == 'persen')
                        ? $hargaAsli * ($promoAktif->nominal_potongan / 100)
                        : (float) $promoAktif->nominal_potongan;

                    $infoPromo = ($promoAktif->tipe_diskon == 'persen')
                        ? "Diskon " . (int)$promoAktif->nominal_potongan . "%"
                        : "Potongan Rp " . number_format($potongan, 0, ',', '.');

                    $hargaAkhir = $hargaAsli - $potongan;
                }

                // Tentukan URL foto:
                // Jika foto diawali 'http' berarti masih URL seeder lama, pakai langsung
                // Jika foto adalah path lokal, generate URL storage
                // Jika foto null/kosong, kembalikan null
                if ($item->foto && str_starts_with($item->foto, 'http')) {
                    $fotoUrl = $item->foto; // URL seeder lama, pakai apa adanya
                } elseif ($item->foto) {
                    $fotoUrl = asset('storage/' . $item->foto); // Path lokal dari upload staff
                } else {
                    $fotoUrl = null; // Belum ada foto
                }

                return [
                    'id'          => $item->id,
                    'nama_tipe'   => $item->nama_tipe,
                    'foto'        => $fotoUrl, // ✅ Ditambahkan
                    'harga_asli'  => $hargaAsli,
                    'harga_akhir' => $hargaAkhir,
                    'kapasitas'   => $item->kapasitas,
                    'fasilitas'   => $item->fasilitas,
                    'deskripsi'   => $item->deskripsi,
                    'promo_aktif' => $infoPromo,
                ];
            });

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. SIMPAN RESERVASI DENGAN LOGIKA CEK STOK & SIMPAN FCM TOKEN
     */
    public function storeReservation(Request $request)
    {
        // =============================================
        // STEP 1: AMBIL USER_ID DARI TOKEN (BUKAN DARI REQUEST BODY)
        // =============================================
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        $userId = AuthServiceHelper::getUserIdFromToken($token);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau expired'
            ], 401);
        }

        // =============================================
        // STEP 2: VALIDASI INPUT (user_id DIHAPUS dari sini)
        // =============================================
        $validator = Validator::make($request->all(), [
            'fcm_token'         => 'nullable|string',
            'tipe_kamar_id'     => 'required|exists:tipe_kamar,id',
            'tgl_checkin'       => 'required|date|after_or_equal:today',
            'tgl_checkout'      => 'required|date|after:tgl_checkin',
            'total_malam'       => 'required|integer|min:1',
            'total_harga'       => 'required|numeric',
            'metode_pembayaran' => 'required|string',
            'nama_tamu'         => 'required|string',
            'nik_identitas'     => 'required|string',
            'jumlah_tamu'       => 'required|integer',
            'promo_id'          => 'nullable|integer|exists:promo,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // =============================================
        // STEP 3: CEK KETERSEDIAAN KAMAR
        // =============================================
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

        // =============================================
        // STEP 4: PROSES RESERVASI
        // =============================================
        DB::beginTransaction();
        try {
            // --- HITUNG ULANG POTONGAN PROMO DI BACKEND ---
            $subtotal      = (float) $request->total_harga;
            $potonganPromo = 0;
            $promoId       = $request->promo_id ?? null;

            if ($promoId) {
                $promo = \App\Models\Hotel\Promo::find($promoId);

                if ($promo) {
                    $masihAktif = $promo->is_active
                        && now()->between($promo->tgl_mulai, $promo->tgl_selesai)
                        && in_array($promo->kategori, ['hotel', 'semua']);

                    // PAKAI $userId DARI TOKEN, BUKAN $request->user_id
                    $sudahDipakai = \App\Models\promo\PromoUsage::where('promo_id', $promoId)
                        ->where('user_id', $userId)
                        ->exists();

                    if ($masihAktif && !$sudahDipakai) {
                        if ($promo->tipe_diskon === 'persen') {
                            $potonganPromo = $subtotal * ($promo->nominal_potongan / 100);
                        } else {
                            $potonganPromo = min((float) $promo->nominal_potongan, $subtotal);
                        }
                    } else {
                        $promoId = null;
                    }
                }
            }

            $totalBayar = max($subtotal - $potonganPromo, 0);

            // --- SIMPAN RESERVASI (PAKAI $userId DARI TOKEN) ---
            $reservasi = Reservasi::create([
                'user_id'             => $userId, // ← DARI TOKEN
                'fcm_token'           => $request->fcm_token,
                'tipe_kamar_id'       => $request->tipe_kamar_id,
                'tgl_checkin'         => $request->tgl_checkin,
                'tgl_checkout'        => $request->tgl_checkout,
                'total_malam'         => $request->total_malam,
                'total_harga'         => $totalBayar,
                'metode_pembayaran'   => $request->metode_pembayaran,
                'status_reservasi_id' => 1,
            ]);

            DetailReservasi::create([
                'reservasi_id'  => $reservasi->id,
                'nama_tamu'     => $request->nama_tamu,
                'nik_identitas' => $request->nik_identitas,
                'jumlah_tamu'   => $request->jumlah_tamu,
            ]);

            // --- CATAT PENGGUNAAN PROMO (PAKAI $userId DARI TOKEN) ---
            if ($promoId) {
                \App\Models\promo\PromoUsage::firstOrCreate(
                    [
                        'promo_id' => $promoId,
                        'user_id'  => $userId, // ← DARI TOKEN
                    ],
                    [
                        'kategori' => 'hotel',
                    ]
                );
            }

            // --- INTEGRASI MIDTRANS ---
            Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $method          = $request->metode_pembayaran;
            $enabledPayments = ($method == 'Transfer Bank')
                ? ['bank_transfer']
                : ['gopay', 'shopeepay', 'dana', 'linkaja', 'qris'];

            $params = [
                'transaction_details' => [
                    'order_id'     => 'INV-' . $reservasi->id . '-' . time(),
                    'gross_amount' => (int) $totalBayar,
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

            $snapToken   = $midtransResponse->token;
            $redirectUrl = $midtransResponse->redirect_url;

            $reservasi->update(['snap_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'snap_token'   => $snapToken,
                'redirect_url' => $redirectUrl,
                'reservasi_id' => $reservasi->id,
                'data'         => [
                    'subtotal'       => $subtotal,
                    'potongan_promo' => $potonganPromo,
                    'total_bayar'    => $totalBayar,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * 3. AMBIL RIWAYAT RESERVASI
     * Logika: Cek ulasan berdasarkan ID Reservasi unik
     */

    public function getReservationHistory(Request $request)
    {
        // Ambil token dari header Authorization
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        // Introspect ke auth-service untuk dapat user_id
        $userId = AuthServiceHelper::getUserIdFromToken($token);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau expired'
            ], 401);
        }

        // Mulai sini sama seperti sebelumnya, tapi user_id dari token, BUKAN dari query param
        try {
            $history = \App\Models\hotel\Reservasi::with(['details', 'tipeKamar'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $history->map(function ($res) {
                $review = \App\Models\hotel\UlasanHotel::where('reservasi_id', $res->id)->first();

                return [
                    'id'                  => $res->id,
                    'tipe_kamar_id'       => $res->tipe_kamar_id,
                    'status_reservasi_id' => $res->status_reservasi_id,
                    'tgl_checkin'         => $res->tgl_checkin,
                    'total_harga'         => $res->total_harga,
                    'total_malam'         => $res->total_malam,
                    'nama_tipe'           => $res->tipeKamar->nama_tipe ?? 'Kamar',
                    'details'             => $res->details,
                    'is_reviewed'         => $review ? true : false,
                    'review_id'           => $review ? $review->id : null,
                    'existing_review'     => $review ? [
                        'rating'       => $review->rating,
                        'komentar'     => $review->komentar,
                        'is_anonymous' => (bool) $review->is_anonymous
                    ] : null,
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
        /**
     * 4. WEBHOOK CALLBACK (OTOMATIS KIRIM NOTIFIKASI HOTEL & RESTO)
     */
    public function handleCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $orderId = $request->order_id;
            $orderIdParts = explode('-', $orderId);
            $prefix = $orderIdParts[0];
            $actualId = $orderIdParts[1];

            // --- A. JIKA TRANSAKSI HOTEL (INV) ---
            if ($prefix == 'INV') {
                $reservasi = \App\Models\hotel\Reservasi::with('tipeKamar')->find($actualId);

                if ($reservasi) {
                    if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {

                        $kamar = \App\Models\hotel\Kamar::where('tipe_kamar_id', $reservasi->tipe_kamar_id)
                                             ->where('status_kamar_id', 1)->first();

                        // Update status lunas & penempatan kamar
                        $reservasi->update([
                            'status_reservasi_id' => 2,
                            'kamar_id' => $kamar ? $kamar->id : null
                        ]);

                        if ($kamar) {
                            $kamar->update(['status_kamar_id' => 2]);
                        }

                        // PICU NOTIFIKASI HOTEL
                        try {
                            $this->notifService->bookingConfirmed(
                                $reservasi->fcm_token ?? 'no_token',
                                $reservasi->user_id,
                                $reservasi->id,
                                $reservasi->tipeKamar->nama_tipe ?? 'Kamar',
                                $reservasi->tgl_checkin
                            );
                        } catch (\Exception $e) {
                            Log::error("Gagal kirim notif hotel: " . $e->getMessage());
                        }
                    } else if (in_array($request->transaction_status, ['deny', 'expire', 'cancel'])) {
                        $reservasi->update(['status_reservasi_id' => 4]);
                    }
                }
            }

            // --- B. JIKA TRANSAKSI RESTORAN (RESTO) ---
    // ... di dalam handleCallback ...
else if ($prefix == 'RESTO') {
    // Gunakan eager load jika perlu, tapi find saja cukup
    $pesanan = \App\Models\restoran\PesananMenu::find($actualId);

    if ($pesanan) {
        if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
            $pesanan->update(['status_pembayaran_id' => 2]);

            // PAKSA KIRIM NOTIFIKASI
            try {
                $this->notifService->orderConfirmed(
                    $pesanan->fcm_token ?? 'TOKEN_KOSONG',
                    $pesanan->user_id,
                    $pesanan->id,
                    (float)$pesanan->total_harga
                );
            } catch (\Exception $e) {
                Log::error("ERROR_NOTIF_RESTO: " . $e->getMessage());
            }
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



    /**
     * 6. STAFF HOTEL: KONFIRMASI CHECK-IN
     * Alur: Cari Kamar Kosong -> Update Status Reservasi (3) -> Kunci Kamar (2) -> Kirim Notif
     */
    public function confirmCheckIn(Request $request, $id)
    {
        // 1. Validasi Input Staff
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // 2. Cari data reservasi
            $reservasi = \App\Models\hotel\Reservasi::find($id);

            if (!$reservasi) {
                return response()->json(['success' => false, 'message' => 'Data reservasi tidak ditemukan.'], 404);
            }

            // Keamanan: Cek apakah sudah check-in sebelumnya?
            if ($reservasi->status_reservasi_id == 3) {
                return response()->json(['success' => false, 'message' => 'Tamu ini sudah berstatus Check-in.'], 400);
            }

            // 3. Cari unit kamar fisik yang masih status 1 (Tersedia) sesuai tipe yang dipesan
            $kamarTersedia = \App\Models\hotel\Kamar::where('tipe_kamar_id', $reservasi->tipe_kamar_id)
                                  ->where('status_kamar_id', 1)
                                  ->first();

            if (!$kamarTersedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, tidak ada unit kamar fisik yang kosong untuk tipe ini.'
                ], 400);
            }

            DB::beginTransaction();

            // 4. Update data Reservasi: Status jadi 3 (Check-in), Pasang ID Kamar, Set Deposit
            $reservasi->update([
                'status_reservasi_id' => 3,
                'kamar_id'            => $kamarTersedia->id,
                'deposit_amount'      => 100000.00, // Deposit otomatis sistem
                'confirmed_at'        => now(),
                'confirmed_by'        => $request->staff_id
            ]);

            // 5. Update status unit Kamar fisik menjadi 2 (Terisi/Occupied)
            $kamarTersedia->update(['status_kamar_id' => 2]);

            DB::commit();

            // 6. PICU NOTIFIKASI "SELAMAT MENIKMATI" KE HP USER (Port 8002)
            try {
                $this->notifService->sendCheckinSuccess(
                    $reservasi->fcm_token ?? 'no_token',
                    $reservasi->user_id,
                    $kamarTersedia->nomor_kamar
                );
            } catch (\Exception $e) {
                Log::error("Gagal kirim notif checkin: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Proses Check-in Berhasil!',
                'data' => [
                    'nomor_kamar' => $kamarTersedia->nomor_kamar,
                    'nama_tamu'   => $reservasi->details[0]->nama_tamu ?? 'Tamu',
                    'deposit'     => "Rp 100.000 (Tercatat)",
                    'status'      => 'SUDAH CHECK-IN'
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }

/**
     * 7. STAFF HOTEL: KONFIRMASI CHECK-OUT
     * Alur: Bebaskan Kamar -> Update Status Reservasi ke SELESAI (4) -> Kirim Notif
     */
    public function confirmCheckOut($id)
    {
        try {
            // Cari reservasi beserta data kamar fisiknya menggunakan relasi 'kamar'
            $reservasi = \App\Models\hotel\Reservasi::with('kamar')->find($id);
            
            if (!$reservasi) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data reservasi tidak ditemukan'
                ], 404);
            }

            // Pastikan hanya tamu yang sedang Check-in (ID 3) yang bisa Check-out
            if ($reservasi->status_reservasi_id != 3) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Hanya tamu dengan status Check-in yang dapat melakukan Check-out.'
                ], 400);
            }

            DB::beginTransaction();

            // 1. Ubah status Kamar Fisik kembali ke 1 (Tersedia/Available)
            if ($reservasi->kamar) {
                $reservasi->kamar->update(['status_kamar_id' => 1]);
            }

            // 2. Update status reservasi menjadi SELESAI (ID 4)
            // KUNCI: Sekarang kita gunakan ID 4 sesuai urutan database yang baru
            $reservasi->update([
                'status_reservasi_id' => 4,
                'updated_at' => now()
            ]);

            DB::commit();

            // 3. PICU NOTIFIKASI CHECK-OUT KE HP USER (Port 8002)
            try {
                $this->notifService->sendCheckoutSuccess(
                    $reservasi->fcm_token ?? 'no_token',
                    $reservasi->user_id,
                    $reservasi->id
                );
            } catch (\Exception $e) {
                // Gunakan \Log agar tidak error "Undefined type Log"
                Log::error("Gagal kirim notif checkout: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil. Kamar nomor ' . ($reservasi->kamar->nomor_kamar ?? '-') . ' kini tersedia kembali.',
                'status' => 'SELESAI'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

/**
 * UPLOAD FOTO TIPE KAMAR
 */
public function uploadFotoTipeKamar(Request $request, $id)
{
    $request->validate([
        'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $tipeKamar = TipeKamar::findOrFail($id);

    // Hapus foto lama jika bukan URL eksternal
    if ($tipeKamar->foto && !str_starts_with($tipeKamar->foto, 'http')) {
        Storage::disk('public')->delete($tipeKamar->foto);
    }

    // Simpan foto baru
    $path = $request->file('foto')->store('tipe_kamar', 'public');

    $tipeKamar->update(['foto' => $path]);

    return response()->json([
        'success' => true,
        'message' => 'Foto berhasil diupload',
        'foto_url' => asset('storage/' . $path),
    ]);
}


}
