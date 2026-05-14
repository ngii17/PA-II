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
    'fcm_token',
    'total_harga',
    'metode_pembayaran',
    'snap_token',
    'status_pembayaran_id',
    'tipe_pengantaran', // <--- TAMBAHKAN INI (meja / kamar)
    'nomor_lokasi',    // <--- TAMBAHKAN INI (no meja / no kamar)
];

    /**
     * RELASI: Satu nota punya banyak rincian menu
     */
    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_menu_id');
    }
}