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
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            
            // 1. Relasi ke Kategori (Tabel Induk 1)
            $table->foreignId('kategori_menu_id')->constrained('kategori_menu');

            $table->string('nama_menu');
            $table->text('deskripsi');
            $table->decimal('harga', 12, 2);
            $table->string('foto_menu')->nullable();

            // 2. Relasi ke Status (Tabel Induk 2)
            $table->foreignId('status_menu_id')->constrained('status_menu');

            // 3. Relasi ke Promo (Optional)
            $table->unsignedBigInteger('promo_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};