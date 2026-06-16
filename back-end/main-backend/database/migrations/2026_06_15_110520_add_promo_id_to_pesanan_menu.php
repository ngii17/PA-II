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
                $table->foreign('promo_id')
                    ->references('id')
                    ->on('promo')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }

        public function down(): void
        {
            Schema::table('pesanan_menu', function (Blueprint $table) {
                $table->dropForeign(['promo_id']);
            });
        }
};
