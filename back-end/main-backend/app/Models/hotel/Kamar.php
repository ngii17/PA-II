<?php

namespace App\Models\hotel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kamar extends Model
{
    use SoftDeletes;

    protected $table = 'kamar';
    protected $fillable = ['nomor_kamar', 'tipe_kamar_id', 'status_kamar_id'];

    // INI YANG WAJIB ADA AGAR TIDAK ERROR 500
    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }

    public function statusKamar()
    {
        return $this->belongsTo(StatusKamar::class, 'status_kamar_id');
    }
}
