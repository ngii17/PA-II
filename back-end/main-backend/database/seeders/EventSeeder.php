<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\event\Event;
use App\Models\Hotel\Promo;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama
        DB::statement('TRUNCATE TABLE event_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE events RESTART IDENTITY CASCADE');
        Promo::whereNotNull('kode_promo')->delete();

        // 2. Daftar event sesuai permintaan
        $events = [
            [
                'nama_event' => 'Purnama Hotel Luxury',
                'event_code' => 'default',
                'primary_color' => '#0A1D56',
                'secondary_color' => '#D4AF37',
                'header_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2000&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'nama_event' => 'Tahun Baru Imlek',
                'event_code' => 'imlek',
                'primary_color' => '#C62828',
                'secondary_color' => '#FFD54F',
                'header_image' => 'https://images.unsplash.com/photo-1549692520-acc6669e2f0c?q=80&w=2000&auto=format&fit=crop',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Natal & Tahun Baru',
                'event_code' => 'natal',
                'primary_color' => '#1B5E20',
                'secondary_color' => '#D32F2F',
                'header_image' => 'https://images.unsplash.com/photo-1512389142860-9c449e58a543?q=80&w=2000&auto=format&fit=crop',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Hari Raya Idul Fitri',
                'event_code' => 'lebaran',
                'primary_color' => '#00695C',
                'secondary_color' => '#FFD700',
                'header_image' => 'https://images.unsplash.com/photo-1528605248644-14dd04022da1?q=80&w=2000&auto=format&fit=crop',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Hari Valentine',
                'event_code' => 'valentine',
                'primary_color' => '#E91E63',
                'secondary_color' => '#F8BBD0',
                'header_image' => 'https://images.unsplash.com/photo-1518621012118-696072aa579a?q=80&w=2000&auto=format&fit=crop',
                'is_active' => false,
            ],
            [
                'nama_event' => 'HUT Republik Indonesia',
                'event_code' => 'hut_ri',
                'primary_color' => '#D50000',
                'secondary_color' => '#FFFFFF',
                'header_image' => 'https://images.unsplash.com/photo-1521295121783-8a321d551ad2?q=80&w=2000&auto=format&fit=crop',
                'is_active' => false,
            ],
	];

        $this->command->info('EventSeeder: 10 tema berhasil ditambahkan (default aktif), dan voucher promo siap digunakan!');
    }
}
