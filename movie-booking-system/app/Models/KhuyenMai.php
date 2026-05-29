<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    protected $table = 'khuyen_mai';

    protected $primaryKey = 'maKhuyenMai';

    protected $fillable = [
        'maNguoiDung',
        'noiDung',
        'maCode',
        'giaKhuyenMai',
        'ngayBatDau',
        'thoiHan',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'maNguoiDung');
    }
}
