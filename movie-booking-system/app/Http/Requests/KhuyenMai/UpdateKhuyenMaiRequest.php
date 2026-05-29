<?php

namespace App\Http\Requests\KhuyenMai;

use Illuminate\Validation\Rule;

class UpdateKhuyenMaiRequest
extends StoreKhuyenMaiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('khuyen_mai');

        $idValue = is_object($id) ? $id->maKhuyenMai : $id;

        return [
            'noiDung' => 'required|string|max:255',
            'maCode' => [
                'required',
                Rule::unique('khuyen_mai', 'maCode')->ignore($idValue, 'maKhuyenMai'),
            ],
            'giaKhuyenMai' => 'required|numeric|min:0|max:100',
            'ngayBatDau' => 'required|date',
            'thoiHan' => 'required|date|after_or_equal:ngayBatDau',
        ];
    }
}
