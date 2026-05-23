<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DatVe\HoldSeatsRequest;
use App\Services\HoldSeatsService;

class HoldSeatsController extends Controller
{
    protected $holdSeatsService;

    public function __construct(
        HoldSeatsService $holdSeatsService
    ) {
        $this->holdSeatsService
            = $holdSeatsService;
    }
    
    public function holdSeats(HoldSeatsRequest $request)
    {
        return $this->holdSeatsService
            ->holdSeats(
                auth()->id(),
                $request->validated()
            );
    }
}
