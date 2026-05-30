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
        $data = $this->model
            ->with(['phim', 'phongChieu.rapChieu'])
            ->paginate(20);

        $now = now();

        $data->getCollection()->transform(function ($item) use ($now) {

            if ($item->thoiGianKetThuc < $now) {
                $item->trangThai = 'da_chieu';
            } elseif ($item->thoiGianBatDau <= $now) {
                $item->trangThai = 'dang_chieu';
            } else {
                $item->trangThai = 'sap_chieu';
            }

            return $item;
        });

        return $data;
    }

    public function getById($id)
    {
        return $this->model
            ->with([
                'phim',
                'phongChieu.rapChieu'
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
