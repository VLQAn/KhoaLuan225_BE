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
}
