<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NguoiDungResource
    extends JsonResource
{
    /**
     * Transform resource
     */
    public function toArray(
        Request $request
    ): array {
        return [
            'maNguoiDung' =>
                $this->maNguoiDung,

            'tenNguoiDung' =>
                $this->tenNguoiDung,

            'email' =>
                $this->email,

            'vaiTro' => [
                'maVaiTro' =>
                    $this->role?->maVaiTro,

                'tenVaiTro' =>
                    $this->role?->vaiTro
            ],

            'created_at' =>
                $this->created_at
        ];
    }
}
