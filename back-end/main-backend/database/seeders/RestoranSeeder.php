<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\restoran\StatusMenu;
use App\Models\restoran\StatusPesanan;
use App\Models\restoran\KategoriMenu;
use App\Models\restoran\Menu;

class RestoranSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Status
        $tersedia = StatusMenu::create(['nama_status' => 'Tersedia']);
        $habis = StatusMenu::create(['nama_status' => 'Habis']);

        StatusPesanan::create(['nama_status' => 'Menunggu']);
        StatusPesanan::create(['nama_status' => 'Dimasak']);
        StatusPesanan::create(['nama_status' => 'Disajikan']);
        StatusPesanan::create(['nama_status' => 'Selesai']);

        // 2. Buat Kategori
        $makanan = KategoriMenu::create(['nama_kategori' => 'Makanan']);
        $minuman = KategoriMenu::create(['nama_kategori' => 'Minuman']);

        // ... (bagian status dan kategori tetap sama)

        // 3. Isi Daftar Menu dengan stok masing-masing 2
        Menu::create([
            'kategori_menu_id' => $makanan->id,
            'nama_menu' => 'Nasi Goreng Spesial Purnama',
            'deskripsi' => 'Nasi goreng dengan topping ayam, telur, udang, dan kerupuk.',
            'harga' => 35000,
            'stok' => 2, // <--- SET STOK JADI 2
            'foto_menu' => 'https://images.unsplash.com/photo-1512058560366-cd2427ffaa64?q=80&w=1000&auto=format&fit=crop',
            'status_menu_id' => $tersedia->id,
        ]);

        Menu::create([
            'kategori_menu_id' => $makanan->id,
            'nama_menu' => 'Ayam Bakar Madu',
            'deskripsi' => 'Ayam bakar dengan bumbu madu rahasia.',
            'harga' => 45000,
            'stok' => 2, // <--- SET STOK JADI 2
            'foto_menu' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?q=80&w=1000&auto=format&fit=crop',
            'status_menu_id' => $tersedia->id,
        ]);

        Menu::create([
            'kategori_menu_id' => $minuman->id,
            'nama_menu' => 'Es Teh Manis Kristal',
            'deskripsi' => 'Teh segar dengan es batu kristal.',
            'harga' => 8000,
            'stok' => 2, // <--- SET STOK JADI 2
            'foto_menu' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=1000&auto=format&fit=crop',
            'status_menu_id' => $tersedia->id,
        ]);
        
        }
}