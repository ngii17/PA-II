<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import SoftDeletes

class KategoriMenu extends Model
{
    use SoftDeletes; // 2. Gunakan Trait SoftDeletes

    protected $table = 'kategori_menu';

    // 3. Tambahkan 'deskripsi' ke dalam fillable agar bisa disimpan ke database
    protected $fillable = [
        'nama_kategori',
        'deskripsi'
    ];

    protected $dates = ['deleted_at'];

    // Relasi One-to-Many ke Tabel Menu
    public function menus()
    {
        return $this->hasMany(Menu::class, 'kategori_menu_id');
    }
}
