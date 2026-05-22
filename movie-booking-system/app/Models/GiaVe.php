<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiaVe extends Model
{
    protected $table = 'gia_ve';

    protected $primaryKey = 'maGiaVe';

    protected $fillable = [
        'gioBatDau',
        'gioKetThuc',
        'gia',
        'moTa',
        'doTuoi'
    ];
}
