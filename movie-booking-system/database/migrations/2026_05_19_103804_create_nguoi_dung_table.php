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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id('maNguoiDung');

            $table->string('tenNguoiDung');

            $table->string('email')
                ->unique();

            $table->string('matKhau');

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('vaiTro');

            $table->foreign('vaiTro')
                ->references('maVaiTro')
                ->on('vai_tro');

            /*
            |--------------------------------------------------------------------------
            | Optional fields
            |--------------------------------------------------------------------------
            */

            $table->string('nhaCungCap')
                ->nullable();

            $table->string('maXacThuc')
                ->nullable();

            $table->timestamp('thoiGianXacThuc')
                ->nullable();

            $table->rememberToken();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
