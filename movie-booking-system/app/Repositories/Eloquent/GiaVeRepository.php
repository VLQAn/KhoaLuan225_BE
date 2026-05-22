<?php

namespace App\Repositories\Eloquent;

use App\Models\GiaVe;
use App\Repositories\Interfaces\GiaVeRepositoryInterface;

class GiaVeRepository
implements GiaVeRepositoryInterface
{
    public function getAll()
    {
        return GiaVe::all();
    }

    public function findById($id)
    {
        return GiaVe::find($id);
    }

    public function create(array $data)
    {
        return GiaVe::create($data);
    }

    public function update(
        $id,
        array $data
    ) {
        $giaVe = $this->findById($id);

        $giaVe->update($data);

        return $giaVe;
    }

    public function delete($id)
    {
        return GiaVe::destroy($id);
    }
}
