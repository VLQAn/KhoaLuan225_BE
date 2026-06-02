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
        Schema::create('hoa_don_bap_nuoc', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('maHoaDon');

            $table->unsignedBigInteger('maMon');

            $table->integer('soLuong');

            $table->decimal('donGia', 10, 2);

            $table->decimal('thanhTien', 10, 2);

            $table->timestamps();

            $table->foreign('maHoaDon')
                ->references('maHoaDon')
                ->on('hoa_don')
                ->onDelete('cascade');

            $table->foreign('maMon')
                ->references('maMon')
                ->on('bap_nuoc')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don_bap_nuoc');
    }
};
