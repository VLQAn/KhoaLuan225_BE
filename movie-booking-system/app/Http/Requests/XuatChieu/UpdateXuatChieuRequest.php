<?php

namespace App\Http\Requests\XuatChieu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateXuatChieuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maPhim' => [
                'sometimes',
                'exists:phim,maPhim'
            ],

            'maPhong' => [
                'sometimes',
                'exists:phong_chieu,maPhong'
            ],

            'thoiGianBatDau' => [
                'sometimes',
                'date',
                'after:now'
            ]
        ];
    }
}
