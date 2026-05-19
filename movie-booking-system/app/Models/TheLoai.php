<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TheLoai extends Model
{
    protected $table = 'the_loai';

    protected $primaryKey = 'maTheLoai';

    protected $fillable = [
        'tenTheLoai',
        'moTa'
    ];

    /**
     * Phim
     */
    public function phim(): BelongsToMany
    {
        return $this->belongsToMany(
            Phim::class,
            'phim_the_loai',
            'maTheLoai',
            'maPhim'
        );
    }
}
