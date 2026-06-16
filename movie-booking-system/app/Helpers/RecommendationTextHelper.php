<?php

namespace App\Helpers;

class RecommendationTextHelper
{
    public static function extractMovieNameFromRecommendation(
        string $message
    ) {
        $message =
            TextHelper::normalize(
                $message
            );

        $keywords = [
            'goi y',
            'tuong tu',
            'giong',
            'de xuat',
            'phim'
        ];

        foreach (
            $keywords as $keyword
        ) {
            $message =
                str_replace(
                    $keyword,
                    '',
                    $message
                );
        }

        return trim($message);
    }
}
