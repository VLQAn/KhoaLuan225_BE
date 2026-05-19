<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;

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
| Public Movie APIs
|--------------------------------------------------------------------------
*/

Route::prefix('movies')->group(function () {

    Route::get('/', [
        MovieController::class,
        'index'
    ]);

    Route::get('/{id}', [
        MovieController::class,
        'show'
    ]);
});


/*
|--------------------------------------------------------------------------
| Admin Movie APIs
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum'
])->prefix('admin/movies')->group(function () {

    Route::post('/', [
        MovieController::class,
        'store'
    ]);

    Route::put('/{id}', [
        MovieController::class,
        'update'
    ]);

    Route::delete('/{id}', [
        MovieController::class,
        'destroy'
    ]);
});
