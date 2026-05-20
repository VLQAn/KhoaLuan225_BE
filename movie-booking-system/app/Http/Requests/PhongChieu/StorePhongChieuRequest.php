<?php

namespace App\Http\Requests\PhongChieu;

use Illuminate\Foundation\Http\FormRequest;

class StorePhongChieuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maRap' => [
                'required',
                'exists:rap_chieu,maRap'
            ],

            'tenPhong' => [
                'required',
                'string',
                'max:255'
            ]
        ];
    }
}