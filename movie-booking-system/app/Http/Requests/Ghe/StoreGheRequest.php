<?php

namespace App\Http\Requests\Ghe;

use Illuminate\Foundation\Http\FormRequest;

class StoreGheRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maPhong' => [
                'required',
                'exists:phong_chieu,maPhong'
            ],

            'hangGhe' => [
                'required',
                'string',
                'max:5'
            ],

            'soGhe' => [
                'required',
                'integer',
                'min:1'
            ],

            'loaiGhe' => [
                'required',
                'in:thuong,vip'
            ]
        ];
    }
}
