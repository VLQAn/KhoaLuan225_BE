<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'maPhim' => $this->maPhim,
            'tieuDe' => $this->tieuDe,
            'moTa' => $this->moTa,
            'thoiLuong' => $this->thoiLuong,
            'ngayCongChieu' => $this->ngayCongChieu,
            'anhPoster' => $this->anhPoster,
            'anhBanner' => $this->anhBanner,
            'danhGia' => $this->danhGia,
            'dienVien' => $this->dienVien,
            'daoDien' => $this->daoDien,
            'trangThai' => $this->trangThai,
            'theLoai' => $this->theLoai->map(function ($theLoai) {
                return [
                    'maTheLoai' => $theLoai->maTheLoai,
                    'tenTheLoai' => $theLoai->tenTheLoai,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
