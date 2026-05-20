<?php

namespace App\Services;

use App\Models\Phim;
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

    public function updateXuatChieu($id, array $data)
    {
        return $this->xuatChieuRepository->update($id, $data);
    }

    public function deleteXuatChieu($id)
    {
        return $this->xuatChieuRepository->delete($id); 
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
