<?php

namespace App\Services;

use Exception;
use App\Models\RapChieu;
use App\Repositories\Interfaces\GiaVeRepositoryInterface;
use App\Models\XuatChieu;
use App\Models\GiaVe;
use Illuminate\Support\Facades\Auth;

class GiaVeService
{
    protected $giaVeRepository;

    public function __construct(
        GiaVeRepositoryInterface
        $giaVeRepository
    ) {
        $this->giaVeRepository
            = $giaVeRepository;
    }

    public function getAllGiaVe()
    {
        return $this->giaVeRepository
            ->getAll();
    }

    public function getGiaVeById($id)
    {
        return $this->giaVeRepository
            ->findById($id);
    }

    public function createGiaVe(array $data)
    {
        $data['maNguoiDung']
            = Auth::id();

        return $this->giaVeRepository
            ->create($data);
    }

    public function updateGiaVe(
        $id,
        array $data
    ) {
        $giaVe = $this->giaVeRepository
            ->findById($id);

        if (
            $giaVe->rapChieu->maNguoiDung
            != Auth::id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->giaVeRepository
            ->update($id, $data);
    }

    public function deleteGiaVe($id)
    {
        $giaVe = $this->giaVeRepository
            ->findById($id);

        if (
            $giaVe->rapChieu->maNguoiDung
            != Auth::id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->giaVeRepository
            ->delete($id);
    }

    public function getGiaVeByXuatChieu(
        int $maXuatChieu
    ) {

        $xuatChieu =
            XuatChieu::findOrFail(
                $maXuatChieu
            );

        $gioChieu =
            $xuatChieu
                ->thoiGianBatDau
                ->format('H:i:s');

        $giaVe = GiaVe::where(
                'gioBatDau',
                '<=',
                $gioChieu
            )
            ->where(
                'gioKetThuc',
                '>=',
                $gioChieu
            )
            ->first();

        if (!$giaVe) {

            throw new Exception(
                'Không tìm thấy giá vé phù hợp'
            );
        }

        return $giaVe;
    }
}
