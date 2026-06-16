<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuTroChuyen extends Model
{
    protected $table = 'lich_su_tro_chuyen';

    protected $primaryKey = 'maTinNhan';

    protected $fillable = [

        'maPhien',

        'nguoiGui',

        'noiDung'
    ];
}
