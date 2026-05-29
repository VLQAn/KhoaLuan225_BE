<?php

namespace App\Services;

use App\Repositories\Interfaces\RapChieuRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;

class RapChieuService
{
    protected $rapChieuRepository;

    public function __construct(
        RapChieuRepositoryInterface $rapChieuRepository
    ) {
        $this->rapChieuRepository = $rapChieuRepository;
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
        $data['maNguoiDung']
            = Auth::id();

        return $this->rapChieuRepository
            ->create($data);
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
