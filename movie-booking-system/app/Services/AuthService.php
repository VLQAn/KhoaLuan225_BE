<?php

namespace App\Services;

use App\Models\VaiTro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\AuthRepositoryInterface;

class AuthService
{
    protected $authRepository;

    public function __construct(
        AuthRepositoryInterface $authRepository
    ) {
        $this->authRepository = $authRepository;
    }

    /**
     * Register
     */
    public function register(
        array $data
    ) {
        $customerRole = VaiTro::where(
            'vaiTro',
            'customer'
        )->first();

        if (!$customerRole) {
            throw new \Exception(
                'Customer role not found'
            );
        }

        $user = $this->authRepository
            ->create([
                'tenNguoiDung' => $data['tenNguoiDung'],
                'email' => $data['email'],
                'matKhau' => Hash::make(
                    $data['matKhau']
                ),
                'vaiTro' => $customerRole->maVaiTro
            ]);

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Login
     */
    public function login(
        array $data
    ) {
        $user = $this->authRepository
            ->findByEmail($data['email']);

        if (
            !$user ||
            !Hash::check(
                $data['matKhau'],
                $user->matKhau
            )
        ) {
            return null;
        }

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Logout
     */
    public function logout(
        $user
    ): void {
        $user->currentAccessToken()
            ->delete();
    }
}
