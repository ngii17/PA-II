<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('pesanan_menu', function (Blueprint $table) {
        // Kita simpan snap_token setelah kolom metode_pembayaran
        $table->string('snap_token')->nullable()->after('metode_pembayaran');
        
        // Kita juga butuh status_pembayaran_id (1=Pending, 2=Lunas, 4=Batal)
        // Agar sama dengan logika hotel
        $table->unsignedBigInteger('status_pembayaran_id')->default(1)->after('snap_token');
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
