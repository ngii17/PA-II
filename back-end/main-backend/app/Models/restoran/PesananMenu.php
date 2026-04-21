<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class PesananMenu extends Model
{
    // Nama tabel di database
    protected $table = 'pesanan_menu';

    // Daftar kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'promo_id',
        'total_harga',
        'metode_pembayaran',
        'snap_token',           // <--- TAMBAHKAN INI
        'status_pembayaran_id', // <--- TAMBAHKAN INI
    ];

    /**
     * RELASI: Satu nota punya banyak rincian menu
     */
    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_menu_id');
    }
}