<?php

namespace App\Models\event;

use Illuminate\Database\Eloquent\Model;
// Import Model Menu agar bisa dikenali
use App\Models\restoran\Menu;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'nama_event',
        'event_code',
        'primary_color',
        'secondary_color',
        'header_image',
        'background_image',
        'decoration_image',
        'is_active',
        'deskripsi',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * RELASI: Satu Event bisa memiliki banyak Menu Spesial
     * Menggunakan tabel jembatan: event_menu
     */
    public function menus()
{
    // Kita ubah 'event_restoran_id' menjadi 'event_id' sesuai migrasi terbaru
    return $this->belongsToMany(\App\Models\restoran\Menu::class, 'event_menu', 'event_id', 'menu_id');
}
}