<?php

namespace App\Repositories\Eloquent;

use App\Models\TheLoai;
use App\Repositories\Interfaces\TheLoaiRepositoryInterface;

class TheLoaiRepository
    implements TheLoaiRepositoryInterface
{
    public function getAll()
    {
        return TheLoai::all();
    }
}
