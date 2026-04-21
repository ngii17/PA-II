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
    // 1. Tabel Header Pesanan
    Schema::create('pesanan_menu', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // ID dari microservice Auth
        $table->unsignedBigInteger('promo_id')->nullable(); // Jika pakai promo
        $table->decimal('total_harga', 12, 2);
        $table->string('metode_pembayaran'); // Contoh: QRIS, Cash, Tagih ke Kamar
        $table->timestamps();
    });

    // 2. Tabel Rincian Pesanan (Detail)
    Schema::create('detail_pesanan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pesanan_menu_id')->constrained('pesanan_menu')->onDelete('cascade');
        $table->foreignId('menu_id')->constrained('menu');
        $table->integer('jumlah'); // Berapa porsi
        $table->decimal('harga_at_porsi', 12, 2); // Harga saat dibeli (takutnya nanti harga menu naik)
        
        // Menghubungkan ke status_pesanan (Menunggu, Dimasak, Selesai)
        $table->foreignId('status_pesanan_id')->constrained('status_pesanan');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_order_tables');
    }
};
