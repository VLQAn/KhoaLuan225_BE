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
        Schema::create('PHIM', function (Blueprint $table) {
            $table->id();

            $table->string('tieuDe');

            $table->text('moTa')->nullable();

            $table->integer('thoiLuong')->nullable();

            $table->date('ngayCongChieu')->nullable();

            $table->string('anhPoster')->nullable();

            $table->string('anhBanner')->nullable();

            $table->string('danhGia')->nullable();

            $table->string('dienVien')->nullable();

            $table->string('daoDien')->nullable();

            $table->enum('trangThai', [
                'sap_chieu',
                'dang_chieu',
                'ngung_chieu'
            ])->default('sap_chieu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PHIM');
    }
};
