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
        // Kolom untuk menyimpan token dari Midtrans
        $table->string('snap_token')->nullable()->after('status_reservasi_id');
    });
}

public function down(): void
{
    Schema::table('reservasi', function (Blueprint $table) {
        $table->dropColumn('snap_token');
    });
}
};
