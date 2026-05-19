<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaiTro extends Model
{
    protected $table = 'vai_tro';

    protected $primaryKey = 'maVaiTro';

    protected $fillable = [
        'vaiTro'
    ];

    /**
     * Users
     */
    public function nguoiDung(): HasMany
    {
        return $this->hasMany(
            NguoiDung::class,
            'vaiTro',
            'maVaiTro'
        );
    }
}
