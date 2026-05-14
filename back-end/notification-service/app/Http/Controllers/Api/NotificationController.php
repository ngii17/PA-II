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

    /**
     * NOTIFIKASI BROADCAST (PROMO/EVENT)
     */
    public function sendBroadcast(Request $request)
    {
        // Validasi: Meminta daftar token (array), judul, dan isi pesan
        $this->validateRequest($request, ['tokens', 'title', 'body']);

        $tokens = $request->tokens; // Ini adalah Array of Strings
        $successCount = 0;

        foreach ($tokens as $token) {
            // Kita gunakan fungsi executeSend yang sudah ada
            // user_id kita set 0 atau null untuk broadcast umum jika tidak spesifik
            $send = $this->fcm->send($token, $request->title, $request->body, ['type' => 'promo']);
            
            if ($send) {
                $successCount++;
                // Opsional: Catat ke log agar muncul di inbox masing-masing user
                // Tapi ini butuh mapping token ke user_id. 
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Broadcast terkirim ke $successCount perangkat."
        ]);
    }


    // Ambil Detail
public function show(Request $request, $id)
{
    $userId = $request->query('user_id'); // Kita kirim user_id via query param
    $notif = NotifLog::find($id);

    if (!$notif) return response()->json(['status' => 'error', 'message' => 'Notifikasi tidak ditemukan'], 404);
    
    // Validasi Pemilik
    if ($notif->user_id != $userId) {
        return response()->json(['status' => 'error', 'message' => 'Akses dilarang'], 403);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Data ditemukan',
        'data' => $notif
    ], 200);
}

// Tandai Dibaca
public function markAsRead(Request $request, $id)
{
    $userId = $request->query('user_id');
    $notif = NotifLog::find($id);

    if ($notif && $notif->user_id == $userId) {
        $notif->update(['is_read' => true]);
        return response()->json(['status' => 'success', 'message' => 'Telah dibaca']);
    }
    return response()->json(['status' => 'error', 'message' => 'Gagal update'], 400);
}

// Hapus Notifikasi
public function destroy(Request $request, $id)
{
    $userId = $request->query('user_id');
    $notif = NotifLog::find($id);

    if ($notif && $notif->user_id == $userId) {
        $notif->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil dihapus']);
    }
    return response()->json(['status' => 'error', 'message' => 'Gagal menghapus'], 403);
}


public function sendMassNotification(Request $request) 
{
    // Kita terima array: [['user_id' => 1, 'token' => '...'], dst]
    $recipients = $request->recipients; 
    $title = $request->title;
    $body = $request->body;

    foreach ($recipients as $target) {
        // 1. Kirim Pop-up
        $this->fcm->send($target['token'], $title, $body, ['type' => 'broadcast']);

        // 2. Simpan ke Log (Agar muncul di Kotak Masuk/Inbox user)
        \App\Models\NotifLog::create([
            'user_id'   => $target['user_id'],
            'type'      => 'broadcast',
            'title'     => $title,
            'body'      => $body,
            'fcm_token' => $target['token'],
            'status'    => 'success',
            'sent_at'   => now(),
        ]);
    }

    return response()->json(['status' => 'success', 'message' => 'Broadcast sukses']);
}



    /**
     * NOTIFIKASI SAAT TAMU RESMI CHECK-IN (DIUBAH OLEH ADMIN)
     */
    public function hotelCheckinSuccess(Request $request)
    {
        // 1. Validasi input agar tidak error saat merakit pesan
        $this->validateRequest($request, ['fcm_token', 'user_id', 'room_number']);

        // 2. Susun Judul & Pesan yang lebih hangat (Warm Greeting)
        $title = "Check-in Berhasil! 🏨";
        $body  = "Selamat datang di Purnama Hotel! 🎉 Anda resmi check-in di Kamar {$request->room_number}. Silakan nikmati fasilitas terbaik kami, semoga istirahat Anda sangat menyenangkan.";
        
        // 3. Kirim dan Simpan Log
        // Kita gunakan 'checkin_reminder' sebagai tipe log agar ikon hotel otomatis muncul di Flutter
        return $this->executeSend(
            $request->fcm_token, 
            $request->user_id, 
            $title, 
            $body, 
            [
                'type' => 'hotel_checkin', 
                'room_number' => (string)$request->room_number,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK' // Memastikan klik mengarah ke aplikasi
            ], 
            'checkin_reminder' 
        );
    }


    public function hotelCheckoutSuccess(Request $request)
    {
        // Validasi input
        $this->validateRequest($request, ['fcm_token', 'user_id', 'reservation_id']);

        $title = "Check-out Berhasil! ✨";
        $body  = "Terima kasih telah menginap di Purnama Hotel. Pesanan #INV-{$request->reservation_id} telah selesai. Sampai jumpa kembali!";
        
        return $this->executeSend(
            $request->fcm_token, 
            $request->user_id, 
            $title, 
            $body, 
            ['type' => 'hotel_checkout', 'reservation_id' => (string)$request->reservation_id], 
            'checkout_reminder' // Tipe ini agar ikon pintu keluar muncul di Flutter
        );
    }


    

}