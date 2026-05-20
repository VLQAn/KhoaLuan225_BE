<?php

namespace App\Services;

use App\Repositories\Interfaces\PhongChieuRepositoryInterface;

class PhongChieuService
{
    protected $phongChieuRepository;

    public function __construct(
        PhongChieuRepositoryInterface $phongChieuRepository
    ) {
        $this->phongChieuRepository =
            $phongChieuRepository;
    }

    public function getAllPhongChieu()
    {
        return $this->phongChieuRepository
            ->getAll();
    }

    public function getPhongChieuById($id)
    {
        return $this->phongChieuRepository
            ->getById($id);
    }

    public function createPhongChieu(array $data)
    {
        return $this->phongChieuRepository
            ->create($data);
    }

    public function updatePhongChieu($id, array $data)
    {
        return $this->phongChieuRepository
            ->update($id, $data);
    }

    public function deletePhongChieu($id)
    {
        return $this->phongChieuRepository
            ->delete($id);
    }
}
