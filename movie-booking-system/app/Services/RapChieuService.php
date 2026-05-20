<?php

namespace App\Services;

use App\Repositories\Interfaces\RapChieuRepositoryInterface;

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
        return $this->rapChieuRepository->getAll();
    }

    public function getRapChieuById($id)
    {
        return $this->rapChieuRepository->getById($id);
    }

    public function createRapChieu(array $data)
    {
        return $this->rapChieuRepository->create($data);
    }

    public function updateRapChieu($id, array $data)
    {
        return $this->rapChieuRepository->update($id, $data);
    }

    public function deleteRapChieu($id)
    {
        return $this->rapChieuRepository->delete($id);
    }
}
