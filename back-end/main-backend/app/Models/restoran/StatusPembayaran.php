<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class StatusPembayaran extends Model
{
    protected $table = 'status_pembayaran';
    protected $fillable = ['nama_status'];
}
