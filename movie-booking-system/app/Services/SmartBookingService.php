<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\RapChieu;
use App\Models\XuatChieu;
use App\Models\Ve;
use App\Models\Ghe;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\ChatbotSessionService;
use App\Services\XuatChieuService;

class SmartBookingService
{
    protected $sessionService;
    protected $xuatChieuService;

    public function __construct(
        ChatbotSessionService $sessionService,
        XuatChieuService $xuatChieuService
    ) {
        $this->sessionService = $sessionService;
        $this->xuatChieuService = $xuatChieuService;
    }

    public function handle(
        array $aiIntent,
        ?int $userId = null
    ) {
        $session =
            $this->sessionService
            ->getOrCreate($userId);

        $movie =
            $this->findMovie(
                $aiIntent['movie'] ?? null
            );

        if (!$movie) {

            return [

                'type' => 'movie_not_found',

                'reply' =>
                'Không tìm thấy phim.'
            ];
        }

        if (empty($aiIntent['city'])) {

            return [

                'type' => 'ask_city',

                'movie' => $movie->tieuDe,

                'reply' =>
                'Bạn muốn xem phim ở thành phố nào?'
            ];
        }

        $raps =
            $this->findRapsByCity(
                $aiIntent['city']
            );

        if ($raps->isEmpty()) {

            return [

                'type' => 'cinema_not_found',

                'reply' =>
                'Không tìm thấy rạp tại khu vực này.'
            ];
        }

        if (empty($aiIntent['cinema'])) {

            return [

                'type' => 'ask_cinema',

                'city' =>
                $aiIntent['city'],

                'cinemas' =>
                $raps,

                'reply' =>
                'Bạn muốn xem ở rạp nào?'
            ];
        }

        $rap =
            $this->findCinema(
                $raps,
                $aiIntent['cinema']
            );

        if (!$rap) {

            return [

                'type' => 'cinema_not_found',

                'reply' =>
                'Không tìm thấy rạp phù hợp.'
            ];
        }

        $showtime =
            $this->findBestShowtime(
                $movie->maPhim,
                $rap->maRap,
                $aiIntent['date'] ?? null,
                $aiIntent['time_period'] ?? null
            );

        if (!$showtime) {

            return [

                'type' => 'showtime_not_found',

                'reply' =>
                'Không tìm thấy suất chiếu phù hợp.'
            ];
        }

        $quantity =
            $aiIntent['quantity'] ?? 1;

        $seats =
            $this->findBestSeats(
                $showtime,
                $quantity
            );

        $this->sessionService
            ->setMovie(
                $session->maPhien,
                $movie->maPhim
            );

        $this->sessionService
            ->setShowtime(
                $session->maPhien,
                $showtime->maXuatChieu
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'selected_seat_ids',
                $seats->pluck('maGhe')->toArray()
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'selected_seats',
                $seats->map(
                    fn($s)
                    =>
                    $s->hangGhe . $s->soGhe
                )->toArray()
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'quantity',
                $quantity
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'booking_step',
                'smart_booking_ready'
            );
        $this->sessionService
            ->setData(
                $session->maPhien,
                'movie_id',
                $movie->maPhim
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'showtime_id',
                $showtime->maXuatChieu
            );

        $this->sessionService
            ->setData(
                $session->maPhien,
                'cinema_id',
                $rap->maRap
            );

        $session->refresh();

        Log::info('SMART_BOOKING_SESSION_AFTER_SAVE', [
            'session_id' => $session->maPhien,
            'movie' => $session->phimDangChon,
            'showtime' => $session->xuatChieuDangChon,
            'data' => $session->duLieu
        ]);

        return [

            'type' => 'smart_booking_checkout',

            'movie' => $movie,

            'cinema' => $rap,

            'showtime' => $showtime,

            'quantity' => $quantity,

            'seats' => $seats,

            'checkoutUrl' => '/checkout',

            'reply' =>
            '🎟️ Tôi đã tìm được suất chiếu phù hợp. Nhấn để xem thông tin đặt vé.'
        ];
    }

    private function findMovie(
        ?string $movieName
    ) {
        if (!$movieName) {
            return null;
        }

        return Phim::where(
            'tieuDe',
            'like',
            '%' . $movieName . '%'
        )->first();
    }

    private function findRapsByCity(
        string $city
    ) {
        return RapChieu::where(
            'diaChi',
            'like',
            '%' . $city . '%'
        )->get();
    }

    private function findCinema(
        $raps,
        string $cinema
    ) {
        return $raps->first(
            function ($rap)
            use ($cinema) {

                return str_contains(
                    mb_strtolower(
                        $rap->tenRap
                    ),
                    mb_strtolower(
                        $cinema
                    )
                );
            }
        );
    }

    private function findBestShowtime(
        int $movieId,
        int $rapId,
        ?string $date,
        ?string $timePeriod = null
    ) {
        $showtimes =
            XuatChieu::with(
                'phongChieu.rapChieu'
            )
            ->where(
                'maPhim',
                $movieId
            )
            ->where(
                'trangThai',
                'Sap_Chieu'
            )
            ->get();

        $showtimes =
            $showtimes->filter(
                fn($s)
                =>
                $s->phongChieu->maRap == $rapId
            );

        /*
    |--------------------------------------------------------------------------
    | Filter theo ngày
    |--------------------------------------------------------------------------
    */
        if ($date) {

            $targetDate =
                $this->xuatChieuService
                ->resolveDate($date);

            if ($targetDate) {

                $showtimes =
                    $showtimes->filter(
                        function ($s)
                        use ($targetDate) {

                            return Carbon::parse(
                                $s->thoiGianBatDau
                            )->isSameDay(
                                $targetDate
                            );
                        }
                    );
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Filter theo buổi
    |--------------------------------------------------------------------------
    */
        if ($timePeriod) {

            $showtimes =
                $showtimes->filter(
                    function ($s)
                    use ($timePeriod) {

                        $hour =
                            Carbon::parse(
                                $s->thoiGianBatDau
                            )->hour;

                        return match ($timePeriod) {

                            'morning'
                            =>
                            $hour >= 6
                                && $hour < 12,

                            'afternoon'
                            =>
                            $hour >= 12
                                && $hour < 18,

                            'evening'
                            =>
                            $hour >= 18,

                            default => true
                        };
                    }
                );
        }

        return $showtimes
            ->sortBy(
                function ($s) {

                    $hour =
                        Carbon::parse(
                            $s->thoiGianBatDau
                        )->hour;

                    return abs(
                        $hour - 20
                    );
                }
            )
            ->first();
    }

    private function resolveDate(
        ?string $date
    ) {
        if (!$date) {
            return null;
        }

        return match ($date) {

            'today'
            => Carbon::today(),

            'tomorrow'
            => Carbon::tomorrow(),

            'Monday'
            => Carbon::parse('next monday'),

            'Tuesday'
            => Carbon::parse('next tuesday'),

            'Wednesday'
            => Carbon::parse('next wednesday'),

            'Thursday'
            => Carbon::parse('next thursday'),

            'Friday'
            => Carbon::parse('next friday'),

            'Saturday'
            => Carbon::parse('next saturday'),

            'Sunday'
            => Carbon::parse('next sunday'),

            'weekend'
            => Carbon::parse('next saturday'),

            default
            => $this->parseCustomDate(
                $date
            )
        };
    }

    private function parseCustomDate(
        string $date
    ) {
        if (
            preg_match(
                '/(\d{1,2})\/(\d{1,2})/',
                $date,
                $matches
            )
        ) {

            $day =
                (int)$matches[1];

            $month =
                (int)$matches[2];

            return Carbon::create(
                now()->year,
                $month,
                $day
            );
        }

        return null;
    }

    private function isTonight(
        $showtime
    ) {
        $time =
            Carbon::parse(
                $showtime->thoiGianBatDau
            );

        return
            $time->isToday()
            &&
            $time->hour >= 18;
    }

    private function findBestSeats(
        XuatChieu $showtime,
        int $quantity
    ) {
        $allSeats =
            Ghe::where(
                'maPhong',
                $showtime->maPhong
            )->get();

        $booked =
            Ve::where(
                'maXuatChieu',
                $showtime->maXuatChieu
            )
            ->whereIn(
                'trangThai',
                [
                    'Dang_Chon',
                    'Da_Dat'
                ]
            )
            ->pluck(
                'maGhe'
            )
            ->toArray();

        $available =
            $allSeats
            ->whereNotIn(
                'maGhe',
                $booked
            );

        $available =
            $available
            ->sortBy(
                function ($seat) {

                    $rowScore =
                        abs(
                            ord(
                                strtoupper(
                                    $seat->hangGhe
                                )
                            )
                                -
                                ord('E')
                        );

                    $centerScore =
                        abs(
                            $seat->soGhe - 5
                        );

                    return
                        $rowScore * 10
                        +
                        $centerScore;
                }
            );

        return
            $available
            ->take($quantity)
            ->values();
    }
}
