<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPesananSeeder extends Seeder
{
    public function run()
    {
        // PENTING: Tambahkan TRUNCATE agar data lama dihapus sebelum isi baru
        DB::statement('TRUNCATE TABLE status_pesanan RESTART IDENTITY CASCADE');

        DB::table('status_pesanan')->insert([
            ['id' => 1, 'nama_status' => 'MENUNGGU', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_status' => 'DIMASAK', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_status' => 'DISAJIKAN', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_status' => 'SELESAI', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}