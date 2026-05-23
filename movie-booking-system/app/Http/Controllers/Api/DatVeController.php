<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DatVe\StoreDatVeRequest;
use App\Services\DatVeService;
use Illuminate\Http\JsonResponse;

class DatVeController extends Controller
{
    protected $datVeService;

    public function __construct(
        DatVeService $datVeService
    ) {
        $this->datVeService
            = $datVeService;
    }

    public function store(
        StoreDatVeRequest $request
    ): JsonResponse {

        $data =
            $this->datVeService
            ->datVe(
                $request->validated()
            );

        return response()->json([
            'message' =>
            'Đặt vé thành công',

            'data' => $data
        ]);
    }
}
