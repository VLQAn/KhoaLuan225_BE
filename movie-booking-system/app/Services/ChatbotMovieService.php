<?php

namespace App\Services;

use App\Models\Phim;
use App\Helpers\TextHelper;

class ChatbotMovieService
{
    /*
    =========================
    PHÂN TÍCH Ý ĐỊNH PHIM
    =========================
    */

    /*
    =========================
    SO SÁNH PHIM
    =========================
    */
    public function detectComparison(
        string $message
    ) {

        $message =
            TextHelper::normalize(
                $message
            );

        if (
            !str_contains(
                $message,
                'so sanh'
            )
        ) {
            return null;
        }

        $message =
            str_replace(
                'so sanh',
                '',
                $message
            );

        $parts =
            preg_split(
                '/\s+(va|voi)\s+/',
                $message
            );

        if (
            count($parts) < 2
        ) {
            return null;
        }

        $movie1 =
            $this->findMovie(
                trim($parts[0])
            );

        $movie2 =
            $this->findMovie(
                trim($parts[1])
            );

        if (
            !$movie1 ||
            !$movie2
        ) {
            return null;
        }

        return [
            'movie1' => $movie1,
            'movie2' => $movie2
        ];
    }

    /*
    =========================
    RATING
    =========================
    */
    public function detectRating(
        string $message
    ) {
        $message =
            TextHelper::normalize(
                $message
            );

        if (
            !preg_match(
                '/(tren|tu)\s*(\d+(\.\d+)?)/',
                $message,
                $matches
            )
        ) {

            return null;
        }

        return (float)
        $matches[2];
    }

    /*
    =========================
    THỂ LOẠI
    =========================
    */
    public function detectGenre(
        string $message
    ) {
        $message =
            TextHelper::normalize(
                $message
            );

        $genres = [

            'hanh dong',

            'kinh di',

            'hai',

            'vien tuong',

            'tinh cam',

            'phieu luu',

            'hoat hinh'
        ];

        foreach (
            $genres as $genre
        ) {

            if (
                str_contains(
                    $message,
                    $genre
                )
            ) {

                return $genre;
            }
        }

        return null;
    }

    /*
    =========================
    TOP PHIM
    =========================
    */
    public function detectTopMovies(
        string $message
    ) {
        $message =
            TextHelper::normalize(
                $message
            );

        return
            str_contains(
                $message,
                'top'
            )
            ||
            str_contains(
                $message,
                'hay nhat'
            )
            ||
            str_contains(
                $message,
                'danh gia cao'
            );
    }

    /*
    =========================
    GỢI Ý PHIM
    =========================
    */
    public function detectRecommendation(
        string $message
    ) {
        $message =
            TextHelper::normalize(
                $message
            );

        return
            str_contains(
                $message,
                'goi y'
            )
            ||
            str_contains(
                $message,
                'tuong tu'
            )
            ||
            str_contains(
                $message,
                'giong'
            )
            ||
            str_contains(
                $message,
                'de xuat'
            );
    }

    /*
    =========================
    TRUY VẤN DỮ LIỆU PHIM
    =========================
    */
    public function getRecommendedMovies(
        string $movieName
    ) {
        $movie =
            $this->findMovie(
                $movieName
            );

        if (!$movie) {
            return collect();
        }

        $genreIds =
            $movie->theLoai
            ->pluck('maTheLoai');

        return Phim::with('theLoai')
            ->where(
                'maPhim',
                '!=',
                $movie->maPhim
            )
            ->whereHas(
                'theLoai',
                function ($query)
                use ($genreIds) {

                    $query->whereIn(
                        'the_loai.maTheLoai',
                        $genreIds
                    );
                }
            )
            ->limit(5)
            ->get();
    }

    /*
    =========================
    THÔNG TIN PHIM
    =========================
    */
    public function findMovie(string $message)
    {
        $message =
            TextHelper::cleanMovieQuery(
                $message
            );

        $movies =
            Phim::with(
                'theLoai'
            )->get();

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

    public function getMovieByName(
        string $movieName
    ) {
        return $this->findMovie(
            $movieName
        );
    }

    // public function getMovieInfo(
    //     string $message
    // ) {
    //     $movie =
    //         $this->findMovie(
    //             $message
    //         );

    //     if (!$movie) {
    //         return null;
    //     }

    //     $infoType =
    //         $this->detectMovieInfoIntent(
    //             $message
    //         );

    //     return [
    //         'movie' => $movie,
    //         'infoType' => $infoType
    //     ];
    // }

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

    public function getMoviesByGenre(
        string $genre
    ) {
        $genreMap = [

            'hanh dong' => 'Hành động',

            'kinh di' => 'Kinh dị',

            'hai' => 'Hài',

            'vien tuong' => 'Viễn tưởng',

            'tinh cam' => 'Tình cảm',

            'phieu luu' => 'Phiêu lưu',

            'hoat hinh' => 'Hoạt hình'
        ];

        $realGenre =
            $genreMap[$genre]
            ?? $genre;

        return Phim::with('theLoai')
            ->whereHas(
                'theLoai',
                function ($query)
                use ($realGenre) {

                    $query->where(
                        'tenTheLoai',
                        $realGenre
                    );
                }
            )
            ->get();
    }

    public function getTopMovies(
        int $limit = 5
    ) {
        return Phim::orderByDesc(
            'danhGia'
        )
            ->limit(
                $limit
            )
            ->get();
    }

    // public function detectMovieInfoIntent(
    //     string $message
    // ) {
    //     $message =
    //         TextHelper::normalize(
    //             $message
    //         );

    //     if (
    //         str_contains(
    //             $message,
    //             'noi dung'
    //         )
    //     ) {
    //         return 'summary';
    //     }

    //     if (
    //         str_contains(
    //             $message,
    //             'dao dien'
    //         )
    //     ) {
    //         return 'director';
    //     }

    //     if (
    //         str_contains(
    //             $message,
    //             'dien vien'
    //         )
    //     ) {
    //         return 'actor';
    //     }

    //     return null;
    // }

    /*
    =========================
    ĐẶT VÉ
    =========================
    */

    /*
    =========================
    OPENAI FALLBACK
    =========================
    */
}
