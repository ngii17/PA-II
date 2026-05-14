<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Alamat Pintu Pendaftaran (Register)
Route::post('/register', [AuthController::class, 'register']);

// 2. Alamat Pintu Verifikasi OTP
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// 3. Alamat Pintu Masuk (Login)
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::get('/internal/user-tokens', [AuthController::class, 'getAllUserTokens']);


// Route yang WAJIB LOGIN (Dijaga Satpam Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Alamat untuk ambil data profil (Langkah 2 Profil)
    Route::get('/user/profile', [AuthController::class, 'profile']);
    
    // TAMBAHKAN INI (Gunakan POST karena kita akan kirim file/gambar)
    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    Route::delete('/user/delete-photo', [AuthController::class, 'deletePhoto']);

    // 2. Alamat untuk logout (Yang sudah kita buat kemarin)
    Route::post('/logout', [AuthController::class, 'logout']);
    
});