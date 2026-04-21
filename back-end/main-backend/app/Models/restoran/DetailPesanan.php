<?php

namespace App\Models\restoran;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $fillable = [
        'pesanan_menu_id',
        'menu_id',
        'jumlah',
        'harga_at_porsi',
        'status_pesanan_id',
    ];

    /**
     * RELASI: Detail ini milik Nota yang mana?
     */
    public function header()
    {
        return $this->belongsTo(PesananMenu::class, 'pesanan_menu_id');
    }

    /**
     * RELASI: Detail ini merujuk ke Makanan yang mana?
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    /**
     * RELASI: Detail ini statusnya apa (Menunggu/Dimasak/Selesai)?
     */
    public function statusPesanan()
    {
        return $this->belongsTo(StatusPesanan::class, 'status_pesanan_id');
    }
}