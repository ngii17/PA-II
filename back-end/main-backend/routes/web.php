<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthDashboardController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PenggunaController;
use App\Http\Controllers\Dashboard\PembayaranController;
use App\Http\Controllers\Dashboard\LaporanController;
use App\Http\Controllers\Dashboard\PromoController;
use App\Http\Controllers\Dashboard\UlasanController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\Hotel\KamarController;
use App\Http\Controllers\Dashboard\Hotel\TipeKamarController;
use App\Http\Controllers\Dashboard\Hotel\ReservasiHotelController;
use App\Http\Controllers\Dashboard\Hotel\PembayaranHotelController;
use App\Http\Controllers\Dashboard\Hotel\UlasanHotelController;
use App\Http\Controllers\Dashboard\Restoran\MenuController;
use App\Http\Controllers\Dashboard\Restoran\KategoriMenuController;
use App\Http\Controllers\Dashboard\Restoran\PesananController;
use App\Http\Controllers\Dashboard\Restoran\StokMenuController;
use App\Http\Controllers\Dashboard\Restoran\MenuEventController;
use App\Http\Controllers\Dashboard\Restoran\PembayaranRestoController;
use App\Http\Controllers\Dashboard\Restoran\UlasanRestoranController;
use App\Http\Controllers\Dashboard\AdminBroadcastController;
use App\Http\Controllers\Dashboard\ProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes - Dashboard Port 8001
|--------------------------------------------------------------------------
*/

Route::get('/', function () { return redirect('/dashboard'); });

// --- AUTH DASHBOARD ---
Route::get('/dashboard/login', [AuthDashboardController::class, 'showLogin'])->name('dashboard.login');
Route::post('/dashboard/login', [AuthDashboardController::class, 'login'])->name('dashboard.login.post');
Route::post('/dashboard/logout', [AuthDashboardController::class, 'logout'])->name('dashboard.logout');

// --- GRUP DASHBOARD (DILINDUNGI MIDDLEWARE) ---
Route::middleware(['dashboard'])->prefix('dashboard')->group(function () {
    
    // Halaman Ringkasan Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    // ============================================================
    // 🛡️ KENDALI ADMIN (MANAGEMENT GLOBAL)
    // ============================================================
    
    // --- MANAJEMEN PENGGUNA (FIX: Tambah rute CRUD lengkap) ---
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('dashboard.pengguna');
    Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('dashboard.pengguna.create');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('dashboard.pengguna.store');
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy'])->name('dashboard.pengguna.destroy');

    // --- PEMBAYARAN GLOBAL ---
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('dashboard.pembayaran');
    
    // --- LAPORAN SISTEM ---
    Route::get('/laporan', [LaporanController::class, 'index'])->name('dashboard.laporan');
    Route::get('/laporan/export/excel/hotel', [LaporanController::class, 'exportExcelHotel'])->name('dashboard.laporan.excel.hotel');
    Route::get('/laporan/export/excel/restoran', [LaporanController::class, 'exportExcelRestoran'])->name('dashboard.laporan.excel.restoran');
    Route::get('/laporan/export/pdf/hotel', [LaporanController::class, 'exportPdfHotel'])->name('dashboard.laporan.pdf.hotel');
    Route::get('/laporan/export/pdf/restoran', [LaporanController::class, 'exportPdfRestoran'])->name('dashboard.laporan.pdf.restoran');
    
    // --- MODERASI ULASAN GLOBAL ---
    Route::get('/ulasan', [UlasanController::class, 'index'])->name('dashboard.ulasan');
    Route::patch('/ulasan/{tipe}/{id}/toggle', [UlasanController::class, 'toggle'])->name('dashboard.ulasan.toggle');           
    // --- MANAJEMEN PROMO ---
    Route::resource('promo', PromoController::class)->names('dashboard.promo');

    // --- MANAJEMEN TEMA (PINDAHAN DARI RESTORAN) ---
    Route::get('/event', [EventController::class, 'index'])->name('dashboard.event.index');
    Route::get('/event/{id}/edit', [EventController::class, 'edit'])->name('dashboard.event.edit');
    Route::put('/event/{id}', [EventController::class, 'update'])->name('dashboard.event.update');


    // ============================================================
    // 🏨 STAFF HOTEL (OPERASIONAL HOTEL)
    // ============================================================
    Route::prefix('hotel')->group(function () {

        // FIX: Parameter current_kamar_id ditambahkan sebagai opsional
        // agar kamar yang sedang dipakai reservasi tetap muncul di dropdown
        // meski status_kamar_id = 2 (Terisi)
        Route::get('/get-available-rooms/{tipe_id}/{current_kamar_id?}', [ReservasiHotelController::class, 'getAvailableRooms'])->name('dashboard.hotel.reservasi.getRooms');

        // TAMBAHKAN INI ↓
        Route::post('/reservasi/check-voucher', [ReservasiHotelController::class, 'checkVoucher'])
            ->name('dashboard.hotel.reservasi.checkVoucher');

        Route::resource('tipe-kamar', TipeKamarController::class)->names('dashboard.hotel.tipe-kamar');
        Route::resource('kamar', KamarController::class)->names('dashboard.hotel.kamar');
        Route::resource('reservasi', ReservasiHotelController::class)->names('dashboard.hotel.reservasi');
        
        Route::get('/pembayaran', [PembayaranHotelController::class, 'index'])->name('dashboard.hotel.pembayaran');
        
        Route::get('/ulasan', [UlasanHotelController::class, 'index'])->name('dashboard.hotel.ulasan');
        Route::patch('/ulasan/{id}/toggle', [UlasanController::class, 'toggle'])->name('dashboard.ulasan.hotel.toggle');

    });


    // ============================================================
    // 🍽️ STAFF RESTORAN (OPERASIONAL RESTORAN)
    // ============================================================
    Route::prefix('restoran')->group(function () {
        Route::resource('menu', MenuController::class)->names('dashboard.restoran.menu');
        Route::resource('kategori', KategoriMenuController::class)->names('dashboard.restoran.kategori');
        Route::resource('pesanan', PesananController::class)->names('dashboard.restoran.pesanan');
        Route::resource('menu-event', MenuEventController::class)->names('dashboard.restoran.menu-event');
        
        // STOK MENU
        Route::get('/stok', [StokMenuController::class, 'index'])->name('dashboard.restoran.stok');
        Route::get('/stok/{id}', [StokMenuController::class, 'show'])->name('dashboard.restoran.stok.show');
        Route::get('/stok/{id}/edit', [StokMenuController::class, 'edit'])->name('dashboard.restoran.stok.edit');
        Route::put('/stok/{id}', [StokMenuController::class, 'update'])->name('dashboard.restoran.stok.update');
        Route::delete('/stok/{id}', [StokMenuController::class, 'destroy'])->name('dashboard.restoran.stok.destroy');
        
        // PEMBAYARAN & ULASAN RESTO
        Route::get('/pembayaran', [PembayaranRestoController::class, 'index'])->name('dashboard.restoran.pembayaran');
        Route::get('/ulasan', [UlasanRestoranController::class, 'index'])->name('dashboard.restoran.ulasan');
        Route::patch('/ulasan/{tipe}/{id}/toggle', [UlasanController::class, 'toggle'])->name('dashboard.ulasan.restoran.toggle');
    });

    Route::prefix('admin')->group(function () {
    Route::get('/broadcast', [AdminBroadcastController::class, 'index'])->name('dashboard.admin.broadcast.index');
    Route::get('/broadcast/create', [AdminBroadcastController::class, 'create'])->name('dashboard.admin.broadcast.create');
    Route::post('/broadcast', [AdminBroadcastController::class, 'store'])->name('dashboard.admin.broadcast.store');
    Route::post('/broadcast/send/{id}', [AdminBroadcastController::class, 'send'])->name('dashboard.admin.broadcast.send');
    Route::delete('/broadcast/{id}', [AdminBroadcastController::class, 'destroy'])->name('dashboard.admin.broadcast.destroy');
    });

    // ============================================================
    // 👤 PROFIL PENGGUNA (SEMUA ROLE)
    // ============================================================
    Route::get('/profile', [ProfileController::class, 'show'])->name('dashboard.profile');
});