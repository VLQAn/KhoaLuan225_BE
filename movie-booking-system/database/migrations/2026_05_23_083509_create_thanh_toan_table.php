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
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id('maThanhToan');

            /**
             * Foreign key hóa đơn
             */
            $table->unsignedBigInteger(
                'maHoaDon'
            );

            /**
             * VNPay | MoMo
             */
            $table->string(
                'phuongThucThanhToan'
            );

            /**
             * pending
             * success
             * failed
             */
            $table->enum(
                'trangThai',
                [
                    'pending',
                    'success',
                    'failed'
                ]
            )->default('pending');

            /**
             * Mã giao dịch từ gateway
             */
            $table->string(
                'maGiaoDich'
            )->nullable();

            /**
             * Số tiền thanh toán
             */
            $table->decimal(
                'soTien',
                12,
                2
            );

            /**
             * Raw callback response
             */
            $table->longText(
                'duLieuPhanHoi'
            )->nullable();

            /**
             * Thời gian thanh toán
             */
            $table->timestamp(
                'gioThanhToan'
            )->nullable();

            $table->timestamps();

            /**
             * Foreign key
             */
            $table->foreign('maHoaDon')
                ->references('maHoaDon')
                ->on('hoa_don')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};
