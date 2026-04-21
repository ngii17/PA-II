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
    Schema::create('kamar', function (Blueprint $table) {
        $table->id();
        $table->string('nomor_kamar'); // Contoh: A101, B202, dll.
        
        // Relasi ke Tipe Kamar
        $table->foreignId('tipe_kamar_id')->constrained('tipe_kamar');
        
        // Relasi ke Status Kamar
        $table->foreignId('status_kamar_id')->constrained('status_kamar');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
