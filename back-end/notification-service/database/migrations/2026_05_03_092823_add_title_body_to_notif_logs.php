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
    Schema::table('notif_logs', function (Blueprint $table) {
        // Simpan judul dan isi pesan asli agar bisa ditampilkan di HP user
        $table->string('title')->nullable()->after('type');
        $table->text('body')->nullable()->after('title');
    });
}

public function down(): void
{
    Schema::table('notif_logs', function (Blueprint $table) {
        $table->dropColumn(['title', 'body']);
    });
}
};
