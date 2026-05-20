<?php

namespace App\Repositories\Eloquent;

use App\Models\PhongChieu;
use App\Repositories\Interfaces\PhongChieuRepositoryInterface;

class PhongChieuRepository implements PhongChieuRepositoryInterface
{
    protected $model;

    public function __construct(PhongChieu $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model
            ->with('rapChieu')
            ->latest()
            ->paginate(10);
    }

    public function getById($id)
    {
        return $this->model
            ->with('rapChieu')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $phong = $this->getById($id);

        $phong->update($data);

        return $phong;
    }

    public function delete($id)
    {
        $phong = $this->getById($id);

        return $phong->delete();
    }
}
