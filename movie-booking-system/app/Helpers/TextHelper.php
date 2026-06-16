<?php

namespace App\Helpers;

class TextHelper
{
    public static function normalize(
        string $text
    ): string {

        $text =
            mb_strtolower($text);

        $text =
            iconv(
                'UTF-8',
                'ASCII//TRANSLIT',
                $text
            );

        return trim($text);
    }

    public static function removeAccents(
        string $text
    ): string {

        return self::normalize(
            $text
        );
    }

    public static function cleanMovieQuery(
        string $text
    ): string {

        $text =
            self::normalize(
                $text
            );

        return preg_replace(
            '/(noi dung|tom tat|dao dien|dien vien|phim|cho xem)/u',
            '',
            $text
        );
    }
}
