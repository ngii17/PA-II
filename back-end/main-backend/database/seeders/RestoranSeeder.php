<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Restoran\StatusMenu;
use App\Models\Restoran\StatusPesanan;
use App\Models\Restoran\KategoriMenu;
use App\Models\Restoran\Menu;
use App\Models\Hotel\Promo;

class RestoranSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama dengan TRUNCATE CASCADE (Reset ID ke 1)
        DB::statement('TRUNCATE TABLE ulasan_restoran RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE detail_pesanan RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE pesanan_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE event_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE kategori_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE status_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE status_pesanan RESTART IDENTITY CASCADE');

        // 2. Isi Status Menu (Ketersediaan Stok)
        $tersedia = StatusMenu::create(['id' => 1, 'nama_status' => 'Tersedia']);
        $habis    = StatusMenu::create(['id' => 2, 'nama_status' => 'Habis']);

        // 3. Isi Status Pesanan (Alur Dapur)
        StatusPesanan::create(['id' => 1, 'nama_status' => 'MENUNGGU']);
        StatusPesanan::create(['id' => 2, 'nama_status' => 'DIMASAK']);
        StatusPesanan::create(['id' => 3, 'nama_status' => 'DISAJIKAN']);
        StatusPesanan::create(['id' => 4, 'nama_status' => 'SELESAI']);

        // 4. Isi 5 Kategori Menu
        $cat1 = KategoriMenu::create(['nama_kategori' => 'Makanan Berat']);
        $cat2 = KategoriMenu::create(['nama_kategori' => 'Minuman Segar']);
        $cat3 = KategoriMenu::create(['nama_kategori' => 'Snack & Cemilan']);
        $cat4 = KategoriMenu::create(['nama_kategori' => 'Dessert Manis']);
        $cat5 = KategoriMenu::create(['nama_kategori' => 'Paket Hemat']);

        // 5. Daftar 25 Menu (5 per Kategori)
        $menuData = [
            // Kategori 1: Makanan Berat
            ['cat' => $cat1->id, 'name' => 'Nasi Goreng Spesial Purnama', 'price' => 35000, 'img' => 'https://images.unsplash.com/photo-1512058560366-cd2427ffaa64'],
            ['cat' => $cat1->id, 'name' => 'Ayam Bakar Madu Pedas', 'price' => 45000, 'img' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b'],
            ['cat' => $cat1->id, 'name' => 'Iga Bakar Konro', 'price' => 85000, 'img' => 'https://images.unsplash.com/photo-1544025162-d76694265947'],
            ['cat' => $cat1->id, 'name' => 'Sate Ayam Madura (10 Tusuk)', 'price' => 30000, 'img' => 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba'],
            ['cat' => $cat1->id, 'name' => 'Mie Goreng Seafood Premium', 'price' => 38000, 'img' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246'],

            // Kategori 2: Minuman Segar
            ['cat' => $cat2->id, 'name' => 'Es Teh Manis Kristal', 'price' => 8000, 'img' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc'],
            ['cat' => $cat2->id, 'name' => 'Jus Jeruk Peras Murni', 'price' => 15000, 'img' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423'],
            ['cat' => $cat2->id, 'name' => 'Kopi Susu Gula Aren', 'price' => 18000, 'img' => 'https://images.unsplash.com/photo-1559496417-e7f25cb247f3'],
            ['cat' => $cat2->id, 'name' => 'Es Kelapa Muda Batok', 'price' => 20000, 'img' => 'https://images.unsplash.com/photo-1553177595-4de2bb0842b9'],
            ['cat' => $cat2->id, 'name' => 'Avocado Float Creamy', 'price' => 25000, 'img' => 'https://images.unsplash.com/photo-1541658016709-82535e94bc71'],

            // Kategori 3: Snack & Cemilan
            ['cat' => $cat3->id, 'name' => 'Kentang Goreng Truffle', 'price' => 22000, 'img' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877'],
            ['cat' => $cat3->id, 'name' => 'Tempe Mendoan Panas (5 Pcs)', 'price' => 15000, 'img' => 'https://images.unsplash.com/photo-1605333396915-47ed6b68a00e'],
            ['cat' => $cat3->id, 'name' => 'Cireng Bumbu Rujak', 'price' => 18000, 'img' => 'https://images.unsplash.com/photo-1626132647523-66f5bf380027'],
            ['cat' => $cat3->id, 'name' => 'Otak-Otak Bakar Ikan Tenggiri', 'price' => 25000, 'img' => 'https://images.unsplash.com/photo-1624300627236-407133763f91'],
            ['cat' => $cat3->id, 'name' => 'Bakwan Jagung Manis Krispi', 'price' => 12000, 'img' => 'https://images.unsplash.com/photo-1621841957884-1210fe19d66d'],

            // Kategori 4: Dessert Manis
            ['cat' => $cat4->id, 'name' => 'Pisang Goreng Keju Cokelat', 'price' => 20000, 'img' => 'https://images.unsplash.com/photo-1621841957884-1210fe19d66d'],
            ['cat' => $cat4->id, 'name' => 'Puding Karamel Lembut', 'price' => 18000, 'img' => 'https://images.unsplash.com/photo-1541783245831-57d6fb0926d3'],
            ['cat' => $cat4->id, 'name' => 'Gelato Vanilla 2 Scoop', 'price' => 25000, 'img' => 'https://images.unsplash.com/photo-1501443762994-82bd5dabb892'],
            ['cat' => $cat4->id, 'name' => 'Fruit Salad Honey Yoghurt', 'price' => 28000, 'img' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd'],
            ['cat' => $cat4->id, 'name' => 'Molten Lava Cake', 'price' => 35000, 'img' => 'https://images.unsplash.com/photo-1624353365286-3f8d6263f7a9'],

            // Kategori 5: Paket Hemat
            ['cat' => $cat5->id, 'name' => 'Paket Nasi Ayam + Es Teh', 'price' => 40000, 'img' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7bb7445'],
            ['cat' => $cat5->id, 'name' => 'Paket Ikan Bakar Lengkap', 'price' => 55000, 'img' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2'],
            ['cat' => $cat5->id, 'name' => 'Paket Family (4 Orang)', 'price' => 185000, 'img' => 'https://images.unsplash.com/photo-1547573854-74d2a71d0826'],
            ['cat' => $cat5->id, 'name' => 'Paket Brunch (Nasi + Kopi)', 'price' => 45000, 'img' => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666'],
            ['cat' => $cat5->id, 'name' => 'Paket Snack Berdua', 'price' => 30000, 'img' => 'https://images.unsplash.com/photo-1603333396915-47ed6b68a00e'],
        ];

        foreach ($menuData as $item) {
            Menu::create([
                'kategori_menu_id' => $item['cat'],
                'nama_menu'       => $item['name'],
                'deskripsi'       => 'Menu spesial olahan Chef Purnama. Dibuat dengan bahan segar berkualitas.',
                'harga'           => $item['price'],
                'stok'            => 20, // Stok masing-masing 20 porsi
                'foto_menu'       => $item['img'] . '?auto=format&fit=crop&q=80&w=1000',
                'status_menu_id'  => $tersedia->id,
            ]);
        }

        // 6. Buat Promo Restoran Baru (Otomatis Aktif & Tanpa Kode agar Muncul Pop-up)
        Promo::create([
            'nama_promo'       => 'Promo Makan Kenyang',
            'kode_promo'       => null,
            'kategori'         => 'restoran',
            'tipe_diskon'      => 'persen',
            'nominal_potongan' => 10,
            'tgl_mulai'        => now(),
            'tgl_selesai'      => now()->addDays(7),
            'is_active'        => true,
        ]);

        $this->command->info('RestoranSeeder: 5 Kategori, 25 Menu, dan 1 Promo Berhasil Dibuat!');
    }
}