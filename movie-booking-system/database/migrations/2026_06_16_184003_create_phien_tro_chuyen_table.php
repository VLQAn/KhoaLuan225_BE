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
        Schema::create('phien_tro_chuyen', function (Blueprint $table) {

            $table->id('maPhien');

            $table->unsignedBigInteger(
                'maNguoiDung'
            )->nullable();

            $table->unsignedBigInteger(
                'phimDangChon'
            )->nullable();

            $table->unsignedBigInteger(
                'xuatChieuDangChon'
            )->nullable();

            $table->string(
                'trangThai'
            )->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phien_tro_chuyen');
    }
};
