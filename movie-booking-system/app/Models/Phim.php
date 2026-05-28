<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Phim extends Model
{
    protected $table = 'phim';

    protected $primaryKey = 'maPhim';

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
     * Thể loại
     */
    public function theLoai(): BelongsToMany
    {
        return $this->belongsToMany(
            TheLoai::class,
            'phim_the_loai',
            'maPhim',      
            'maTheLoai'    
        );
    }
}
