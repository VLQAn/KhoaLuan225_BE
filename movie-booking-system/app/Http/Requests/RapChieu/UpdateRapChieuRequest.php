<?php

namespace App\Http\Requests\RapChieu;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRapChieuRequest extends FormRequest
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

            'tenRap' => 'sometimes|string|max:255',

            'diaChi' => 'sometimes|string|max:500',

            'soDienThoai' => [
                'required',
                'regex:/^(0)[0-9]{9}$/'
            ],

            'phongChieus' => 'sometimes|array',

            'phongChieus.*.maPhong' =>
            'nullable|integer',

            'phongChieus.*.tenPhong' =>
            'required|string|max:255',

            'phongChieus.*.soHang' =>
            'required|integer|min:1',

            'phongChieus.*.soCot' =>
            'required|integer|min:1',

            'phongChieus.*.ghe' =>
            'sometimes|array',

            'phongChieus.*.ghe.*.maGhe' =>
            'sometimes|integer',

            'phongChieus.*.ghe.*.loaiGhe' =>
            'sometimes|string',

            'phongChieus.*.ghe.*.trangThai' =>
            'sometimes|string',

        ];
    }
}
