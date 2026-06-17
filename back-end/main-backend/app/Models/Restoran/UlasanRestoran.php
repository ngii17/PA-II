<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class UlasanRestoran extends Model
{
    protected $table = 'ulasan_restoran';

protected $fillable = [
    'user_id',
    'menu_id',
    'pesanan_menu_id', // <--- WAJIB ADA
    'rating',
    'komentar',
    'is_anonymous',
    'is_hidden',
];

    /**
     * RELASI: Ulasan ini merujuk pada Menu apa?
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
