<?php

namespace App\Services;

use App\Models\XuatChieu;

class ChatbotShowtimeService
{
    public function search(
        array $filters
    ) {

        $query =
            XuatChieu::with([
                'phim',
                'phongChieu.rapChieu'
            ]);

        if (
            !empty(
                $filters['movieId']
            )
        ) {

            $query->where(
                'maPhim',
                $filters['movieId']
            );
        }

        return $query
            ->get();
    }
}
