<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\ChatbotContextService;
use App\Services\ChatbotIntentService;
use App\Services\ChatbotBookingService;
use App\Services\ChatbotSessionService;

class ChatbotAIController extends Controller
{
    protected $contextService;
    protected $intentService;
    protected $bookingService;
    protected $sessionService;

    public function __construct(
        ChatbotContextService $contextService,
        ChatbotIntentService $intentService,
        ChatbotBookingService $bookingService,
        ChatbotSessionService $sessionService
    ) {
        $this->contextService = $contextService;
        $this->intentService = $intentService;
        $this->bookingService = $bookingService;
        $this->sessionService = $sessionService;
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

        if (
            $intentName === 'book_ticket'
        ) {

            $bookingData =
                $this->bookingService
                ->handle(
                    $request->message,
                    Auth::id()
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
