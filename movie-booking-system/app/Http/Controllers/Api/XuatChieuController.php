<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XuatChieuService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\XuatChieu\StoreXuatChieuRequest;
use App\Http\Requests\XuatChieu\UpdateXuatChieuRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\XuatChieu;

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

    public function seatMap($maXuatChieu)
    {
        return response()->json(
            $this->xuatChieuService
                ->getSeatMap($maXuatChieu)
        );
    }

    public function myShowtimes()
    {
        $user = Auth::user();

        $showtimes = XuatChieu::with([
            'phim',
            'phongChieu.rapChieu'
        ])
            ->whereHas(
                'phongChieu.rapChieu',
                function ($query) use ($user) {

                    $query->where(
                        'maNguoiDung',
                        $user->maNguoiDung
                    );
                }
            )
            ->orderBy(
                'thoiGianBatDau',
                'desc'
            )
            ->get();

        $showtimes->transform(function ($showtime) {

            $showtime->thoiGianBatDau =
                $showtime->thoiGianBatDau
                ->timezone('Asia/Ho_Chi_Minh')
                ->format('Y-m-d H:i:s');

            $showtime->thoiGianKetThuc =
                $showtime->thoiGianKetThuc
                ->timezone('Asia/Ho_Chi_Minh')
                ->format('Y-m-d H:i:s');

            return $showtime;
        });

        return response()->json($showtimes);
    }
}
