<?php

namespace App\Models\hotel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotel\Promo;

class Reservasi extends Model
{
    use SoftDeletes;

    protected $table = 'reservasi';

    protected $fillable = [
        'user_id',
        'fcm_token',
        'tipe_kamar_id',
        'kamar_id',
        'tgl_checkin',
        'tgl_checkout',
        'total_malam',
        'total_harga',
        'metode_pembayaran',
        'status_reservasi_id',
        'snap_token',
        'deposit_amount',
        'confirmed_by',
        'confirmed_at',
        'promo_id',               // <-- tambahan
        'kode_voucher_digunakan', // <-- tambahan
        'nominal_diskon',         // <-- tambahan
    ];
    /**
     * TAMBAHKAN RELASI INI (WAJIB)
     * Agar data Nama Tamu & NIK dari tabel detail_reservasi bisa terbaca
     */
    public function details()
    {
        return $this->hasMany(DetailReservasi::class, 'reservasi_id');
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
    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }
}
