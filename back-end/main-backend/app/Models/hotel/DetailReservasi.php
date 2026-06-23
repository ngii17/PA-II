<?php
namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class DetailReservasi extends Model
{
    protected $table = 'detail_reservasi';

    protected $fillable = [
        'reservasi_id',
        'nama_tamu',
        'nik_identitas',
        'jumlah_tamu',
    ];

    protected $casts = [
        'nik_identitas' => 'encrypted',
    ];
}