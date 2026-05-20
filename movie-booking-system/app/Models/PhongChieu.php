<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhongChieu extends Model
{
    protected $table = 'phong_chieu';

    protected $primaryKey = 'maPhong';

    protected $fillable = [
        'maRap',
        'tenPhong',
    ];

    /**
     * Relationship with RapChieu
     */
    public function rapChieu(): BelongsTo
    {
        return $this->belongsTo(
            RapChieu::class,
            'maRap',
            'maRap'
        );
    }

    /**
     * Relationship with Ghe
     */
    public function ghe(): HasMany
    {
        return $this->hasMany(
            Ghe::class,
            'maPhong',
            'maPhong'
        );
    }
}
