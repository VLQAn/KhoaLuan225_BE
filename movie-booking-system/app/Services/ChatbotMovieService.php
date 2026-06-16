<?php

namespace App\Services;

use App\Models\Phim;

class ChatbotMovieService
{
    public function findMovie(
        string $message
    ) {
        $message =
            mb_strtolower($message);

        $movies =
            Phim::with('theLoai')
            ->get();

        foreach ($movies as $movie) {

            if (
                str_contains(
                    $message,
                    mb_strtolower(
                        $movie->tieuDe
                    )
                )
            ) {
                return $movie;
            }
        }

        return null;
    }
}
