<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JANGAN ADA $this->call(RoleSeeder::class) DI SINI!
        
        Role::create(['name' => 'admin']);    // ID 1
        Role::create(['name' => 'customer']); // ID 2
        Role::create(['name' => 'staff']);    // ID 3
    }
}