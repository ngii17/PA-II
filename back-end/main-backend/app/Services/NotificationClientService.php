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

    // 1. Pesanan Dikonfirmasi
    public function bookingConfirmed($fcmToken, $userId, $bookingId, $roomType, $checkinDate)
    {
        return $this->post('booking-confirmed', [
            'fcm_token'    => $fcmToken,
            'user_id'      => $userId,
            'booking_id'   => $bookingId,
            'room_type'    => $roomType,
            'checkin_date' => $checkinDate,
        ]);
    }

    // 2. Pesanan Dibatalkan
    public function bookingCancelled($fcmToken, $userId, $bookingId, $refundInfo)
    {
        return $this->post('booking-cancelled', [
            'fcm_token'   => $fcmToken,
            'user_id'     => $userId,
            'booking_id'  => $bookingId,
            'refund_info' => $refundInfo,
        ]);
    }

    // 3. Pengingat Check-in
    public function sendCheckinReminder($fcmToken, $userId, $bookingId, $checkinTime)
    {
        return $this->post('checkin-reminder', [
            'fcm_token'    => $fcmToken,
            'user_id'      => $userId,
            'booking_id'   => $bookingId,
            'checkin_time' => $checkinTime,
        ]);
    }

    // 4. Kamar Siap
    public function roomReady($fcmToken, $userId, $roomNumber)
    {
        return $this->post('room-ready', [
            'fcm_token'   => $fcmToken,
            'user_id'     => $userId,
            'room_number' => $roomNumber,
        ]);
    }

    // 5. Pengingat Check-out
    public function sendCheckoutReminder($fcmToken, $userId, $bookingId)
    {
        return $this->post('checkout-reminder', [
            'fcm_token'  => $fcmToken,
            'user_id'    => $userId,
            'booking_id' => $bookingId,
        ]);
    }

    // 6. Pembayaran Gagal
    public function paymentFailed($fcmToken, $userId, $bookingId)
    {
        return $this->post('payment-failed', [
            'fcm_token'  => $fcmToken,
            'user_id'    => $userId,
            'booking_id' => $bookingId,
        ]);
    }

    // 7. Pesanan Restoran Dikonfirmasi
    public function orderConfirmed($fcmToken, $userId, $orderId, $totalHarga)
    {
        return $this->post('order-confirmed', [
            'fcm_token'   => $fcmToken,
            'user_id'     => $userId,
            'order_id'    => $orderId,
            'total_bayar' => $totalHarga,
        ]);
    }

    // 8. Pesanan Restoran Siap
    public function orderReady($fcmToken, $userId, $orderId)
    {
        return $this->post('order-ready', [
            'fcm_token' => $fcmToken,
            'user_id'   => $userId,
            'order_id'  => $orderId,
        ]);
    }

    // 9. Pesanan Restoran Dibatalkan
    public function orderCancelled($fcmToken, $userId, $orderId)
    {
        return $this->post('order-cancelled', [
            'fcm_token' => $fcmToken,
            'user_id'   => $userId,
            'order_id'  => $orderId,
        ]);
    }

    // 10. Broadcast Promo (versi lama - tetap dipertahankan)
    public function broadcastPromo($tokens, $title, $body)
    {
        return $this->post('broadcast', [
            'tokens' => $tokens,
            'title'  => $title,
            'body'   => $body,
        ]);
    }

    /**
     * 11. Kirim Notifikasi Massal (Broadcast Promo) ke Port 8002
     * FIX: Filter recipients yang tokennya null sebelum dikirim
     */
    public function massSend($data)
    {
        // Filter hanya recipients yang punya token valid
        $data['recipients'] = array_values(array_filter(
            $data['recipients'],
            fn($r) => !empty($r['token'])
        ));

        // Jika tidak ada recipient valid, log warning dan berhenti
        if (empty($data['recipients'])) {
            Log::warning('massSend: Tidak ada recipient dengan token valid, broadcast dibatalkan.');
            return null;
        }

        Log::info('massSend: Mengirim broadcast ke ' . count($data['recipients']) . ' perangkat.');

        return $this->post('broadcast/send', $data);
    }

    // 12. Notifikasi Check-in Berhasil
    public function sendCheckinSuccess($fcmToken, $userId, $roomNumber)
    {
        return $this->post('hotel/checkin-success', [
            'fcm_token'   => $fcmToken,
            'user_id'     => $userId,
            'room_number' => $roomNumber,
        ]);
    }

    // 13. Notifikasi Check-out Berhasil
    public function sendCheckoutSuccess($fcmToken, $userId, $reservationId)
    {
        return $this->post('hotel/checkout-success', [
            'fcm_token'      => $fcmToken,
            'user_id'        => $userId,
            'reservation_id' => $reservationId,
        ]);
    }
}