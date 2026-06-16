<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\XuatChieu;

class ChatbotBookingService
{
    public function findMovie(
        string $message
    )
    {
        $movies = Phim::all();

        foreach ($movies as $movie) {

            if (
                str_contains(
                    mb_strtolower($message),
                    mb_strtolower($movie->tieuDe)
                )
            ) {

                return $movie;
            }
        }

        return null;
    }

    public function handle(
        string $message
    )
    {
        $movie =
            $this->findMovie($message);

        if (!$movie) {

            return [
                'type' => 'booking',
                'action' => 'ask_movie',
                'reply' =>
                    'Bạn muốn đặt vé phim nào?'
            ];
        }

        $showtimes =
            XuatChieu::with([
                'phim',
                'phongChieu.rapChieu'
            ])
            ->where(
                'maPhim',
                $movie->maPhim
            )
            ->where(
                'trangThai',
                'Sap_Chieu'
            )
            ->orderBy(
                'thoiGianBatDau'
            )
            ->get();

        return [
            'type' =>
                'booking_showtimes',

            'movieId' =>
                $movie->maPhim,

            'movieTitle' =>
                $movie->tieuDe,

            'reply' =>
                "🎟️ Các suất chiếu của {$movie->tieuDe}",

            'showtimes' =>
                $showtimes
        ];
    }
}
