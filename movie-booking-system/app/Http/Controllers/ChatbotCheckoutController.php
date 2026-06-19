<?php

namespace App\Http\Controllers;

use App\Services\ChatbotSessionService;
use Illuminate\Support\Facades\Auth;
use App\Models\XuatChieu;
use App\Models\Ghe;
use App\Models\GiaVe;
use Carbon\Carbon;

class ChatbotCheckoutController extends Controller
{
    public function info()
    {
        $session =
            app(ChatbotSessionService::class)
            ->getOrCreate(Auth::id());

        $data =
            json_decode(
                $session->duLieu,
                true
            );

        $showtime =
            XuatChieu::with([
                'phim',
                'phongChieu.rapChieu'
            ])
            ->find(
                $session->xuatChieuDangChon
            );

        $seatIds =
            $data['selected_seat_ids']
            ?? [];

        $seats =
            Ghe::whereIn(
                'maGhe',
                $seatIds
            )->get();

        $gioChieu =
            Carbon::parse(
                $showtime->thoiGianBatDau
            )->format('H:i:s');

        $giaVeModel =
            GiaVe::where(
                'gioBatDau',
                '<=',
                $gioChieu
            )
            ->where(
                'gioKetThuc',
                '>',
                $gioChieu
            )
            ->first();

        $giaVe =
            $giaVeModel?->gia ?? 0;

        return response()->json([

            'xuatChieu' =>
            $showtime,

            'selectedSeats' =>
            $seats,

            'giaVe' =>
            $giaVe,

            'seatPrice' =>
            $giaVe * count($seats),

            'total' =>
            $giaVe * count($seats),

            'foods' => [],

            'cart' => []
        ]);
    }
}
