<?php

namespace App\Models\hotel;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = [
        'user_id', 'fcm_token', 'tipe_kamar_id', 'kamar_id', 
        'tgl_checkin', 'tgl_checkout', 'total_malam', 
        'total_harga', 'metode_pembayaran', 'status_reservasi_id', 
        'snap_token', 'deposit_amount', 'confirmed_by', 'confirmed_at'
    ];

    /**
     * RELASI: Satu Reservasi terhubung ke satu unit Kamar fisik
     */
    public function kamar()
    {
        // Hubungkan ke model Kamar menggunakan kolom 'kamar_id'
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    /**
     * RELASI: Satu Reservasi terhubung ke satu Tipe Kamar
     */
    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }

    /**
     * RELASI: Detail Tamu
     */
    public function details()
    {
        return $this->hasMany(DetailReservasi::class, 'reservasi_id');
    }
}