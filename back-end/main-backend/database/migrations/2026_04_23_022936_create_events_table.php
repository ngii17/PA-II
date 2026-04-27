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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('nama_event'); // Contoh: Perayaan Kemerdekaan RI
        
        // event_code adalah KUNCI yang akan dibaca Flutter untuk ganti tema
        // Isinya nanti: 'default', 'imlek', 'natal', 'lebaran', 'valentine', 'hut_ri'
        $table->string('event_code')->unique(); 
        
        // is_active adalah saklarnya
        $table->boolean('is_active')->default(false); 
        
        $table->text('deskripsi')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
