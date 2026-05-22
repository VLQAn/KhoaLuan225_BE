<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GiaVeService;
use App\Http\Requests\GiaVe\StoreGiaVeRequest;
use App\Http\Requests\GiaVe\UpdateGiaVeRequest;

class GiaVeController extends Controller
{
    protected $giaVeService;

    public function __construct(
        GiaVeService $giaVeService
    ) {
        $this->giaVeService
            = $giaVeService;
    }

    public function index(): JsonResponse
    {
        return response()->json(
            $this->giaVeService
                ->getAllGiaVe()
        );
    }

    public function show($id): JsonResponse
    {
        return response()->json(
            $this->giaVeService
                ->getGiaVeById($id)
        );
    }

    public function store(
        StoreGiaVeRequest $request
    ): JsonResponse {

        $data = $this->giaVeService
            ->createGiaVe(
                $request->validated()
            );

        return response()->json([
            'message'
                => 'Tạo giá vé thành công',

            'data' => $data
        ], 201);
    }

    public function update(
        UpdateGiaVeRequest $request,
        $id
    ): JsonResponse {

        $data = $this->giaVeService
            ->updateGiaVe(
                $id,
                $request->validated()
            );

        return response()->json([
            'message'
                => 'Cập nhật giá vé thành công',

            'data' => $data
        ]);
    }

    public function destroy(
        $id
    ): JsonResponse {

        $this->giaVeService
            ->deleteGiaVe($id);

        return response()->json([
            'message'
                => 'Xóa giá vé thành công'
        ]);
    }
}
