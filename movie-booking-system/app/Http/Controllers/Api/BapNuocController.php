<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\BapNuocService;
use App\Http\Requests\BapNuoc\StoreBapNuocRequest;
use App\Http\Requests\BapNuoc\UpdateBapNuocRequest;

class BapNuocController extends Controller
{
    protected $bapNuocService;

    public function __construct(
        BapNuocService $bapNuocService
    ) {
        $this->bapNuocService = $bapNuocService;
    }

    public function index(): JsonResponse
    {
        $data = $this->bapNuocService
            ->getAllBapNuoc();

        return response()->json($data);
    }

    public function show($id): JsonResponse
    {
        $data = $this->bapNuocService
            ->getBapNuocById($id);

        return response()->json($data);
    }

    public function store(
        StoreBapNuocRequest $request
    ): JsonResponse {

        $data = $this->bapNuocService
            ->createBapNuoc(
                $request->validated()
            );

        return response()->json([
            'message' => 'Tạo món bắp nước thành công',
            'data' => $data
        ], 201);
    }

    public function update(
        UpdateBapNuocRequest $request,
        $id
    ): JsonResponse {

        $data = $this->bapNuocService
            ->updateBapNuoc(
                $id,
                $request->validated()
            );

        return response()->json([
            'message' => 'Cập nhật món bắp nước thành công',
            'data' => $data
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->bapNuocService
            ->deleteBapNuoc($id);

        return response()->json([
            'message' => 'Xóa món bắp nước thành công'
        ]);
    }
}

