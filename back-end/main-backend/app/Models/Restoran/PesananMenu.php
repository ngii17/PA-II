<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesananMenu extends Model
{
    use SoftDeletes;

    protected $table = 'pesanan_menu';

protected $fillable = [
    'user_id',
    'fcm_token',
    'total_harga',
    'metode_pembayaran',
    'snap_token',
    'status_pembayaran_id',
    'status_pesanan_id', // <--- TAMBAHKAN INI
    'nomor_lokasi',
    'tipe_pengantaran',
];

    /**
     * RELASI: Rincian item makanan yang dibeli
     */
    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_menu_id');
    }

    /**
     * RELASI: Menghubungkan ke tabel Status Pembayaran (Lunas/Pending)
     * INI YANG MENYEBABKAN ERROR TADI (PASTIKAN ADA)
     */
    public function statusPembayaran()
    {
        return $this->belongsTo(StatusPembayaran::class, 'status_pembayaran_id');
    }

    /**
     * RELASI: Menghubungkan ke tabel Status Pesanan (Proses/Selesai)
     */
    public function statusPesanan()
    {
        return $this->belongsTo(StatusPesanan::class, 'status_pesanan_id');
    }
}
