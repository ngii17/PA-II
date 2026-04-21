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
    Schema::table('menu', function (Blueprint $table) {
        // Kita tambahkan kolom stok setelah kolom harga
        $table->integer('stok')->default(0)->after('harga');
    });
}

public function down(): void
{
    Schema::table('menu', function (Blueprint $table) {
        $table->dropColumn('stok');
    });
}
};
