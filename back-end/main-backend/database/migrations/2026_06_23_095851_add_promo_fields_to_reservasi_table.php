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
            $table->string('kode_voucher_digunakan')->nullable()->after('promo_id');
            $table->decimal('nominal_diskon', 12, 2)->default(0)->after('kode_voucher_digunakan');
        });
    }

    public function down()
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn(['kode_voucher_digunakan', 'nominal_diskon']);
        });
    }
};
