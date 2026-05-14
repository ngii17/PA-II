<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationClientService
{
    protected $baseUrl;
    protected $secret;

    public function __construct()
    {
        $this->baseUrl = config('services.notification.url');
        $this->secret = config('app.service_secret_key');
    }

    /**
     * Pintu utama pengiriman ke Microservice Notifikasi
     */
    private function post($endpoint, $data)
    {
        try {
            $response = Http::withHeaders([
                'X-Service-Secret' => $this->secret,
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/notify/' . $endpoint, $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("NOTIFICATION_SERVICE_CONNECTION_ERROR: " . $e->getMessage());
            return null;
        }
    }

    // 1. Pesanan Dikonfirmasi (Ditambah $userId)
    public function bookingConfirmed($fcmToken, $userId, $bookingId, $roomType, $checkinDate)
    {
        return $this->post('booking-confirmed', [
            'fcm_token'    => $fcmToken,
            'user_id'      => $userId, // <--- Sekarang terkirim ke 8002
            'booking_id'   => $bookingId,
            'room_type'    => $roomType,
            'checkin_date' => $checkinDate,
        ]);
    }

    // 2. Pesanan Dibatalkan (Ditambah $userId)
    public function bookingCancelled($fcmToken, $userId, $bookingId, $refundInfo)
    {
        return $this->post('booking-cancelled', [
            'fcm_token'   => $fcmToken,
            'user_id'     => $userId,
            'booking_id'  => $bookingId,
            'refund_info' => $refundInfo,
        ]);
    }

    // 3. Pengingat Check-in (Ditambah $userId)
    public function sendCheckinReminder($fcmToken, $userId, $bookingId, $checkinTime)
    {
        return $this->post('checkin-reminder', [
            'fcm_token'    => $fcmToken,
            'user_id'      => $userId,
            'booking_id'   => $bookingId,
            'checkin_time' => $checkinTime,
        ]);
    }

    // 4. Kamar Siap (Ditambah $userId)
    public function roomReady($fcmToken, $userId, $roomNumber)
    {
        return $this->post('room-ready', [
            'fcm_token'   => $fcmToken,
            'user_id'     => $userId,
            'room_number' => $roomNumber,
        ]);
    }

    // 5. Pengingat Check-out (Ditambah $userId)
    public function sendCheckoutReminder($fcmToken, $userId, $bookingId)
    {
        return $this->post('checkout-reminder', [
            'fcm_token'  => $fcmToken,
            'user_id'    => $userId,
            'booking_id' => $bookingId,
        ]);
    }

    // 6. Pembayaran Gagal (Ditambah $userId)
    public function paymentFailed($fcmToken, $userId, $bookingId)
    {
        return $this->post('payment-failed', [
            'fcm_token'  => $fcmToken,
            'user_id'    => $userId,
            'booking_id' => $bookingId,
        ]);
    }

    // Tambahkan ini di dalam class NotificationClientService

    /**
     * Notifikasi Pesanan Restoran Dikonfirmasi
     */
    public function orderConfirmed($fcmToken, $userId, $orderId, $totalHarga)
{
    // Pastikan endpoint-nya adalah 'order-confirmed' sesuai routes/api.php di Port 8002
    return $this->post('order-confirmed', [
        'fcm_token'  => $fcmToken,
        'user_id'    => $userId,
        'order_id'   => $orderId,
        'total_bayar'=> $totalHarga,
    ]);
}

/**
     * Notifikasi Pesanan Restoran Siap Disajikan
     */
    public function orderReady($fcmToken, $userId, $orderId)
    {
        return $this->post('order-ready', [
            'fcm_token' => $fcmToken,
            'user_id'   => $userId,
            'order_id'  => $orderId,
        ]);
    }

    /**
     * Notifikasi Pesanan Restoran Dibatalkan
     */
    public function orderCancelled($fcmToken, $userId, $orderId)
    {
        return $this->post('order-cancelled', [
            'fcm_token' => $fcmToken,
            'user_id'   => $userId,
            'order_id'  => $orderId,
        ]);
    }

    public function broadcastPromo($tokens, $title, $body)
    {
        return $this->post('broadcast', [
            'tokens' => $tokens, // Mengirimkan Array
            'title'  => $title,
            'body'   => $body,
        ]);
    }

    /**
     * Kirim Notifikasi Massal (Broadcast) ke Port 8002
     */
    public function massSend($data)
    {
        // Pastikan endpoint-nya sesuai dengan yang ada di Port 8002
        // Berdasarkan route:list kamu sebelumnya, prefix-nya adalah 'notify'
        return $this->post('broadcast/send', $data);
    }


    /**
     * Notifikasi Check-in Berhasil
     */
    public function sendCheckinSuccess($fcmToken, $userId, $roomNumber)
    {
        // Fungsi ini memanggil 'post' yang private (karena masih satu class, ini dibolehkan)
        return $this->post('hotel/checkin-success', [
            'fcm_token'   => $fcmToken,
            'user_id'     => $userId,
            'room_number' => $roomNumber,
        ]);
    }



    /**
     * Notifikasi Check-out Berhasil
     */
    public function sendCheckoutSuccess($fcmToken, $userId, $reservationId)
    {
        return $this->post('hotel/checkout-success', [
            'fcm_token'      => $fcmToken,
            'user_id'        => $userId,
            'reservation_id' => $reservationId,
        ]);
    }
    
}