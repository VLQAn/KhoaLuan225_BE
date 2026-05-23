<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(
        PaymentService $paymentService
    ) {
        $this->paymentService = $paymentService;
    }

    /**
     * VNPay payment URL
     */
    public function createVNPayPayment(
        $maHoaDon
    ) {
        return $this->paymentService
            ->createVNPayPayment(
                $maHoaDon
            );
    }

    /**
     * VNPay return
     */
    public function vnpayReturn(
        Request $request
    ) {
        return $this->paymentService
            ->handleVNPayReturn(
                $request->all()
            );
    }

    /**
     * MoMo payment URL
     */
    public function createMoMoPayment(
        $maHoaDon
    ) {
        return $this->paymentService
            ->createMoMoPayment(
                $maHoaDon
            );
    }

    /**
     * MoMo IPN
     */
    public function momoIPN(
        Request $request
    ) {
        return $this->paymentService
            ->handleMoMoIPN(
                $request->all()
            );
    }
}
