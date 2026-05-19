<?php

namespace App\Repositories\Eloquent;

use App\Models\NguoiDung;
use App\Repositories\Interfaces\AuthRepositoryInterface;

class AuthRepository
    implements AuthRepositoryInterface
{
    /**
     * Create user
     */
    public function create(
        array $data
    ): NguoiDung {
        return NguoiDung::create($data);
    }

    /**
     * Find by email
     */
    public function findByEmail(
        string $email
    ): ?NguoiDung {
        return NguoiDung::where(
            'email',
            $email
        )->first();
    }
}
