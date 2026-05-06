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
        Schema::table('reservasi', function (Blueprint $table) {
            // Kita tambahkan kolom fcm_token untuk menyimpan identitas HP user
            // Dibuat nullable agar tidak error jika suatu saat token gagal terambil
            $table->text('fcm_token')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn('fcm_token');
        });
    }
};