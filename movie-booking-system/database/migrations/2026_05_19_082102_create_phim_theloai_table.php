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
        Schema::create('phim_the_loai', function (Blueprint $table) {

            $table->unsignedBigInteger('maPhim');
            $table->unsignedBigInteger('maTheLoai');

            // FK phim
            $table->foreign('maPhim')
                ->references('maPhim')
                ->on('phim')
                ->onDelete('cascade');

            // FK thể loại
            $table->foreign('maTheLoai')
                ->references('maTheLoai')
                ->on('the_loai')
                ->onDelete('cascade');

            // tránh trùng dữ liệu
            $table->primary(['maPhim', 'maTheLoai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim_the_loai');
    }
};
