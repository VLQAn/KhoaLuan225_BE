<?php

namespace App\Repositories\Eloquent;

use App\Models\KhuyenMai;

use App\Repositories\Interfaces\KhuyenMaiRepositoryInterface;

class KhuyenMaiRepository
implements KhuyenMaiRepositoryInterface
{
    public function getAll()
    {
        return KhuyenMai::all();
    }

    public function findById($id)
    {
        return KhuyenMai::find($id);
    }

    public function create(array $data)
    {
        return KhuyenMai::create($data);
    }

    public function update(
        $id,
        array $data
    ) {
        $khuyenMai =
            $this->findById($id);

        $khuyenMai->update($data);

        return $khuyenMai;
    }

    public function delete($id)
    {
        return KhuyenMai::destroy($id);
    }
}
