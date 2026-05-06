<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Notification Service
|--------------------------------------------------------------------------
*/
// Route untuk mengambil inbox notifikasi user
// Taruh di luar atau dalam middleware service.auth (tapi untuk flutter, buat public saja dulu agar gampang)
Route::get('/inbox/{userId}', [NotificationController::class, 'getNotifications']);

// Membungkus semua route dalam middleware 'service.auth' (Checklist Poin 8)
Route::middleware(['service.auth'])->prefix('notify')->group(function () {

    // 1. Notifikasi Pesanan Dikonfirmasi (Poin 1)
    Route::post('/booking-confirmed', [NotificationController::class, 'bookingConfirmed']);

    // 2. Notifikasi Pesanan Dibatalkan (Poin 2)
    Route::post('/booking-cancelled', [NotificationController::class, 'bookingCancelled']);

    // 3. Pengingat Check-in (Poin 3)
    Route::post('/checkin-reminder', [NotificationController::class, 'checkinReminder']);

    // 4. Notifikasi Kamar Siap (Poin 4)
    Route::post('/room-ready', [NotificationController::class, 'roomReady']);

    // 5. Pengingat Check-out (Poin 5)
    Route::post('/checkout-reminder', [NotificationController::class, 'checkoutReminder']);

    // 6. Notifikasi Perubahan Pesanan (Poin 6)
    Route::post('/booking-modified', [NotificationController::class, 'bookingModified']);

    // 7. Notifikasi Pembayaran Gagal (Poin 7)
    Route::post('/payment-failed', [NotificationController::class, 'paymentFailed']);


// --- NOTIFIKASI RESTORAN (KITA LENGKAPI) ---
    // 1. Pesanan Dikonfirmasi (Uang masuk)
    Route::post('/order-confirmed', [NotificationController::class, 'orderConfirmed']);
    
    // 2. Pesanan Dibatalkan (Misal: stok habis mendadak)
    Route::post('/order-cancelled', [NotificationController::class, 'orderCancelled']);
    
    // 3. Pesanan Siap Disajikan / Diantar ke Kamar
    Route::post('/order-ready', [NotificationController::class, 'orderReady']);
    
    // 4. Pembayaran Gagal
    Route::post('/order-payment-failed', [NotificationController::class, 'orderPaymentFailed']);



});