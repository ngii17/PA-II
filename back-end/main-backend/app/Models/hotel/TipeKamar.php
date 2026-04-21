<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class TipeKamar extends Model
{
    protected $table = 'tipe_kamar';
    protected $fillable = ['nama_tipe', 'harga', 'kapasitas', 'fasilitas', 'deskripsi'];
}
