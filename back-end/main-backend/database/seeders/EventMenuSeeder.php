<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\restoran\Menu;
use App\Models\event\Event;
use Illuminate\Support\Facades\DB;

class EventMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan tabel jembatan agar tidak terjadi duplikat data
        // Restart identity agar ID kembali dari 1
        DB::statement('TRUNCATE TABLE event_menu RESTART IDENTITY CASCADE');

        // 2. Ambil Semua Event berdasarkan Code
        $eventHutRi    = Event::where('event_code', 'hut_ri')->first();
        $eventLebaran  = Event::where('event_code', 'lebaran')->first();
        $eventValentine = Event::where('event_code', 'valentine')->first();
        $eventImlek    = Event::where('event_code', 'imlek')->first();
        $eventNatal    = Event::where('event_code', 'natal')->first();

        // 3. Ambil Beberapa Menu secara spesifik untuk dijodohkan
        $nasiGoreng = Menu::where('nama_menu', 'like', '%Nasi Goreng%')->first();
        $ayamBakar  = Menu::where('nama_menu', 'like', '%Ayam Bakar%')->first();
        $sateAyam   = Menu::where('nama_menu', 'like', '%Sate Ayam%')->first();
        $esTeh      = Menu::where('nama_menu', 'like', '%Es Teh%')->first();
        $jusJeruk   = Menu::where('nama_menu', 'like', '%Jus Jeruk%')->first();
        $avocado    = Menu::where('nama_menu', 'like', '%Avocado%')->first();
        $molten     = Menu::where('nama_menu', 'like', '%Molten Lava%')->first();
        $igaBakar   = Menu::where('nama_menu', 'like', '%Iga Bakar%')->first();

        // 4. Proses Input Relasi (Minimal 2-3 Menu per Event agar tidak kosong)

        // --- MENU UNTUK HUT RI (ID 6 - Merah Putih) ---
        if ($eventHutRi) {
            $this->linkMenu($eventHutRi->id, $nasiGoreng->id);
            $this->linkMenu($eventHutRi->id, $sateAyam->id);
            $this->linkMenu($eventHutRi->id, $jusJeruk->id);
        }

        // --- MENU UNTUK LEBARAN (ID 4 - Hijau Kuning) ---
        if ($eventLebaran) {
            $this->linkMenu($eventLebaran->id, $ayamBakar->id);
            $this->linkMenu($eventLebaran->id, $igaBakar->id);
            $this->linkMenu($eventLebaran->id, $esTeh->id);
        }

        // --- MENU UNTUK VALENTINE (ID 5 - Pink) ---
        if ($eventValentine) {
            $this->linkMenu($eventValentine->id, $molten->id);
            $this->linkMenu($eventValentine->id, $avocado->id);
        }

        // --- MENU UNTUK IMLEK (ID 2 - Merah Kuning) ---
        if ($eventImlek) {
            $this->linkMenu($eventImlek->id, $igaBakar->id);
            $this->linkMenu($eventImlek->id, $jusJeruk->id);
        }

        // --- MENU UNTUK NATAL (ID 3 - Hijau Merah) ---
        if ($eventNatal) {
            $this->linkMenu($eventNatal->id, $ayamBakar->id);
            $this->linkMenu($eventNatal->id, $molten->id);
        }

        $this->command->info('EventMenuSeeder: Seluruh Menu Spesial Berhasil Dijodohkan ke Event!');
    }

    /**
     * Helper untuk memasukkan data ke tabel pivot dengan atribut lengkap
     */
    private function linkMenu($eventId, $menuId)
    {
        if ($menuId && $eventId) {
            DB::table('event_menu')->insert([
                'event_id'      => $eventId,
                'menu_id'       => $menuId,
                'is_active'     => true, // Pastikan menu ini AKTIF di dalam event
                'harga_khusus'  => null, // Bisa diisi jika ada harga coret khusus event
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}