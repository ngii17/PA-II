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
        // Tambahkan kolom status pesanan di header nota
        $table->unsignedBigInteger('status_pesanan_id')->default(1)->after('status_pembayaran_id');
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
