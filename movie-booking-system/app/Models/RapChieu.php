<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RapChieu extends Model
{
    protected $table = 'rap_chieu';

    protected $primaryKey = 'maRap';

    protected $fillable = [
        'tenRap',
        'diaChi',
    ];

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
}
