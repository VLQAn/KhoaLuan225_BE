<?php

namespace App\Http\Requests\Movie;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
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
            'tieuDe' => 'required|string|max:255',
            'moTa' => 'nullable|string',
            'thoiLuong' => 'required|integer',
            'ngayCongChieu' => 'required|date',
            'anhPoster' => 'nullable|string',
            'anhBanner' => 'nullable|string',
            'danhGia' => 'nullable|string',
            'dienVien' => 'nullable|string',
            'daoDien' => 'nullable|string',
            'trangThai' => 'required|in:sap_chieu,dang_chieu,ngung_chieu',
        ];
    }
}
