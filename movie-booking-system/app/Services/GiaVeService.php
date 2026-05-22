<?php

namespace App\Services;

use Exception;
use App\Models\RapChieu;
use App\Repositories\Interfaces\GiaVeRepositoryInterface;

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
            = auth()->id();

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
            != auth()->id()
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
            != auth()->id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->giaVeRepository
            ->delete($id);
    }
}
