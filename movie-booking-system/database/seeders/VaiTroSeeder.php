<?php

namespace Database\Seeders;

use App\Models\VaiTro;
use Illuminate\Database\Seeder;

class VaiTroSeeder extends Seeder
{
    /**
     * Run database seeds
     */
    public function run(): void
    {
        VaiTro::create([
            'vaiTro' => 'admin'
        ]);

        VaiTro::create([
            'vaiTro' => 'staff'
        ]);

        VaiTro::create([
            'vaiTro' => 'customer'
        ]);
    }
}
