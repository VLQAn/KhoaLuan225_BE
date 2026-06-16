<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class ChatbotAIController extends Controller
{
    public function ask(Request $request)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' =>
                    'Bạn là trợ lý của RACSO Cinema.
                 Trả lời ngắn gọn bằng tiếng Việt.
                 Chỉ hỗ trợ các vấn đề liên quan đến phim,
                 lịch chiếu và đặt vé.'
                ],
                [
                    'role' => 'user',
                    'content' => $request->message
                ]
            ]
        ]);
    }
}
