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
    Schema::create('notif_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable(); // Siapa penerimanya
        $table->string('type');      // Jenis: booking_confirmed, reminder, dll
        $table->string('fcm_token')->nullable();
        $table->string('status');    // sukses atau gagal
        $table->timestamp('sent_at')->useCurrent();
        $table->text('error')->nullable(); // Jika gagal, simpan pesan errornya di sini
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notif_logs');
    }
};
