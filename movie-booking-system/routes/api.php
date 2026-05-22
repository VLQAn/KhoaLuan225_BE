<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\RapChieuController;
use App\Http\Controllers\Api\PhongChieuController;
use App\Http\Controllers\Api\GheController;
use App\Http\Controllers\Api\XuatChieuController;
use App\Http\Controllers\Api\BapNuocController;

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

Route::middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'bap-nuoc',
            BapNuocController::class
        );
    });
