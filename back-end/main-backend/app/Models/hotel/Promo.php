<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';    
protected $fillable = [
    'nama_promo',
    'kode_promo',
    'kategori',
    'tipe_diskon',
    'nominal_potongan',
    'tgl_mulai',
    'tgl_selesai',
    'is_active', // <--- Tambahkan ini
];
}
