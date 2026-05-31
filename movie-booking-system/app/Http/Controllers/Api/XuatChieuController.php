<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XuatChieuService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\XuatChieu\StoreXuatChieuRequest;
use App\Http\Requests\XuatChieu\UpdateXuatChieuRequest;

class XuatChieuController extends Controller
{
    protected $xuatChieuService;

    public function __construct(XuatChieuService $xuatChieuService)
    {
        $this->xuatChieuService = $xuatChieuService;
    }

    public function index(): JsonResponse
    {
        $data = $this->xuatChieuService
            ->getAllXuatChieu();

        return response()->json($data);
    }

    public function show($id): JsonResponse
    {
        $data = $this->xuatChieuService
            ->getXuatChieuById($id);

        return response()->json($data);
    }

    public function store(
        StoreXuatChieuRequest $request
    ): JsonResponse {

        $data = $this->xuatChieuService
            ->createXuatChieu(
                $request->validated()
            );

        return response()->json([
            'message' => 'Tạo xuất chiếu thành công',
            'data' => $data
        ], 201);
    }

    public function update(
        UpdateXuatChieuRequest $request,
        $id
    ): JsonResponse {

        $data = $this->xuatChieuService
            ->updateXuatChieu(
                $id,
                $request->validated()
            );

        return response()->json([
            'message' => 'Cập nhật xuất chiếu thành công',
            'data' => $data
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->xuatChieuService->deleteXuatChieu($id);

        return response()->json([
            'message' => 'Xóa xuất chiếu thành công'
        ]);
    }

    public function available()
    {
        return response()->json(
            $this->xuatChieuService
                ->getAvailableShowtimes()
        );
    }
}
