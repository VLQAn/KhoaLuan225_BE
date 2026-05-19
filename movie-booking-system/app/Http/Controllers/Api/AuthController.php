<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

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
    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register(
            $request->validated()
        );

        return ApiResponse::success(
            $result,
            'Register successful'
        );
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login(
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
            $result,
            'Login successful'
        );
    }

    /**
     * Current User
     */
    public function me(Request $request)
    {
        return ApiResponse::success(
            $request->user(),
            'Current user'
        );
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return ApiResponse::success(
            null,
            'Logout successful'
        );
    }
}