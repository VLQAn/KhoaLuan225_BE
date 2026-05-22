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
            'maRap' =>
                'required|exists:rap_chieu,maRap',
                
            'noiDung' =>
                'required|string|max:255',

            'giaKhuyenMai' =>
                'required|numeric|min:0',
                
            'thoiHan' =>
                'required|date|after:now',
        ];
    }
}
