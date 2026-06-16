<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\ChatbotContextService;
use App\Services\ChatbotIntentService;
use App\Services\ChatbotBookingService;

class ChatbotAIController extends Controller
{
    protected $contextService;
    protected $intentService;
    protected $bookingService;

    public function __construct(
        ChatbotContextService $contextService,
        ChatbotIntentService $intentService,
        ChatbotBookingService $bookingService
    ) {
        $this->contextService = $contextService;
        $this->intentService = $intentService;
        $this->bookingService = $bookingService;
    }

    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

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
                    $request->message
                );

            if ($bookingData) {

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
