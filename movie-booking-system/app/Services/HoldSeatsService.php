<?php

namespace App\Services;

use App\Models\Ve;
use Illuminate\Support\Facades\DB;
use Exception;

class HoldSeatsService
{
    public function holdSeats($userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {

            $existingTickets = Ve::where(
                'maXuatChieu',
                $data['maXuatChieu']
            )
                ->whereIn('maGhe', $data['danhSachGhe'])
                ->whereIn('trangThai', ['Dang_chon', 'Da_Dat'])
                ->lockForUpdate()
                ->get();

            if ($existingTickets->isNotEmpty()) {
                throw new Exception(
                    'Ghế đã được đặt hoặc đang giữ'
                );
            }

            $tickets = [];

            foreach ($data['danhSachGhe'] as $maGhe) {

                $tickets[] = Ve::create([
                    'maNguoiDung' => $userId,
                    'maXuatChieu' => $data['maXuatChieu'],
                    'maGhe' => $maGhe,
                    'trangThai' => 'Dang_chon',
                    'thoiHanGiuGhe' => now()->addMinutes(6)
                ]);
            }

            return $tickets;
        });
    }
}
