<?php
namespace App\Models\restoran;
use Illuminate\Database\Eloquent\Model;

class UlasanRestoran extends Model {
    protected $table = 'ulasan_restoran';
    protected $fillable = ['user_id', 'menu_id', 'rating', 'komentar'];

    // Relasi: Ulasan ini milik menu apa?
    public function menu() {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}