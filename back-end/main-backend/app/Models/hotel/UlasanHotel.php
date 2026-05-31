<?php

namespace App\Models\hotel;

use Illuminate\Database\Eloquent\Model;

class UlasanHotel extends Model
{
    protected $table = 'ulasan_hotel';

protected $fillable = [
    'user_id',
    'tipe_kamar_id',
    'reservasi_id', // <--- WAJIB ADA
    'rating',
    'komentar',
    'is_anonymous',
    'is_hidden',
];

    /**
     * RELASI: Ulasan ini merujuk pada Tipe Kamar apa?
     */
    public function tipeKamar()
    {
        // Pastikan model TipeKamar.php sudah ada di folder yang sama
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }
}
