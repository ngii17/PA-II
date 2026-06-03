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
    Schema::create('broadcast_notifications', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('body');
        $table->string('image_url')->nullable();
        $table->string('action_url')->nullable(); // Link tujuan jika diklik
        $table->date('start_date');
        $table->date('end_date');
        $table->enum('status', ['draft', 'sent'])->default('draft');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_notifications');
    }
};
