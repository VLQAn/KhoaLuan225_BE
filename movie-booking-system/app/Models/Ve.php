<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ve extends Model
{
    protected $table = 've';

    protected $primaryKey = 'maVe';

    public $timestamps = false;

    protected $fillable = [
        'maXuatChieu',
        'maGiaVe',
        'maHoaDon',
        'maGhe',
        'gia',
        'trangThai',
    ];
}
