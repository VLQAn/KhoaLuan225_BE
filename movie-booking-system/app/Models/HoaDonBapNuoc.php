<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDonBapNuoc extends Model
{
    protected $table = 'hoa_don_bap_nuoc';

    protected $fillable = [
        'maHoaDon',
        'maMon',
        'soLuong',
        'donGia',
        'thanhTien'
    ];

    public function mon()
    {
        return $this->belongsTo(
            BapNuoc::class,
            'maMon',
            'maMon'
        );
    }
}
