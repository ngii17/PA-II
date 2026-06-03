<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastNotification extends Model
{
    protected $table = 'broadcast_notifications';
    protected $fillable = ['title', 'body', 'image_url', 'action_url', 'start_date', 'end_date', 'status'];
}