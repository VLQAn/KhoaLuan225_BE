<?php

namespace App\Services;

use App\Repositories\Interfaces\TheLoaiRepositoryInterface;

class TheLoaiService
{
    protected $theLoaiRepository;

    public function __construct(
        TheLoaiRepositoryInterface $theLoaiRepository
    ) {
        $this->theLoaiRepository =
            $theLoaiRepository;
    }

    /**
     * Get all genres
     */
    public function getAllTheLoai()
    {
        return $this->theLoaiRepository
            ->getAll();
    }
}
