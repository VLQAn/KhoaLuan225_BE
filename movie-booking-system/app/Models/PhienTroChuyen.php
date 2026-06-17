<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhienTroChuyen extends Model
{
    protected $table = 'phien_tro_chuyen';

    protected $primaryKey = 'maPhien';

    protected $fillable = [

        'maNguoiDung',

        'phimDangChon',

        'xuatChieuDangChon',

        'trangThai'
    ];

    protected $casts = [

        'duLieu' => 'array'
    ];
}
