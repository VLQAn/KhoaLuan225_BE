<?php

namespace App\Repositories\Eloquent;

use App\Models\BapNuoc;
use App\Repositories\Interfaces\BapNuocRepositoryInterface;

class BapNuocRepository implements BapNuocRepositoryInterface
{
    protected $model;

    public function __construct(BapNuoc $model)
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
        return BapNuoc::whereHas(
            'rapChieu',
            function ($query) use ($maNguoiDung) {

                $query->where(
                    'maNguoiDung',
                    $maNguoiDung
                );
            }
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
        $bapNuoc = $this->getById($id);

        $bapNuoc->update($data);

        return $bapNuoc;
    }

    public function delete($id)
    {
        $bapNuoc = $this->getById($id);

        return $bapNuoc->delete();
    }

    public function find($id)
    {
        return $this->model
            ->with('rapChieu')
            ->find($id);
    }
}
