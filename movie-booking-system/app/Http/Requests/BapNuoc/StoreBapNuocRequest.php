<?php

namespace App\Http\Requests\BapNuoc;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBapNuocRequest extends FormRequest
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
            'maRap' => 'required|exists:rap_chieu,maRap',
            'tenMon' => 'required|string|max:255',
            'gia' => 'required|numeric|min:0',
            'hinhAnh' => 'nullable|url',
            'moTa' => 'nullable|string',
            'trangThai' => [
                'required',
                Rule::in([
                    'DANG_BAN',
                    'HET_BAN_TRONG_NGAY',
                    'NGUNG_KINH_DOANH'
                ])
            ]
        ];
    }
}
