<?php

namespace App\Services;

class ChatbotIntentService
{
    public function detect(string $message)
    {
        $message = mb_strtolower($message);

        $bookingKeywords = [
            'đặt vé',
            'mua vé',
            'book vé',
            'giữ chỗ',
            'còn vé',
            'vé xem phim'
        ];

        foreach ($bookingKeywords as $keyword) {

            if (str_contains($message, $keyword)) {

                return [
                    'intent' => 'book_ticket'
                ];
            }
        }

        $showtimeKeywords = [
            'lịch chiếu',
            'suất chiếu',
            'chiếu lúc',
            'chiếu khi nào'
        ];

        foreach ($showtimeKeywords as $keyword) {

            if (str_contains($message, $keyword)) {

                return [
                    'intent' => 'showtime'
                ];
            }
        }

        $promotionKeywords = [
            'khuyến mãi',
            'ưu đãi',
            'giảm giá',
            'voucher'
        ];

        foreach ($promotionKeywords as $keyword) {

            if (str_contains($message, $keyword)) {

                return [
                    'intent' => 'promotion'
                ];
            }
        }

        return [
            'intent' => 'general'
        ];
    }
}
