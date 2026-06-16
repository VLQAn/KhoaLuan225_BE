<?php

namespace App\Services;

class ChatbotParserService
{
    public function parse(string $message)
    {
        $message =
            mb_strtolower($message);

        return [

            'intent' =>
                $this->detectIntent(
                    $message
                ),

            'date' =>
                $this->detectDate(
                    $message
                ),

            'cinema' =>
                $this->detectCinema(
                    $message
                )
        ];
    }

    private function detectIntent(
        string $message
    ) {

        if (
            str_contains(
                $message,
                'đặt vé'
            )
        ) {
            return 'booking';
        }

        if (
            str_contains(
                $message,
                'lịch chiếu'
            )
        ) {
            return 'showtime';
        }

        return 'general';
    }

    private function detectDate(
        string $message
    ) {

        if (
            str_contains(
                $message,
                'hôm nay'
            )
        ) {
            return 'today';
        }

        if (
            str_contains(
                $message,
                'ngày mai'
            )
        ) {
            return 'tomorrow';
        }

        return null;
    }

    private function detectCinema(
        string $message
    ) {
        return null;
    }
}
