<?php

namespace App\Repositories\Eloquent;

use App\Models\RapChieu;
use App\Repositories\Interfaces\RapChieuRepositoryInterface;

class RapChieuRepository implements RapChieuRepositoryInterface
{
    protected $model;

    public function __construct(RapChieu $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model
            ->latest()
            ->paginate(10);
    }

    public function getByOwner($maNguoiDung)
    {
        return RapChieu::where(
            'maNguoiDung',
            $maNguoiDung
        )->get();
    }


    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $rap = $this->getById($id);

        $rap->update($data);

        return $rap;
    }

    public function delete($id)
    {
        $rap = $this->getById($id);

        return $rap->delete();
    }
}
