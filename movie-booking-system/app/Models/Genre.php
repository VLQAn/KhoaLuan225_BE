<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = [
        'tenTheLoai',
        'moTa'
    ];

    /**
     * Movies relationship
     */
    public function movies()
    {
        return $this->belongsToMany(
            Movie::class
        );
    }
}
