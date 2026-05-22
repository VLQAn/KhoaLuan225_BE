<?php

namespace App\Http\Requests\KhuyenMai;

use Illuminate\Validation\Rule;

class UpdateKhuyenMaiRequest
extends StoreKhuyenMaiRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['noiDung'] =
            'required|string|max:255';

        $rules['giaKhuyenMai'] =
            'required|numeric|min:0';
            
        $rules['thoiHan'] =
            'required|date|after:now';

        return $rules;
    }
}