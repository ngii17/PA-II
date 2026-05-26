<?php

namespace App\Models\hotel; // Gunakan 'hotel' kecil agar konsisten

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipeKamar extends Model
{
    use SoftDeletes;

    protected $table = 'tipe_kamar';

    protected $fillable = [
        'nama_tipe',
        'harga',
        'kapasitas',
        'fasilitas',
        'deskripsi'
    ];

    protected $dates = ['deleted_at'];

    /**
     * RELASI: Satu Tipe Kamar memiliki banyak Kamar Fisik
     */
    public function kamar()
    {
        return $this->hasMany(Kamar::class, 'tipe_kamar_id');
    }
}
