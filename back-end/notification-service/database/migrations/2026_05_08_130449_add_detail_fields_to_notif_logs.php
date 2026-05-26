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
    Schema::table('notif_logs', function (Blueprint $table) {
        $table->boolean('is_read')->default(false)->after('status');
        $table->string('image_url')->nullable()->after('is_read');
        $table->string('action_url')->nullable()->after('image_url');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notif_logs', function (Blueprint $table) {
            //
        });
    }
};
