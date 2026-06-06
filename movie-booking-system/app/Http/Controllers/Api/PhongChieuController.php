<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PhongChieu\StorePhongChieuRequest;
use App\Services\PhongChieuService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PhongChieu\UpdatePhongChieuRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\PhongChieu;

class PhongChieuController extends Controller
{
    protected $phongChieuService;

    public function __construct(
        PhongChieuService $phongChieuService
    ) {
        $this->phongChieuService =
            $phongChieuService;
    }

    public function index(): JsonResponse
    {
        return response()->json(
            $this->phongChieuService
                ->getAllPhongChieu()
        );
    }

    public function show($id): JsonResponse
    {
        $data = $this->phongChieuService
            ->getPhongChieuById($id);

        return response()->json($data);
    }

    public function store(
        StorePhongChieuRequest $request
    ): JsonResponse {

        $data = $this->phongChieuService
            ->createPhongChieu(
                $request->validated()
            );

        return response()->json([
            'message' => 'Tạo phòng chiếu thành công',
            'data' => $data
        ], 201);
    }

    public function update(
        UpdatePhongChieuRequest $request,
        $id
    ): JsonResponse {

        $data = $this->phongChieuService
            ->updatePhongChieu(
                $id,
                $request->validated()
            );

        return response()->json([
            'message' => 'Cập nhật phòng chiếu thành công',
            'data' => $data
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->phongChieuService
            ->deletePhongChieu($id);

        return response()->json([
            'message' => 'Xóa phòng chiếu thành công'
        ]);
    }

    public function myRooms()
    {
        $user = Auth::user();

        $rooms = PhongChieu::with('rapChieu')
            ->whereHas(
                'rapChieu',
                function ($query) use ($user) {

                    $query->where(
                        'maNguoiDung',
                        $user->maNguoiDung
                    );
                }
            )
            ->get();

        return response()->json(
            $rooms
        );
    }
}
