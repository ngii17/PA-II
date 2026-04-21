<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya panggil di sini, sekali saja!
        $this->call([
            RoleSeeder::class,
        ]);
    }
}