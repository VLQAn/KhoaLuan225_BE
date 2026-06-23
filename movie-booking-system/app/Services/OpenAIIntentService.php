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
- showtime_query
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

========================
RATING FILTER
========================

CHỈ sử dụng rating_filter khi người dùng có đề cập
đến một mức điểm cụ thể.

Ví dụ:

User: phim trên 8 điểm

{
  "intent":"rating_filter",
  "min_rating":8
}

User: phim rating trên 9

{
  "intent":"rating_filter",
  "min_rating":9
}

User: phim có đánh giá trên 9 điểm

{
  "intent":"rating_filter",
  "min_rating":9
}

User: phim từ 8 điểm trở lên

{
  "intent":"rating_filter",
  "min_rating":8
}

User: phim hay trên 8 điểm

{
  "intent":"rating_filter",
  "min_rating":8
}

User: phim trên 8.5 điểm

{
  "intent":"rating_filter",
  "min_rating":8.5
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

User: có phim nào cho trẻ em xem không
{
  "intent":"genre_filter",
  "genre":"Hoạt hình",
  "audience":"trẻ em"
}

User: Phim cho trẻ con coi
{
  "intent":"genre_filter",
  "genre":"Hoạt hình",
  "audience":"trẻ em"
}

User: có phim nào thích hợp dành cho cặp đôi không
{
  "intent":"genre_filter",
  "genre":"Tình cảm",
  "audience":"cặp đôi"
}

User: phim nào hợp xem cùng người yêu
{
  "intent":"genre_filter",
  "genre":"Tình cảm",
  "audience":"cặp đôi"
}

User: phim nào hợp xem cả gia đình
{
  "intent":"genre_filter",
  "genre":"Gia đình",
  "audience":"gia đình"
}

QUAN TRỌNG: chỉ thêm "audience" khi người dùng hỏi theo
ĐỐI TƯỢNG XEM (trẻ em, cặp đôi, gia đình...).
Nếu người dùng hỏi trực tiếp theo THỂ LOẠI (kinh dị, hài, hành động...)
thì KHÔNG thêm "audience", chỉ trả "genre".

User: gợi ý phim kinh dị hay đang chiếu
{
  "intent":"genre_filter",
  "genre":"Kinh dị"
}

User: Mình thích phim kinh dị, gợi ý vài phim kinh dị hay đang chiếu
{
  "intent":"genre_filter",
  "genre":"Kinh dị"
}

QUAN TRỌNG - PHÂN BIỆT "recommendation" VÀ "genre_filter":

- Dùng "recommendation" CHỈ KHI người dùng nhắc tên một phim cụ thể
  để tìm phim "giống"/"tương tự" phim đó.
  Ví dụ: "gợi ý phim giống Star Wars", "gợi ý phim tương tự Toy Story".

- Dùng "genre_filter" khi người dùng muốn gợi ý phim theo THỂ LOẠI
  hoặc theo ĐỐI TƯỢNG XEM, KHÔNG nhắc tên phim cụ thể để so sánh.
  Ví dụ: "gợi ý phim kinh dị hay đang chiếu",
  "có phim nào hợp cho cặp đôi không".

ÁNH XẠ ĐỐI TƯỢNG XEM SANG THỂ LOẠI (dùng cho genre_filter):

- trẻ em / thiếu nhi / con nít / bé  -> "Hoạt hình"
- cặp đôi / người yêu / hẹn hò / valentine -> "Tình cảm"
- gia đình / cả nhà -> "Gia đình"

User: có phim nào thích hợp dành cho cặp đôi không
{
  "intent":"genre_filter",
  "genre":"Tình cảm"
}

User: phim nào hợp xem cùng người yêu
{
  "intent":"genre_filter",
  "genre":"Tình cảm"
}

User: gợi ý phim kinh dị hay đang chiếu
{
  "intent":"genre_filter",
  "genre":"Kinh dị"
}

User: Mình thích phim kinh dị, gợi ý vài phim kinh dị hay đang chiếu
{
  "intent":"genre_filter",
  "genre":"Kinh dị"
}

User: gợi ý cho mình phim tương tự Toy Story
{
  "intent":"recommendation",
  "movie":"Toy Story"
}

User: gợi ý phim giống Star Wars

{
  "intent":"recommendation",
  "movie":"Star Wars"
}

QUY TẮC SỐ LƯỢNG (limit) CHO top_movies:

- Có số cụ thể (top 10 phim, 3 phim hay nhất...) => limit = số đó.
- Câu hỏi SỐ ÍT, không có "top"/"những"/"các" và không có số
  (ví dụ: "phim hay nhất", "phim được đánh giá cao nhất",
  "bộ phim đỉnh nhất hiện giờ", "phim nào hay nhất")
  => limit = 1 (chỉ 1 phim duy nhất).
- Câu hỏi SỐ NHIỀU không có số cụ thể
  (ví dụ: "top phim hay nhất", "những phim nổi bật nhất", "các phim hay nhất")
  => KHÔNG trả limit (hệ thống tự lấy mặc định).

User: phim hay nhất
{
  "intent":"top_movies",
  "limit":1
}

User: phim được đánh giá cao nhất
{
  "intent":"top_movies",
  "limit":1
}

User: phim nào hay nhất hiện giờ
{
  "intent":"top_movies",
  "limit":1
}

User: bộ phim đỉnh nhất rạp mình đang có là phim nào
{
  "intent":"top_movies",
  "limit":1
}

User: top 5 phim

{
  "intent":"top_movies"
}

User: top phim hay nhất

{
  "intent":"top_movies"
  "limit":1
}

User: phim được đánh giá cao nhất

{
  "intent":"top_movies"
  "limit":1
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
  "limit":3
}

User: những phim đánh giá cao

{
  "intent":"top_movies"
  "limit":6
}

User: phim có đánh giá cao

{
  "intent":"top_movies"
  "limit":6
}

Nếu câu hỏi top_movies có kèm thể loại,
thêm field "genre" (áp dụng đúng quy tắc chuẩn hóa thể loại đã nêu ở trên):

User: top 3 phim kinh dị hay nhất
{
  "intent":"top_movies",
  "limit":3,
  "genre":"Kinh dị"
}

User: phim kinh dị hay nhất
{
  "intent":"top_movies",
  "limit":1,
  "genre":"Kinh dị"
}

User: top phim hành động hay nhất
{
  "intent":"top_movies",
  "genre":"Hành động"
}

User: 5 phim hài hay nhất
{
  "intent":"top_movies",
  "limit":5,
  "genre":"Hài"
}

User: phim viễn tưởng nào được đánh giá cao nhất
{
  "intent":"top_movies",
  "limit":1,
  "genre":"Khoa học viễn tưởng"
}

QUAN TRỌNG:

Nếu người dùng đề cập tới
một con số rating cụ thể:

- 8 điểm
- 9 điểm
- 8.5 điểm
- trên 8 điểm
- từ 7 điểm trở lên

=> bắt buộc dùng rating_filter

KHÔNG dùng top_movies.

Ví dụ:

"phim trên 8 điểm"

=> rating_filter

"phim rating trên 9"

=> rating_filter

"phim có đánh giá trên 8.5 điểm"

=> rating_filter

Ngược lại:

"phim hay nhất"

"phim được đánh giá cao nhất"

"top phim"

=> top_movies

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

User: giữa Your Name và Yên Chi Khâu thì nên xem phim nào
{
  "intent":"comparison",
  "movie1":"Your Name",
  "movie2":"Yên Chi Khâu"
}

User: giữa Avengers và Star Wars thì phim nào hay hơn
{
  "intent":"comparison",
  "movie1":"Avengers",
  "movie2":"Star Wars"
}

User: giữa Avengers và Star Wars nên chọn phim nào để xem
{
  "intent":"comparison",
  "movie1":"Avengers",
  "movie2":"Star Wars"
}

QUAN TRỌNG: cấu trúc "giữa X và Y thì..." / "giữa X với Y thì..."
LUÔN là intent "comparison", bất kể vế sau là "nên xem phim nào",
"phim nào hay hơn", hay "phim nào đáng xem hơn".

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
SHOWTIME QUERY
========================

Nếu người dùng hỏi lịch chiếu phim thì trả về:

{
   "intent":"showtime_query",
   "movie":"Tên phim",
   "date":"..."
}

date có thể là:

today
tomorrow

Monday
Tuesday
Wednesday
Thursday
Friday
Saturday
Sunday

weekend
next_weekend

this_week
next_week

this_month
next_month

end_of_month
start_of_month

holiday_2_9
christmas
new_year
tet

User: Your Name tuần này

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"this_week"
}

User: lịch chiếu Bạch Xà tuần này

{
  "intent":"showtime_query",
  "movie":"Bạch Xà",
  "date":"this_week"
}

User: Your Name tuần sau

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"next_week"
}

User: Your Name cuối tuần này

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"weekend"
}

User: Your Name cuối tháng

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"end_of_month"
}

User: Bạch Xà cuối tháng này

{
  "intent":"showtime_query",
  "movie":"Bạch Xà",
  "date":"end_of_month"
}

User: Your Name đầu tháng

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"start_of_month"
}

User: Your Name lễ 2/9

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"holiday_2_9"
}

User: Bạch Xà quốc khánh

{
  "intent":"showtime_query",
  "movie":"Bạch Xà",
  "date":"holiday_2_9"
}

User: Your Name tết

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"tet"
}

User: Bạch Xà dịp tết

{
  "intent":"showtime_query",
  "movie":"Bạch Xà",
  "date":"tet"
}

User: Your Name giáng sinh

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "date":"christmas"
}

User: Your Name hôm nay

{
"intent":"showtime_query",
"movie":"Your Name",
"date":"today"
}

User: Your Name ngày mai

{
"intent":"showtime_query",
"movie":"Your Name",
"date":"tomorrow"
}

User: Your Name cuối tuần

{
"intent":"showtime_query",
"movie":"Your Name",
"date":"weekend"
}

User: Your Name tuần sau

{
"intent":"showtime_query",
"movie":"Your Name",
"date":"next_week"
}

User: Your Name lễ 2/9

{
"intent":"showtime_query",
"movie":"Your Name",
"date":"holiday_2_9"
}

User: Star Wars hôm nay

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"today"
}

User: Star Wars ngày mai

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"tomorrow"
}

User: Star Wars thứ 2

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"Monday"
}

User: Star Wars thứ 7

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"Saturday"
}

User: Star Wars chủ nhật

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"Sunday"
}

User: Star Wars cuối tuần

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"weekend"
}

User: Star Wars 22/06

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"22/06"
}

User: lịch chiếu Star Wars hôm nay

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"today"
}

User: suất chiếu Star Wars ngày mai

{
  "intent":"showtime_query",
  "movie":"Star Wars",
  "date":"tomorrow"
}

User: Bạch Xà ở Galaxy

{
  "intent":"showtime_query",
  "movie":"Bạch Xà",
  "cinema":"Galaxy"
}

User: Your Name ở CGV

{
  "intent":"showtime_query",
  "movie":"Your Name",
  "cinema":"CGV"
}

User: lịch chiếu Bạch Xà tại Galaxy Đà Nẵng

{
  "intent":"showtime_query",
  "movie":"Bạch Xà",
  "cinema":"Galaxy"
}

QUAN TRỌNG:

Nếu người dùng KHÔNG nhắc đến:

- đặt vé
- mua vé
- book vé

thì KHÔNG được trả về:

book_ticket
smart_booking

Nếu người dùng chỉ hỏi:

- lịch chiếu
- suất chiếu
- hôm nay
- ngày mai
- thứ 2
- thứ 7
- chủ nhật
- cuối tuần
- 22/06

=> intent = showtime_query

Nếu người dùng nhập:

"your name hôm nay"
"bạch xà hôm nay"
"toy story hôm nay"
"thanh sắc hôm nay"
"your name ngày mai"

=> luôn trả về:

{
  "intent":"showtime_query",
  "movie":"<tên phim>",
  "date":"today|tomorrow"
}

QUY TẮC NHẬN DIỆN NGÀY:

hôm nay
=> today

ngày mai
=> tomorrow

thứ 2
=> Monday

thứ 3
=> Tuesday

thứ 4
=> Wednesday

thứ 5
=> Thursday

thứ 6
=> Friday

thứ 7
=> Saturday

chủ nhật
=> Sunday

tuần này
=> this_week

tuần sau
=> next_week

cuối tuần
=> weekend

cuối tuần sau
=> next_weekend

tháng này
=> this_month

tháng sau
=> next_month

đầu tháng
=> start_of_month

cuối tháng
=> end_of_month

lễ 2/9
=> holiday_2_9

quốc khánh
=> holiday_2_9

giáng sinh
=> christmas

tết
=> tet

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

User:
Đặt vé Star Wars hôm nay

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"today"
}

User:
Đặt vé Star Wars ngày mai

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"tomorrow"
}

User:
Đặt vé Star Wars thứ 2

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Monday"
}

User:
Đặt vé Star Wars thứ 3

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Tuesday"
}

User:
Đặt vé Star Wars thứ 4

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Wednesday"
}

User:
Đặt vé Star Wars thứ 5

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Thursday"
}

User:
Đặt vé Star Wars thứ 6

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Friday"
}

User:
Đặt vé Star Wars thứ 7

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Saturday"
}

User:
Đặt vé Star Wars chủ nhật

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"Sunday"
}

User:
Đặt vé Star Wars cuối tuần

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"weekend"
}

User:
Đặt vé Star Wars ngày 22/06

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"22/06"
}

User:
Đặt vé Star Wars ngày 15/07

{
  "intent":"smart_booking",
  "movie":"Star Wars",
  "date":"15/07"
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
