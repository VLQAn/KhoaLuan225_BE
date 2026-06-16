import { normalizeText } from "./chatbotUtils";
import { findBestMovieMatch } from "./chatbotMovie";

export const ChatbotMovieService = {

    findMovie(
        query,
        movies
    ) {

        let movie =
            movies.find(m => {

                const name =
                    normalizeText(
                        m.tieuDe
                    );

                return (
                    query.includes(name)
                    ||
                    name.includes(query)
                );
            });

        if (!movie) {

            movie =
                findBestMovieMatch(
                    query,
                    movies
                );
        }

        return movie;
    },

    getTopMovies(
        movies,
        limit = 5
    ) {

        return movies
            .filter(
                movie =>
                    movie.trangThai ===
                    "dang_chieu"
            )
            .sort(
                (a, b) =>
                    Number(b.danhGia || 0)
                    -
                    Number(a.danhGia || 0)
            )
            .slice(0, limit);
    },

    getMoviesByGenre(
        movies,
        genre
    ) {

        return movies.filter(movie =>

            movie.trangThai ===
            "dang_chieu"

            &&

            movie.theLoai?.some(
                type =>

                    normalizeText(
                        type.tenTheLoai
                    ) === genre
            )
        );
    },

    getMoviesByRating(
        movies,
        rating
    ) {

        return movies
            .filter(movie =>

                Number(
                    movie.danhGia || 0
                ) >= rating

                &&

                movie.trangThai ===
                "dang_chieu"
            )
            .sort(
                (a, b) =>
                    Number(b.danhGia)
                    -
                    Number(a.danhGia)
            );
    }
};
