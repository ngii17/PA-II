<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\hotel\StatusKamar;
use App\Models\hotel\TipeKamar;
use App\Models\hotel\StatusReservasi;
use App\Models\hotel\Promo;
use App\Models\hotel\Kamar;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama agar tidak dobel saat di-seed ulang
        // PENTING: Urutan hapus harus dari tabel anak ke induk
        Kamar::truncate();
        TipeKamar::truncate();
        StatusKamar::truncate();
        StatusReservasi::truncate();
        Promo::truncate();

        // 2. Isi Status Kamar
        StatusKamar::create(['nama_status' => 'Tersedia']); // ID 1
        StatusKamar::create(['nama_status' => 'Terisi']);   // ID 2
        StatusKamar::create(['nama_status' => 'Perbaikan']); // ID 3

        // 3. Isi Status Reservasi
        StatusReservasi::create(['nama_status' => 'Pending']);    // ID 1
        StatusReservasi::create(['nama_status' => 'Terbayar']);   // ID 2
        StatusReservasi::create(['nama_status' => 'Selesai']);    // ID 3
        StatusReservasi::create(['nama_status' => 'Batal']);      // ID 4

        // 4. Isi Tipe Kamar
        $standard = TipeKamar::create([
            'nama_tipe' => 'Standard Room',
            'harga' => 300000,
            'kapasitas' => 2,
            'fasilitas' => 'Free Wifi, AC, TV, Shower',
            'deskripsi' => 'Kamar nyaman untuk kebutuhan menginap harian anda.'
        ]);

        $deluxe = TipeKamar::create([
            'nama_tipe' => 'Deluxe Room',
            'harga' => 600000,
            'kapasitas' => 2,
            'fasilitas' => 'Free Wifi, AC, TV, Bathtub, Mini Bar',
            'deskripsi' => 'Nikmati kemewahan dan pemandangan indah dari kamar Deluxe.'
        ]);

        // 5. ISI STOK KAMAR FISIK (Logika Inventaris)
        // Kita buat 5 kamar Standard (101-105)
        for ($i = 1; $i <= 5; $i++) {
            Kamar::create([
                'nomor_kamar' => '10' . $i,
                'tipe_kamar_id' => $standard->id,
                'status_kamar_id' => 1 // Status: Tersedia
            ]);
        }

        // Kita buat 5 kamar Deluxe (201-205)
        for ($i = 1; $i <= 5; $i++) {
            Kamar::create([
                'nomor_kamar' => '20' . $i,
                'tipe_kamar_id' => $deluxe->id,
                'status_kamar_id' => 1 // Status: Tersedia
            ]);
        }

        // 6. Isi Promo (Opsional)
        Promo::create([
            'nama_promo' => 'Promo Awal Tahun',
            'kode_promo' => null,
            'kategori' => 'hotel',
            'tipe_diskon' => 'persen',
            'nominal_potongan' => 10,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addDays(30),
        ]);
    }
}