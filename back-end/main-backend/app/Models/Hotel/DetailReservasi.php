<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class DetailReservasi extends Model
{
    // 1. Kasih tahu nama tabelnya
    protected $table = 'detail_reservasi';

    // 2. Daftar kolom yang boleh diisi
    protected $fillable = [
        'reservasi_id',
        'nama_tamu',
        'nik_identitas',
        'jumlah_tamu',
    ];
}