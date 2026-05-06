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
    'fcm_token', // <--- Pastikan baris ini ada
    'total_harga',
    'metode_pembayaran',
    'snap_token',
    'status_pembayaran_id',
];

    /**
     * RELASI: Satu nota punya banyak rincian menu
     */
    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_menu_id');
    }
}