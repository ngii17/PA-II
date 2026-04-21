<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $fillable = ['nomor_kamar', 'tipe_kamar_id', 'status_kamar_id'];
}
