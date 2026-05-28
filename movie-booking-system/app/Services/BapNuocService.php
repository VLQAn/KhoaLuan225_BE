<?php

namespace App\Services;

use App\Repositories\Interfaces\BapNuocRepositoryInterface;
use App\Models\RapChieu;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BapNuocService
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct(
        BapNuocRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function getAllBapNuoc()
    {
        return $this->repository
            ->getByOwner(Auth::id());
    }

    public function getBapNuocById($id)
    {
        $mon = $this->repository
            ->find($id);

        if (!$mon) {
            return null;
        }

        if (!$mon->rapChieu) {
            throw new Exception('Không tìm thấy rạp');
        }

        if ($mon->rapChieu->maNguoiDung != Auth::id()) {
            throw new Exception('Không có quyền');
        }

        return $mon;
    }

    public function createBapNuoc(array $data)
    {
        $rap = RapChieu::find(
            $data['maRap']
        );

        if (!$rap) {
            throw new Exception(
                'Rạp không tồn tại'
            );
        }

        // CHECK OWNERSHIP

        if (
            $rap->maNguoiDung
            != Auth::id()
        ) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->repository
            ->create($data);
    }

    public function updateBapNuoc($id, array $data)
    {
        $mon = $this->repository->find($id);

        if (!$mon) {
            throw new Exception('Món không tồn tại');
        }

        if (!$mon->rapChieu) {
            throw new Exception('Món không thuộc rạp nào');
        }

        if ($mon->rapChieu->maNguoiDung != Auth::id()) {
            throw new Exception('Không có quyền');
        }

        return $this->repository->update($id, $data);
    }

    public function deleteBapNuoc($id)
    {
        $mon = $this->repository
            ->find($id);

        if (!$mon) {
            throw new Exception(
                'Món không tồn tại'
            );
        }

        if (!$mon->rapChieu) {
            throw new Exception('Không tìm thấy rạp');
        }

        if ($mon->rapChieu->maNguoiDung != Auth::id()) {
            throw new Exception(
                'Không có quyền'
            );
        }

        return $this->repository
            ->delete($id);
    }

    public function updateStatus($id, array $data)
    {
        $mon = $this->repository->find($id);

        if (!$mon) {
            throw new \Exception("Món không tồn tại");
        }

        if (!$mon->rapChieu) {
            throw new \Exception("Không tìm thấy rạp");
        }

        if ($mon->rapChieu->maNguoiDung != Auth::id()) {
            throw new \Exception("Không có quyền");
        }

        return $this->repository->update($id, $data);
    }
}
