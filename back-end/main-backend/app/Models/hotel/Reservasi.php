<?php

namespace App\Models\hotel;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    // Nama tabel di database pgAdmin
    protected $table = 'reservasi';

    // Daftar kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'user_id',
        'tipe_kamar_id',
        'kamar_id',
        'fcm_token',           // <--- TAMBAHAN: Untuk menyimpan identitas HP User
        'tgl_checkin',
        'tgl_checkout',
        'total_malam',
        'total_harga',
        'metode_pembayaran',
        'status_reservasi_id',
        'snap_token',
    ];

    /**
     * RELASI: Satu reservasi memiliki banyak data tamu
     */
    public function details()
    {
        return $this->hasMany(DetailReservasi::class, 'reservasi_id');
    }

    /**
     * RELASI: Reservasi ini memesan tipe kamar tertentu
     */
    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }

    /**
     * RELASI: Nomor kamar fisik yang didapatkan (diisi setelah bayar)
     */
    public function kamarFisik()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}