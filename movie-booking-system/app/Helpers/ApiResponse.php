<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Success response
     */
    public static function success(
        $data = null,
        string $message = 'Success',
        int $code = 200
    ) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'Error',
        $errors = null,
        int $code = 400
    ) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}