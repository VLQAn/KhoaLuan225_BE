<?php

namespace App\Repositories\Eloquent;

use App\Models\Ghe;
use App\Repositories\Interfaces\GheRepositoryInterface;

class GheRepository implements GheRepositoryInterface
{
    protected $model;

    public function __construct(Ghe $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model
            ->with('phongChieu')
            ->paginate(20);
    }

    public function getById($id)
    {
        return $this->model
            ->with('phongChieu')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $ghe = $this->getById($id);

        $ghe->update($data);

        return $ghe;
    }

    public function delete($id)
    {
        $ghe = $this->getById($id);

        return $ghe->delete();
    }
}
