<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'kategori_menu_id',
        'nama_menu',
        'deskripsi',
        'harga',
        'stok',
        'foto_menu',
        'status_menu_id',
        'promo_id'
    ];

    // --- TALI PENGHUBUNG 1: Ke Kategori ---
    public function kategori()
    {
        return $this->belongsTo(KategoriMenu::class, 'kategori_menu_id');
    }

    // --- TALI PENGHUBUNG 2: Ke Status ---
    public function status()
    {
        return $this->belongsTo(StatusMenu::class, 'status_menu_id');
    }
}