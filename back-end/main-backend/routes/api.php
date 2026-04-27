<?php

use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RestoranController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UlasanController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PromoController;
/*
|--------------------------------------------------------------------------
| API Routes - Port 8001
|--------------------------------------------------------------------------
*/

// 1. Ambil Tipe Kamar & Promo
Route::get('/room-types', [HotelController::class, 'getRoomTypes']);

// 2. Simpan Reservasi Baru
Route::post('/reservasi', [HotelController::class, 'storeReservation']);

// 3. Ambil Riwayat Reservasi Berdasarkan User ID
Route::get('/reservasi/history', [HotelController::class, 'getReservationHistory']);

// 4. Pintu Masuk Laporan Midtrans (Webhook)
Route::post('/midtrans-callback', [HotelController::class, 'handleCallback']);

Route::get('/reservasi/check-status/{id}', [HotelController::class, 'checkStatus']);

// Route khusus Restoran
Route::get('/menus', [RestoranController::class, 'getMenus']);
Route::get('/menus/categories', [RestoranController::class, 'getCategories']);

// Route untuk mengirim pesanan makanan dari Flutter
Route::post('/menus/order', [RestoranController::class, 'placeOrder']);

// Route Callback Otomatis untuk Restoran
Route::post('/midtrans/callback/resto', [RestoranController::class, 'handleRestoCallback']);

// Route untuk cek status pesanan restoran secara berkala
Route::get('/menus/order/status/{id}', [RestoranController::class, 'checkOrderStatus']);

// Route untuk riwayat pesanan restoran
Route::get('/menus/order/history', [RestoranController::class, 'getOrderHistory']);


// Rute untuk kirim ulasan
Route::post('/review/hotel', [UlasanController::class, 'storeHotelReview']);
Route::post('/review/restoran', [UlasanController::class, 'storeRestoReview']);

// Rute untuk melihat ulasan (Public/Umum) - Syarat 3 & 4
Route::get('/review/hotel/{tipe_kamar_id}', [UlasanController::class, 'getHotelReviews']);
Route::get('/review/restoran/{menu_id}', [UlasanController::class, 'getRestoReviews']);


// Rute untuk cek tema aplikasi (Langkah 2.3)
Route::get('/active-event', [EventController::class, 'getActiveEvent']);


// Rute cek kode promo manual
Route::post('/promo/check', [PromoController::class, 'checkPromo']);