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
                'required',
                'exists:phim,maPhim'
            ],

            'maPhong' => [
                'required',
                'exists:phong_chieu,maPhong'
            ],

            'thoiGianBatDau' => [
                'required',
                'date',
                'after:now'
            ]
        ];
    }
}
