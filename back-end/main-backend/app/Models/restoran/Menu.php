<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;
// Import Model Event agar bisa dikenali
use App\Models\event\Event;

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

    public function kategori()
    {
        return $this->belongsTo(KategoriMenu::class, 'kategori_menu_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusMenu::class, 'status_menu_id');
    }

    /**
     * RELASI: Satu Menu bisa terdaftar di banyak Event
     */
public function events()
{
    // Kita ubah 'event_restoran_id' menjadi 'event_id'
    return $this->belongsToMany(\App\Models\event\Event::class, 'event_menu', 'menu_id', 'event_id');
}
}