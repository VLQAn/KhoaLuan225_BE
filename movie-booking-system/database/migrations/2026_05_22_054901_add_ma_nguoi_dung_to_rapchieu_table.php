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
        Schema::table('rap_chieu', function (Blueprint $table) {
            $table->unsignedBigInteger('maNguoiDung')->nullable()->after('maRap');
            $table->foreign('maNguoiDung')->references('maNguoiDung')->on('nguoi_dung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rap_chieu', function (Blueprint $table) {
            //
        });
    }
};
