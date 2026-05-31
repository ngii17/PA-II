<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthDashboardController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PenggunaController;
use App\Http\Controllers\Dashboard\PembayaranController;
use App\Http\Controllers\Dashboard\LaporanController;
use App\Http\Controllers\Dashboard\PromoController;
use App\Http\Controllers\Dashboard\UlasanController;
use App\Http\Controllers\Dashboard\Hotel\KamarController;
use App\Http\Controllers\Dashboard\Hotel\TipeKamarController;
use App\Http\Controllers\Dashboard\Hotel\ReservasiHotelController;
use App\Http\Controllers\Dashboard\Hotel\PembayaranHotelController;
use App\Http\Controllers\Dashboard\Hotel\UlasanHotelController;
use App\Http\Controllers\Dashboard\Restoran\MenuController;
use App\Http\Controllers\Dashboard\Restoran\KategoriMenuController;
use App\Http\Controllers\Dashboard\Restoran\PesananController;
use App\Http\Controllers\Dashboard\Restoran\StokMenuController;
use App\Http\Controllers\Dashboard\Restoran\EventRestoranController;
use App\Http\Controllers\Dashboard\Restoran\MenuEventController;
use App\Http\Controllers\Dashboard\Restoran\PembayaranRestoController;
use App\Http\Controllers\Dashboard\Restoran\UlasanRestoranController;

/*
|--------------------------------------------------------------------------
| Web Routes - Dashboard Port 8001
|--------------------------------------------------------------------------
*/

Route::get('/', function () { return redirect('/dashboard'); });

// RUTE AUTH DASHBOARD
Route::get('/dashboard/login', [AuthDashboardController::class, 'showLogin'])->name('dashboard.login');
Route::post('/dashboard/login', [AuthDashboardController::class, 'login'])->name('dashboard.login.post');
Route::post('/dashboard/logout', [AuthDashboardController::class, 'logout'])->name('dashboard.logout');

// GRUP DASHBOARD (DILINDUNGI MIDDLEWARE)
Route::middleware(['dashboard'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    // ==========================================
    // BAGIAN ADMIN (LAPORAN, PROMO, PENGGUNA)
    // ==========================================
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('dashboard.pengguna');
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('dashboard.pembayaran');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('dashboard.laporan');
    Route::get('/laporan/export/excel/hotel', [LaporanController::class, 'exportExcelHotel'])->name('dashboard.laporan.excel.hotel');
    Route::get('/laporan/export/excel/restoran', [LaporanController::class, 'exportExcelRestoran'])->name('dashboard.laporan.excel.restoran');
    Route::get('/laporan/export/pdf/hotel', [LaporanController::class, 'exportPdfHotel'])->name('dashboard.laporan.pdf.hotel');
    Route::get('/laporan/export/pdf/restoran', [LaporanController::class, 'exportPdfRestoran'])->name('dashboard.laporan.pdf.restoran');
    Route::get('/ulasan', [UlasanController::class, 'index'])->name('dashboard.ulasan');
    Route::patch('/ulasan/{id}/toggle', [UlasanController::class, 'toggle'])->name('dashboard.ulasan.toggle');
    Route::resource('promo', PromoController::class)->names('dashboard.promo');

    // ==========================================
    // BAGIAN STAFF HOTEL
    // ==========================================
    Route::prefix('hotel')->group(function () {
        // --- RUTE BARU: AJAX UNTUK AMBIL KAMAR TERSEDIA ---
        // Digunakan agar saat ganti Tipe Kamar, daftar nomor kamar otomatis berubah (Filter)
        Route::get('/get-available-rooms/{tipe_id}', [ReservasiHotelController::class, 'getAvailableRooms'])->name('dashboard.hotel.reservasi.getRooms');

        Route::resource('tipe-kamar', TipeKamarController::class)->names('dashboard.hotel.tipe-kamar');
        Route::resource('kamar', KamarController::class)->names('dashboard.hotel.kamar');
        Route::resource('reservasi', ReservasiHotelController::class)->names('dashboard.hotel.reservasi');
        Route::get('/pembayaran', [PembayaranHotelController::class, 'index'])->name('dashboard.hotel.pembayaran');
        Route::get('/ulasan', [UlasanHotelController::class, 'index'])->name('dashboard.hotel.ulasan');
        Route::patch('/ulasan/{id}/toggle', [UlasanHotelController::class, 'toggle'])->name('dashboard.hotel.ulasan.toggle');
    });

    // ==========================================
    // BAGIAN STAFF RESTORAN
    // ==========================================
    Route::prefix('restoran')->group(function () {
        Route::resource('menu', MenuController::class)->names('dashboard.restoran.menu');
        Route::resource('kategori', KategoriMenuController::class)->names('dashboard.restoran.kategori');
        Route::resource('pesanan', PesananController::class)->names('dashboard.restoran.pesanan');
        Route::resource('menu-event', MenuEventController::class)->names('dashboard.restoran.menu-event');
        
        // Stok Menu Restoran
        Route::get('/stok', [StokMenuController::class, 'index'])->name('dashboard.restoran.stok');
        Route::get('/stok/{id}', [StokMenuController::class, 'show'])->name('dashboard.restoran.stok.show');
        Route::get('/stok/{id}/edit', [StokMenuController::class, 'edit'])->name('dashboard.restoran.stok.edit');
        Route::put('/stok/{id}', [StokMenuController::class, 'update'])->name('dashboard.restoran.stok.update');
        Route::delete('/stok/{id}', [StokMenuController::class, 'destroy'])->name('dashboard.restoran.stok.destroy');
        
        // Event Restoran
        Route::get('/event', [EventRestoranController::class, 'index'])->name('dashboard.restoran.event');
        Route::get('/event/{id}', [EventRestoranController::class, 'show'])->name('dashboard.restoran.event.show');
        Route::get('/event/{id}/edit', [EventRestoranController::class, 'edit'])->name('dashboard.restoran.event.edit');
        Route::put('/event/{id}', [EventRestoranController::class, 'update'])->name('dashboard.restoran.event.update');
        
        // Pembayaran & Ulasan Resto
        Route::get('/pembayaran', [PembayaranRestoController::class, 'index'])->name('dashboard.restoran.pembayaran');
        Route::get('/ulasan', [UlasanRestoranController::class, 'index'])->name('dashboard.restoran.ulasan');
        Route::patch('/ulasan/{id}/toggle', [UlasanRestoranController::class, 'toggle'])->name('dashboard.restoran.ulasan.toggle');
    });
});