<?php

namespace App\Http\Requests\KhuyenMai;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreKhuyenMaiRequest
extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'noiDung' => 'required|string|max:255',
            'maCode' => 'required|string|unique:khuyen_mai,maCode',
            'giaKhuyenMai' => 'required|numeric|min:0|max:100',
            'ngayBatDau' => 'required|date',
            'thoiHan' => 'required|date|after_or_equal:ngayBatDau',
        ];
    }
}
