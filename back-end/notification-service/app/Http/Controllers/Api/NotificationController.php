<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FcmService;
use App\Models\NotifLog; 
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Inject FcmService via constructor
     */
    public function __construct(private FcmService $fcm)
    {
    }

    // 1. Notifikasi Pesanan Dikonfirmasi
    public function bookingConfirmed(Request $request)
    {
        // Tambahkan user_id dalam validasi
        $this->validateRequest($request, ['fcm_token', 'user_id', 'booking_id', 'room_type', 'checkin_date']);

        $title = "Pesanan Dikonfirmasi! ✅";
        $body = "Pemesanan kamar {$request->room_type} untuk tanggal {$request->checkin_date} telah berhasil dikonfirmasi.";
        
        return $this->executeSend(
            $request->fcm_token, 
            $request->user_id, // Kirim ID user ke fungsi simpan log
            $title, 
            $body, 
            [
                'type' => 'booking_details',
                'booking_id' => $request->booking_id
            ], 
            'booking_confirmed'
        );
    }

    // 2. Notifikasi Pesanan Dibatalkan
    public function bookingCancelled(Request $request)
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'booking_id', 'refund_info']);

        $title = "Pesanan Dibatalkan ❌";
        $body = "Pesanan #{$request->booking_id} telah dibatalkan. Info pengembalian dana: {$request->refund_info}";

        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, [
            'type' => 'booking_cancelled',
            'booking_id' => $request->booking_id
        ], 'booking_cancelled');
    }

    // 3. Pengingat Check-in
    public function checkinReminder(Request $request)
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'booking_id', 'checkin_time']);

        $title = "Siap untuk Check-in? 🏨";
        $body = "Kami menunggu kedatangan Anda hari ini sekitar pukul {$request->checkin_time}.";

        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, [
            'type' => 'checkin_reminder',
            'booking_id' => $request->booking_id
        ], 'checkin_reminder');
    }

    // 4. Notifikasi Kamar Siap
    public function roomReady(Request $request)
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'room_number']);

        $title = "Kamar Anda Sudah Siap! ✨";
        $body = "Kamar nomor {$request->room_number} sudah dibersihkan dan siap ditempati. Selamat beristirahat!";

        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, [
            'type' => 'room_ready',
            'room_number' => $request->room_number
        ], 'room_ready');
    }

    // 5. Pengingat Check-out
    public function checkoutReminder(Request $request)
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'booking_id']);

        $title = "Waktunya Check-out ⏰";
        $body = "Jangan lupa waktu check-out maksimal jam 12.00 hari ini. Pastikan tidak ada barang tertinggal.";

        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, [
            'type' => 'checkout_reminder',
            'booking_id' => $request->booking_id
        ], 'checkout_reminder');
    }

    // 6. Pembayaran Gagal
    public function paymentFailed(Request $request)
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'booking_id']);

        $title = "Pembayaran Gagal ⚠️";
        $body = "Kami tidak berhasil memproses pembayaran untuk pesanan #{$request->booking_id}. Mohon ulangi transaksi Anda.";

        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, [
            'type' => 'payment_failed',
            'booking_id' => $request->booking_id
        ], 'payment_failed');
    }

    /**
     * 7. Fungsi pembantu untuk validasi input
     */
    private function validateRequest($request, $fields)
    {
        $rules = [];
        foreach ($fields as $field) { $rules[$field] = 'required'; }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)->send();
            exit;
        }
    }

    /**
     * 8. FUNGSI EKSEKUSI & LOGGING (Perbaikan: Simpan user_id)
     */
    private function executeSend($token, $userId, $title, $body, $data, $type)
    {
        // 1. Kirim ke Google FCM melalui Service
        $success = $this->fcm->send($token, $title, $body, $data);
        
        // 2. CATAT KE TABEL NOTIF_LOGS (user_id sekarang diisi)
        NotifLog::create([
            'user_id'   => $userId,
            'type'      => $type,
            'title'     => $title, // SIMPAN JUDUL
            'body'      => $body,  // SIMPAN ISI PESAN
            'fcm_token' => $token,
            'status'    => $success ? 'success' : 'failed',
            'sent_at'   => now(),
            'error'     => $success ? null : 'FCM Error atau Token Tidak Valid',
        ]);
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notifikasi terkirim & dicatat' : 'Gagal mengirim, log telah disimpan'
        ], $success ? 200 : 500);
    }

    /**
     * 9. AMBIL DAFTAR NOTIFIKASI KHUSUS UNTUK USER TERTENTU (INBOX)
     */
    public function getNotifications($userId)
    {
        try {
            // Pastikan $userId diubah menjadi angka (integer)
            $notifs = NotifLog::where('user_id', (int) $userId)
                ->whereNotNull('title') // Hanya tampilkan yang ada judulnya
                ->orderBy('sent_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $notifs
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * 10. Notifikasi Pesanan Restoran Dikonfirmasi
     */
    public function orderConfirmed(Request $request) 
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'order_id', 'total_bayar']);
        
        $title = "Pesanan Resto Diterima! 🍔";
        $body = "Pesanan #RS-{$request->order_id} sudah dikonfirmasi. Selamat menikmati!";
        
        return $this->executeSend(
            (string)$request->fcm_token, // Paksa jadi string
            $request->user_id, 
            $title, 
            $body, 
            [
                'type' => 'order_details',
                'order_id' => (string)$request->order_id // Wajib String untuk FCM v1
            ], 
            'order_confirmed'
        );
    }

    /**
     * 11. Pesanan Resto Dibatalkan
     */
    public function orderCancelled(Request $request) 
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'order_id']);
        $title = "Pesanan Resto Dibatalkan ❌";
        $body = "Mohon maaf, pesanan #RS-{$request->order_id} Anda telah dibatalkan. Silakan cek detail pengembalian dana.";
        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, ['order_id' => $request->order_id], 'order_cancelled');
    }

    /**
     * 12. Pesanan Resto Siap
     */
    public function orderReady(Request $request) 
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'order_id']);
        $title = "Makanan Siap Disajikan! 🍕";
        $body = "Pesanan #RS-{$request->order_id} sudah siap. Pelayan kami akan segera mengantarkannya ke meja/kamar Anda.";
        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, ['order_id' => $request->order_id], 'order_ready');
    }

    /**
     * 13. Pembayaran Resto Gagal
     */
    public function orderPaymentFailed(Request $request) 
    {
        $this->validateRequest($request, ['fcm_token', 'user_id', 'order_id']);
        $title = "Pembayaran Gagal ⚠️";
        $body = "Transaksi untuk pesanan #RS-{$request->order_id} gagal diproses. Silakan coba lagi.";
        return $this->executeSend($request->fcm_token, $request->user_id, $title, $body, ['order_id' => $request->order_id], 'order_payment_failed');
    }
}