<?php

namespace App\Services;

use App\Models\Ve;
use App\Models\GiaVe;
use App\Models\KhuyenMai;
use App\Models\HoaDon;
use App\Models\Ghe;
use App\Models\XuatChieu;
use App\Models\BapNuoc;
use App\Models\HoaDonBapNuoc;
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
                            'Dang_Chon',
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

            // kiểm tra xuất chiếu có tồn tại không

            $xuatChieu = XuatChieu::find(
                $data['maXuatChieu']
            );

            if (!$xuatChieu) {

                throw new Exception(
                    'Xuất chiếu không tồn tại'
                );
            }

            // kiểm tra giờ chiếu đã bắt đầu chưa
            $gioChieu =
                Carbon::parse(
                    $xuatChieu->thoiGianBatDau
                )->format('H:i:s');

            // ======================
            // 2. TÍNH TIỀN
            // ======================

            $tongTien = 0;

            $giaVeMap = [];

            foreach ($data['danhSachGhe'] as $maGhe) {

                $ghe = Ghe::find($maGhe);

                if (
                    $ghe->maPhongChieu
                    !=
                    $xuatChieu->maPhongChieu
                ) {

                    throw new Exception(
                        "Ghế {$ghe->tenGhe} không thuộc phòng chiếu của xuất chiếu này"
                    );
                }

                $giaVe = GiaVe::where(
                    'gioBatDau',
                    '<=',
                    $gioChieu
                )
                    ->where(
                        'gioBatDau',
                        '<=',
                        $gioChieu
                    )
                    ->where(
                        'gioKetThuc',
                        '>',
                        $gioChieu
                    )
                    ->first();

                if (!$giaVe) {

                    throw new Exception(
                        'Chưa cấu hình giá vé'
                    );
                }

                $tongTien += $giaVe->gia;

                // lưu lại để tạo vé

                $giaVeMap[$maGhe] = $giaVe;
            }

            //=======================
            // Tính tiền món ăn
            //=======================
            $tongTienDoAn = 0;
            foreach (
                $data['danhSachMonAn'] ?? []
                as $item
            ) {

                $monAn =
                    BapNuoc::find(
                        $item['maMon']
                    );

                if (!$monAn) {

                    throw new Exception(
                        "Món ăn không tồn tại"
                    );
                }

                $tongTienDoAn +=
                    $monAn->gia
                    *
                    $item['soLuong'];
            }
            $tongTien += $tongTienDoAn;

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
                $data['maNguoiDung'],

                'maKhuyenMai' =>
                $data['maKhuyenMai'] ?? null,

                'gioThanhToan' => null,

                'tongTien' =>
                $tongTien,

                'tongThanhToan' =>
                $tongThanhToan,

                'trangThai' =>
                'Dang_Thanh_Toan'
            ]);

            //=======================
            // Tạo chi tiết món ăn
            //=======================
            foreach (
                $data['danhSachMonAn'] ?? []
                as $item
            ) {

                $monAn =
                    BapNuoc::find(
                        $item['maMon']
                    );

                HoaDonBapNuoc::create([

                    'maHoaDon' =>
                    $hoaDon->maHoaDon,

                    'maMon' =>
                    $monAn->maMon,

                    'soLuong' =>
                    $item['soLuong'],

                    'donGia' =>
                    $monAn->gia,

                    'thanhTien' =>
                    $monAn->gia
                        *
                        $item['soLuong']
                ]);
            }

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
                    'Dang_Chon'
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
