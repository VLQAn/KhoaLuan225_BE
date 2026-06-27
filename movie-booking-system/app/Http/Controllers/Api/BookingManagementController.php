<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingManagementService;
use Illuminate\Http\Request;

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
}
