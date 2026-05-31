<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\BapNuocService;
use App\Http\Requests\BapNuoc\StoreBapNuocRequest;
use App\Http\Requests\BapNuoc\UpdateBapNuocRequest;
use App\Models\BapNuoc;
use Illuminate\Http\Request;

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

    public function updateStatus($id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'trangThai' => 'required|in:DANG_BAN,HET_BAN_TRONG_NGAY,NGUNG_KINH_DOANH'
        ]);

        $result = $this->bapNuocService->updateStatus($id, $data);

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $result
        ]);
    }

    public function getByRap($maRap)
    {
        return BapNuoc::where('maRap', $maRap)
            ->where('trangThai', 'DANG_BAN')
            ->get();
    }
}
