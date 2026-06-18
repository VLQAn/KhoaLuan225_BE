<?php

namespace App\Services;

use App\Models\PhienTroChuyen;
use App\Models\LichSuTroChuyen;

class ChatbotSessionService
{
    public function getOrCreate(
        ?int $userId = null
    ) {
        $session =
            PhienTroChuyen::where(
                'maNguoiDung',
                $userId
            )
            ->where(
                'trangThai',
                'active'
            )
            ->latest()
            ->first();

        if ($session) {

            return $session;
        }

        return PhienTroChuyen::create([

            'maNguoiDung' =>
            $userId,

            'trangThai' =>
            'active'
        ]);
    }

    public function saveMessage(
        int $sessionId,
        string $sender,
        string $content
    ) {
        return LichSuTroChuyen::create([

            'maPhien' =>
            $sessionId,

            'nguoiGui' =>
            $sender,

            'noiDung' =>
            $content
        ]);
    }

    public function setMovie(
        int $sessionId,
        int $movieId
    ) {
        return PhienTroChuyen::where(
            'maPhien',
            $sessionId
        )->update([

            'phimDangChon' =>
            $movieId
        ]);
    }

    public function setShowtime(
        int $sessionId,
        int $showtimeId
    ) {
        return PhienTroChuyen::where(
            'maPhien',
            $sessionId
        )->update([

            'xuatChieuDangChon' =>
            $showtimeId
        ]);
    }

    public function setData(
        int $sessionId,
        string $key,
        $value
    ) {
        $session =
            PhienTroChuyen::find(
                $sessionId
            );

        $data =
            $session->duLieu ?? [];

        $data[$key] =
            $value;

        $session->duLieu =
            $data;

        $session->save();
    }

    public function getData(
        int $sessionId,
        string $key
    ) {
        $session =
            PhienTroChuyen::find(
                $sessionId
            );

        $data =
            json_decode(
                $session->duLieu ?? '{}',
                true
            );

        return $data[$key] ?? null;
    }

    public function clearSession(
        int $sessionId
    ) {
        return PhienTroChuyen::where(
            'maPhien',
            $sessionId
        )->update([

            'phimDangChon' =>
            null,

            'xuatChieuDangChon' =>
            null,

            'duLieu' =>
            null
        ]);
    }
}
