<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Resources\NguoiDungResource;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\NguoiDung;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    /**
     * Đăng ký
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
     * Đăng nhập
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
     * User hiện tại
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
     * Đăng xuất
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

    /**
     * Đổi mật khẩu
     */
    public function changePassword(
        ChangePasswordRequest $request
    ) {

        /** @var \App\Models\NguoiDung|null $user */
        $user = Auth::user();

        if (
            !$user || !Hash::check(
                $request->oldPassword,
                $user->matKhau
            )
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu cũ không đúng'
            ], 400);
        }

        $user->update([
            'matKhau' => Hash::make(
                $request->newPassword
            )
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }

    /**
     * Gửi OTP đổi mật khẩu
     */
    public function sendOtp(
        Request $request
    ) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = NguoiDung::where(
            'email',
            $request->email
        )->first();

        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'Email không tồn tại'
            ], 404);
        }

        $otp = rand(
            100000,
            999999
        );

        $user->update([
            'maXacThuc' => $otp,

            'thoiGianXacThuc' =>
            now()->addMinutes(5)
        ]);

        Mail::raw(
            "Mã OTP khôi phục mật khẩu của bạn là: $otp",
            function ($message)
            use ($request) {

                $message
                    ->to($request->email)
                    ->subject(
                        'Khôi phục mật khẩu RACSO Cinema'
                    );
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP đã được gửi'
        ]);
    }

    /**
     * Đổimật khẩu
     */
    public function resetPassword(
        Request $request
    ) {
        $request->validate([
            'email' =>
            'required|email',

            'otp' =>
            'required',

            'newPassword' =>
            'required|min:6'
        ]);

        $user = NguoiDung::where(
            'email',
            $request->email
        )->first();

        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản'
            ], 404);
        }

        if (
            $user->maXacThuc !=
            $request->otp
        ) {

            return response()->json([
                'success' => false,
                'message' => 'OTP không đúng'
            ], 400);
        }

        if (
            now()->gt(
                $user->thoiGianXacThuc
            )
        ) {

            return response()->json([
                'success' => false,
                'message' => 'OTP đã hết hạn'
            ], 400);
        }

        $user->update([

            'matKhau' =>
            Hash::make(
                $request->newPassword
            ),

            'maXacThuc' =>
            null,

            'thoiGianXacThuc' =>
            null
        ]);

        return response()->json([
            'success' => true,
            'message' =>
            'Đổi mật khẩu thành công'
        ]);
    }
}
