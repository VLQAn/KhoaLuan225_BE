<?php

namespace App\Services;

use App\Models\Phim;

class ChatbotBookingService
{
    public function findMovie(
        string $message
    )
    {
        $movies = Phim::all();

        foreach ($movies as $movie) {

            if (
                str_contains(
                    mb_strtolower($message),
                    mb_strtolower($movie->tieuDe)
                )
            ) {

                return $movie;
            }
        }

        return null;
    }
}
