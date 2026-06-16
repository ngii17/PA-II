<?php

namespace App\Models\promo;

use Illuminate\Database\Eloquent\Model;

class PromoUsage extends Model
{
    protected $table = 'promo_usage';

    protected $fillable = [
        'promo_id',
        'user_id',
        'kategori',
    ];

    public function promo()
    {
        return $this->belongsTo(\App\Models\Hotel\Promo::class, 'promo_id');
    }
}