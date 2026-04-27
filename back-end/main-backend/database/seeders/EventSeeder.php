<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\event\Event;
use App\Models\hotel\Promo; // Import Model Promo untuk mengisi voucher manual
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama agar tidak terjadi duplikat ID
        Event::truncate();
        Promo::whereNotNull('kode_promo')->delete(); // Hapus hanya promo yang punya kode (voucher)

        // 2. DAFTAR 6 TEMA VISUAL (Sesuai list kamu)
        $events = [
            [
                'nama_event' => 'Tema Default',
                'event_code' => 'default',
                'primary_color' => '#448AFF',
                'secondary_color' => '#E3F2FD',
                'header_image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2000&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'nama_event' => 'Tahun Baru Imlek',
                'event_code' => 'imlek',
                'primary_color' => '#D32F2F',
                'secondary_color' => '#FFC107',
                'header_image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?q=80&w=2000&auto=format&fit=crop',
                'decoration_image' => 'https://example.com/lampion.png',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Natal & Tahun Baru',
                'event_code' => 'natal',
                'primary_color' => '#2E7D32',
                'secondary_color' => '#C62828',
                'header_image' => 'https://images.unsplash.com/photo-1543589077-47d81606c1bf?q=80&w=2000&auto=format&fit=crop',
                'decoration_image' => 'https://example.com/salju.png',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Hari Raya Idul Fitri',
                'event_code' => 'lebaran',
                'primary_color' => '#1B5E20',
                'secondary_color' => '#FDD835',
                'header_image' => 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?q=80&w=2000&auto=format&fit=crop',
                'decoration_image' => 'https://example.com/ketupat.png',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Hari Valentine',
                'event_code' => 'valentine',
                'primary_color' => '#EC407A',
                'secondary_color' => '#F48FB1',
                'header_image' => 'https://images.unsplash.com/photo-1518199266791-739949406b24?q=80&w=2000&auto=format&fit=crop',
                'decoration_image' => 'https://example.com/hati.png',
                'is_active' => false,
            ],
            [
                'nama_event' => 'HUT Republik Indonesia',
                'event_code' => 'hut_ri',
                'primary_color' => '#FF0000',
                'secondary_color' => '#FFFFFF',
                'header_image' => 'https://images.unsplash.com/photo-1593118247619-e2d6f056869e?q=80&w=2000&auto=format&fit=crop',
                'decoration_image' => 'https://example.com/bendera-kecil.png',
                'is_active' => false,
            ],
        ];

        foreach ($events as $item) {
            Event::create($item);
        }

        // 3. DAFTAR PROMO VOUCHER (Manual Input untuk Tes di Flutter)
        // Voucher khusus Restoran
        Promo::create([
            'nama_promo'       => 'Voucher Makan Hemat',
            'kode_promo'       => 'MAKANPUAS', 
            'kategori'         => 'restoran',
            'tipe_diskon'      => 'nominal',
            'nominal_potongan' => 10000, 
            'tgl_mulai'        => now(),
            'tgl_selesai'      => now()->addDays(30),
        ]);

        // Voucher khusus Hotel
        Promo::create([
            'nama_promo'       => 'Staycation Asik',
            'kode_promo'       => 'HOTELMURAH', 
            'kategori'         => 'hotel',
            'tipe_diskon'      => 'persen',
            'nominal_potongan' => 20, // Diskon 20%
            'tgl_mulai'        => now(),
            'tgl_selesai'      => now()->addDays(30),
        ]);
    }
}