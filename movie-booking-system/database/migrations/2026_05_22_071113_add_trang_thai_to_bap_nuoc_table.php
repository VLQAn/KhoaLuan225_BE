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
        Schema::table('bap_nuoc', function (Blueprint $table) {
            $table->enum('trangThai', [
                'DANG_BAN',
                'HET_BAN_TRONG_NGAY',
                'NGUNG_KINH_DOANH'
            ])->default('DANG_BAN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bap_nuoc', function (Blueprint $table) {
            $table->dropColumn('trangThai');
        });
    }
};
