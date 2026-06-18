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

        if (
            $this->isSelectingShowtime(
                $session
            )
        ) {

            return $this->handleShowtimeSelection(
                $message,
                $session
            );
        }

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
            "🎟️ Hiện {$movie->tieuDe} đang có các xuất chiếu",

            'showtimes' =>
            $showtimes
        ];
    }

    private function isSelectingShowtime(
        $session
    ) {
        $data =
            $session->duLieu
            ?? [];

        return ($data['booking_step']
            ?? null)
            ===
            'select_showtime';
    }

    private function extractNumber(
        string $message
    ) {
        preg_match(
            '/\d+/',
            $message,
            $matches
        );

        return
            $matches[0]
            ?? null;
    }

    private function handleShowtimeSelection(
        string $message,
        $session
    ) {
        $number =
            $this->extractNumber(
                $message
            );

        if (!$number) {

            return [

                'type' =>
                'booking',

                'reply' =>
                'Vui lòng chọn số thứ tự suất chiếu.'
            ];
        }

        $movieId =
            $session->phimDangChon;

        $showtimes =
            XuatChieu::where(
                'maPhim',
                $movieId
            )
            ->where(
                'trangThai',
                'Sap_Chieu'
            )
            ->orderBy(
                'thoiGianBatDau'
            )
            ->get();

        $index =
            $number - 1;

        if (
            !isset(
                $showtimes[$index]
            )
        ) {

            return [

                'type' =>
                'booking',

                'reply' =>
                'Số thứ tự không hợp lệ.'
            ];
        }

        $showtime =
            $showtimes[$index];

        $this->sessionService
            ->setShowtime(
                $session->maPhien,
                $showtime->maXuatChieu
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'booking_step',
                'select_seat'
            );

        return [

            'type' =>
            'booking_select_seat',

            'showtimeId' =>
            $showtime->maXuatChieu,

            'reply' =>
            '🎟️ Đã chọn suất chiếu. Chuyển sang chọn ghế.'
        ];
    }
}
