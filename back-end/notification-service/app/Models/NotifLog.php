<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifLog extends Model
{
    protected $fillable = [
    'user_id', 
    'type', 
    'title', // Tambah ini
    'body',  // Tambah ini
    'fcm_token', 
    'status', 
    'sent_at', 
    'error'
    ];
}
