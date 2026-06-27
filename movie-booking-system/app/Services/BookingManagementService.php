<?php

namespace App\Services;

use App\Models\HoaDon;

class BookingManagementService
{
    public function getAll(int $maNguoiDung)
    {
        $hoaDons = HoaDon::with([
            'thanhToan',
            'khuyenMai',
            'ves.ghe',
            'ves.xuatChieu.phim',
            'ves.xuatChieu.phongChieu.rapChieu'
        ])
            ->whereHas('ves.xuatChieu.phongChieu.rapChieu', function ($query) use ($maNguoiDung) {
                $query->where('maNguoiDung', $maNguoiDung);
            })
            ->orderByDesc('maHoaDon')
            ->get();

        return $hoaDons->map(function ($hoaDon) {

            $firstVe = $hoaDon->ves->first();

            if (!$firstVe) {
                return null;
            }

            $xuatChieu = $firstVe->xuatChieu;
            $phong = $xuatChieu?->phongChieu;
            $rap = $phong?->rapChieu;

            return [

                'id' => $hoaDon->maHoaDon,

                'movie' =>
                $xuatChieu?->phim?->tieuDe,

                'theater' =>
                $rap?->tenRap,

                'address' =>
                $rap?->diaChi,

                'room' =>
                $phong?->tenPhong,

                'showtime' =>
                optional(
                    $xuatChieu?->thoiGianBatDau
                )->format(
                    'H:i - d/m/Y'
                ),

                'seats' =>
                $hoaDon->ves
                    ->map(function ($ve) {

                        return
                            $ve->ghe->hangGhe .
                            $ve->ghe->soGhe;
                    })
                    ->values(),

                // ===== GIÁ VÉ =====

                'ticketPrice' =>
                $hoaDon->ves->sum('gia'),

                // ===== BẮP NƯỚC =====

                'food' =>
                'Không có',

                'foodPrice' =>
                0,

                // ===== KHUYẾN MÃI =====

                'promotionCode' =>
                $hoaDon->khuyenMai?->maCode,

                'promotionPercent' =>
                $hoaDon->khuyenMai
                    ? (int) $hoaDon->khuyenMai->giaKhuyenMai
                    : null,

                // ===== THANH TOÁN =====

                'paymentMethod' =>
                $hoaDon->thanhToan?->phuongThucThanhToan,

                'totalPrice' =>
                $hoaDon->tongTien,

                'totalPayment' =>
                $hoaDon->tongThanhToan,

                'status' =>
                match ($hoaDon->trangThai) {

                    'Dang_Thanh_Toan'
                    => 'paying',

                    'Da_Thanh_Toan'
                    => 'paid',

                    'Da_Huy'
                    => 'cancelled',

                    default
                    => 'paying'
                }
            ];
        })
            ->filter()
            ->values();
    }
}
