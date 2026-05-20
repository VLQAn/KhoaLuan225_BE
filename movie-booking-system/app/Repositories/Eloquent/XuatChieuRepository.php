<?php

namespace App\Repositories\Eloquent;

use App\Models\XuatChieu;
use App\Repositories\Interfaces\XuatChieuRepositoryInterface;

class XuatChieuRepository
implements XuatChieuRepositoryInterface
{
    protected $model;

    public function __construct(XuatChieu $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model
            ->with([
                'phim',
                'phongChieu'
            ])
            ->paginate(10);
    }

    public function getById($id)
    {
        return $this->model
            ->with([
                'phim',
                'phongChieu'
            ])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $xuatChieu = $this->getById($id);

        $xuatChieu->update($data);

        return $xuatChieu;
    }

    public function delete($id)
    {
        $xuatChieu = $this->getById($id);

        return $xuatChieu->delete();
    }

    /**
     * Check overlap schedule
     */
    public function checkRoomScheduleConflict(
        int $maPhong,
        $startTime,
        $endTime,
        ?int $ignoreId = null
    ) {
        $query = $this->model
            ->where('maPhong', $maPhong)
            ->where(function ($query)
            use ($startTime, $endTime) {

                $query->where(
                    'thoiGianBatDau',
                    '<',
                    $endTime
                )
                    ->where(
                        'thoiGianKetThuc',
                        '>',
                        $startTime
                    );
            });

        if ($ignoreId !== null) {
            $query->where(
                'maXuatChieu',
                '!=',
                $ignoreId
            );
        }

        return $query->exists();
    }
}
