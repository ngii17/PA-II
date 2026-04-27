<?php

namespace App\Models\hotel; // <-- Pastikan ini ada 'hotel' nya

use Illuminate\Database\Eloquent\Model;

class UlasanHotel extends Model
{
    protected $table = 'ulasan_hotel';

    protected $fillable = [
        'user_id',
        'tipe_kamar_id',
        'rating',
        'komentar'
    ];
}