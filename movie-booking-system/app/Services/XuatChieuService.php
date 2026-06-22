<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\XuatChieu;
use App\Models\Ve;
use App\Models\Ghe;
use App\Repositories\Interfaces\XuatChieuRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class XuatChieuService
{
    protected $xuatChieuRepository;

    public function __construct(
        XuatChieuRepositoryInterface
        $xuatChieuRepository
    ) {
        $this->xuatChieuRepository =
            $xuatChieuRepository;
    }

    public function getAllXuatChieu()
    {
        return $this->xuatChieuRepository->getAll();
    }

    public function getXuatChieuById($id)
    {
        return $this->xuatChieuRepository->getById($id);
    }

    public function updateXuatChieu(
        int $id,
        array $data
    ) {
        return DB::transaction(function ()
        use ($id, $data) {

            /**
             * Get showtime
             */
            $xuatChieu = XuatChieu::findOrFail($id);

            /**
             * Không cho sửa suất đã chiếu
             */
            if (
                in_array(
                    $xuatChieu->trangThai,
                    ['dang_chieu', 'da_chieu']
                )
            ) {
                throw new Exception(
                    'Không thể sửa suất chiếu này'
                );
            }

            /**
             * Movie ID
             */
            $maPhim = $data['maPhim']
                ?? $xuatChieu->maPhim;

            /**
             * Room ID
             */
            $maPhong = $data['maPhong']
                ?? $xuatChieu->maPhong;

            /**
             * Start time
             */
            $startTime = isset(
                $data['thoiGianBatDau']
            )
                ? Carbon::parse(
                    $data['thoiGianBatDau']
                )
                : $xuatChieu->thoiGianBatDau;

            /**
             * Get movie
             */
            $phim = Phim::findOrFail($maPhim);

            /**
             * Calculate end time
             */
            $endTime = $startTime
                ->copy()
                ->addMinutes(
                    $phim->thoiLuong
                );

            /**
             * Check overlap
             */
            $isConflict = $this
                ->xuatChieuRepository
                ->checkRoomScheduleConflict(
                    $maPhong,
                    $startTime,
                    $endTime,
                    $id
                );

            if ($isConflict) {
                throw new Exception(
                    'Phòng chiếu đã có lịch bị trùng'
                );
            }

            /**
             * Update
             */
            return $this
                ->xuatChieuRepository
                ->update($id, [
                    'maPhim' => $maPhim,
                    'maPhong' => $maPhong,
                    'thoiGianBatDau' => $startTime,
                    'thoiGianKetThuc' => $endTime,
                ]);
        });
    }

    public function deleteXuatChieu(
        int $id
    ) {
        return DB::transaction(function ()
        use ($id) {

            $xuatChieu = XuatChieu::findOrFail($id);

            /**
             * Không cho xóa suất đã chiếu
             */
            if (
                in_array(
                    $xuatChieu->trangThai,
                    ['dang_chieu', 'da_chieu']
                )
            ) {
                throw new Exception(
                    'Không thể xóa suất chiếu này'
                );
            }

            /**
             * Check booking
             */
            if (
                $xuatChieu
                ->chiTietDatVe()
                ->exists()
            ) {
                throw new Exception(
                    'Xuất chiếu đã có người đặt vé'
                );
            }

            /**
             * Delete
             */
            return $this
                ->xuatChieuRepository
                ->delete($id);
        });
    }

    /**
     * Create showtime
     */
    public function createXuatChieu(
        array $data
    ) {
        return DB::transaction(function ()
        use ($data) {

            /**
             * Get movie
             */
            $phim = Phim::findOrFail(
                $data['maPhim']
            );

            /**
             * Start time
             */
            $startTime = Carbon::parse(
                $data['thoiGianBatDau']
            );

            /**
             * Calculate end time
             */
            $endTime = $startTime
                ->copy()
                ->addMinutes(
                    $phim->thoiLuong
                );

            /**
             * Check overlap
             */
            $isConflict = $this
                ->xuatChieuRepository
                ->checkRoomScheduleConflict(
                    $data['maPhong'],
                    $startTime,
                    $endTime
                );

            if ($isConflict) {
                throw new Exception(
                    'Phòng chiếu đã có lịch bị trùng'
                );
            }

            /**
             * Create showtime
             */
            return $this
                ->xuatChieuRepository
                ->create([
                    'maPhim' => $data['maPhim'],
                    'maPhong' => $data['maPhong'],
                    'thoiGianBatDau' => $startTime,
                    'thoiGianKetThuc' => $endTime,
                    'trangThai' => 'sap_chieu'
                ]);
        });
    }

    public function getAvailableShowtimes()
    {
        return $this
            ->xuatChieuRepository
            ->getAvailableShowtimes();
    }

    public function getSeatMap(
        int $maXuatChieu
    ) {
        $xuatChieu =
            XuatChieu::findOrFail(
                $maXuatChieu
            );

        $allSeats =
            Ghe::where(
                'maPhong',
                $xuatChieu->maPhong
            )
            ->get();

        $bookedSeats =
            Ve::where(
                'maXuatChieu',
                $maXuatChieu
            )
            ->pluck('maGhe')
            ->toArray();

        return [

            'maPhong' =>
            $xuatChieu->maPhong,

            'seats' =>
            $allSeats->map(function ($ghe)
            use ($bookedSeats) {

                return [

                    'maGhe' =>
                    $ghe->maGhe,

                    'hangGhe' =>
                    $ghe->hangGhe,

                    'soGhe' =>
                    $ghe->soGhe,

                    'tenGhe' =>
                    $ghe->hangGhe .
                        $ghe->soGhe,

                    'loaiGhe' =>
                    $ghe->loaiGhe,

                    'daDat' =>
                    in_array(
                        $ghe->maGhe,
                        $bookedSeats
                    )
                ];
            })
        ];
    }

    public function getShowtimesByMovie(
        int $movieId,
        ?string $date = null
    ) {
        $showtimes =
            XuatChieu::with([
                'phongChieu.rapChieu',
                'phim'
            ])
            ->where(
                'maPhim',
                $movieId
            )
            ->where(
                'trangThai',
                'Sap_Chieu'
            )
            ->get();

        if ($date) {

            $range =
                $this->resolveDateRange(
                    $date
                );

            if ($range) {

                [$start, $end] = $range;

                $showtimes =
                    $showtimes->filter(
                        function ($showtime)
                        use ($start, $end) {

                            return Carbon::parse(
                                $showtime->thoiGianBatDau
                            )->between(
                                $start,
                                $end
                            );
                        }
                    );
            } else {

                $targetDate =
                    $this->resolveDate(
                        $date
                    );

                if ($targetDate) {

                    $showtimes =
                        $showtimes->filter(
                            function ($showtime)
                            use ($targetDate) {

                                return Carbon::parse(
                                    $showtime->thoiGianBatDau
                                )->isSameDay(
                                    $targetDate
                                );
                            }
                        );
                }
            }
        }

        return $showtimes->values();
    }

    private function resolveDateRange(
        string $date
    ) {
        return match ($date) {

            'today' => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            ],

            'tomorrow' => [
                Carbon::tomorrow()->startOfDay(),
                Carbon::tomorrow()->endOfDay()
            ],

            'this_week' => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ],

            'next_week' => [
                Carbon::now()
                    ->addWeek()
                    ->startOfWeek(),

                Carbon::now()
                    ->addWeek()
                    ->endOfWeek()
            ],

            'weekend' => [
                Carbon::now()->next(Carbon::SATURDAY)
                    ->startOfDay(),

                Carbon::now()->next(Carbon::SUNDAY)
                    ->endOfDay()
            ],

            'next_weekend' => [
                Carbon::now()
                    ->addWeek()
                    ->next(Carbon::SATURDAY)
                    ->startOfDay(),

                Carbon::now()
                    ->addWeek()
                    ->next(Carbon::SUNDAY)
                    ->endOfDay()
            ],

            'this_month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ],

            'next_month' => [
                Carbon::now()
                    ->addMonth()
                    ->startOfMonth(),

                Carbon::now()
                    ->addMonth()
                    ->endOfMonth()
            ],

            'end_of_month' => [
                Carbon::now()
                    ->copy()
                    ->endOfMonth()
                    ->subDays(6),

                Carbon::now()
                    ->copy()
                    ->endOfMonth()
            ],

            'start_of_month' => [
                Carbon::now()
                    ->copy()
                    ->startOfMonth(),

                Carbon::now()
                    ->copy()
                    ->startOfMonth()
                    ->addDays(6)
            ],

            'holiday_2_9' => [
                Carbon::create(
                    now()->year,
                    9,
                    2
                )->startOfDay(),

                Carbon::create(
                    now()->year,
                    9,
                    2
                )->endOfDay()
            ],

            'christmas' => [
                Carbon::create(
                    now()->year,
                    12,
                    25
                )->startOfDay(),

                Carbon::create(
                    now()->year,
                    12,
                    25
                )->endOfDay()
            ],

            default => null
        };
    }

    public function resolveDate(
        ?string $date
    ) {
        if (!$date) {
            return null;
        }

        return match ($date) {

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
                '/(\d{1,2})[\/\-](\d{1,2})/',
                $date,
                $matches
            )
        ) {

            return Carbon::create(
                now()->year,
                (int)$matches[2],
                (int)$matches[1]
            );
        }

        return null;
    }
}
