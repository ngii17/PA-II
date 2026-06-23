<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class EncryptNikLama extends Command
{
    protected $signature   = 'nik:encrypt-lama';
    protected $description = 'Enkripsi semua NIK lama yang masih plain text di tabel detail_reservasi';

    public function handle()
    {
        $rows = DB::table('detail_reservasi')->get();
        $berhasil = 0;
        $gagal    = 0;

        foreach ($rows as $row) {
            try {
                // Cek apakah sudah terenkripsi (crypt Laravel diawali dengan "eyJ")
                Crypt::decryptString($row->nik_identitas);
                $this->line("ID {$row->id}: sudah terenkripsi, skip.");
            } catch (\Exception $e) {
                // Belum terenkripsi, enkripsi sekarang
                try {
                    DB::table('detail_reservasi')
                        ->where('id', $row->id)
                        ->update(['nik_identitas' => Crypt::encryptString($row->nik_identitas)]);
                    $this->info("ID {$row->id}: berhasil dienkripsi.");
                    $berhasil++;
                } catch (\Exception $e2) {
                    $this->error("ID {$row->id}: gagal — " . $e2->getMessage());
                    $gagal++;
                }
            }
        }

        $this->info("\nSelesai. Berhasil: {$berhasil}, Gagal: {$gagal}");
    }
}