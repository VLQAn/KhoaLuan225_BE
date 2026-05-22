<?php

namespace App\Http\Requests\BapNuoc;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBapNuocRequest extends FormRequest
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
            'maRap' => 'sometimes|exists:rap_chieu,maRap',
            'tenMon' => 'sometimes|string|max:255',
            'gia' => 'sometimes|numeric|min:0',
            'hinhAnh' => 'nullable|url',
            'moTa' => 'nullable|string',
            'trangThai' => [
                'sometimes',
                Rule::in([
                    'DANG_BAN',
                    'HET_BAN_TRONG_NGAY',
                    'NGUNG_KINH_DOANH'
                ])
            ]
        ];
    }
}
