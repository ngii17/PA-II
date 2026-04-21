<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class KategoriMenu extends Model
{
    protected $table = 'kategori_menu';
    protected $fillable = ['nama_kategori'];

    // Relasi: Satu kategori punya banyak menu
    public function menus()
    {
        return $this->hasMany(Menu::class, 'kategori_menu_id');
    }
}