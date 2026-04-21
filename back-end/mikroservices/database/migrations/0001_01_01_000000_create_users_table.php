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
        // Tabel Users sesuai desain kamu + standar Laravel
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('role_id')->constrained('roles');
    $table->string('username')->unique(); // Tambahan: Username unik
    $table->string('full_name'); // Nama ganti jadi Nama Lengkap
    $table->string('email')->unique();
    $table->string('password');
    $table->string('phone');
    $table->text('address');
    $table->string('otp')->nullable();
    $table->boolean('is_verified')->default(false);
    $table->timestamps();
});

    // Bagian ini biarkan saja (Fitur bawaan Laravel untuk reset password)
    Schema::create('password_reset_tokens', function (Blueprint $table) {
        $table->string('email')->primary();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
    });

    // Bagian ini biarkan saja (Fitur bawaan Laravel untuk sesi login)
    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
