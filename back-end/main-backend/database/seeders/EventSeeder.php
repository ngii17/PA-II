<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\event\Event;
use App\Models\hotel\Promo;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama dengan TRUNCATE CASCADE agar ID reset ke 1
        // PENTING: Kita bersihkan tabel event_menu juga agar tidak ada relasi yatim piatu
        DB::statement('TRUNCATE TABLE event_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE events RESTART IDENTITY CASCADE');
        
        // Hapus hanya promo yang memiliki kode_promo (Voucher manual)
        // Kita biarkan promo otomatis (is_active saklar) yang dibuat di seeder sebelumnya
        Promo::whereNotNull('kode_promo')->delete();

        // 2. DAFTAR 6 TEMA VISUAL (Sinkron dengan Flutter UI)
        $events = [
            [
                'nama_event' => 'Tema Default',
                'event_code' => 'default',
                'primary_color' => '#448AFF', // Hijau Standar Purnama
                'secondary_color' => '#E3F2FD',
                'header_image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb',
                'is_active' => true, // Tema awal yang aktif
            ],
            [
                'nama_event' => 'Tahun Baru Imlek',
                'event_code' => 'imlek',
                'primary_color' => '#D32F2F', // Merah Imlek
                'secondary_color' => '#FFC107',
                'header_image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Natal & Tahun Baru',
                'event_code' => 'natal',
                'primary_color' => '#2E7D32', // Hijau Pinus
                'secondary_color' => '#C62828',
                'header_image' => 'https://images.unsplash.com/photo-1543589077-47d81606c1bf',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Hari Raya Idul Fitri',
                'event_code' => 'lebaran',
                'primary_color' => '#1B5E20', // Hijau Tua
                'secondary_color' => '#FDD835', // Kuning Emas
                'header_image' => 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f',
                'is_active' => false,
            ],
            [
                'nama_event' => 'Hari Valentine',
                'event_code' => 'valentine',
                'primary_color' => '#EC407A', // Pink
                'secondary_color' => '#F48FB1',
                'header_image' => 'https://images.unsplash.com/photo-1518199266791-739949406b24',
                'is_active' => false,
            ],
            [
                'nama_event' => 'HUT Republik Indonesia',
                'event_code' => 'hut_ri',
                'primary_color' => '#FF0000', // Merah Bendera
                'secondary_color' => '#FFFFFF', // Putih
                'header_image' => 'https://images.unsplash.com/photo-1593118247619-e2d6f056869e',
                'is_active' => false,
            ],
        ];

        foreach ($events as $item) {
            // Tambahkan formatting unsplash agar gambar pas di header HP
            $item['header_image'] .= '?q=80&w=2000&auto=format&fit=crop';
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
            'is_active'        => true, // Pastikan AKTIF agar bisa dipakai
        ]);

        // Voucher khusus Hotel
        Promo::create([
            'nama_promo'       => 'Staycation Asik',
            'kode_promo'       => 'HOTELMURAH', 
            'kategori'         => 'hotel',
            'tipe_diskon'      => 'persen',
            'nominal_potongan' => 20, 
            'tgl_mulai'        => now(),
            'tgl_selesai'      => now()->addDays(30),
            'is_active'        => true, // Pastikan AKTIF
        ]);

        $this->command->info('EventSeeder: 6 Tema Berhasil Dibuat & Voucher MAKANPUAS, HOTELMURAH siap digunakan!');
    }
}