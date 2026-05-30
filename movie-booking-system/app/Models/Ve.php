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

    public function hoaDon()
    {
        return $this->belongsTo(
            HoaDon::class,
            'maHoaDon'
        );
    }

    public function ghe()
    {
        return $this->belongsTo(
            Ghe::class,
            'maGhe',
            'maGhe'
        );
    }

    public function xuatChieu()
    {
        return $this->belongsTo(
            XuatChieu::class,
            'maXuatChieu',
            'maXuatChieu'
        );
    }
}
