<?php

namespace App\Http\Requests\RapChieu;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRapChieuRequest extends FormRequest
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

            'tenRap' => 'required|string|max:255',

            'diaChi' => 'required|string|max:500',

            'soDienThoai' => [
                'required',
                'regex:/^(0)[0-9]{9}$/'
            ],

            'phongChieus' => 'required|array|min:1',

            'phongChieus.*.tenPhong'
            => 'required|string|max:255',

            'phongChieus.*.soHang'
            => 'required|integer|min:1|max:26',

            'phongChieus.*.soCot'
            => 'required|integer|min:1|max:30',

        ];
    }
}
