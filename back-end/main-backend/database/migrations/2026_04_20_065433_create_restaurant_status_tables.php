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
    // 1. Tabel Status Menu (Tersedia, Habis)
    Schema::create('status_menu', function (Blueprint $table) {
        $table->id();
        $table->string('nama_status'); // Contoh: Tersedia, Habis
        $table->timestamps();
    });

    // 2. Tabel Status Pesanan (Menunggu, Dimasak, Disajikan, Selesai)
    Schema::create('status_pesanan', function (Blueprint $table) {
        $table->id();
        $table->string('nama_status');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_status_tables');
    }
};
