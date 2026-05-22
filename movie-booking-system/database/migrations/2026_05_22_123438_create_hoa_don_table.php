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
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->id('maHoaDon');

            $table->unsignedBigInteger('maNguoiDung')->unsigned();
            $table->foreign('maNguoiDung')->references('maNguoiDung')->on('nguoi_dung')->onDelete('cascade');

            $table->unsignedBigInteger('maKhuyenMai')->unsigned()->nullable();
            $table->foreign('maKhuyenMai')->references('maKhuyenMai')->on('khuyen_mai')->onDelete('set null');

            $table->dateTime('gioThanhToan')->nullable();

            $table->decimal('tongTien', 10, 2)->default(0);

            $table->enum('trangThai', ['Dang_Thanh_Toan', 'Da_Thanh_Toan', 'Da_Huy', 'Het_Han'])->default('Dang_Thanh_Toan');

            $table->decimal('tongThanhToan', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
