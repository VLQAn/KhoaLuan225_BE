<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingManagementService;

class BookingManagementController
extends Controller
{
    protected $service;

    public function __construct(
        BookingManagementService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'data' =>
            $this->service->getAll()
        ]);
    }
}
