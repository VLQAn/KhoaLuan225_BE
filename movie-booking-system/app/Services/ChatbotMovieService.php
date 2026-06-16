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

    public function detectMovieInfoIntent(
        string $message
    ) {
        $message =
            mb_strtolower($message);

        if (
            str_contains(
                $message,
                'noi dung'
            )
        ) {

            return 'summary';
        }

        if (
            str_contains(
                $message,
                'dao dien'
            )
        ) {

            return 'director';
        }

        if (
            str_contains(
                $message,
                'dien vien'
            )
        ) {

            return 'actor';
        }

        return null;
    }

    public function getMovieInfo(
        string $message
    ) {
        $movie =
            $this->findMovie(
                $message
            );

        if (!$movie) {
            return null;
        }

        $infoType =
            $this->detectMovieInfoIntent(
                $message
            );

        return [
            'movie' => $movie,
            'infoType' => $infoType
        ];
    }

    public function detectComparison(
        string $message
    ) {
        $movies =
            Phim::all();

        $found = [];

        foreach (
            $movies as $movie
        ) {

            if (
                str_contains(
                    mb_strtolower($message),
                    mb_strtolower($movie->tieuDe)
                )
            ) {

                $found[] =
                    $movie;
            }
        }

        if (
            count($found) >= 2
        ) {

            return [
                'movie1' =>
                $found[0],

                'movie2' =>
                $found[1]
            ];
        }

        return null;
    }
}
