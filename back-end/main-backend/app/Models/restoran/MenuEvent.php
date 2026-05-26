<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\event\Event;

class MenuEvent extends Model
{
    use SoftDeletes;
    protected $table = 'event_menu';
    protected $fillable = ['event_id', 'menu_id', 'harga_khusus', 'is_active'];

    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function menu() {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
