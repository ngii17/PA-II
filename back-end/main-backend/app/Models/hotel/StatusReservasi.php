<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class StatusReservasi extends Model
{
    protected $table = 'status_reservasi';    
    protected $fillable = ['nama_status'];
}
