<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BapNuoc extends Model
{
    protected $table = 'bap_nuoc';

    protected $primaryKey = 'maMon';

    protected $fillable = [
        'maRap',
        'tenMon',
        'gia',
        'hinhAnh',
        'moTa',
        'trangThai'
    ];

    public function rapChieu()
    {
        return $this->belongsTo(
            RapChieu::class,
            'maRap',
            'maRap'
        );
    }
}
