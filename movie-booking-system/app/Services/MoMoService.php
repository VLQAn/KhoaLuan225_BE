<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoMoService
{
    public function createPaymentUrl(
        $hoaDon
    ) {

        $endpoint =
            env('MOMO_ENDPOINT');

        $partnerCode =
            env('MOMO_PARTNER_CODE');

        $accessKey =
            env('MOMO_ACCESS_KEY');

        $secretKey =
            env('MOMO_SECRET_KEY');

        $orderId =
            $hoaDon->maHoaDon;

        $requestId =
            time() . '';

        $rawHash =
            'accessKey=' . $accessKey .
            '&amount=' . $hoaDon->tongTien .
            '&extraData=' .
            '&ipnUrl=' . env('MOMO_NOTIFY_URL') .
            '&orderId=' . $orderId .
            '&orderInfo=Thanh toan ve xem phim' .
            '&partnerCode=' . $partnerCode .
            '&redirectUrl=' . env('MOMO_RETURN_URL') .
            '&requestId=' . $requestId .
            '&requestType=captureWallet';

        $signature =
            hash_hmac(
                'sha256',
                $rawHash,
                $secretKey
            );

        $data = [

            'partnerCode'
                => $partnerCode,

            'partnerName'
                => 'Cinema',

            'storeId'
                => 'CinemaStore',

            'requestId'
                => $requestId,

            'amount'
                => (string)
                    $hoaDon->tongTien,

            'orderId'
                => $orderId,

            'orderInfo'
                => 'Thanh toan ve xem phim',

            'redirectUrl'
                => env('MOMO_RETURN_URL'),

            'ipnUrl'
                => env('MOMO_NOTIFY_URL'),

            'lang' => 'vi',

            'requestType'
                => 'captureWallet',

            'autoCapture'
                => true,

            'extraData'
                => '',

            'signature'
                => $signature
        ];

        $response =
            Http::post(
                $endpoint,
                $data
            );

        return response()->json([
            'payment_url'
                => $response['payUrl']
        ]);
    }

    /**
     * Verify MoMo signature
     */
    public function verifySignature(
        array $data
    ) {

        $secretKey =
            env('MOMO_SECRET_KEY');

        $rawHash =
            'accessKey=' .
                env('MOMO_ACCESS_KEY') .

            '&amount=' .
                $data['amount'] .

            '&extraData=' .
                $data['extraData'] .

            '&message=' .
                $data['message'] .

            '&orderId=' .
                $data['orderId'] .

            '&orderInfo=' .
                $data['orderInfo'] .

            '&orderType=' .
                $data['orderType'] .

            '&partnerCode=' .
                $data['partnerCode'] .

            '&payType=' .
                $data['payType'] .

            '&requestId=' .
                $data['requestId'] .

            '&responseTime=' .
                $data['responseTime'] .

            '&resultCode=' .
                $data['resultCode'] .

            '&transId=' .
                $data['transId'];

        $signature =
            hash_hmac(
                'sha256',
                $rawHash,
                $secretKey
            );

        return $signature
            === $data['signature'];
    }
}
