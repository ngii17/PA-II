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
    // 1. Tabel Status Kamar (Contoh isinya: Tersedia, Terisi, Perbaikan)
    Schema::create('status_kamar', function (Blueprint $table) {
        $table->id();
        $table->string('nama_status');
        $table->timestamps();
    });

    // 2. Tabel Tipe Kamar (Contoh isinya: Deluxe, Suite, Executive)
    Schema::create('tipe_kamar', function (Blueprint $table) {
        $table->id();
        $table->string('nama_tipe');
        $table->decimal('harga', 12, 2);
        $table->integer('kapasitas');
        $table->text('fasilitas');
        $table->text('deskripsi');
        $table->timestamps();
    });

    // 3. Tabel Status Reservasi (Contoh isinya: Pending, Terbayar, Selesai)
    Schema::create('status_reservasi', function (Blueprint $table) {
        $table->id();
        $table->string('nama_status');
        $table->timestamps();
    });

    // 4. Tabel Promo
Schema::create('promo', function (Blueprint $table) {
    $table->id();
    $table->string('nama_promo'); // Contoh: Promo Grand Opening
    $table->string('kode_promo')->nullable(); // Jika NULL = Diskon Otomatis di Katalog
    
    // Pembeda: Promo ini untuk apa?
    $table->enum('kategori', ['hotel', 'restoran', 'semua'])->default('semua');
    
    // Tipe Diskon: Potongan Persen (10%) atau Nominal (Rp 50.000)?
    $table->enum('tipe_diskon', ['persen', 'nominal'])->default('persen');
    $table->decimal('nominal_potongan', 12, 2); 
    
    $table->date('tgl_mulai');
    $table->date('tgl_selesai');
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_master_tables');
    }
};
