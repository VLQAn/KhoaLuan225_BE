<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\ApiResponse;

Route::get('/test-response', function () {

    return ApiResponse::success(
        [
            'project' => 'Movie Booking System'
        ],
        'API working successfully'
    );

});