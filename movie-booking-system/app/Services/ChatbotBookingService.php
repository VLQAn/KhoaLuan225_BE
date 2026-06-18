<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\XuatChieu;
use App\Services\ChatbotSessionService;
use Illuminate\Support\Facades\Log;

class ChatbotBookingService
{

    protected $sessionService;

    public function __construct(
        ChatbotSessionService $sessionService
    ) {

        $this->sessionService =
            $sessionService;
    }

    // handle()
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

        return $this->handleBookingStart(
            $message,
            $session,
            $aiIntent
        );
    }

    //  handleBookingStart()
    private function handleBookingStart(
        string $message,
        $session,
        array $aiIntent
    ) {
        $movieName =
            $aiIntent['movie']
            ?? null;

        $movie = null;

        if ($movieName) {

            $movie =
                $this->findMovie(
                    $movieName
                );

            Log::info('BOOKING_MOVIE', [
                'movieId' => $movie?->maPhim,
                'title'   => $movie?->tieuDe
            ]);
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
            "🎟️ Hiện {$movie->tieuDe} đang có các suất chiếu",

            'showtimes' =>
            $showtimes
        ];
    }

    // findSelectedShowtime()
    private function findSelectedShowtime(
        string $message,
        $showtimes
    ) {
        $selection =
            $this->extractShowtimeSelection(
                $message
            );

        Log::info('SHOWTIME_SELECTION', [
            'message' => $message,
            'selection' => $selection,
            'count' => $showtimes->count()
        ]);

        if (!$selection) {
            return null;
        }

        $index =
            $selection - 1;

        if (
            !isset(
                $showtimes[$index]
            )
        ) {
            return null;
        }

        return $showtimes[$index];
    }

    // handleShowtimeSelection()
    private function handleShowtimeSelection(
        string $message,
        $session
    ) {

        $movieId =
            $session->phimDangChon;

        $showtimes =
            XuatChieu::where(
                'maPhim',
                $movieId
            )
            ->where(
                'trangThai',
                'sap_Chieu'
            )
            ->orderBy(
                'thoiGianBatDau'
            )
            ->get();

        Log::info('MOVIE_ID', [
            'movieId' => $movieId
        ]);

        Log::info('SHOWTIMES_FOUND', [
            'count' => $showtimes->count()
        ]);

        $showtime =
            $this->findSelectedShowtime(
                $message,
                $showtimes
            );

        if (!$showtime) {

            return [

                'type' =>
                'booking',

                'reply' =>
                'Không xác định được suất chiếu. Vui lòng chọn lại.'
            ];
        }

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

            'quantity' =>
            $session->duLieu['quantity']
                ?? 1,

            'reply' =>
            '🎟️ Đã chọn suất chiếu. Vui lòng chọn ghế.'
        ];
    }

    // findMovie()
    public function findMovie(
        string $message
    ) {
        $movies = Phim::all();

        $message =
            mb_strtolower(trim($message));

        foreach ($movies as $movie) {

            $title =
                mb_strtolower($movie->tieuDe);

            if (
                str_contains(
                    $title,
                    $message
                )
            ) {

                return $movie;
            }
        }

        return null;
    }

    // isSelectingShowtime()
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

    // extractShowtimeSelection()
    private function extractShowtimeSelection(
        string $message
    ): ?int {

        $message =
            mb_strtolower(
                trim($message)
            );

        // chỉ nhập số
        if (
            preg_match(
                '/^\d+$/',
                $message
            )
        ) {
            return (int) $message;
        }

        // chọn 2
        // xuất 2
        // suất số 2
        // mình chọn xuất 2
        if (
            preg_match(
                '/(?:chon|chon xuat|xuat|suat)?\s*(?:so\s*)?(\d+)/u',
                $message,
                $matches
            )
        ) {

            return (int) $matches[1];
        }

        return null;
    }

    private function findShowtimeByDate(
        string $message,
        $showtimes
    ) {
        // xử lý sau
    }
}
