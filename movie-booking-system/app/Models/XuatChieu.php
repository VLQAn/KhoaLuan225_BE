<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XuatChieu extends Model
{
    protected $table = 'xuat_chieu';

    protected $primaryKey = 'maXuatChieu';

    protected $fillable = [
        'maPhim',
        'maPhong',
        'thoiGianBatDau',
        'thoiGianKetThuc',
        'trangThai',
    ];

    protected $casts = [
        'thoiGianBatDau' => 'datetime',
        'thoiGianKetThuc' => 'datetime',
    ];

    /**
     * Movie relationship
     */
    public function phim(): BelongsTo
    {
        return $this->belongsTo(
            Phim::class,
            'maPhim',
            'maPhim'
        );
    }

    /**
     * Room relationship
     */
    public function phongChieu(): BelongsTo
    {
        return $this->belongsTo(
            PhongChieu::class,
            'maPhong',
            'maPhong'
        );
    }

    public function chiTietDatVe()
    {
        return $this->hasMany(
            ChiTietDatVe::class,
            'maXuatChieu',
            'maXuatChieu'
        );
    }
}
