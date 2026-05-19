<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'tieuDe',
        'moTa',
        'thoiLuong',
        'ngayCongChieu',
        'anhPoster',
        'anhBanner',
        'danhGia',
        'dienVien',
        'daoDien',
        'trangThai'
    ];

    /**
     * Genres relationship
     */
    public function genres()
    {
        return $this->belongsToMany(
            Genre::class
        );
    }

}
