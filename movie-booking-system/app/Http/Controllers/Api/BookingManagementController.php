<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingManagementService;
use Illuminate\Http\Request;
use Exception;

class BookingManagementController
extends Controller
{
    protected $service;

    public function __construct(
        BookingManagementService $service
    ) {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $maNguoiDung = $request->user()->maNguoiDung;

        return response()->json([
            'data' =>
            $this->service->getAll($maNguoiDung)
        ]);
    }

    public function cancel(Request $request, $maHoaDon)
    {
        $maNguoiDung = $request->user()->maNguoiDung;

        try {
            $hoaDon = $this->service->cancel(
                (int) $maHoaDon,
                $maNguoiDung
            );

            return response()->json([
                'message' => 'Hủy vé thành công',
                'data' => $hoaDon
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
