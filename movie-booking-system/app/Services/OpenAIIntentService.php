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

User: gợi ý phim giống Star Wars

{
  "intent":"recommendation",
  "movie":"Star Wars"
}

User: top 5 phim

{
  "intent":"top_movies"
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
