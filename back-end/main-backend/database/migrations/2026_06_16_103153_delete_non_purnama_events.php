<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← tambahkan ini


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('events')->whereNotIn('event_code', [
            'default',
            'idul_fitri',
            'natal_tahun_baru',
            'kemerdekaan',
            'imlek',
            'valentine',
        ])->delete();
    }

    public function down(): void
    {
        // tidak perlu rollback
    }
};
