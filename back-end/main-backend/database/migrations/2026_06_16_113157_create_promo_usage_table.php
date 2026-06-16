<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promo_id');
            $table->unsignedBigInteger('user_id');
            $table->string('kategori'); // hotel / restoran
            $table->timestamps();

            $table->foreign('promo_id')->references('id')->on('promo')->onDelete('cascade');
            // 1 user hanya bisa pakai 1 voucher yang sama 1 kali
            $table->unique(['promo_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_usage');
    }
};