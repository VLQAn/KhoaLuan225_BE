<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RapChieuService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RapChieu\StoreRapChieuRequest;
use App\Http\Requests\RapChieu\UpdateRapChieuRequest;

class RapChieuController extends Controller
{
    protected $rapChieuService;

    public function __construct(
        RapChieuService $rapChieuService
    ) {
        $this->rapChieuService = $rapChieuService;
    }

    public function index(): JsonResponse
    {
        $data = $this->rapChieuService
            ->getAllRapChieu();

        return response()->json($data);
    }

    public function show($id): JsonResponse
    {
        $data = $this->rapChieuService
            ->getRapChieuById($id);

        return response()->json($data);
    }

    public function store(
        StoreRapChieuRequest $request
    ): JsonResponse {
        $data = $this->rapChieuService
            ->createRapChieu($request->validated());

        return response()->json([
            'message' => 'Tạo rạp chiếu thành công',
            'data' => $data
        ], 201);
    }

    public function update(
        UpdateRapChieuRequest $request,
        $id
    ): JsonResponse {
        $data = $this->rapChieuService->updateRapChieu(
            $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Cập nhật rạp chiếu thành công',
            'data' => $data
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->rapChieuService
            ->deleteRapChieu($id);

        return response()->json([
            'message' => 'Xóa rạp chiếu thành công'
        ]);
    }
}
