<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RapChieu extends Model
{
    protected $table = 'rap_chieu';

    protected $primaryKey = 'maRap';

    protected $fillable = [
        'maNguoiDung',
        'tenRap',
        'diaChi',
    ];

    /**
     * Relationship with NguoiDung (owner of the cinema)
     */
    public function chuRap()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'maNguoiDung',
            'maNguoiDung'
        );
    }

    /**
     * Relationship with PhongChieu
     */
    public function phongChieu(): HasMany
    {
        return $this->hasMany(
            PhongChieu::class,
            'maRap',
            'maRap'
        );
    }

    /**
     * Relationship with BapNuoc
     */
    public function bapNuocs()
    {
        return $this->hasMany(
            BapNuoc::class,
            'maRap',
            'maRap'
        );
    }
}
