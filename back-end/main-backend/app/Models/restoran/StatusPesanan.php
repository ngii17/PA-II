<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class StatusPesanan extends Model
{
    protected $table = 'status_pesanan';

    protected $fillable = ['nama_status'];
}