<?php

namespace App\Services;

use App\Models\HoaDon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

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

     public function cancel(int $maHoaDon, int $maNguoiDung)
    {
        return DB::transaction(function () use ($maHoaDon, $maNguoiDung) {

            $hoaDon = HoaDon::with('ves.xuatChieu')
                ->where('maHoaDon', $maHoaDon)
                ->where('maNguoiDung', $maNguoiDung)
                ->first();

            if (!$hoaDon) {
                throw new Exception(
                    'Không tìm thấy hóa đơn hoặc bạn không có quyền hủy vé này'
                );
            }

            if ($hoaDon->trangThai === 'Da_Huy') {
                throw new Exception('Vé này đã được hủy trước đó');
            }

            $xuatChieu = $hoaDon->ves->first()?->xuatChieu;

            if (!$xuatChieu) {
                throw new Exception('Không tìm thấy thông tin xuất chiếu');
            }

            // Chưa đến giờ chiếu mới được hủy
            if (Carbon::now()->greaterThanOrEqualTo($xuatChieu->thoiGianBatDau)) {
                throw new Exception('Không thể hủy vé vì đã đến giờ chiếu');
            }

            // Hủy từng vé -> trả ghế lại cho người khác đặt được
            foreach ($hoaDon->ves as $ve) {
                $ve->update(['trangThai' => 'Da_Huy']);
            }

            $hoaDon->update(['trangThai' => 'Da_Huy']);

            return $hoaDon;
        });
    }
}
