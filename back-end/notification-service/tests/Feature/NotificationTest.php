<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    // Kita gunakan data asli atau database testing
    protected $secretKey = "PurnamaSecretNotif123";

    /**
     * TEST 1: Memastikan akses DITOLAK jika tanpa header (Poin 3 Modul 8)
     */
    public function test_access_denied_without_secret_key(): void
    {
        $response = $this->postJson('/api/notify/booking-confirmed', [
            'fcm_token' => 'test_token',
            'booking_id' => 1,
            'room_type' => 'Deluxe',
            'checkin_date' => '2026-05-01'
        ]);

        // Harus membalas 401
        $response->assertStatus(401)
                 ->assertJson(['success' => false]);
    }

    /**
     * TEST 2: Memastikan akses DITERIMA jika header benar (Poin 2 Modul 8)
     */
    public function test_access_granted_with_correct_secret_key(): void
    {
        $response = $this->withHeaders([
            'X-Service-Secret' => $this->secretKey,
        ])->postJson('/api/notify/booking-confirmed', [
            'fcm_token' => 'test_token',
            'booking_id' => 1,
            'room_type' => 'Deluxe',
            'checkin_date' => '2026-05-01'
        ]);

        // Harus membalas 200 atau 500 (bukan 401 lagi)
        // Karena fcm_token palsu, biasanya 500 tapi itu artinya middleware sudah lolos
        $this->assertNotEquals(401, $response->getStatusCode());
    }
}