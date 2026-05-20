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
        Schema::create('ghe', function (Blueprint $table) {
            $table->id('maGhe');

            $table->unsignedBigInteger('maPhong');

            $table->string('hangGhe', 5);

            $table->integer('soGhe');

            $table->enum('loaiGhe', [
                'thuong',
                'vip'
            ])->default('thuong');

            $table->enum('trangThai', [
                'hoat_dong',
                'bao_tri'
            ])->default('hoat_dong');

            $table->timestamps();

            /**
             * Foreign key
             */
            $table->foreign('maPhong')
                ->references('maPhong')
                ->on('phong_chieu')
                ->onDelete('cascade');

            /**
             * Unique seat in room
             */
            $table->unique([
                'maPhong',
                'hangGhe',
                'soGhe'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ghe');
    }
};
