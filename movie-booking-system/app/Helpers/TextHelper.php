<?php

namespace App\Helpers;

class TextHelper
{
    public static function normalize(
        string $text
    ): string {

        $text =
            mb_strtolower($text);

        $search = [
            'à','á','ạ','ả','ã',
            'â','ầ','ấ','ậ','ẩ','ẫ',
            'ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ',
            'ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ',
            'ô','ồ','ố','ộ','ổ','ỗ',
            'ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ',
            'ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ'
        ];

        $replace = [
            'a','a','a','a','a',
            'a','a','a','a','a','a',
            'a','a','a','a','a','a',
            'e','e','e','e','e',
            'e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o',
            'o','o','o','o','o','o',
            'o','o','o','o','o','o',
            'u','u','u','u','u',
            'u','u','u','u','u','u',
            'y','y','y','y','y',
            'd'
        ];

        return trim(
            str_replace(
                $search,
                $replace,
                $text
            )
        );
    }

    public static function cleanMovieQuery(
        string $text
    ): string {

        $text =
            self::normalize(
                $text
            );

        return trim(
            preg_replace(
                '/(noi dung|tom tat|dao dien|dien vien|phim|cho xem)/u',
                '',
                $text
            )
        );
    }
}
