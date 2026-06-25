<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\ChatbotContextService;
use App\Services\ChatbotIntentService;
use App\Services\ChatbotBookingService;
use App\Services\ChatbotSessionService;
use App\Services\ChatbotMovieService;
use App\Services\OpenAIIntentService;
use App\Services\SmartBookingService;
use App\Models\XuatChieu;
use App\Services\XuatChieuService;

class ChatbotAIController extends Controller
{
    protected $contextService;
    protected $intentService;
    protected $bookingService;
    protected $sessionService;
    protected $movieService;
    protected $openAIIntentService;
    protected $smartBookingService;
    protected $xuatChieuService;

    public function __construct(
        ChatbotContextService $contextService,
        ChatbotIntentService $intentService,
        ChatbotBookingService $bookingService,
        ChatbotSessionService $sessionService,
        ChatbotMovieService $movieService,
        OpenAIIntentService $openAIIntentService,
        SmartBookingService $smartBookingService,
        XuatChieuService $xuatChieuService
    ) {
        $this->contextService = $contextService;
        $this->intentService = $intentService;
        $this->bookingService = $bookingService;
        $this->sessionService = $sessionService;
        $this->movieService = $movieService;
        $this->openAIIntentService = $openAIIntentService;
        $this->smartBookingService = $smartBookingService;
        $this->xuatChieuService = $xuatChieuService;
    }

    private function normalizeGenre(string $genre)
    {
        $map = [
            'Viễn tưởng' => 'Khoa học viễn tưởng',
            'Sci-fi' => 'Khoa học viễn tưởng',
            'Khoa học viễn tưởng' => 'Khoa học viễn tưởng',
            'Trẻ em' => 'Hoạt hình',
            'Thiếu nhi' => 'Hoạt hình',

            // mới thêm cho gợi ý theo đối tượng xem
            'Cặp đôi' => 'Tình cảm',
            'Người yêu' => 'Tình cảm',
            'Hẹn hò' => 'Tình cảm',
            'Valentine' => 'Tình cảm',
        ];

        return $map[$genre] ?? $genre;
    }

    private function buildComparisonVerdict($movie1, $movie2)
    {
        $describe = function ($movie) {
            $genres = $movie->theLoai->pluck('tenTheLoai')->implode(', ');

            return
                "- Tên: {$movie->tieuDe}\n" .
                "- Thể loại: {$genres}\n" .
                "- Đánh giá: {$movie->danhGia}/10\n" .
                "- Thời lượng: {$movie->thoiLuong} phút\n" .
                "- Đạo diễn: {$movie->daoDien}\n" .
                "- Diễn viên: {$movie->dienVien}\n" .
                "- Tóm tắt: {$movie->moTa}\n";
        };

        $prompt =
            "Dưới đây là dữ liệu 2 bộ phim:\n\n" .
            "PHIM 1:\n" . $describe($movie1) . "\n" .
            "PHIM 2:\n" . $describe($movie2);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'temperature' => 0.3,
            'max_tokens' => 300,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "
Bạn là trợ lý tư vấn phim của RACSO Cinema.

NHIỆM VỤ: dựa vào dữ liệu 2 bộ phim được cung cấp, hãy:
1. Kết luận nên xem phim nào (chọn 1 trong 2; nếu thực sự khó phân định
   thì nói rõ là hai phim ngang nhau và gợi ý theo sở thích).
2. Giải thích lý do ngắn gọn, dựa trên khác biệt cụ thể
   (đánh giá, thể loại, thời lượng, nội dung).

QUY TẮC:
- CHỈ dùng dữ liệu được cung cấp, KHÔNG bịa thêm thông tin
  (không tự thêm doanh thu, giải thưởng, đánh giá khán giả... nếu không có trong dữ liệu).
- Trả lời bằng tiếng Việt, tối đa 3-4 câu, văn phong tự nhiên như đang tư vấn trực tiếp.
- Không liệt kê gạch đầu dòng, viết thành đoạn văn liền mạch.
- Nếu điểm đánh giá gần bằng nhau, ưu tiên gợi ý theo khác biệt thể loại/nội dung
  thay vì chỉ dựa vào điểm số.
"
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        return trim($response->choices[0]->message->content);
    }

    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $session =
            $this->sessionService
            ->getOrCreate(
                Auth::id()
            );

        Log::info('SESSION_LOADED', [
            'session_id' => $session->maPhien,
            'duLieu' => $session->duLieu,
            'movie' => $session->phimDangChon,
            'showtime' => $session->xuatChieuDangChon
        ]);

        // Parse duLieu JSON string đúng cách - handle both array and string
        $data = is_array($session->duLieu)
            ? $session->duLieu
            : json_decode(
                $session->duLieu ?? '{}',
                true
            );

        $currentStep =
            $data['booking_step']
            ?? null;

        Log::info('BOOKING_GATE_CHECK', [
            'currentStep' => $currentStep,
            'message' => $request->message,
            'isNewBookingRequest' =>
            $this->bookingService
                ->isNewBookingRequest(
                    $request->message
                )
        ]);

        if ($currentStep === 'payment') {

            Log::info('BOOKING_COMPLETED_CLEAR_SESSION');

            $this->sessionService
                ->clearSession(
                    $session->maPhien
                );

            $session->refresh();

            $currentStep = null;
        }

        Log::info('CURRENT_STEP', [
            'step' => $currentStep,
            'data' => $data
        ]);

        if (
            in_array(
                $currentStep,
                [
                    'select_showtime',
                    'select_seat',
                    'ask_food',
                    'select_food',
                    'checkout',
                    'confirm_booking',
                    'payment',
                    'smart_booking_ready'
                ]
            )
            &&
            !$this->bookingService
                ->isNewBookingRequest(
                    $request->message
                )
        ) {

            $this->sessionService
                ->saveMessage(
                    $session->maPhien,
                    'user',
                    $request->message
                );

            $session->refresh();

            Log::info('AUTH_USER', [
                'id' => Auth::id()
            ]);

            $bookingData =
                $this->bookingService
                ->handle(
                    $request->message,
                    Auth::id()
                );

            if (
                !empty($bookingData['reply'])
            ) {

                $this->sessionService
                    ->saveMessage(
                        $session->maPhien,
                        'bot',
                        $bookingData['reply']
                    );
            }

            return response()->json(
                $bookingData
            );
        }

        $aiIntent =
            $this->openAIIntentService
            ->parse(
                $request->message
            );

        $aiIntentName =
            $aiIntent['intent']
            ?? 'unknown';

        $this->sessionService
            ->saveMessage(

                $session->maPhien,

                'user',

                $request->message
            );

        $intent =
            $this->intentService
            ->detect($request->message);

        $intentName =
            $intent['intent'];

        $context =
            $this->contextService
            ->buildContext();

        $movieText = "";

        foreach ($context['movies'] as $movie) {

            $movieText .=
                "- {$movie->tieuDe}
                ({$movie->trangThai})
                \n";
        }

        $promoText = "";

        foreach (
            $context['promotions']
            as $promo
        ) {

            $promoText .=
                "- {$promo->tenKhuyenMai}
                giảm {$promo->giaKhuyenMai}%\n";
        }

        $systemPrompt = "

Bạn là trợ lý ảo của RACSO Cinema.

NHIỆM VỤ:

- Tư vấn phim
- Tư vấn lịch chiếu
- Hướng dẫn đặt vé
- Tư vấn khuyến mãi

QUY TẮC:

- Luôn trả lời bằng tiếng Việt.
- Trả lời ngắn gọn.
- Không bịa dữ liệu.
- Chỉ sử dụng dữ liệu được cung cấp.
- Nếu không có dữ liệu hãy nói:
  'Xin lỗi, hiện tôi chưa có thông tin này.'

=================
PHIM ĐANG CHIẾU
=================

{$movieText}

=================
KHUYẾN MÃI
=================

{$promoText}

";

        Log::info('RULE INTENT', [
            'intent' => $intentName
        ]);

        Log::info(
            'AI INTENT',
            [
                'intent' => $aiIntentName,
                'data' => $aiIntent
            ]
        );

        if (
            $aiIntentName === 'book_ticket'
            &&
            (
                !empty($aiIntent['city'])
                ||
                !empty($aiIntent['cinema'])
                ||
                !empty($aiIntent['date'])
            )
        ) {

            return response()->json(

                $this->smartBookingService
                    ->handle(
                        $aiIntent,
                        Auth::id()
                    )
            );
        }

        if (
            $aiIntentName  === 'book_ticket'
        ) {

            Log::info('AUTH_USER', [
                'id' => Auth::id()
            ]);

            $bookingData =
                $this->bookingService
                ->handle(
                    $request->message,
                    Auth::id(),
                    $aiIntent
                );

            if ($bookingData) {

                $this->sessionService
                    ->saveMessage(

                        $session->maPhien,

                        'bot',

                        $bookingData['reply']
                    );

                return response()->json(
                    $bookingData
                );
            }
        }

        if (
            $aiIntentName === 'smart_booking'
        ) {

            Log::info('SMART_BOOKING_CONTROLLER');

            $result =
                $this->smartBookingService
                ->handle(
                    $aiIntent,
                    Auth::id()
                );

            Log::info(
                'SMART_BOOKING_RESULT',
                $result
            );

            return response()->json(
                $result
            );
        }

        if ($aiIntentName === 'comparison') {

            $movie1 = $this->movieService->getMovieByName($aiIntent['movie1']);
            $movie2 = $this->movieService->getMovieByName($aiIntent['movie2']);

            if (!$movie1 || !$movie2) {
                return response()->json([
                    'type' => 'movie_not_found'
                ]);
            }

            $verdict = $this->buildComparisonVerdict($movie1, $movie2);

            return response()->json([
                'type' => 'comparison',
                'movie1' => $movie1,
                'movie2' => $movie2,
                'verdict' => $verdict
            ]);
        }

        // rating
        if (
            $aiIntentName ===
            'rating_filter'
        ) {

            $minRating =
                (float)
                ($aiIntent['min_rating'] ?? 8);

            return response()->json([

                'type' =>
                'rating_filter',

                'min_rating' =>
                $minRating,

                'movies' =>
                $this->movieService
                    ->getMoviesByRating(
                        $minRating
                    )
            ]);
        }

        // genre AI
        if ($aiIntentName === 'genre_filter') {

            $genre = $this->normalizeGenre($aiIntent['genre']);
            $audience = $aiIntent['audience'] ?? null;

            $movies = $this->movieService->getMoviesByGenre($genre);

            if ($movies->isEmpty()) {
                return response()->json([
                    'type' => 'genre_filter',
                    'genre' => $genre,
                    'audience' => $audience,
                    'movies' => [],
                    'reply' => $audience
                        ? "Xin lỗi, hiện tôi chưa có phim nào dành cho {$audience} đang chiếu."
                        : "Xin lỗi, hiện tôi chưa có phim {$genre} đang chiếu."
                ]);
            }

            return response()->json([
                'type' => 'genre_filter',
                'genre' => $genre,
                'audience' => $audience,
                'movies' => $movies
            ]);
        }

        if ($aiIntentName === 'top_movies') {

            $limit = $aiIntent['limit'] ?? 5;
            $limit = max(1, min((int) $limit, 20));

            $genre = !empty($aiIntent['genre'])
                ? $this->normalizeGenre($aiIntent['genre'])
                : null;

            $movies = $genre
                ? $this->movieService->getTopMoviesByGenre($genre, $limit)
                : $this->movieService->getTopMovies($limit);

            return response()->json([
                'type' => 'top_movies',
                'limit' => $limit,
                'genre' => $genre,
                'movies' => $movies
            ]);
        }

        // recommendMovies
        if ($aiIntentName === 'recommendation') {

            $movies = $this->movieService->getRecommendedMovies($aiIntent['movie']);

            return response()->json([
                'type' => 'recommendation',
                'title' => "Có thể bạn cũng thích các phim sau",
                'movies' => $movies
            ]);
        }

        if (
            $aiIntentName ===
            'showtime_query'
        ) {
            $movie =
                $this->movieService
                ->getMovieByName(
                    $aiIntent['movie']
                );

            if (!$movie) {

                return response()->json([
                    'type' => 'movie_not_found'
                ]);
            }

            $showtimes =
                $this->xuatChieuService
                ->getShowtimesByMovie(
                    $movie->maPhim,
                    $aiIntent['date'] ?? null,
                    $aiIntent['cinema'] ?? null
                );

            return response()->json([

                'type' => 'showtime_query',

                'movie' => $movie,

                'date' => $aiIntent['date'] ?? null,

                'showtimes' => $showtimes,

                'cinema' => $aiIntent['cinema'] ?? null
            ]);
        }

        if (
            $aiIntentName ===
            'movie_info'
        ) {

            $movie =
                $this->movieService
                ->getMovieByName(
                    $aiIntent['movie']
                );

            if (!$movie) {

                return response()->json([

                    'type' => 'movie_not_found'
                ]);
            }

            return response()->json([

                'type' =>
                'movie_info',

                'infoType' =>
                $aiIntent['infoType']
                    ?? null,

                'movie' => [

                    'id' => $movie->maPhim,

                    'title' =>
                    $movie->tieuDe,

                    'poster' => $movie->anhPoster,

                    'genres' => $movie->theLoai,

                    'description' => $movie->moTa,

                    'director' => $movie->daoDien,

                    'actors' => $movie->dienVien,

                    'rating' => $movie->danhGia,

                    'duration' => $movie->thoiLuong,

                    'status' => $movie->trangThai
                ]
            ]);
        }

        // OpenAI
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $request->message
                ]
            ]
        ]);

        return response()->json([
            'type' => $intentName,
            'reply' => $response->choices[0]->message->content
        ]);
    }
}
