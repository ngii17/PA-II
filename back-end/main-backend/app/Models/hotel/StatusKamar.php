<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class StatusKamar extends Model
{
    protected $table = 'status_kamar';

    protected $fillable = ['nama_status'];

    /**
     * RELASI: Satu Status bisa dimiliki oleh banyak Kamar
     */
    public function kamar()
    {
        return $this->hasMany(Kamar::class, 'status_kamar_id');
    }
}
