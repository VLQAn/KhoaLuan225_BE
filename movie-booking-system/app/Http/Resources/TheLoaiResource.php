<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TheLoaiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(
        Request $request
    ): array {
        return [
            'maTheLoai' =>
                $this->maTheLoai,

            'tenTheLoai' =>
                $this->tenTheLoai,

            'moTa' =>
                $this->moTa,
        ];
    }
}
