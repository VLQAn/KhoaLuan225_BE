<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\XuatChieu;
use App\Services\ChatbotSessionService;

class ChatbotBookingService
{
    protected $sessionService;

    public function __construct(
        ChatbotSessionService $sessionService
    ) {

        $this->sessionService =
            $sessionService;
    }

    public function findMovie(
        string $message
    ) {
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
        string $message,
        ?int $userId = null
    ) {
        $session =
            $this->sessionService
            ->getOrCreate($userId);

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

        // lưu phim đang chọn
        $this->sessionService
            ->setMovie(
                $session->maPhien,
                $movie->maPhim
            );

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
