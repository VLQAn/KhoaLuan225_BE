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

    protected $datVeService;

    public function __construct(
        ChatbotSessionService $sessionService,
        DatVeService $datVeService
    ) {
        $this->sessionService =
            $sessionService;

        $this->datVeService =
            $datVeService;
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

        // Parse duLieu JSON string đúng cách - handle both array and string
        $data = is_array($session->duLieu)
            ? $session->duLieu
            : json_decode(
                $session->duLieu ?? '{}',
                true
            );

        $step =
            $data['booking_step']
            ?? null;

        Log::info('BOOKING_HANDLE', [
            'step' => $step,
            'message' => $message
        ]);

        if (
            $this->isCancelMessage(
                $message
            )
        ) {

            $this->sessionService
                ->clearSession(
                    $session->maPhien
                );

            Log::info('BOOKING_SESSION_RESET');

            return [

                'type' =>
                'booking_cancel',

                'reply' =>
                '✅ Đã hủy phiên đặt vé hiện tại. Bạn muốn đặt vé phim nào?'
            ];
        }

        if (
            $step &&
            $this->isNewBookingRequest(
                $message
            )
        ) {

            $this->sessionService
                ->clearSession(
                    $session->maPhien
                );

            $session =
                $this->sessionService
                ->getOrCreate(
                    $userId
                );

            $step = null;

            Log::info('BOOKING_RESTART');
        }

        switch ($step) {
            case 'select_showtime':

                return
                    $this->handleShowtimeSelection(
                        $message,
                        $session
                    );

            case 'select_seat':

                return
                    $this->handleSeatSelection(
                        $message,
                        $session
                    );

            case 'confirm_booking':

                return
                    $this->handleConfirmBooking(
                        $message,
                        $session,
                        $userId
                    );

            default:

                return
                    $this->handleBookingStart(
                        $message,
                        $session,
                        $aiIntent
                    );
        }
    }

    //  handleBookingStart()
    private function handleBookingStart(
        string $message,
        $session,
        array $aiIntent
    ) {
        Log::info('AI_MOVIE', [
            'movie' => $aiIntent['movie'] ?? null
        ]);

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

        $quantity = $aiIntent['quantity'] ?? null;
        
        // Fallback: Nếu OpenAI không trả quantity, extract từ message
        if (empty($quantity)) {
            $quantity = $this->extractQuantityFromMessage($message);
            Log::info('FALLBACK_QUANTITY_IN_BOOKING', [
                'message' => $message,
                'extracted' => $quantity
            ]);
        }

        if (!empty($quantity)) {

            Log::info('QUANTITY_SET_FROM_AI_INTENT', [
                'quantity' => $quantity,
                'movie' => $aiIntent['movie'] ?? null
            ]);

            $this->sessionService
                ->setData(
                    $session->maPhien,
                    'quantity',
                    $quantity
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

        // Log quantity đã được lưu
        $savedData = is_array($session->duLieu)
            ? $session->duLieu
            : json_decode(
                $session->duLieu ?? '{}',
                true
            );
        Log::info('BOOKING_START_SAVED_DATA', [
            'quantity' => $savedData['quantity'] ?? null,
            'movie' => $movie->tieuDe
        ]);

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

        // Parse duLieu JSON để lấy quantity - handle both array and string
        $sessionData = is_array($session->duLieu)
            ? $session->duLieu
            : json_decode(
                $session->duLieu ?? '{}',
                true
            );
        $quantity = $sessionData['quantity'] ?? 1;

        Log::info('SHOWTIME_SELECTED_QUANTITY', [
            'quantity' => $quantity,
            'showtimeId' => $showtime->maXuatChieu
        ]);

        return [

            'type' =>
            'booking_select_seat',

            'showtimeId' =>
            $showtime->maXuatChieu,

            'quantity' =>
            $quantity,

            'reply' =>
            '🎟️ Đã chọn suất chiếu. Vui lòng chọn ghế.'
        ];
    }

    // findMovie()
    public function findMovie(string $message)
    {
        Log::info('FIND_MOVIE', [
            'search' => $message
        ]);

        $message = mb_strtolower(trim($message));

        // loại bỏ từ khóa đặt vé
        $message = preg_replace(
            '/đặt\s*\d*\s*vé|dat\s*\d*\s*ve/ui',
            '',
            $message
        );

        $message = trim($message);

        $movies = Phim::all();

        foreach ($movies as $movie) {

            $title =
                mb_strtolower(
                    trim($movie->tieuDe)
                );

            if (
                str_contains(
                    $title,
                    $message
                )
            ) {
                return $movie;
            }

            if (
                str_contains(
                    $message,
                    $title
                )
            ) {
                return $movie;
            }
        }

        return null;
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

    private function isConfirmMessage(
        string $message
    ): bool {

        $message =
            mb_strtolower(
                trim($message)
            );

        $confirmWords = [

            'ok',
            'oke',

            'dong y',
            'đồng ý',

            'xac nhan',
            'xác nhận',

            'dat ve',
            'đặt vé',

            'yes'
        ];

        return
            str_contains($message, 'xac nhan')
            ||
            str_contains($message, 'xác nhận')
            ||
            str_contains($message, 'dong y')
            ||
            str_contains($message, 'đồng ý');
    }

    private function isCancelMessage(
        string $message
    ): bool {
        $message =
            mb_strtolower(
                trim($message)
            );

        $cancelWords = [

            'huy',
            'hủy',

            'huy dat ve',
            'hủy đặt vé',

            'thoat',
            'thoát',

            'bat dau lai',
            'bắt đầu lại',

            'reset',

            'dat ve moi',
            'đặt vé mới'
        ];

        return in_array(
            $message,
            $cancelWords
        );
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

            // Tìm ghế trước
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

            // Kiểm tra ghế đã được đặt chưa
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

            $seatIds[] =
                $seat->maGhe;
        }

        // Parse duLieu to get quantity - handle both array and string
        $sessionDataForQuantity = is_array($session->duLieu)
            ? $session->duLieu
            : json_decode(
                $session->duLieu ?? '{}',
                true
            );
        $quantity =
            $sessionDataForQuantity['quantity']
            ?? 1;

        if (
            count($seats)
            != $quantity
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

    private function handleConfirmBooking(
        string $message,
        $session,
        ?int $userId = null
    ) {
        if (
            !$this->isConfirmMessage(
                $message
            )
        ) {
            return [

                'type' =>
                'booking',

                'reply' =>
                'Vui lòng nhập "xác nhận" để hoàn tất đặt vé.'
            ];
        }

        // Parse duLieu for confirm booking - handle both array and string
        $data = is_array($session->duLieu)
            ? $session->duLieu
            : json_decode(
                $session->duLieu ?? '{}',
                true
            );

        try {

            $result =
                $this->datVeService
                ->datVe([
                    'maNguoiDung' =>
                    $userId,

                    'maXuatChieu' =>
                    $session->xuatChieuDangChon,

                    'danhSachGhe' =>
                    $data['selected_seat_ids']
                        ?? []
                ]);

            $this->sessionService
                ->setData(
                    $session->maPhien,
                    'maHoaDon',
                    $result['hoaDon']->maHoaDon
                );

            $this->sessionService
                ->setData(
                    $session->maPhien,
                    'booking_step',
                    'payment'
                );

            return [

                'type' =>
                'booking_payment',

                'invoiceId' =>
                $result['hoaDon']->maHoaDon,

                'total' =>
                $result['tongThanhToan'],

                'reply' =>
                '🎟️ Đặt vé thành công. Tổng tiền: '
                    . number_format(
                        $result['tongThanhToan']
                    )
                    . ' VNĐ'
            ];
        } catch (\Exception $e) {

            return [

                'type' =>
                'booking',

                'reply' =>
                $e->getMessage()
            ];
        }
    }

    private function isNewBookingRequest(
        string $message
    ): bool {
        $message =
            mb_strtolower(trim($message));

        return
            preg_match(
                '/đặt.*vé/u',
                $message
            )
            ||
            preg_match(
                '/dat.*ve/i',
                $message
            );
    }

    private function findShowtimeByDate(
        string $message,
        $showtimes
    ) {
        // xử lý sau
    }

    /**
     * Extract số lượng vé từ message
     * Ví dụ: "đặt 2 vé", "mua 3 vé", "tôi muốn 5 vé"
     */
    private function extractQuantityFromMessage(string $message): ?int
    {
        $lowerMessage = mb_strtolower($message);

        // Pattern: số trước từ khóa vé
        if (preg_match('/(\d+)\s*(?:cai|chiếc|tấm|ve|vé|ticket|vé phim|vé xem|vé chiếu|bộ vé)/u', $lowerMessage, $matches)) {
            return (int) $matches[1];
        }

        // Pattern: từ khóa trước số
        if (preg_match('/(đặt|mua|book|order|buy)?\s*(\d+)\s*(?:cai|chiếc|tấm|ve|vé|ticket)/u', $lowerMessage, $matches)) {
            return (int) $matches[2];
        }

        return null;
    }
}
