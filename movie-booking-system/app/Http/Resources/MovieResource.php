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
            'id' => $this->id,
            'tieuDe' => $this->title,
            'moTa' => $this->description,
            'thoiLuong' => $this->duration,
            'ngayCongChieu' => $this->release_date,
            'anhPoster' => $this->poster,
            'anhBanner' => $this->banner,
            'danhGia' => $this->rating,
            'dienVien' => $this->actors,
            'daoDien' => $this->director,
            'trangThai' => $this->status,
            'theLoai' => $this->genres,
            'created_at' => $this->created_at,
        ];
    }
}
