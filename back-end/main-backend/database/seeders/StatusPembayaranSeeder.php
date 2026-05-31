<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPembayaranSeeder extends Seeder
{
    public function run()
    {
        // Gunakan truncate agar jika dijalankan ulang tidak duplikat
        // Restart identity khusus untuk PostgreSQL agar ID mulai dari 1
        DB::statement('TRUNCATE TABLE status_pembayaran RESTART IDENTITY CASCADE');

        DB::table('status_pembayaran')->insert([
            ['id' => 1, 'nama_status' => 'Menunggu Pembayaran', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_status' => 'Lunas', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_status' => 'Expired', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_status' => 'Gagal', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}