<?php

namespace App\Http\Requests\GiaVe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGiaVeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gioBatDau' =>
                'sometimes|date_format:H:i',
            'gioKetThuc' =>
                'sometimes|date_format:H:i|after:gioBatDau',
            'gia' =>
                'sometimes|numeric|min:0',
            'moTa' =>
                'nullable|string|max:255',
            'doTuoi' =>
                'sometimes|integer|min:0',
        ];
    }
}
