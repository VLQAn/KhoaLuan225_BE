<?php

namespace App\Http\Requests\GiaVe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGiaVeRequest
extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gioBatDau' =>
                'required|date_format:H:i',
            'gioKetThuc' =>
                'required|date_format:H:i|after:gioBatDau',
            'gia' =>
                'required|numeric|min:0',
            'moTa' =>
                'nullable|string|max:255',
            'doTuoi' =>
                'required|integer|min:0',
        ];
    }
}
