<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $table = 'reservasi';
    protected $fillable = [
        'user_id', 
        'tipe_kamar_id', 
        'tgl_checkin', 
        'tgl_checkout', 
        'total_malam', 
        'total_harga', 
        'metode_pembayaran', 
        'status_reservasi_id', 
        'snap_token',
        'kamar_id',
    ];

    // TALI PENGHUBUNG 1: Ke data tamu
    public function details() {
        return $this->hasMany(DetailReservasi::class, 'reservasi_id');
    }

    // TALI PENGHUBUNG 2: Ke data tipe kamar
    public function tipeKamar() {
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }
}