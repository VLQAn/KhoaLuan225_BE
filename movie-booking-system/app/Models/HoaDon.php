<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    protected $table = 'hoa_don';

    protected $primaryKey = 'maHoaDon';

    public $timestamps = false;

    protected $fillable = [
        'maNguoiDung',
        'maKhuyenMai',
        'gioThanhToan',
        'tongTien',
        'trangThai',
        'tongThanhToan'
    ];

    public function thanhToan()
    {
        return $this->hasOne(
            ThanhToan::class,
            'maHoaDon'
        );
    }

    public function ves()
    {
        return $this->hasMany(
            Ve::class,
            'maHoaDon',
            'maHoaDon'
        );
    }

    public function khuyenMai()
    {
        return $this->belongsTo(
            KhuyenMai::class,
            'maKhuyenMai',
            'maKhuyenMai'
        );
    }

    public function nguoiDung()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'maNguoiDung',
            'maNguoiDung'
        );
    }

    public function bapNuocs()
    {
        return $this->hasMany(
            HoaDonBapNuoc::class,
            'maHoaDon',
            'maHoaDon'
        );
    }
}
