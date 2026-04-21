<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class StatusMenu extends Model
{
    // Kasih tahu Laravel nama tabel aslinya
    protected $table = 'status_menu';

    protected $fillable = ['nama_status'];

    // Relasi: Satu status bisa dimiliki banyak menu
public function menus() {
    return $this->hasMany(Menu::class, 'status_menu_id');
}
}