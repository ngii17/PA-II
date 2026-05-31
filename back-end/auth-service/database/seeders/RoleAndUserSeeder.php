<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RoleAndUserSeeder extends Seeder
{
    public function run()
    {
        // 1. Bersihkan tabel dengan TRUNCATE CASCADE agar tidak kena Foreign Key Violation
        // Restart Identity supaya ID balik lagi dari angka 1
        DB::statement('TRUNCATE TABLE users, roles RESTART IDENTITY CASCADE');

        // 2. Masukkan 4 Role Baru sesuai struktur kolom 'name' yang ada di migration kamu
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'customer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'staff hotel', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'staff restoran', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Buat Akun Admin Utama agar kamu bisa tembus login Dashboard Web
        User::create([
            'username'    => 'admin_utama',
            'full_name'   => 'Administrator Purnama',
            'email'       => 'admin@purnama.com',
            'password'    => Hash::make('password123'),
            'phone'       => '+6281234567890',
            'address'     => 'Kantor Pusat Purnama',
            'role_id'     => 1, // Hubungkan ke ID 1 (admin)
            'is_verified' => true,
        ]);
        
        // 4. Buat Akun Staff Hotel (UNTUK TESTING KAMU SEKARANG)
        User::create([
            'username'    => 'staff_hotel',
            'full_name'   => 'Budi Staff Hotel',
            'email'       => 'hotel@purnama.com',
            'password'    => Hash::make('password123'),
            'phone'       => '+628222222222', // Tambahkan nomor HP
            'address'     => 'Front Desk Purnama Hotel', // Tambahkan Alamat
            'role_id'     => 3,
            'is_verified' => true,
        ]);

        // 5. Buat Akun Staff Restoran
        User::create([
            'username'    => 'staff_resto',
            'full_name'   => 'Siti Staff Restoran',
            'email'       => 'resto@purnama.com',
            'password'    => Hash::make('password123'),
            'phone'       => '+628333333333', // Tambahkan nomor HP
            'address'     => 'Dapur Utama Purnama Resto', // Tambahkan Alamat
            'role_id'     => 4,
            'is_verified' => true,
        ]);

        $this->command->info('SUKSES: Database Sinkron! Kolom "name" terisi. Admin: admin@purnama.com | pass: password123');
    }
}