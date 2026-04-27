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
    // 1. Tabel Ulasan Hotel (Per Tipe Kamar)
    Schema::create('ulasan_hotel', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // Siapa yang mengulas
        $table->foreignId('tipe_kamar_id')->constrained('tipe_kamar')->onDelete('cascade');
        $table->integer('rating'); // Bintang 1-5
        $table->text('komentar');
        $table->timestamps();
    });

    // 2. Tabel Ulasan Restoran (Per Menu)
    Schema::create('ulasan_restoran', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // Siapa yang mengulas
        $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
        $table->integer('rating'); // Bintang 1-5
        $table->text('komentar');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasan_tables');
    }
};
