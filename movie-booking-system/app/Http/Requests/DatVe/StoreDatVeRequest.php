<?php

namespace App\Http\Requests\DatVe;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDatVeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'maXuatChieu' =>
            'required|exists:xuat_chieu,maXuatChieu',

            'danhSachGhe' =>
            'required|array|min:1',

            'danhSachGhe.*' =>
            'exists:ghe,maGhe',

            'maKhuyenMai' =>
            'nullable|exists:khuyen_mai,maKhuyenMai',

            'danhSachMonAn' => ['nullable', 'array'],

            'danhSachMonAn.*.maMon' =>
            ['required_with:danhSachMonAn'],

            'danhSachMonAn.*.soLuong' =>
            ['required_with:danhSachMonAn']
        ];
    }
}
