<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('ulasan_restoran', function (Blueprint $table) {
        // Tambahkan kolom ID Pesanan
        $table->unsignedBigInteger('pesanan_menu_id')->nullable()->after('menu_id');
    });

    Schema::table('ulasan_hotel', function (Blueprint $table) {
        // Tambahkan kolom ID Reservasi
        $table->unsignedBigInteger('reservasi_id')->nullable()->after('tipe_kamar_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
