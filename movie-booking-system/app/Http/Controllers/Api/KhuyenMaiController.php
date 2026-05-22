<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\KhuyenMai\StoreKhuyenMaiRequest;
use App\Http\Requests\KhuyenMai\UpdateKhuyenMaiRequest;
use App\Services\KhuyenMaiService;
use Illuminate\Http\JsonResponse;

class KhuyenMaiController extends Controller
{
    protected $khuyenMaiService;

    public function __construct(
        KhuyenMaiService
        $khuyenMaiService
    ) {
        $this->khuyenMaiService
            = $khuyenMaiService;
    }

    public function index():
    JsonResponse
    {
        return response()->json(
            $this->khuyenMaiService
                ->getAllKhuyenMai()
        );
    }

    public function show($id):
    JsonResponse
    {
        return response()->json(
            $this->khuyenMaiService
                ->getKhuyenMaiById($id)
        );
    }

    public function store(
        StoreKhuyenMaiRequest
        $request
    ): JsonResponse {

        $data =
            $this->khuyenMaiService
                ->createKhuyenMai(
                    $request->validated()
                );

        return response()->json([
            'message' =>
                'Tạo khuyến mãi thành công',

            'data' => $data
        ], 201);
    }

    public function update(
        UpdateKhuyenMaiRequest
        $request,

        $id
    ): JsonResponse {

        $data =
            $this->khuyenMaiService
                ->updateKhuyenMai(
                    $id,
                    $request->validated()
                );

        return response()->json([
            'message' =>
                'Cập nhật khuyến mãi thành công',

            'data' => $data
        ]);
    }

    public function destroy(
        $id
    ): JsonResponse {

        $this->khuyenMaiService
            ->deleteKhuyenMai($id);

        return response()->json([
            'message' =>
                'Xóa khuyến mãi thành công'
        ]);
    }
}
