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
        // 1. Tabel Utama Reservasi
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID User dari Mikroservice sebelah
            $table->foreignId('tipe_kamar_id')->constrained('tipe_kamar');
            $table->date('tgl_checkin');
            $table->date('tgl_checkout');
            $table->integer('total_malam');
            $table->decimal('total_harga', 12, 2);
            $table->string('metode_pembayaran');
            $table->foreignId('status_reservasi_id')->constrained('status_reservasi');
            $table->timestamps();
        });

        // 2. Tabel Detail Reservasi (Untuk Data Tamu)
        Schema::create('detail_reservasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained('reservasi')->onDelete('cascade');
            $table->string('nama_tamu');
            $table->string('nik_identitas');
            $table->integer('jumlah_tamu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_reservasi');
        Schema::dropIfExists('reservasi');
    }
};