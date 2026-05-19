<?php

namespace App\Repositories\Interfaces;

use App\Models\NguoiDung;

interface AuthRepositoryInterface
{
    public function create(array $data): NguoiDung;

    public function findByEmail(
        string $email
    ): ?NguoiDung;
}
