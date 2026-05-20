<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\XuatChieu;
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
}
