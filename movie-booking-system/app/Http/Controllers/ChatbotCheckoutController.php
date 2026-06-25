<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\BapNuoc;
use Illuminate\Support\Facades\Auth;

class ChatbotCheckoutController extends Controller
{
    public function info($maHoaDon)
    {
        $hoaDon = HoaDon::with([
            'ves.ghe',
            'ves.xuatChieu.phim',
            'ves.xuatChieu.phongChieu.rapChieu',
            'bapNuocs'
        ])->find($maHoaDon);

        if (!$hoaDon) {
            return response()->json([
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        if ($hoaDon->maNguoiDung !== Auth::id()) {
            return response()->json([
                'message' => 'Bạn không có quyền xem hóa đơn này'
            ], 403);
        }

        $xuatChieu = $hoaDon->ves->first()?->xuatChieu;

        $seats = $hoaDon->ves
            ->map(fn($ve) => $ve->ghe)
            ->filter()
            ->values();

        $giaVe = $hoaDon->ves->first()?->gia ?? 0;

        $seatPrice = $hoaDon->ves->sum('gia');

        $monIds = $hoaDon->bapNuocs->pluck('maMon');

        $monAns = BapNuoc::whereIn('maMon', $monIds)
            ->get()
            ->keyBy('maMon');

        $foods = $hoaDon->bapNuocs->map(function ($item) use ($monAns) {

            $mon = $monAns->get($item->maMon);

            return [
                'maMon' => $item->maMon,
                'tenMon' => $mon?->tenMon,
                'gia' => $item->donGia
            ];
        })->values();

        $cart = $hoaDon->bapNuocs
            ->pluck('soLuong', 'maMon')
            ->toArray();

        return response()->json([

            'invoiceId' => $hoaDon->maHoaDon,

            'xuatChieu' => $xuatChieu,

            'selectedSeats' => $seats,

            'giaVe' => $giaVe,

            'seatPrice' => $seatPrice,

            'total' => $hoaDon->tongThanhToan,

            'foods' => $foods,

            'cart' => $cart,

            'trangThai' => $hoaDon->trangThai,

            'chatbotBooking' => true
        ]);
    }
}
