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

class ChatbotAIController extends Controller
{
    protected $contextService;
    protected $intentService;
    protected $bookingService;
    protected $sessionService;
    protected $movieService;
    protected $openAIIntentService;

    public function __construct(
        ChatbotContextService $contextService,
        ChatbotIntentService $intentService,
        ChatbotBookingService $bookingService,
        ChatbotSessionService $sessionService,
        ChatbotMovieService $movieService,
        OpenAIIntentService $openAIIntentService
    ) {
        $this->contextService = $contextService;
        $this->intentService = $intentService;
        $this->bookingService = $bookingService;
        $this->sessionService = $sessionService;
        $this->movieService = $movieService;
        $this->openAIIntentService = $openAIIntentService;
    }

    private function normalizeGenre(
        string $genre
    ) {
        $map = [

            'Viễn tưởng' =>
            'Khoa học viễn tưởng',

            'Sci-fi' =>
            'Khoa học viễn tưởng',

            'Khoa học viễn tưởng' =>
            'Khoa học viễn tưởng',

            'Trẻ em' =>
            'Hoạt hình',

            'Thiếu nhi' =>
            'Hoạt hình'
        ];

        return
            $map[$genre]
            ?? $genre;
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

        $data =
            $session->duLieu ?? [];

        $currentStep =
            $data['booking_step']
            ?? null;

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
                    'confirm_booking',
                    'payment'
                ]
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
            $aiIntentName ===
            'comparison'
        ) {

            $movie1 =
                $this->movieService
                ->getMovieByName(
                    $aiIntent['movie1']
                );

            $movie2 =
                $this->movieService
                ->getMovieByName(
                    $aiIntent['movie2']
                );

            if (
                !$movie1 ||
                !$movie2
            ) {

                return response()->json([
                    'type' =>
                    'movie_not_found'
                ]);
            }

            return response()->json([

                'type' =>
                'comparison',

                'movie1' =>
                $movie1,

                'movie2' =>
                $movie2
            ]);
        }

        // rating
        if (
            $aiIntentName ===
            'rating_filter'
        ) {

            return response()->json([

                'type' =>
                'rating_filter',

                'rating' =>
                $aiIntent['rating'],

                'movies' =>
                $this->movieService
                    ->getMoviesByRating(
                        (float)
                        $aiIntent['rating']
                    )
            ]);
        }

        // genre AI
        if (
            $aiIntentName ===
            'genre_filter'
        ) {

            $genre =
                $this->normalizeGenre(
                    $aiIntent['genre']
                );

            return response()->json([

                'type' =>
                'genre_filter',

                'genre' =>
                $genre,

                'movies' =>
                $this->movieService
                    ->getMoviesByGenre(
                        $genre
                    )
            ]);
        }

        if (
            $aiIntentName ===
            'top_movies'
        ) {

            $limit =
                $aiIntent['limit']
                ?? 5;

            return response()->json([

                'type' =>
                'top_movies',

                'limit' =>
                $limit,

                'movies' =>
                $this->movieService
                    ->getTopMovies(
                        (int)$limit
                    )
            ]);
        }

        // recommendMovies
        if (
            $aiIntentName ===
            'recommendation'
        ) {

            return response()->json([

                'type' =>
                'recommendation',

                'movies' =>
                $this->movieService
                    ->getRecommendedMovies(
                        $aiIntent['movie']
                    )
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

                    'id' =>
                    $movie->maPhim,

                    'title' =>
                    $movie->tieuDe,

                    'description' =>
                    $movie->moTa,

                    'director' =>
                    $movie->daoDien,

                    'actors' =>
                    $movie->dienVien,

                    'rating' =>
                    $movie->danhGia,

                    'duration' =>
                    $movie->thoiLuong,

                    'status' =>
                    $movie->trangThai
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
