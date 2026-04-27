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
    Schema::table('events', function (Blueprint $table) {
        // Kita tambahkan kolom atribut visual
        $table->string('primary_color')->default('#448AFF')->after('event_code'); // Warna Utama
        $table->string('secondary_color')->default('#2979FF')->after('primary_color'); // Warna Aksen
        $table->string('header_image')->nullable()->after('secondary_color'); // Link Gambar Atas
        $table->string('background_image')->nullable()->after('header_image'); // Link Background Layar
        $table->string('decoration_image')->nullable()->after('background_image'); // Link Hiasan Sudut
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn(['primary_color', 'secondary_color', 'header_image', 'background_image', 'decoration_image']);
    });
}
};
