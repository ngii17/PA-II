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
    Schema::table('pesanan_menu', function (Blueprint $table) {
        $table->string('tipe_pengantaran')->nullable(); // 'meja' atau 'kamar'
        $table->string('nomor_lokasi')->nullable();    // '12' atau '201'
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_menu', function (Blueprint $table) {
            //
        });
    }
};
