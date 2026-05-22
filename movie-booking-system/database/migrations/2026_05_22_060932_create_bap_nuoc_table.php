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
        Schema::create('bap_nuoc', function (Blueprint $table) {
            $table->id('maMon');

            $table->unsignedBigInteger('maRap')->nullable()->after('maMon');
            $table->foreign('maRap')->references('maRap')->on('rap_chieu')->onDelete('cascade');

            $table->string('tenMon');

            $table->decimal('gia', 8, 2);

            $table->string('hinhAnh')->nullable();

            $table->text('moTa')->nullable();

            $table->enum('trangThai', [
                'DANG_BAN',
                'HET_BAN_TRONG_NGAY',
                'NGUNG_KINH_DOANH'
            ])->default('DANG_BAN');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bap_nuoc');
    }
};
