<?php

namespace App\Services;

use Exception;
use App\Models\Ve;
use App\Models\HoaDon;
use App\Models\ThanhToan;
use Illuminate\Support\Facades\DB;
use App\Services\VNPayService;
use App\Services\MoMoService;

class PaymentService
{
    protected $vnpayService;
    protected $momoService;

    public function __construct(
        VNPayService $vnpayService,
        MoMoService $momoService
    ) {
        $this->vnpayService = $vnpayService;
        $this->momoService = $momoService;
    }

    /**
     * Create VNPay URL
     */
    public function createVNPayPayment(
        $maHoaDon
    ) {

        $hoaDon = HoaDon::findOrFail(
            $maHoaDon
        );

        if (
            $hoaDon->trangThai !== 'Dang_Thanh_Toan'
        ) {
            throw new Exception(
                'Hóa đơn không hợp lệ'
            );
        }

        return $this->vnpayService
            ->createPaymentUrl($hoaDon);
    }

    /**
     * Handle VNPay callback
     */
    public function handleVNPayReturn(
        array $data
    ) {

        return DB::transaction(function ()
        use ($data) {

            $isValid = $this->vnpayService
                ->verifyPayment($data);

            if (!$isValid) {
                throw new Exception(
                    'Invalid signature'
                );
            }

            $maHoaDon = $data['vnp_TxnRef'];

            $hoaDon = HoaDon::where(
                'maHoaDon',
                $maHoaDon
            )
            ->lockForUpdate()
            ->firstOrFail();

            /**
             * Already paid
             */
            if (
                $hoaDon->trangThai === 'paid'
            ) {
                return response()->json([
                    'message' =>
                        'Hóa đơn đã thanh toán'
                ]);
            }

            /**
             * Payment failed
             */
            if (
                $data['vnp_ResponseCode']
                !== '00'
            ) {

                ThanhToan::create([
                    'maHoaDon' =>
                        $hoaDon->maHoaDon,

                    'phuongThucThanhToan'
                        => 'vnpay',

                    'trangThai'
                        => 'failed',

                    'gioThanhToan'
                        => now()
                ]);

                return response()->json([
                    'message' =>
                        'Thanh toán thất bại'
                ]);
            }

            /**
             * Update invoice
             */
            $hoaDon->update([
                'trangThai' => 'paid',
                'gioThanhToan' => now()
            ]);

            /**
             * Update tickets
             */
            Ve::where(
                'maHoaDon',
                $hoaDon->maHoaDon
            )->update([
                'trangThai' => 'paid'
            ]);

            /**
             * Create payment
             */
            ThanhToan::create([
                'maHoaDon' =>
                    $hoaDon->maHoaDon,

                'phuongThucThanhToan'
                    => 'vnpay',

                'trangThai'
                    => 'success',

                'gioThanhToan'
                    => now()
            ]);

            return response()->json([
                'message' =>
                    'Thanh toán thành công'
            ]);
        });
    }

    /**
     * Create MoMo URL
     */
    public function createMoMoPayment(
        $maHoaDon
    ) {

        $hoaDon = HoaDon::findOrFail(
            $maHoaDon
        );

        if (
            $hoaDon->trangThai !== 'Dang_Thanh_Toan'
        ) {
            throw new Exception(
                'Hóa đơn không hợp lệ'
            );
        }

        return $this->momoService
            ->createPaymentUrl($hoaDon);
    }

    /**
     * Handle MoMo IPN
     */
    public function handleMoMoIPN(
        array $data
    ) {

        return DB::transaction(function ()
        use ($data) {

            $isValid = $this->momoService
                ->verifySignature($data);

            if (!$isValid) {
                throw new Exception(
                    'Invalid signature'
                );
            }

            $maHoaDon = $data['orderId'];

            $hoaDon = HoaDon::where(
                'maHoaDon',
                $maHoaDon
            )
            ->lockForUpdate()
            ->firstOrFail();

            /**
             * Already paid
             */
            if (
                $hoaDon->trangThai === 'paid'
            ) {
                return response()->json([
                    'message' =>
                        'Hóa đơn đã thanh toán'
                ]);
            }

            /**
             * Payment failed
             */
            if (
                $data['resultCode'] != 0
            ) {

                ThanhToan::create([
                    'maHoaDon'
                        => $hoaDon->maHoaDon,

                    'phuongThucThanhToan'
                        => 'momo',

                    'trangThai'
                        => 'failed',

                    'gioThanhToan'
                        => now()
                ]);

                return response()->json([
                    'message' =>
                        'Thanh toán thất bại'
                ]);
            }

            /**
             * Update invoice
             */
            $hoaDon->update([
                'trangThai' => 'paid',
                'gioThanhToan' => now()
            ]);

            /**
             * Update ticket
             */
            Ve::where(
                'maHoaDon',
                $hoaDon->maHoaDon
            )->update([
                'trangThai' => 'paid'
            ]);

            /**
             * Create payment
             */
            ThanhToan::create([
                'maHoaDon'
                    => $hoaDon->maHoaDon,

                'phuongThucThanhToan'
                    => 'momo',

                'trangThai'
                    => 'success',

                'gioThanhToan'
                    => now()
            ]);

            return response()->json([
                'message' =>
                    'Thanh toán thành công'
            ]);
        });
    }
}
