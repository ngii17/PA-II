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
    Schema::table('pesanan_menu', function (Blueprint $table) {
        // Simpan token HP user di tabel pesanan restoran
        $table->text('fcm_token')->nullable()->after('user_id');
    });
}

public function down(): void
{
    Schema::table('pesanan_menu', function (Blueprint $table) {
        $table->dropColumn('fcm_token');
    });
}
};
