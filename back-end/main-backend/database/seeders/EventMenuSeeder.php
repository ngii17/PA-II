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
        // Kosongkan tabel jembatan agar tidak duplikat
        DB::table('event_menu')->truncate();

        // 1. Ambil data Menu dan Event dari database
        $nasiGoreng = Menu::where('nama_menu', 'like', '%Nasi Goreng%')->first();
        $jusAlpukat  = Menu::where('nama_menu', 'like', '%Jus Alpukat%')->first();
        
        $eventHutRi    = Event::where('event_code', 'hut_ri')->first();
        $eventValentine = Event::where('event_code', 'valentine')->first();

        // 2. Jodohkan Menu ke Event
        
        if ($eventHutRi && $nasiGoreng) {
            DB::table('event_menu')->insert([
                'event_id' => $eventHutRi->id, // Sesuaikan nama kolom jadi event_id
                'menu_id' => $nasiGoreng->id,
            ]);
        }

        if ($eventValentine && $jusAlpukat) {
            DB::table('event_menu')->insert([
                'event_id' => $eventValentine->id, // Sesuaikan nama kolom jadi event_id
                'menu_id' => $jusAlpukat->id,
            ]);
        }
    }
}