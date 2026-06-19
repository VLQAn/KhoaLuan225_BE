<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
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

Chỉ trả về JSON hợp lệ.

KHÔNG được trả về:

```json
{ ... }

Các intent hợp lệ:

- movie_info
- recommendation
- comparison
- genre_filter
- rating_filter
- top_movies
- book_ticket
- smart_booking
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

User: đặt vé Star Wars

{
  "intent":"book_ticket",
  "movie":"Star Wars"
}

User: đặt 2 vé Star Wars

{
  "intent":"book_ticket",
  "movie":"Star Wars",
  "quantity":2
}

User: mua 3 vé Avengers

{
  "intent":"book_ticket",
  "movie":"Avengers",
  "quantity":3
}

User: tôi muốn đặt vé phim Star Wars

{
  "intent":"book_ticket",
  "movie":"Star Wars"
}

User: đặt 4 vé Star Wars lúc 18 giờ

{
  "intent":"book_ticket",
  "movie":"Star Wars",
  "quantity":4,
  "time":"18:00"
}


========================
SMART BOOKING
========================

User:
Đặt 2 vé Star Wars tối nay giờ đẹp ghế đẹp tại Galaxy Đà Nẵng

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "quantity":2,
  "city":"Đà Nẵng",
  "cinema":"Galaxy",
  "date":"today",
  "time_period":"evening",
  "seat_preference":"best"
}

User:
Đặt 2 vé Star Wars tối nay giờ đẹp ghế đẹp tại Đà Nẵng

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "quantity":2,
  "city":"Đà Nẵng",
  "date":"today",
  "time_period":"evening",
  "seat_preference":"best"
}

User:
Đặt 2 vé Star Wars tối nay giờ đẹp ghế đẹp

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "quantity":2,
  "date":"today",
  "time_period":"evening",
  "seat_preference":"best"
}

User:
Đặt 2 vé Star Wars tối nay

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "quantity":2,
  "date":"today",
  "time_period":"evening"
}

User:
Đặt vé Star Wars ngày 22/6 tại Nha Trang

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "city":"Nha Trang",
  "date":"22/6"
}

User:
Đặt vé Star Wars thứ 7 này

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Saturday"
}

User:
Đặt vé Star Wars sáng mai

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"tomorrow",
  "time_period":"morning"
}

User:
Đặt vé Star Wars chiều mai

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"tomorrow",
  "time_period":"afternoon"
}

User:
Đặt vé Star Wars tối mai

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"tomorrow",
  "time_period":"evening"
}

THÀNH PHỐ HỢP LỆ

- Đà Nẵng
- Hồ Chí Minh
- Sài Gòn
- Hà Nội
- Nha Trang
- Hải Phòng
- Cần Thơ
- Huế
- Vũng Tàu
- Biên Hòa

RẠP HỢP LỆ

- Galaxy
- CGV
- Lotte
- BHD
- Beta
- Cinestar

Nếu người dùng nói:

sáng
=> morning

chiều
=> afternoon

tối
=> evening

giờ đẹp
=> best_time = true

ghế đẹp
=> seat_preference = best

'
        ],

        [
          'role' => 'user',
          'content' => $message
        ]
      ],

      'temperature' => 0
    ]);

    $content =
      trim(
        $response->choices[0]
          ->message
          ->content
      );

    $content =
      preg_replace(
        '/^```json\s*/',
        '',
        $content
      );

    $content =
      preg_replace(
        '/\s*```$/',
        '',
        $content
      );

    $result =
      json_decode(
        $content,
        true
      );

    Log::info('OPENAI_INTENT', $result);

    Log::info(
      'SMART_BOOKING_PARSE',
      $result
    );

    Log::info('OPENAI_RAW_RESPONSE', [
      'raw_content' => $content,
      'parsed_result' => $result,
      'json_error' => json_last_error_msg()
    ]);

    if (
      json_last_error() !== JSON_ERROR_NONE
    ) {

      return [
        'intent' => 'unknown'
      ];
    }

    // Fallback: nếu intent là book_ticket nhưng không có quantity,
    // thử extract từ message bằng regex
    Log::info('OPENAI_PARSE_RESULT', [
      'intent' => $result['intent'] ?? null,
      'has_quantity' => !empty($result['quantity']),
      'quantity_value' => $result['quantity'] ?? null
    ]);

    if (
      $result['intent'] === 'book_ticket' &&
      empty($result['quantity'])
    ) {
      Log::info('FALLBACK_TRIGGERED', [
        'message' => $message,
        'intent' => $result['intent']
      ]);
      $quantity = $this->extractQuantityFromMessage($message);
      Log::info('FALLBACK_RESULT', [
        'message' => $message,
        'extracted_quantity' => $quantity
      ]);
      if ($quantity) {
        $result['quantity'] = $quantity;
        Log::info('QUANTITY_EXTRACTED_FROM_FALLBACK', [
          'message' => $message,
          'quantity' => $quantity
        ]);
      }
    }

    if ($result['intent'] === 'book_ticket') {
      Log::info('BOOKING_INTENT_DETECTED', [
        'message' => $message,
        'result' => $result
      ]);
    }

    return $result;
  }

  /**
   * Extract số lượng vé từ message
   * Ví dụ: "đặt 2 vé", "mua 3 vé", "tôi muốn 5 vé"
   */
  private function extractQuantityFromMessage(string $message): ?int
  {
    $lowerMessage = mb_strtolower($message);
    Log::info('EXTRACT_QUANTITY_DEBUG', [
      'original' => $message,
      'lowercase' => $lowerMessage
    ]);

    // Pattern: số trước từ khóa vé/vé/ticket
    // "đặt 2 vé", "mua 5 vé", "tôi muốn 3 vé"
    if (preg_match('/(\d+)\s*(?:cai|chiếc|tấm|ve|vé|ticket|vé phim|vé xem|vé chiếu|bộ vé)/u', $lowerMessage, $matches)) {
      Log::info('PATTERN1_MATCHED', [
        'message' => $lowerMessage,
        'matches' => $matches,
        'quantity' => (int) $matches[1]
      ]);
      return (int) $matches[1];
    }

    Log::info('PATTERN1_NO_MATCH', [
      'message' => $lowerMessage
    ]);

    // Pattern: từ khóa trước số
    // "2 vé", "3 vé", "5 vé"
    if (preg_match('/(đặt|mua|book|order|buy)?\s*(\d+)\s*(?:cai|chiếc|tấm|ve|vé|ticket)/u', $lowerMessage, $matches)) {
      Log::info('PATTERN2_MATCHED', [
        'message' => $lowerMessage,
        'matches' => $matches,
        'quantity' => (int) $matches[2]
      ]);
      return (int) $matches[2];
    }

    Log::info('PATTERN2_NO_MATCH', [
      'message' => $lowerMessage
    ]);

    return null;
  }
}
