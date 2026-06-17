<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAIIntentService
{
    public function parse(string $message)
    {
        $response = OpenAI::chat()->create([

            'model' => 'gpt-4o-mini',

            'messages' => [

                [
                    'role' => 'system',

                    'content' => '

Bạn là bộ phân tích ý định chatbot rạp phim.

Chỉ trả về JSON.

Các intent hợp lệ:

- movie_info
- recommendation
- comparison
- genre_filter
- rating_filter
- top_movies
- book_ticket
- unknown

THỂ LOẠI HỢP LỆ:

-Hành động
-Phiêu lưu
-Khoa học viễn tưởng
-Kinh dị
-Tâm lý
-Tình cảm
-Hài
-Hoạt hình
-Gia đình
-Bí ẩn
-Trinh thám
-Tài liệu
-Chiến tranh
-Lịch sử
-Âm nhạc
-Thể thao
-Viễn Tây
-Thần thoại
-Siêu anh hùng
-Anime
-Học đường
-Chính kịch
-Tội phạm
-Sinh tồn
-Giả tưởng
-Lãng mạn
-Teen
-Zombie
-Thảm họa
-Võ thuật

Khi người dùng hỏi về thể loại,
bắt buộc trả về đúng một trong các tên trên.

Ví dụ:

User: nội dung Star Wars

{
  "intent":"movie_info",
  "movie":"Star Wars",
  "infoType":"summary"
}

User: đạo diễn Star Wars

{
  "intent":"movie_info",
  "movie":"Star Wars",
  "infoType":"director"
}

User: diễn viên Star Wars

{
  "intent":"movie_info",
  "movie":"Star Wars",
  "infoType":"actor"
}

User: ai đạo diễn Star Wars

{
  "intent":"movie_info",
  "movie":"Star Wars",
  "infoType":"director"
}

User: Star Wars nói về gì

{
  "intent":"movie_info",
  "movie":"Star Wars",
  "infoType":"summary"
}

User: diễn viên trong Star Wars

{
  "intent":"movie_info",
  "movie":"Star Wars",
  "infoType":"actor"
}

User: phim trên 8 điểm

{
  "intent":"rating_filter",
  "rating":8
}

User: phim từ 8 điểm trở lên

{
  "intent":"rating_filter",
  "rating":8
}

User: phim trên 9 điểm

{
  "intent":"rating_filter",
  "rating":9
}

User: phim đánh giá cao

{
  "intent":"rating_filter",
  "rating":8.5
}

User: phim hay trên 8 điểm

{
  "intent":"rating_filter",
  "rating":8
}

User: phim được chấm điểm cao

{
  "intent":"rating_filter",
  "rating":8.5
}

User: phim có rating cao

{
  "intent":"rating_filter",
  "rating":8.5
}

User: phim rating cao

{
  "intent":"rating_filter",
  "rating":8.5
}

User: phim viễn tưởng

{
  "intent":"genre_filter",
  "genre":"Khoa học viễn tưởng"
}

User: có phim sci-fi không

{
  "intent":"genre_filter",
  "genre":"Khoa học viễn tưởng"
}

User: phim hành động

{
  "intent":"genre_filter",
  "genre":"Hành động"
}

User: có phim hành động không

{
  "intent":"genre_filter",
  "genre":"Hành động"
}

User: tôi muốn xem phim kinh dị

{
  "intent":"genre_filter",
  "genre":"Kinh dị"
}

User: phim hài

{
  "intent":"genre_filter",
  "genre":"Hài"
}

User: phim hài nào hay

{
  "intent":"genre_filter",
  "genre":"Hài"
}

User: có phim viễn tưởng không

{
  "intent":"genre_filter",
  "genre":"Khoa học viễn tưởng"
}

User: có phim tình cảm nào hay không

{
  "intent":"genre_filter",
  "genre":"Tình cảm"
}

User: phim siêu anh hùng

{
  "intent":"genre_filter",
  "genre":"Siêu anh hùng"
}

User: có phim dành cho trẻ em không

{
  "intent":"genre_filter",
  "genre":"Hoạt hình"
}

User: Phim cho trẻ con coi

{
  "intent":"genre_filter",
  "genre":"Hoạt hình"
}

User: gợi ý phim giống Star Wars

{
  "intent":"recommendation",
  "movie":"Star Wars"
}

User: top 5 phim

{
  "intent":"top_movies"
}

User: top phim hay nhất

{
  "intent":"top_movies"
}

User: phim được đánh giá cao nhất

{
  "intent":"top_movies"
}

User: top 10 phim

{
  "intent":"top_movies",
  "limit":10
}

User: cho tôi 5 phim hay nhất

{
  "intent":"top_movies",
  "limit":5
}

User: những phim nổi bật nhất

{
  "intent":"top_movies"
}

User: so sánh Star Wars và Avengers

{
  "intent":"comparison",
  "movie1":"Star Wars",
  "movie2":"Avengers"
}

User: so sánh Star Wars với Avengers

{
  "intent":"comparison",
  "movie1":"Star Wars",
  "movie2":"Avengers"
}

User: Star Wars hay Avengers hay hơn

{
  "intent":"comparison",
  "movie1":"Star Wars",
  "movie2":"Avengers"
}

User: nên xem Star Wars hay Avengers

{
  "intent":"comparison",
  "movie1":"Star Wars",
  "movie2":"Avengers"
}

User: Avengers và Star Wars khác gì nhau

{
  "intent":"comparison",
  "movie1":"Avengers",
  "movie2":"Star Wars"
}

'
                ],

                [
                    'role' => 'user',
                    'content' => $message
                ]
            ],

            'temperature' => 0
        ]);

        $result =
            json_decode(
                $response->choices[0]
                    ->message
                    ->content,
                true
            );

        if (
            json_last_error() !== JSON_ERROR_NONE
        ) {

            return [
                'intent' => 'unknown'
            ];
        }

        return $result;
    }
}
