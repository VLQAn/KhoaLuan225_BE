<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Resources\NguoiDungResource;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    /**
     * Register
     */
    public function register(
        RegisterRequest $request
    ) {
        $result = $this->authService
            ->register(
                $request->validated()
            );

        return ApiResponse::success(
            [
                'user' => new NguoiDungResource(
                    $result['user']
                ),

                'token' => $result['token']
            ],
            'Register successful',
            201
        );
    }

    /**
     * Login
     */
    public function login(
        LoginRequest $request
    ) {
        $result = $this->authService
            ->login(
                $request->validated()
            );

        if (!$result) {
            return ApiResponse::error(
                'Invalid credentials',
                null,
                401
            );
        }

        return ApiResponse::success(
            [
                'user' => new NguoiDungResource(
                    $result['user']
                ),

                'token' => $result['token']
            ],
            'Login successful'
        );
    }

    /**
     * Current user
     */
    public function me()
    {
        return ApiResponse::success(
            new NguoiDungResource(
                Auth::user()
            )
        );
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->authService
            ->logout(
                Auth::user()
            );

        return ApiResponse::success(
            null,
            'Logout successful'
        );
    }
}
