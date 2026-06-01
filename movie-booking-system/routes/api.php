<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\TheLoaiController;
use App\Http\Controllers\Api\RapChieuController;
use App\Http\Controllers\Api\PhongChieuController;
use App\Http\Controllers\Api\GheController;
use App\Http\Controllers\Api\XuatChieuController;
use App\Http\Controllers\Api\BapNuocController;
use App\Http\Controllers\Api\GiaVeController;
use App\Http\Controllers\Api\KhuyenMaiController;
use App\Http\Controllers\Api\DatVeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\BookingManagementController;

/*
|--------------------------------------------------------------------------
| Auth APIs
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public APIs
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/register',
        [AuthController::class, 'register']
    );

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );

    /*
    |--------------------------------------------------------------------------
    | Protected APIs
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')
        ->group(function () {

            Route::get(
                '/me',
                [AuthController::class, 'me']
            );

            Route::post(
                '/logout',
                [AuthController::class, 'logout']
            );
        });
});

/*
|--------------------------------------------------------------------------
| Movies
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'movies',
            MovieController::class
        );
    });

Route::apiResource(
    'movies',
    MovieController::class
)->only(['index', 'show']);

/*
|-------------------------------------------------------------------------- 
| The Loai
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::resource(
        'the-loai',
        TheLoaiController::class
    );
});

/*
|--------------------------------------------------------------------------
| Rap Chieu
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'rap-chieu',
            RapChieuController::class
        );
    });

/*
|--------------------------------------------------------------------------
| Phong Chieu
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'phong-chieu',
            PhongChieuController::class
        );
    });

/*
|--------------------------------------------------------------------------
| Ghe
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::post(
            'ghe/generate',
            [GheController::class, 'generateSeats']
        );

        Route::apiResource(
            'ghe',
            GheController::class
        );
    });

/*
|--------------------------------------------------------------------------
| Xuat Chieu
|--------------------------------------------------------------------------
*/
Route::get(
    '/xuat-chieu/available',
    [XuatChieuController::class, 'available']
);

Route::get(
    '/xuat-chieu/{maXuatChieu}/seats',
    [XuatChieuController::class, 'seatMap']
);

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'xuat-chieu',
            XuatChieuController::class
        );
    });

/*
|--------------------------------------------------------------------------
| Bap Nuoc
|--------------------------------------------------------------------------
*/
Route::get('/bap-nuoc/by-rap/{maRap}', [BapNuocController::class, 'getByRap']);

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'bap-nuoc',
            BapNuocController::class
        );

        Route::put(
            'bap-nuoc/{id}/status',
            [BapNuocController::class, 'updateStatus']
        );
    });

/*
|--------------------------------------------------------------------------
| Gia Ve
|--------------------------------------------------------------------------
*/

Route::get(
    'gia-ve-hien-tai',
    [GiaVeController::class, 'current']
);

Route::get(
    'gia-ve/xuat-chieu/{maXuatChieu}',
    [
        GiaVeController::class,
        'getByXuatChieu'
    ]
);

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'gia-ve',
            GiaVeController::class
        );
    });

/*
|-------------------------------------------------------------------------- 
| Khuyen Mai
|-------------------------------------------------------------------------- 
*/
Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'khuyen-mai',
            KhuyenMaiController::class
        );
    });

/*
|-------------------------------------------------------------------------- 
| Dat Ve
|-------------------------------------------------------------------------- 
*/
Route::middleware('auth:sanctum')
    ->group(function () {

        Route::post(
            'dat-ve',
            [DatVeController::class, 'store']
        );
    });

/*
|--------------------------------------------------------------------------
| Payments
|--------------------------------------------------------------------------
*/
Route::prefix('payments')
    ->group(function () {

        Route::post(
            'vnpay/{maHoaDon}',
            [
                PaymentController::class,
                'createVNPayPayment'
            ]
        );

        Route::get(
            'vnpay-return',
            [
                PaymentController::class,
                'vnpayReturn'
            ]
        );

        Route::post(
            'momo/{maHoaDon}',
            [
                PaymentController::class,
                'createMoMoPayment'
            ]
        );

        Route::post(
            'momo-ipn',
            [
                PaymentController::class,
                'momoIPN'
            ]
        );
    });

/*
|-------------------------------------------------------------------------- 
| Dơn đặt vé
|-------------------------------------------------------------------------- 
*/
Route::middleware('auth:sanctum')
    ->group(function () {

        Route::get(
            '/admin/bookings',
            [
                BookingManagementController::class,
                'index'
            ]
        );
    });
