<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\KhuyenMai;
use App\Models\XuatChieu;

class ChatbotContextService
{
    public function buildContext()
    {
        return [

            'movies' => Phim::select(
                'maPhim',
                'tieuDe',
                'trangThai'
            )->get(),

            'promotions' => KhuyenMai::where(
                'trangThai',
                'Hoat_Dong'
            )->get(),

            'showtimes' => XuatChieu::with(
                'phim',
                'phongChieu.rapChieu'
            )
                ->where(
                    'trangThai',
                    'Sap_Chieu'
                )
                ->limit(50)
                ->get()
        ];
    }
}
