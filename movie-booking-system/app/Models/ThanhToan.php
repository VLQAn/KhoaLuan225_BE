<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'thanh_toan';

    protected $primaryKey =
        'maThanhToan';

    protected $fillable = [

        'maHoaDon',

        'phuongThucThanhToan',

        'trangThai',

        'maGiaoDich',

        'soTien',

        'duLieuPhanHoi',

        'gioThanhToan'
    ];

    public function hoaDon()
    {
        return $this->belongsTo(
            HoaDon::class,
            'maHoaDon'
        );
    }
}
