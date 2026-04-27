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
    Schema::create('event_menu', function (Blueprint $table) {
        $table->id();
        
        // Menghubungkan ke tabel events (Saklar Tema)
        $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
        
        // Menghubungkan ke tabel menu (Makanan)
        $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
        
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('event_menu');
}

};
