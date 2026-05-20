<?php

namespace App\Http\Requests\PhongChieu;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhongChieuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maRap' => [
                'sometimes',
                'exists:rap_chieu,maRap'
            ],

            'tenPhong' => [
                'sometimes',
                'string',
                'max:255'
            ]
        ];
    }
}
