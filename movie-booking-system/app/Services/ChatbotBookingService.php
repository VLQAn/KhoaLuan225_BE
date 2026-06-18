<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\XuatChieu;
use App\Services\ChatbotSessionService;
use Illuminate\Support\Facades\Log;
use App\Models\Ghe;
use App\Models\Ve;

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

        $step =
            $session->duLieu['booking_step']
            ?? null;

        if ($step === 'select_showtime') {
            return $this->handleShowtimeSelection(
                $message,
                $session
            );
        }

        if ($step === 'select_seat') {
            return $this->handleSeatSelection(
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

    private function extractSeats(
        string $message
    ) {
        preg_match_all(
            '/[A-Z]\d+/i',
            strtoupper($message),
            $matches
        );

        return
            $matches[0]
            ?? [];
    }

    private function parseSeat(
        string $seatName
    ) {
        preg_match(
            '/([A-Z]+)(\d+)/',
            strtoupper($seatName),
            $matches
        );

        if (
            count($matches) < 3
        ) {
            return null;
        }

        return [

            'row' =>
            $matches[1],

            'number' =>
            (int) $matches[2]
        ];
    }

    private function handleSeatSelection(
        string $message,
        $session
    ) {
        $seats =
            $this->extractSeats(
                $message
            );

        if (empty($seats)) {
            return [
                'type' =>
                'booking',

                'reply' =>
                'Vui lòng chọn ghế. Ví dụ: A1 A2'
            ];
        }

        $showtime =
            XuatChieu::with(
                'phongChieu'
            )
            ->find(
                $session->xuatChieuDangChon
            );

        if (!$showtime) {

            return [

                'type' =>
                'booking',

                'reply' =>
                'Không tìm thấy suất chiếu.'
            ];
        }

        $seatIds = [];

        foreach ($seats as $seatName) {
            $seatIds[] =
                $seat->maGhe;

            $parsed =
                $this->parseSeat(
                    $seatName
                );

            if (!$parsed) {
                return [

                    'type' =>
                    'booking',

                    'reply' =>
                    "Ghế {$seatName} không hợp lệ."
                ];
            }

            $booked =
                Ve::where(
                    'maXuatChieu',
                    $session->xuatChieuDangChon
                )
                ->where(
                    'maGhe',
                    $seat->maGhe
                )
                ->whereIn(
                    'trangThai',
                    [
                        'Dang_Chon',
                        'Da_Dat'
                    ]
                )
                ->exists();

            if ($booked) {
                return [

                    'type' =>
                    'booking',

                    'reply' =>
                    "Ghế {$seatName} đã có người chọn."
                ];
            }

            $seat =
                Ghe::where(
                    'maPhong',
                    $showtime->maPhong
                )
                ->where(
                    'hangGhe',
                    $parsed['row']
                )
                ->where(
                    'soGhe',
                    $parsed['number']
                )
                ->first();

            if (!$seat) {
                return [

                    'type' =>
                    'booking',

                    'reply' =>
                    "Ghế {$seatName} không tồn tại."
                ];
            }
        }

        $quantity =
            $session->duLieu['quantity']
            ?? 1;

        if (
            count($seats)
            !=
            $quantity
        ) {
            return [
                'type' =>
                'booking',

                'reply' =>
                "Bạn đã đặt {$quantity} vé nên cần chọn {$quantity} ghế."
            ];
        }

        $this->sessionService
            ->setData(
                $session->maPhien,
                'selected_seats',
                $seats
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'selected_seat_ids',
                $seatIds
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'booking_step',
                'confirm_booking'
            );

        return [

            'type' =>
            'booking_confirm',

            'reply' =>
            '✅ Đã chọn ghế '
                . implode(', ', $seats)
                . '. Bạn có muốn xác nhận đặt vé không?'
        ];
    }

    private function findShowtimeByDate(
        string $message,
        $showtimes
    ) {
        // xử lý sau
    }
}
