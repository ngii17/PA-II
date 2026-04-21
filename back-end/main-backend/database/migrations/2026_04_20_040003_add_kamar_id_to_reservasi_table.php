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
    Schema::table('reservasi', function (Blueprint $table) {
        // PASTIKAN BARIS INI ADA DI DALAM FILE SEBELUM KAMU MIGRATE
        $table->foreignId('kamar_id')->nullable()->constrained('kamar')->after('tipe_kamar_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            //
        });
    }
};
