<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restoran\PesananMenu;
use App\Models\Restoran\DetailPesanan;
use App\Models\Restoran\Menu;
use App\Models\Restoran\KategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// --- IMPORT MIDTRANS ---
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\event\Event;
use App\Services\NotificationClientService;
use Illuminate\Support\Facades\Log; // <--- Tambahkan ini
use App\Helpers\AuthServiceHelper; // <--- Helper untuk validasi token

class RestoranController extends Controller
{


    protected $notifService;

    public function __construct(NotificationClientService $notifService)
    {
        $this->notifService = $notifService;
    }
    /**
     * 1. MENGAMBIL DAFTAR MENU (DENGAN FILTER EVENT)
     */
public function getMenus()
    {
        try {
            // 1. Cari Event Aktif
            $activeEvent = Event::where('is_active', true)->first();

            // 2. Cari Promo Global Restoran (is_active = true)
            $promoGlobal = \App\Models\Hotel\Promo::where('kategori', 'restoran')
                ->where('is_active', true)
                ->whereNull('kode_promo')
                ->whereDate('tgl_mulai', '<=', now())
                ->whereDate('tgl_selesai', '>=', now())
                ->first();

            // 3. Ambil Menu berdasarkan Event
            if (!$activeEvent || $activeEvent->event_code == 'default') {
                $rawMenus = Menu::with(['kategori', 'status'])->get();
            } else {
                // TETAP MENGGUNAKAN RELASI DENGAN withPivot
                $rawMenus = $activeEvent->menus()->with(['kategori', 'status'])->get();
                
                // Fallback jika menu event kosong
                if ($rawMenus->isEmpty()) {
                    $rawMenus = Menu::with(['kategori', 'status'])->get();
                }
            }

            // 4. LOGIKA HITUNG HARGA (PARETO PROMO)
            $data = $rawMenus->map(function ($item) use ($promoGlobal) {
                $hargaAsli = (float) $item->harga;
                $hargaAkhir = $hargaAsli;
                $infoPromo = null;

                // Cek apakah ada diskon global (misal 3%)
                if ($promoGlobal) {
                    $potongan = ($promoGlobal->tipe_diskon == 'persen') 
                        ? $hargaAsli * ($promoGlobal->nominal_potongan / 100) 
                        : (float) $promoGlobal->nominal_potongan;
                    
                    $infoPromo = ($promoGlobal->tipe_diskon == 'persen') 
                        ? "Diskon " . (int)$promoGlobal->nominal_potongan . "%" 
                        : "Potongan Rp " . number_format($potongan, 0, ',', '.');
                    
                    $hargaAkhir = $hargaAsli - $potongan;
                }

                return [
                    'id' => $item->id,
                    'nama_menu' => $item->nama_menu,
                    'deskripsi' => $item->deskripsi,
                    'harga_asli' => $hargaAsli,
                    'harga_akhir' => $hargaAkhir,
                    'stok' => $item->stok,
                    'foto_menu' => $item->foto_menu,
                    'kategori' => $item->kategori,
                    'status' => $item->status,
                    'promo_aktif' => $infoPromo, // Teks ini untuk label di Flutter
                ];
            });

            return response()->json([
                'success' => true,
                'active_theme' => $activeEvent->event_code ?? 'default',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
/**
     * 2. PROSES PEMESANAN & GENERATE PEMBAYARAN MIDTRANS
     * (Versi Lengkap: Dengan Lokasi Pengantaran Meja/Kamar)
     */
    public function placeOrder(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
        }

        $userId = AuthServiceHelper::getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid atau expired'], 401);
        }

        $validator = Validator::make($request->all(), [
            'fcm_token'         => 'nullable|string',
            'metode_pembayaran' => 'required|string',
            'tipe_pengantaran'  => 'required|string',
            'nomor_lokasi'      => 'required|string',
            'items'             => 'required|array',
            'items.*.menu_id'   => 'required|exists:menu,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'promo_id'          => 'nullable|integer|exists:promo,id',
            'potongan_promo'    => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $totalHarga = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);

                if ($menu->stok < $item['jumlah']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok untuk {$menu->nama_menu} tidak mencukupi. Sisa stok: {$menu->stok}"
                    ], 400);
                }

                $menu->decrement('stok', $item['jumlah']);

                $totalHarga += ($menu->harga * $item['jumlah']);
                $orderItems[] = [
                    'menu_id'           => $menu->id,
                    'jumlah'            => $item['jumlah'],
                    'harga_at_porsi'    => $menu->harga,
                    'status_pesanan_id' => 1,
                ];
            }

            $potonganPromo = 0;
            $promoId       = $request->promo_id ?? null;

            if ($promoId) {
                $promo = \App\Models\Hotel\Promo::find($promoId);

                if ($promo) {
                    if ($promo->tipe_diskon === 'persen') {
                        $potonganPromo = $totalHarga * ($promo->nominal_potongan / 100);
                    } else {
                        $potonganPromo = min((float) $promo->nominal_potongan, $totalHarga);
                    }
                }
            }

            $totalBayar = max($totalHarga - $potonganPromo, 0);

            $pesanan = PesananMenu::create([
                'user_id'              => $userId, // ← DARI TOKEN
                'fcm_token'            => $request->fcm_token,
                'total_harga'          => $totalBayar,
                'metode_pembayaran'    => $request->metode_pembayaran,
                'status_pembayaran_id' => 1,
                'tipe_pengantaran'     => $request->tipe_pengantaran,
                'nomor_lokasi'         => $request->nomor_lokasi,
            ]);

            foreach ($orderItems as $orderItem) {
                $pesanan->details()->create($orderItem);
            }

            if ($promoId) {
                \App\Models\promo\PromoUsage::firstOrCreate(
                    [
                        'promo_id' => $promoId,
                        'user_id'  => $userId, // ← DARI TOKEN
                    ],
                    ['kategori' => 'restoran']
                );
            }

            $snapToken   = null;
            $redirectUrl = null;

            if ($request->metode_pembayaran !== 'Bayar di Kasir') {
                Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
                Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
                Config::$isSanitized  = true;
                Config::$is3ds        = true;

                $enabledPayments = ($request->metode_pembayaran == 'Transfer Bank')
                    ? ['bank_transfer']
                    : ['gopay', 'shopeepay', 'dana', 'linkaja', 'qris'];

                $params = [
                    'transaction_details' => [
                        'order_id'     => 'RESTO-' . $pesanan->id . '-' . time(),
                        'gross_amount' => (int) $totalBayar,
                    ],
                    'customer_details' => [
                        'first_name' => 'User ID ' . $userId, // ← DARI TOKEN
                        'notes'      => 'Antar ke ' . $request->tipe_pengantaran . ' ' . $request->nomor_lokasi,
                    ],
                    'enabled_payments' => $enabledPayments,
                ];

                $midtransResponse = Snap::createTransaction($params);
                $snapToken        = $midtransResponse->token;
                $redirectUrl      = $midtransResponse->redirect_url;

                $pesanan->update(['snap_token' => $snapToken]);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat!',
                'snap_token'   => $snapToken,
                'redirect_url' => $redirectUrl,
                'data'         => [
                    'order_id'       => $pesanan->id,
                    'subtotal'       => $totalHarga,
                    'potongan_promo' => $potonganPromo,
                    'total_bayar'    => $totalBayar,
                    'lokasi'         => $request->tipe_pengantaran . ' ' . $request->nomor_lokasi,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal memproses pesanan: ' . $e->getMessage()], 500);
        }
    }
    /**
     * 3. MENGAMBIL KATEGORI
     */
    public function getCategories()
    {
        try {
            $categories = KategoriMenu::all();
            return response()->json(['success' => true, 'data' => $categories], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 4. WEBHOOK CALLBACK RESTORAN
     */
    public function handleRestoCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $orderIdParts = explode('-', $request->order_id);
            $pesananId = $orderIdParts[1];

            $pesanan = PesananMenu::find($pesananId);

            if ($pesanan) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $pesanan->update(['status_pembayaran_id' => 2]);
                } else if (in_array($request->transaction_status, ['deny', 'expire', 'cancel'])) {
                    $pesanan->update(['status_pembayaran_id' => 4]);
                }
            }
        }

        return response()->json(['message' => 'Resto Notification Handled']);
    }

    /**
     * 5. CEK STATUS PESANAN (Polling)
     */
    public function checkOrderStatus($id)
    {
        $pesanan = PesananMenu::find($id);
        if (!$pesanan) return response()->json(['success' => false], 404);

        return response()->json([
            'success' => true,
            'status_bayar_id' => (int) $pesanan->status_pembayaran_id,
        ]);
    }

/**
     * 6. AMBIL RIWAYAT PESANAN RESTORAN
     * Logika: Cek ulasan berdasarkan ID Pesanan & ID Menu
     */
    public function getOrderHistory(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
        }

        $userId = AuthServiceHelper::getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid atau expired'], 401);
        }

        try {
            $history = PesananMenu::with(['details.menu'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $history->map(function ($order) {
                $order->details = $order->details->map(function ($detail) use ($order) {
                    $review = \App\Models\restoran\UlasanRestoran::where('pesanan_menu_id', $order->id)
                        ->where('menu_id', $detail->menu_id)
                        ->first();

                    $detail->is_reviewed = $review ? true : false;
                    $detail->review_id = $review ? $review->id : null;
                    $detail->existing_review = $review ? [
                        'rating'       => $review->rating,
                        'komentar'     => $review->komentar,
                        'is_anonymous' => (bool) $review->is_anonymous
                    ] : null;

                    $detail->status_pesanan_id = (int) $order->status_pesanan_id;

                    return $detail;
                });
                return $order;
            });

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * FUNGSI UPDATE STATUS PESANAN (Hanya 4 Status)
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi: Hanya menerima ID 1, 2, 3, atau 4
        $validator = Validator::make($request->all(), [
            'status_pesanan_id' => 'required|integer|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Cari data pesanan
            $pesanan = \App\Models\restoran\PesananMenu::find($id);

            if (!$pesanan) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
            }

            // Update status di database (Pastikan nama kolom di tabel pesanan_menu sesuai)
            $pesanan->update([
                'status_pesanan_id' => $request->status_pesanan_id
            ]);

            // --- LOGIKA PEMICU NOTIFIKASI ---

            // HANYA jika status berubah menjadi 3 (Disajikan)
            if ($request->status_pesanan_id == 3) {
                $this->notifService->orderReady(
                    $pesanan->fcm_token ?? 'no_token',
                    $pesanan->user_id,
                    $pesanan->id
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan diperbarui ke: ' . $this->_getStatusName($request->status_pesanan_id),
                'notif_sent' => ($request->status_pesanan_id == 3) ? 'Yes' : 'No'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk mempermudah pembacaan response (Opsional)
     */
    private function _getStatusName($id) {
        $names = [1 => 'Menunggu', 2 => 'Dimasak', 3 => 'Disajikan', 4 => 'Selesai'];
        return $names[$id] ?? 'Unknown';
    }


    /**
     * MENGAMBIL PROMO UMUM AKTIF UNTUK POP-UP (RESTORAN)
     * Sinkron dengan fitur saklar is_active
     */
    public function getActivePublicPromo()
    {
        try {
            $today = now()->format('Y-m-d');

            // --- QUERY DENGAN PENGECEKAN IS_ACTIVE ---
            $promo = \App\Models\hotel\Promo::whereIn('kategori', ['restoran', 'semua'])
                ->where('is_active', true) // <--- SAKLAR UTAMA: Wajib Aktif
                ->where(function($query) {
                    $query->whereNull('kode_promo')
                          ->orWhere('kode_promo', ''); // Handle jika admin mengisi string kosong
                })
                ->whereDate('tgl_mulai', '<=', $today)
                ->whereDate('tgl_selesai', '>=', $today)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$promo) {
                // Gunakan \Log agar tidak error "Undefined Type Log"
                Log::info("Promo Resto Pop-up: Tidak ditemukan promo aktif untuk tanggal $today");

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada promo aktif'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id'            => $promo->id,
                    'nama_promo'    => $promo->nama_promo,
                    'tipe_diskon'   => $promo->tipe_diskon,
                    'nominal'       => (float)$promo->nominal_potongan,
                    'tgl_selesai'   => $promo->tgl_selesai,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil promo: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * KONFIRMASI PEMBAYARAN OLEH STAFF RESTORAN (KASIR)
     * Dipanggil saat user membayar di kasir
     */
    public function confirmPaymentByStaff(Request $request, $id)
    {
        // 1. Validasi input metode bayar asli (Tunai/Debit/QRIS Manual)
        $validator = Validator::make($request->all(), [
            'metode_final' => 'required|string', // Contoh: "Tunai" atau "Debit"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $pesanan = PesananMenu::find($id);

            if (!$pesanan) {
                return response()->json(['success' => false, 'message' => 'Nota tidak ditemukan'], 404);
            }

            // 2. Update Status menjadi PAID (ID 2) dan catat metode final
            $pesanan->update([
                'status_pembayaran_id' => 2, // LUNAS
                'metode_pembayaran'    => $request->metode_final, // Update dari "Bayar di Kasir" ke riil
            ]);

            // 3. KIRIM NOTIFIKASI KE USER (Bahwa pembayaran sukses)
            try {
                $this->notifService->orderConfirmed(
                    $pesanan->fcm_token ?? 'no_token',
                    $pesanan->user_id,
                    $pesanan->id,
                    (float)$pesanan->total_harga
                );
            } catch (\Exception $e) {
                Log::error("Gagal kirim notif lunas kasir: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi oleh Staff Restoran.',
                'data'    => $pesanan
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


}
