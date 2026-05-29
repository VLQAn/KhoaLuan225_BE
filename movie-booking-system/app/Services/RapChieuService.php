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
            ->update($id, $data);
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
