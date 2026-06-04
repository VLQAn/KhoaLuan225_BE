<?php

namespace App\Http\Controllers\Api;

use App\Models\RapChieu;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;

class TheaterController extends Controller
{
    public function index()
    {
        $theaters = RapChieu::with('chuRap')
            ->get()
            ->map(function ($rap) {

                return [
                    'maRap' => $rap->maRap,
                    'tenRap' => $rap->tenRap,
                    'diaChi' => $rap->diaChi,

                    'thuongHieu' =>
                        $rap->chuRap?->tenNguoiDung,

                    'logo' =>
                        $rap->chuRap?->logo,

                    'hinhAnh' =>
                        $rap->chuRap?->hinhAnh,
                ];
            });

        return ApiResponse::success(
            $theaters,
            'Danh sách rạp'
        );
    }
}
