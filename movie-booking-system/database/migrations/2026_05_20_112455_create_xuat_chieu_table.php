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
        Schema::create('xuat_chieu', function (Blueprint $table) {
            $table->id('maXuatChieu');

            $table->unsignedBigInteger('maPhim');

            $table->unsignedBigInteger('maPhong');

            $table->dateTime('thoiGianBatDau');

            $table->dateTime('thoiGianKetThuc');

            $table->enum('trangThai', [
                'sap_chieu',
                'dang_chieu',
                'da_chieu',
                'da_huy'
            ])->default('sap_chieu');

            $table->timestamps();

            /**
             * Foreign keys
             */
            $table->foreign('maPhim')
                ->references('maPhim')
                ->on('phim')
                ->onDelete('cascade');

            $table->foreign('maPhong')
                ->references('maPhong')
                ->on('phong_chieu')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xuat_chieu');
    }
};
