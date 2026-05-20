<?php

namespace App\Http\Requests\Ghe;

use Illuminate\Foundation\Http\FormRequest;

class GenerateGheRequest extends FormRequest
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

            'soHang' => [
                'required',
                'integer',
                'min:1',
                'max:26'
            ],

            'soCot' => [
                'required',
                'integer',
                'min:1',
                'max:30'
            ]
        ];
    }
}
