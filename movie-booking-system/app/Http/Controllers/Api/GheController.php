<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GheService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Ghe\StoreGheRequest;
use App\Http\Requests\Ghe\UpdateGheRequest;
use App\Http\Requests\Ghe\GenerateGheRequest;

class GheController extends Controller
{
    protected $gheService;

    public function __construct(
        GheService $gheService
    ) {
        $this->gheService = $gheService;
    }

    public function index(): JsonResponse
    {
        $data = $this->gheService
            ->getAllGhe();

        return response()->json($data);
    }

    public function show($id): JsonResponse
    {
        $data = $this->gheService
            ->getGheById($id);

        return response()->json($data);
    }

    public function store(
        StoreGheRequest $request
    ): JsonResponse {
        $data = $this->gheService
            ->createGhe($request->validated());

        return response()->json([
            'message' => 'Tạo ghế thành công',
            'data' => $data
        ], 201);
    }

    public function update(
        UpdateGheRequest $request,
        $id
    ): JsonResponse {
        $data = $this->gheService->updateGhe(
            $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Cập nhật ghế thành công',
            'data' => $data
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->gheService->deleteGhe($id);

        return response()->json([
            'message' => 'Xóa ghế thành công'
        ]);
    }

    public function generateSeats(
        GenerateGheRequest $request
    ): JsonResponse {

        $result = $this->gheService
            ->generateSeats(
                $request->validated()
            );

        return response()->json($result);
    }
}
