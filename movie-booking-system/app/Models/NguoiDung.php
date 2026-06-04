<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class NguoiDung extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'nguoi_dung';

    protected $primaryKey = 'maNguoiDung';

    protected $fillable = [
        'tenNguoiDung',
        'email',
        'matKhau',
        'vaiTro',
        'diaChi',
        'logo',
        'hinhAnh'
    ];

    protected $hidden = [
        'matKhau',
        'remember_token'
    ];

    /**
     * Password field for Laravel Auth
     */
    public function getAuthPassword()
    {
        return $this->matKhau;
    }

    /**
     * Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(
            VaiTro::class,
            'vaiTro',
            'maVaiTro'
        );
    }

    /**
     * Check role
     */
    public function hasRole(
        string $role
    ): bool {

        return strtolower(
            $this->role?->vaiTro
        ) === strtolower($role);
    }

    public function rapChieus()
    {
        return $this->hasMany(
            RapChieu::class,
            'maNguoiDung',
            'maNguoiDung'
        );
    }
}
