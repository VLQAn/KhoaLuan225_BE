<?php

namespace App\Services;

use App\Models\Ve;
use App\Models\GiaVe;
use App\Models\KhuyenMai;
use App\Models\HoaDon;
use App\Models\Ghe;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class DatVeService
{
    public function datVe(array $data)
    {
        return DB::transaction(function () use ($data) {

            // ======================
            // 1. CHECK GHẾ
            // ======================

            foreach ($data['danhSachGhe'] as $maGhe) {

                $exists = Ve::where(
                    'maXuatChieu',
                    $data['maXuatChieu']
                )
                    ->where('maGhe', $maGhe)
                    ->whereIn(
                        'trangThai',
                        [
                            'Dang_chon',
                            'Da_Dat'
                        ]
                    )
                    ->exists();

                if ($exists) {

                    throw new Exception(
                        "Ghế {$maGhe} đã được đặt"
                    );
                }
            }

            // ======================
            // 2. TÍNH TIỀN
            // ======================

            $tongTien = 0;

            $giaVeMap = [];

            foreach ($data['danhSachGhe'] as $maGhe) {

                $ghe = Ghe::find($maGhe);

                if (!$ghe) {

                    throw new Exception(
                        "Ghế {$maGhe} không tồn tại"
                    );
                }

                // Tạm thời lấy giá vé đầu tiên
                // Sau này có thể lọc theo:
                // giờ chiếu, loại ghế, độ tuổi...

                $giaVe = GiaVe::first();

                if (!$giaVe) {

                    throw new Exception(
                        'Chưa cấu hình giá vé'
                    );
                }

                $tongTien += $giaVe->gia;

                // lưu lại để tạo vé

                $giaVeMap[$maGhe] = $giaVe;
            }

            // ======================
            // 3. ÁP KHUYẾN MÃI
            // ======================

            $tongGiam = 0;

            if (!empty($data['maKhuyenMai'])) {

                $khuyenMai = KhuyenMai::find(
                    $data['maKhuyenMai']
                );

                if (!$khuyenMai) {

                    throw new Exception(
                        'Khuyến mãi không tồn tại'
                    );
                }

                // kiểm tra thời hạn

                if (
                    !empty($khuyenMai->thoiHan)
                    &&
                    Carbon::now()->gt(
                        $khuyenMai->thoiHan
                    )
                ) {

                    throw new Exception(
                        'Khuyến mãi đã hết hạn'
                    );
                }

                // giảm %

                $tongGiam =
                    (
                        $tongTien
                        *
                        $khuyenMai->giaKhuyenMai
                    ) / 100;
            }

            $tongThanhToan =
                max(
                    0,
                    $tongTien - $tongGiam
                );

            // ======================
            // 4. TẠO HÓA ĐƠN
            // ======================

            $hoaDon = HoaDon::create([

                'maNguoiDung' =>
                Auth::id(),

                'maKhuyenMai' =>
                $data['maKhuyenMai']
                    ?? null,

                // chưa thanh toán nên để null
                'gioThanhToan' => null,

                'tongTien' =>
                $tongTien,

                'tongThanhToan' =>
                $tongThanhToan,

                'trangThai' =>
                'Dang_Thanh_Toan'
            ]);

            // ======================
            // 5. TẠO VÉ
            // ======================

            foreach (
                $data['danhSachGhe']
                as $maGhe
            ) {

                $giaVe =
                    $giaVeMap[$maGhe];

                Ve::create([

                    'maHoaDon' =>
                    $hoaDon->maHoaDon,

                    'maXuatChieu' =>
                    $data['maXuatChieu'],

                    'maGiaVe' =>
                    $giaVe->maGiaVe,

                    'maGhe' =>
                    $maGhe,

                    'gia' =>
                    $giaVe->gia,

                    'trangThai' =>
                    'Dang_chon'
                ]);
            }

            return [

                'hoaDon' => $hoaDon,

                'tongTien' =>
                $tongTien,

                'tongGiam' =>
                $tongGiam,

                'tongThanhToan' =>
                $tongThanhToan
            ];
        });
    }
}
