<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\hotel\Reservasi;
use App\Services\NotificationClientService;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

// Perintah bawaan Laravel (Biarkan saja)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * 1. CRON JOB: CHECK-IN REMINDER (Poin 1 & 3)
 * Mencari tamu yang masuk BESOK, diingatkan jam 09:00 pagi hari ini.
 */
Schedule::call(function () {
    $besok = Carbon::tomorrow()->toDateString();
    
    // Ambil data reservasi yang sudah bayar (status 2) dan checkin besok
    $reservations = Reservasi::where('status_reservasi_id', 2)
        ->where('tgl_checkin', $besok)
        ->get();

    // Panggil Kurir Notifikasi (Microservice Port 8002)
    $notif = app(NotificationClientService::class);

    foreach ($reservations as $res) {
        $notif->sendCheckinReminder(
            'dummy_token_user', // Token HP User
            $res->id,
            '14:00' // Jam check-in standar
        );
    }
})->dailyAt('09:00');

/**
 * 2. CRON JOB: CHECK-OUT REMINDER (Poin 2 & 3)
 * Mencari tamu yang harus keluar HARI INI, diingatkan jam 08:00 pagi.
 */
Schedule::call(function () {
    $hariIni = Carbon::today()->toDateString();
    
    // Ambil data reservasi yang lunas dan checkout hari ini
    $reservations = Reservasi::where('status_reservasi_id', 2)
        ->where('tgl_checkout', $hariIni)
        ->get();

    $notif = app(NotificationClientService::class);

    foreach ($reservations as $res) {
        $notif->sendCheckoutReminder(
            'dummy_token_user', 
            $res->id
        );
    }
})->dailyAt('08:00');