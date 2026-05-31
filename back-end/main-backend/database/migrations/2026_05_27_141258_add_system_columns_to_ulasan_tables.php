<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah kolom ke tabel ulasan restoran
        Schema::table('ulasan_restoran', function (Blueprint $table) {
            if (!Schema::hasColumn('ulasan_restoran', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false)->after('komentar');
            }
            if (!Schema::hasColumn('ulasan_restoran', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false)->after('is_hidden');
            }
        });

        // Tambah kolom ke tabel ulasan hotel
        Schema::table('ulasan_hotel', function (Blueprint $table) {
            if (!Schema::hasColumn('ulasan_hotel', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false)->after('komentar');
            }
            // is_anonymous biasanya sudah ada, kalau belum ada tambahkan di sini
        });
    }

    public function down()
    {
        Schema::table('ulasan_restoran', function (Blueprint $table) {
            $table->dropColumn(['is_hidden', 'is_anonymous']);
        });
        Schema::table('ulasan_hotel', function (Blueprint $table) {
            $table->dropColumn(['is_hidden']);
        });
    }
};