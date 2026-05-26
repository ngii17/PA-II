<?php

namespace App\Models\hotel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservasi extends Model
{
    use SoftDeletes;

    protected $table = 'reservasi';

    // Daftar kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'user_id',
        'tipe_kamar_id',
        'kamar_id',
        'tgl_checkin',
        'tgl_checkout',
        'total_malam',
        'total_harga',
        'metode_pembayaran',
        'status_reservasi_id',
        'snap_token',
    ];

    /**
     * TAMBAHKAN RELASI INI (WAJIB)
     * Agar data Nama Tamu & NIK dari tabel detail_reservasi bisa terbaca
     */
    public function kamarFisik()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function statusReservasi()
    {
        return $this->belongsTo(StatusReservasi::class, 'status_reservasi_id');
    }

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
