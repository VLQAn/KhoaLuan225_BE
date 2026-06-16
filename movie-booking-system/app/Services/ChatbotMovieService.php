<?php

namespace App\Services;

use App\Models\Phim;
use App\Helpers\TextHelper;

class ChatbotMovieService
{
    public function findMovie(string $message)
    {
        $message =
            TextHelper::cleanMovieQuery(
                $message
            );

        $movies = Phim::all();

        /*
    =========================
    ƯU TIÊN MATCH TRỰC TIẾP
    =========================
    */

        foreach ($movies as $movie) {

            $title =
                TextHelper::normalize(
                    $movie->tieuDe
                );

            if (
                str_contains(
                    $title,
                    $message
                )
            ) {

                return $movie;
            }
        }

        /*
    =========================
    FALLBACK FUZZY MATCH
    =========================
    */

        $bestMovie = null;
        $bestScore = 0;

        foreach ($movies as $movie) {

            $title =
                TextHelper::normalize(
                    $movie->tieuDe
                );

            similar_text(
                $message,
                $title,
                $percent
            );

            if ($percent > $bestScore) {

                $bestScore = $percent;
                $bestMovie = $movie;
            }
        }

        return $bestScore >= 40
            ? $bestMovie
            : null;
    }

    public function detectMovieInfoIntent(
        string $message
    ) {
        $message =
            TextHelper::normalize(
                $message
            );

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
            $message =
                TextHelper::normalize(
                    $message
                );

            $title =
                TextHelper::normalize(
                    $movie->tieuDe
                );

            if (
                str_contains(
                    $message,
                    $title
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

    public function detectRating(
        string $message
    ) {
        preg_match(
            '/(\d+(\.\d+)?)/',
            $message,
            $matches
        );

        return
            $matches[1]
            ?? null;
    }

    public function getMoviesByRating(
        float $rating
    ) {
        return Phim::where(
            'danhGia',
            '>=',
            $rating
        )
            ->orderByDesc(
                'danhGia'
            )
            ->get();
    }
}
