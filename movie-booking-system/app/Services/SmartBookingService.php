<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\RapChieu;
use App\Models\XuatChieu;
use App\Models\Ve;
use App\Models\Ghe;
use Carbon\Carbon;

class SmartBookingService
{
    public function handle(array $aiIntent)
    {
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
                $aiIntent['date'] ?? null
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

        return [

            'type' => 'smart_booking',

            'movie' => $movie,

            'cinema' => $rap,

            'showtime' => $showtime,

            'seats' => $seats,

            'reply' =>
            '🎟️ Tôi đã tìm được suất chiếu và ghế đẹp nhất cho bạn.'
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
        ?string $date
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
                function ($showtime)
                use ($rapId) {

                    return
                        $showtime
                        ->phongChieu
                        ->maRap
                        ==
                        $rapId;
                }
            );

        if ($date === 'toi_nay') {

            $showtimes =
                $showtimes->filter(
                    fn($s)
                    =>
                    $this->isTonight($s)
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
