<?php

namespace App\Services;

use Exception;
use App\Models\RapChieu;

use App\Repositories\Interfaces\KhuyenMaiRepositoryInterface;

class KhuyenMaiService
{
    protected $khuyenMaiRepository;

    public function __construct(
        KhuyenMaiRepositoryInterface
        $khuyenMaiRepository
    ) {
        $this->khuyenMaiRepository
            = $khuyenMaiRepository;
    }

    public function getAllKhuyenMai()
    {
        return $this->khuyenMaiRepository
            ->getAll();
    }

    public function getKhuyenMaiById($id)
    {
        return $this->khuyenMaiRepository
            ->findById($id);
    }

    public function createKhuyenMai(
        array $data
    ) {

        $rap = RapChieu::find(
            $data['maRap']
        );

        if (
            $rap->maNguoiDung
            != auth()->id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->khuyenMaiRepository
            ->create($data);
    }

    public function updateKhuyenMai(
        $id,
        array $data
    ) {

        $khuyenMai =
            $this->khuyenMaiRepository
                ->findById($id);

        if (
            $khuyenMai->rapChieu
                ->maNguoiDung
            != auth()->id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->khuyenMaiRepository
            ->update($id, $data);
    }

    public function deleteKhuyenMai(
        $id
    ) {

        $khuyenMai =
            $this->khuyenMaiRepository
                ->findById($id);

        if (
            $khuyenMai->rapChieu
                ->maNguoiDung
            != auth()->id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->khuyenMaiRepository
            ->delete($id);
    }
}
