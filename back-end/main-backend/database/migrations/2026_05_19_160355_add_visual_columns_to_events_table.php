<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Cek satu per satu, jika belum ada baru buat
            if (!Schema::hasColumn('events', 'primary_color')) {
                $table->string('primary_color')->nullable();
            }
            if (!Schema::hasColumn('events', 'secondary_color')) {
                $table->string('secondary_color')->nullable();
            }
            if (!Schema::hasColumn('events', 'header_image')) {
                $table->string('header_image')->nullable();
            }
            if (!Schema::hasColumn('events', 'decoration_image')) {
                $table->string('decoration_image')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['primary_color', 'secondary_color', 'header_image', 'decoration_image']);
        });
    }
};
