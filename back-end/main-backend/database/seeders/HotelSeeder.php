<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\hotel\StatusKamar;
use App\Models\hotel\TipeKamar;
use App\Models\hotel\StatusReservasi;
use App\Models\hotel\Promo;
use App\Models\hotel\Kamar;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama dengan TRUNCATE CASCADE (Khusus PostgreSQL agar ID reset ke 1)
        DB::statement('TRUNCATE TABLE ulasan_hotel RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE detail_reservasi RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE reservasi RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE kamar RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE tipe_kamar RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE status_kamar RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE status_reservasi RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE promo RESTART IDENTITY CASCADE');

        // 2. Isi Status Kamar Fisik
        StatusKamar::create(['id' => 1, 'nama_status' => 'Tersedia']);   // Ready untuk tamu
        StatusKamar::create(['id' => 2, 'nama_status' => 'Terisi']);     // Sedang dihuni
        StatusKamar::create(['id' => 3, 'nama_status' => 'Maintenance']); // Perbaikan/Pembersihan

        // 3. Isi Status Reservasi (SINKRON DENGAN LOGIKA FLUTTER 1-5)
        StatusReservasi::create(['id' => 1, 'nama_status' => 'Pending']);   // Menunggu Bayar
        StatusReservasi::create(['id' => 2, 'nama_status' => 'Terbayar']);  // Lunas tapi belum masuk
        StatusReservasi::create(['id' => 3, 'nama_status' => 'Check-in']);  // Tamu di dalam kamar
        StatusReservasi::create(['id' => 4, 'nama_status' => 'Selesai']);   // Sudah pulang (Check-out)
        StatusReservasi::create(['id' => 5, 'nama_status' => 'Batal']);     // Dibatalkan

        // 4. Isi 5 Tipe Kamar (Realistis)
        $roomTypes = [
            [
                'nama_tipe' => 'Standard Room',
                'harga' => 350000,
                'kapasitas' => 2,
                'fasilitas' => 'Single Bed, AC, TV, WiFi, Shower',
                'deskripsi' => 'Pilihan ekonomis untuk kenyamanan istirahat Anda.'
            ],
            [
                'nama_tipe' => 'Superior Room',
                'harga' => 550000,
                'kapasitas' => 2,
                'fasilitas' => 'Queen Bed, AC, Smart TV, WiFi, Coffee Maker, Shower',
                'deskripsi' => 'Kamar dengan ruang lebih luas dan fasilitas lengkap.'
            ],
            [
                'nama_tipe' => 'Deluxe Room',
                'harga' => 850000,
                'kapasitas' => 2,
                'fasilitas' => 'King Bed, AC, Smart TV, WiFi, Mini Bar, Bathtub',
                'deskripsi' => 'Kemewahan ekstra dengan pemandangan kota yang indah.'
            ],
            [
                'nama_tipe' => 'Junior Suite',
                'harga' => 1500000,
                'kapasitas' => 3,
                'fasilitas' => 'King Bed + Sofa Bed, Kitchenette, Mini Bar, Bathtub, Living Area',
                'deskripsi' => 'Sangat cocok untuk keluarga kecil yang menginginkan privasi lebih.'
            ],
            [
                'nama_tipe' => 'Presidential Suite',
                'harga' => 3500000,
                'kapasitas' => 4,
                'fasilitas' => '2 Super King Bed, Private Balcony, Jacuzzi, Dining Room, Personal Butler',
                'deskripsi' => 'Layanan kasta tertinggi dengan fasilitas super mewah kelas dunia.'
            ],
        ];

        foreach ($roomTypes as $index => $type) {
            $createdType = TipeKamar::create($type);

            // 5. ISI STOK KAMAR FISIK (5 Unit per Tipe)
            // Lantai 1 untuk Standard (101-105), Lantai 2 untuk Superior (201-205), dst.
            $floor = $index + 1;
            for ($i = 1; $i <= 5; $i++) {
                Kamar::create([
                    'nomor_kamar' => $floor . '0' . $i,
                    'tipe_kamar_id' => $createdType->id,
                    'status_kamar_id' => 1 // Semua mulai dari status Tersedia
                ]);
            }
        }

        // 6. Isi Promo (Otomatis Aktif & Sinkron dengan is_active)
        Promo::create([
            'nama_promo' => 'Diskon Lebaran Purnama',
            'kode_promo' => null, // null agar jadi pop-up otomatis
            'kategori' => 'hotel',
            'tipe_diskon' => 'persen',
            'nominal_potongan' => 15,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addDays(14),
            'is_active' => true // Fitur saklar yang kita buat tadi
        ]);

        $this->command->info('HotelSeeder: 5 Tipe Kamar & 25 Unit Kamar Fisik Berhasil Dibuat!');
    }
}