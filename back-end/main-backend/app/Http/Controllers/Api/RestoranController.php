<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\restoran\PesananMenu;
use App\Models\restoran\DetailPesanan;
use App\Models\restoran\Menu;
use App\Models\restoran\KategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// --- IMPORT MIDTRANS ---
use Midtrans\Config;
use Midtrans\Snap;

class RestoranController extends Controller
{
    /**
     * 1. MENGAMBIL DAFTAR MENU
     */
    public function getMenus()
    {
        try {
            $menus = Menu::with(['kategori', 'status'])->get();
            return response()->json(['success' => true, 'data' => $menus], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. PROSES PEMESANAN & GENERATE PEMBAYARAN MIDTRANS
     */
public function placeOrder(Request $request)
{
    // 1. Validasi Input Dasar
    $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'metode_pembayaran' => 'required|string',
        'items' => 'required|array',
        'items.*.menu_id' => 'required|exists:menu,id',
        'items.*.jumlah' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();

    try {
        $totalHarga = 0;
        $orderItems = [];

        // --- 2. LOGIKA VALIDASI & PENGURANGAN STOK ---
        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);

            // Cek apakah stok cukup
            if ($menu->stok < $item['jumlah']) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok untuk {$menu->nama_menu} tidak mencukupi. Sisa stok: {$menu->stok}"
                ], 400);
            }

            // Kurangi stok di database
            $menu->decrement('stok', $item['jumlah']);

            // Hitung harga
            $totalHarga += ($menu->harga * $item['jumlah']);
            $orderItems[] = [
                'menu_id' => $menu->id,
                'jumlah' => $item['jumlah'],
                'harga_at_porsi' => $menu->harga,
                'status_pesanan_id' => 1,
            ];
        }

        // 3. Simpan Nota (Header)
        $pesanan = PesananMenu::create([
            'user_id' => $request->user_id,
            'total_harga' => $totalHarga,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pembayaran_id' => 1, // Pending
        ]);

        // 4. Simpan Rincian (Detail)
        foreach ($orderItems as $orderItem) {
            $pesanan->details()->create($orderItem);
        }

        // 5. Integrasi Midtrans (Hanya jika bukan Bayar di Kasir)
        $snapToken = null;
        $redirectUrl = null;

        if ($request->metode_pembayaran !== 'Bayar di Kasir') {
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $enabledPayments = ($request->metode_pembayaran == 'Transfer Bank') ? ['bank_transfer'] : ['gopay', 'shopeepay', 'qris'];

            $params = [
                'transaction_details' => [
                    'order_id' => 'RESTO-' . $pesanan->id . '-' . time(),
                    'gross_amount' => (int) $totalHarga,
                ],
                'customer_details' => ['first_name' => 'User ID ' . $request->user_id],
                'enabled_payments' => $enabledPayments,
            ];

            $midtransResponse = Snap::createTransaction($params);
            $snapToken = $midtransResponse->token;
            $redirectUrl = $midtransResponse->redirect_url;

            $pesanan->update(['snap_token' => $snapToken]);
        }

        DB::commit();

        return response()->json([
            'success'      => true,
            'message'      => 'Pesanan berhasil dibuat!',
            'snap_token'   => $snapToken,
            'redirect_url' => $redirectUrl,
            'data'         => [ // Kita bungkus kembali agar Flutter tidak bingung
                'order_id'    => $pesanan->id,
                'total_bayar' => $totalHarga
            ]
        ], 201);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
    }
}    /**
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
     * 4. WEBHOOK CALLBACK RESTORAN (Update Status Bayar Otomatis)
     */
    public function handleRestoCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        
        // 1. Buat Hash untuk mencocokkan Signature Key (Keamanan)
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            // 2. Ambil ID Pesanan dari Order ID Midtrans (Format: RESTO-ID-TIME)
            $orderIdParts = explode('-', $request->order_id);
            $pesananId = $orderIdParts[1];

            $pesanan = PesananMenu::find($pesananId);

            if ($pesanan) {
                // 3. Logika Update Status Pembayaran
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $pesanan->update(['status_pembayaran_id' => 2]); // 2 = Terbayar/Lunas
                } else if (in_array($request->transaction_status, ['deny', 'expire', 'cancel'])) {
                    $pesanan->update(['status_pembayaran_id' => 4]); // 4 = Batal/Gagal
                }
            }
        }

        return response()->json(['message' => 'Resto Notification Handled']);
    }

    /**
     * 5. CEK STATUS PESANAN (Polling dari Flutter)
     */
    public function checkOrderStatus($id)
    {
        $pesanan = PesananMenu::find($id);
        if (!$pesanan) return response()->json(['success' => false], 404);

        return response()->json([
            'success' => true,
            // Pastikan kuncinya 'status_bayar_id' agar sama dengan harapan Flutter
            'status_bayar_id' => (int) $pesanan->status_pembayaran_id, 
        ]);
    }
    /**
     * 6. AMBIL RIWAYAT PESANAN RESTORAN USER
     */
    public function getOrderHistory(Request $request)
    {
        $userId = $request->query('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID tidak ditemukan.'], 400);
        }

        try {
            // Ambil data pesanan beserta detail makanan di dalamnya
            $history = PesananMenu::with(['details.menu'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $history
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}