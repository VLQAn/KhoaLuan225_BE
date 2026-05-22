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
        Schema::create('ve', function (Blueprint $table) {
            $table->id('maVe');

            $table->unsignedBigInteger('maXuatChieu')->unsigned();
            $table->foreign('maXuatChieu')->references('maXuatChieu')->on('xuat_chieu')->onDelete('cascade');

            $table->unsignedBigInteger('maGiaVe')->unsigned();
            $table->foreign('maGiaVe')->references('maGiaVe')->on('gia_ve')->onDelete('cascade');

            $table->unsignedBigInteger('maHoaDon')->unsigned();
            $table->foreign('maHoaDon')->references('maHoaDon')->on('hoa_don')->onDelete('cascade');

            $table->unsignedBigInteger('maGhe')->unsigned();
            $table->foreign('maGhe')->references('maGhe')->on('ghe')->onDelete('cascade');

            $table->decimal('gia', 10, 2);

            $table->enum('trangThai', ['Dang_chon', 'Da_Dat'])->default('Dang_chọn');

            $table->timestamps();

            $table->unique(
                [
                    'maXuatChieu',
                    'maGhe'
                ],
                'unique_ghe_xuatchieu'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ve');
    }
};
