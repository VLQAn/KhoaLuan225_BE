<?php

namespace App\Services;



class VNPayService
{
    public function createPaymentUrl(
        $hoaDon
    ) {

        $vnp_Url =
            env('VNPAY_URL');

        $vnp_Returnurl =
            env('VNPAY_RETURN_URL');

        $vnp_TmnCode =
            env('VNPAY_TMN_CODE');

        $vnp_HashSecret =
            env('VNPAY_HASH_SECRET');

        $inputData = [

            "vnp_Version" => "2.1.0",

            "vnp_TmnCode" => $vnp_TmnCode,

            "vnp_Amount" =>
            $hoaDon->tongThanhToan * 100,

            "vnp_Command" => "pay",

            "vnp_CreateDate" =>
            date('YmdHis'),

            "vnp_CurrCode" => "VND",

            "vnp_IpAddr" =>
            '127.0.0.1',

            "vnp_Locale" => "vn",

            "vnp_OrderInfo" =>
            "Thanh toan ve xem phim",

            "vnp_OrderType" =>
            "billpayment",

            "vnp_ReturnUrl" =>
            $vnp_Returnurl,

            "vnp_TxnRef" =>
            (string) $hoaDon->maHoaDon
        ];

        ksort($inputData);

        $query = "";
        $hashdata = "";

        $i = 0;

        foreach (
            $inputData
            as $key => $value
        ) {

            if ($i == 1) {

                $hashdata .= '&'
                    . urlencode($key)
                    . "="
                    . urlencode($value);
            } else {

                $hashdata .= urlencode($key)
                    . "="
                    . urlencode($value);

                $i = 1;
            }

            $query .= urlencode($key)
                . "="
                . urlencode($value)
                . '&';
        }

        $query = rtrim($query, '&');

        $vnpSecureHash = hash_hmac(
            'sha512',
            $hashdata,
            $vnp_HashSecret
        );

        $paymentUrl = $vnp_Url
            . "?"
            . $query
            . '&vnp_SecureHash='
            . $vnpSecureHash;

        return response()->json([
            'payment_url'
            => $paymentUrl
        ]);
    }

    /**
     * Verify VNPay signature
     */
    public function verifyPayment(
        array $data
    ) {

        $vnp_HashSecret =
            env('VNPAY_HASH_SECRET');

        $secureHash =
            $data['vnp_SecureHash'];

        unset(
            $data['vnp_SecureHash']
        );

        unset(
            $data['vnp_SecureHashType']
        );

        ksort($data);

        $hashData = "";

        $i = 0;

        foreach (
            $data
            as $key => $value
        ) {

            if (
                str_starts_with(
                    $key,
                    'vnp_'
                )
            ) {

                if ($i == 1) {

                    $hashData .= '&'
                        . urlencode($key)
                        . "="
                        . urlencode($value);
                } else {

                    $hashData .= urlencode($key)
                        . "="
                        . urlencode($value);

                    $i = 1;
                }
            }
        }

        $calculatedHash =
            hash_hmac(
                'sha512',
                $hashData,
                $vnp_HashSecret
            );

        return $secureHash
            === $calculatedHash;
    }
}
