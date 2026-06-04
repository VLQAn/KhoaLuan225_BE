<?php

namespace App\Http\Controllers\Api;

use App\Models\HoaDon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $hoaDons = HoaDon::with([
            'ves.ghe',
            'ves.xuatChieu.phim',
            'ves.xuatChieu.phongChieu.rapChieu',
            'bapNuocs.mon'
        ])
        ->where('maNguoiDung', $userId)
        ->orderByDesc('maHoaDon')
        ->get();

        $result = $hoaDons->map(function ($hoaDon) {

            $veDauTien =
                $hoaDon->ves->first();

            if (!$veDauTien) {
                return null;
            }

            $xuatChieu =
                $veDauTien->xuatChieu;

            $phim =
                $xuatChieu?->phim;

            $rap =
                $xuatChieu?->phongChieu?->rapChieu;

            return [

                'id' =>
                    $hoaDon->maHoaDon,

                'movie' =>
                    $phim?->tieuDe,

                'poster' =>
                    $phim?->anhPoster,

                'cinema' =>
                    $rap?->tenRap,

                'add' =>
                    $rap?->diaChi,

                'time' =>
                    $xuatChieu?->thoiGianBatDau
                        ? $xuatChieu
                            ->thoiGianBatDau
                            ->format('H:i - d/m/Y')
                        : null,

                'seats' =>
                    $hoaDon->ves
                        ->map(fn($ve) =>
                            $ve->ghe->hangGhe .
                            $ve->ghe->soGhe
                        )
                        ->values(),

                'food' =>
                    $hoaDon->bapNuocs
                        ->map(fn($item) =>
                            $item->mon?->tenMon
                        )
                        ->filter()
                        ->values(),

                'total' =>
                    $hoaDon->tongThanhToan,

                'status' =>
                    $hoaDon->trangThai,
            ];
        })
        ->filter()
        ->values();

        return ApiResponse::success(
            $result,
            'Lịch sử đặt vé'
        );
    }
}
