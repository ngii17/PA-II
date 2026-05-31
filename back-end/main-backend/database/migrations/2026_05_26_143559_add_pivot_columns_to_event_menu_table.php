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
    Schema::table('event_menu', function (Blueprint $table) {
        // Tambahkan kolom yang diminta oleh withPivot di model
        $table->decimal('harga_khusus', 12, 2)->nullable()->after('menu_id');
        $table->boolean('is_active')->default(true)->after('harga_khusus');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_menu', function (Blueprint $table) {
            //
        });
    }
};
