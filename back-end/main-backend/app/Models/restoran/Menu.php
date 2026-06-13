<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;
// Import Model Event agar bisa dikenali
use App\Models\event\Event;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

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

// app/Models/restoran/Menu.php

// app/Models/restoran/Menu.php

// app/Models/restoran/Menu.php

// public function getFotoMenuAttribute($value)
// {
//     if (!$value) return asset('assets/img/no-image.png');

//     // 1. Jika ini link internet (Seeder), langsung kembalikan
//     if (filter_var($value, FILTER_VALIDATE_URL)) {
//         return $value;
//     }

//     // 2. Jika ini hasil upload staff (Lokal), buang sampah URL jika ada
//     // Kita hanya ambil nama filenya (misal: menu/piscok.jpg)
//     $cleanPath = $value;
//     if (str_contains($value, 'storage/')) {
//         $cleanPath = explode('storage/', $value)[1];
//     }

//     // 3. Gabungkan dengan URL sakti (mengikuti APP_URL di .env)
//     return url('storage/' . $cleanPath);
// }
}
