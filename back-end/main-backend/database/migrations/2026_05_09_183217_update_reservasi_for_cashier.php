<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('reservasi', function (Blueprint $table) {
        $table->decimal('deposit_amount', 12, 2)->default(0); // Uang Jaminan
        $table->integer('confirmed_by')->nullable(); // ID Staf yang memproses
        $table->timestamp('confirmed_at')->nullable(); // Waktu konfirmasi
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            //
        });
    }
};
