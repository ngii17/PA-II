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
    Schema::table('users', function (Blueprint $table) {
        // Kita tambah kolom foto setelah full_name
        // Nullable artinya boleh kosong (nanti otomatis pakai avatar default)
        $table->string('profile_photo')->nullable()->after('full_name');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('profile_photo');
    });
}
};
