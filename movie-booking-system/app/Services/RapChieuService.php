<?php

namespace App\Services;

use App\Repositories\Interfaces\RapChieuRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RapChieuService
{
    protected $rapChieuRepository;

    protected $phongChieuService;

    protected $gheService;

    public function __construct(
        RapChieuRepositoryInterface $rapChieuRepository,
        PhongChieuService $phongChieuService,
        GheService $gheService
    ) {
        $this->rapChieuRepository = $rapChieuRepository;

        $this->phongChieuService = $phongChieuService;

        $this->gheService = $gheService;
    }

    public function getAllRapChieu()
    {
        return $this->rapChieuRepository
            ->getByOwner(Auth::id());
    }

    public function getRapChieuById($id)
    {
        return $this->rapChieuRepository->getById($id);
    }

    public function createRapChieu(array $data)
    {
        return DB::transaction(function () use ($data) {

            /**
             * Add owner
             */
            $data['maNguoiDung'] = Auth::id();

            /**
             * Tách danh sách phòng
             */
            $phongChieus =
                $data['phongChieus'];

            unset($data['phongChieus']);

            /**
             * Create theater
             */
            $rap = $this->rapChieuRepository
                ->create($data);

            /**
             * Create rooms
             */
            foreach ($phongChieus as $phong) {

                $newPhong =
                    $this->phongChieuService
                    ->createPhongChieu([

                        'maRap' => $rap->maRap,

                        'tenPhong' =>
                        $phong['tenPhong']
                    ]);

                /**
                 * Generate seats
                 */
                $this->gheService
                    ->generateSeats([

                        'maPhong' =>
                        $newPhong->maPhong,

                        'soHang' =>
                        $phong['soHang'],

                        'soCot' =>
                        $phong['soCot'],
                    ]);
            }

            return $rap;
        });
    }

    public function updateRapChieu($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $rap = $this->rapChieuRepository
                ->getById($id);

            if (!$rap) {
                throw new Exception(
                    'Rạp không tồn tại'
                );
            }

            if (
                $rap->maNguoiDung != Auth::id()
            ) {
                throw new Exception(
                    'Không có quyền'
                );
            }

            /**
             * Tách danh sách phòng
             */
            $phongChieus =
                $data['phongChieus'] ?? [];

            unset($data['phongChieus']);

            /**
             * Update thông tin rạp
             */
            $rap->update($data);

            /**
             * Lấy danh sách ID phòng hiện tại trong DB
             */
            $existingRoomIds = $rap
                ->phongChieu
                ->pluck('maPhong')
                ->toArray();

            /**
             * Lấy danh sách ID phòng từ frontend
             */
            $clientRoomIds = collect($phongChieus)
                ->pluck('maPhong')
                ->filter()
                ->toArray();

            /**
             * Tìm các phòng cần xóa
             */
            $roomsToDelete = array_diff(
                $existingRoomIds,
                $clientRoomIds
            );

            /**
             * Xóa phòng + ghế
             */
            foreach ($roomsToDelete as $roomId) {

                $room = \App\Models\PhongChieu::find($roomId);

                if ($room) {

                    /**
                     * Xóa ghế trước
                     */
                    $room->ghe()->delete();

                    /**
                     * Xóa phòng
                     */
                    $room->delete();
                }
            }

            /**
             * Update hoặc thêm mới phòng
             */
            foreach ($phongChieus as $phong) {

                /**
                 * =========================
                 * UPDATE PHÒNG
                 * =========================
                 */
                if (!empty($phong['maPhong'])) {

                    $room =
                        \App\Models\PhongChieu::find(
                            $phong['maPhong']
                        );

                    if ($room) {

                        $room->update([
                            'tenPhong' =>
                            $phong['tenPhong']
                        ]);

                        /**
                         * Update ghế
                         */
                        if (!empty($phong['ghe'])) {

                            foreach (
                                $phong['ghe']
                                as $ghe
                            ) {

                                \App\Models\Ghe::where(
                                    'maGhe',
                                    $ghe['maGhe']
                                )->update([

                                    'loaiGhe' =>
                                    $ghe['loaiGhe'],

                                    'trangThai' =>
                                    $ghe['trangThai'],
                                ]);
                            }
                        }
                    }
                }

                /**
                 * =========================
                 * THÊM PHÒNG MỚI
                 * =========================
                 */
                else {

                    $newPhong =
                        $this->phongChieuService
                        ->createPhongChieu([

                            'maRap' => $rap->maRap,

                            'tenPhong' =>
                            $phong['tenPhong']
                        ]);

                    /**
                     * Generate ghế mới
                     */
                    $this->gheService
                        ->generateSeats([

                            'maPhong' =>
                            $newPhong->maPhong,

                            'soHang' =>
                            $phong['soHang'],

                            'soCot' =>
                            $phong['soCot'],
                        ]);
                }
            }

            /**
             * Trả dữ liệu mới nhất
             */
            return $this->rapChieuRepository
                ->getById($id);
        });
    }
    
    public function deleteRapChieu($id)
    {
        $rap = $this->rapChieuRepository
            ->getById($id);

        if (!$rap) {
            throw new Exception(
                'Rạp không tồn tại'
            );
        }

        if (
            $rap->maNguoiDung
            != Auth::id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->rapChieuRepository
            ->delete($id);
    }
}
