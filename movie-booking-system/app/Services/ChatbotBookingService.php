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
                    mb_strtolower($movie->tieuDe),
                    mb_strtolower($message)
                )
            ) {

                return $movie;
            }
        }

        return null;
    }

    public function handle(
        string $message,
        ?int $userId = null,
        array $aiIntent = []
    ) {
        $session =
            $this->sessionService
            ->getOrCreate($userId);

        $movieName =
            $aiIntent['movie']
            ?? null;

        $movie = null;

        if ($movieName) {

            $movie =
                $this->findMovie(
                    $movieName
                );
        }

        if (!$movie) {

            $movie =
                $this->findMovie(
                    $message
                );
        }

        if (!$movie) {

            return [

                'type' =>
                'booking',

                'action' =>
                'ask_movie',

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

        if (
            !empty($aiIntent['quantity'])
        ) {

            $this->sessionService
                ->setData(
                    $session->maPhien,
                    'quantity',
                    $aiIntent['quantity']
                );
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

        $this->sessionService
            ->setData(
                $session->maPhien,
                'booking_step',
                'select_showtime'
            );

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
