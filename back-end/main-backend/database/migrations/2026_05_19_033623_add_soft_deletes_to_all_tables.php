<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToAllTables extends Migration
{
    public function up()
    {
        $tables = [
            'promo',
            'menu',
            'pesanan_menu',
            'event_menu',
            'kategori_menu',
            'reservasi',
            'kamar',
            'tipe_kamar'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'deleted_at')) {
                        $table->softDeletes();
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = ['promo', 'menu', 'pesanan_menu', 'event_menu', 'kategori_menu', 'reservasi', 'kamar', 'tipe_kamar'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
}
