<?php

namespace App\Services;

use App\Repositories\Interfaces\GheRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Ghe;

class GheService
{
    protected $gheRepository;

    public function __construct(
        GheRepositoryInterface $gheRepository
    ) {
        $this->gheRepository =
            $gheRepository;
    }

    public function getAllGhe()
    {
        return $this->gheRepository
            ->getAll();
    }

    public function getGheById($id)
    {
        return $this->gheRepository
            ->getById($id);
    }

    public function createGhe(array $data)
    {
        return $this->gheRepository
            ->create($data);
    }

    public function updateGhe($id, array $data)
    {
        return $this->gheRepository
            ->update($id, $data);
    }

    public function deleteGhe($id)
    {
        return $this->gheRepository
            ->delete($id);
    }

    public function generateSeats(array $data)
    {
        return DB::transaction(function () use ($data) {

            $maPhong = $data['maPhong'];

            $soHang = $data['soHang'];

            $soCot = $data['soCot'];

            /**
             * Xóa ghế cũ nếu có
             */
            Ghe::where('maPhong', $maPhong)
                ->delete();

            $seats = [];

            /**
             * Generate seats
             */
            for ($i = 0; $i < $soHang; $i++) {

                /**
                 * Convert:
                 * 0 => A
                 * 1 => B
                 */
                $hangGhe = chr(65 + $i);

                for ($j = 1; $j <= $soCot; $j++) {

                    $loaiGhe = 'thuong';

                    /**
                     * Example:
                     * hàng cuối là VIP
                     */
                    if ($i >= $soHang - 2) {
                        $loaiGhe = 'vip';
                    }

                    $seats[] = [
                        'maPhong' => $maPhong,
                        'hangGhe' => $hangGhe,
                        'soGhe' => $j,
                        'loaiGhe' => $loaiGhe,
                        'trangThai' => 'hoat_dong',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Ghe::insert($seats);

            return [
                'message' => 'Generate ghế thành công',
                'totalSeats' => count($seats)
            ];
        });
    }
}
