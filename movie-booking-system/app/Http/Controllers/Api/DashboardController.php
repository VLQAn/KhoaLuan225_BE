<?php

namespace App\Http\Controllers\Api;

use App\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        DashboardService $dashboardService
    ) {
        return response()->json(
            $dashboardService->getDashboard()
        );
    }
}
