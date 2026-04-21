<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil HotelSeeder agar data Kamar, Status, dan Promo masuk ke database
        $this->call([
            HotelSeeder::class,
            RestoranSeeder::class,
        ]);
    }
}