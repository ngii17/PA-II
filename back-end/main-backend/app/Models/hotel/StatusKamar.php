<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class StatusKamar extends Model
{
    protected $table = 'status_kamar'; // Kasih tahu Laravel nama tabel aslinya
    protected $fillable = ['nama_status'];
}
