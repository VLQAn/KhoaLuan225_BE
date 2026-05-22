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
        Schema::create('gia_ve', function (Blueprint $table) {
            $table->id('maGiaVe');

            $table->time('gioBatDau');

            $table->time('gioKetThuc');

            $table->decimal('gia', 8, 2);

            $table->string('moTa')->nullable();

            $table->integer('doTuoi');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gia_ve');
    }
};
