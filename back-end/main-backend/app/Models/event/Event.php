<?php

namespace App\Models\event;

use Illuminate\Database\Eloquent\Model;
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

    // Kolom yang harus diperlakukan sebagai tanggal oleh Laravel
    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * RELASI: Satu Event bisa memiliki banyak Menu Spesial
     * Menggunakan tabel jembatan: event_menu
     */
    public function menus()
    {
        // 3. Tambahkan withPivot agar kamu bisa memanggil 'harga_khusus'
        // yang ada di tabel jembatan (pivot)
        return $this->belongsToMany(\App\Models\restoran\Menu::class, 'event_menu', 'event_id', 'menu_id')
                    ->withPivot('harga_khusus', 'is_active')
                    ->withTimestamps();
    }
}
