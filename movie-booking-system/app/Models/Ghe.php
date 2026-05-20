<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ghe extends Model
{
    protected $table = 'ghe';

    protected $primaryKey = 'maGhe';

    protected $fillable = [
        'maPhong',
        'hangGhe',
        'soGhe',
        'loaiGhe',
        'trangThai',
    ];

    /**
     * Relationship with PhongChieu
     */
    public function phongChieu(): BelongsTo
    {
        return $this->belongsTo(
            PhongChieu::class,
            'maPhong',
            'maPhong'
        );
    }

    /**
     * Get full seat name
     */
    public function getTenGheAttribute(): string
    {
        return $this->hangGhe . $this->soGhe;
    }
}
