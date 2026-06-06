<?php

namespace App\Services;

use App\Models\Ve;
use App\Models\Ghe;
use App\Models\Phim;
use App\Models\RapChieu;
use App\Models\BapNuoc;
use App\Models\HoaDonBapNuoc;
use App\Models\PhongChieu;
use App\Models\XuatChieu;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboard()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Rạp thuộc tài khoản hiện tại
        |--------------------------------------------------------------------------
        */

        $rapIds = RapChieu::where(
            'maNguoiDung',
            $user->maNguoiDung
        )->pluck('maRap');

        /*
        |--------------------------------------------------------------------------
        | Phòng chiếu
        |--------------------------------------------------------------------------
        */

        $phongIds = PhongChieu::whereIn(
            'maRap',
            $rapIds
        )->pluck('maPhong');

        /*
        |--------------------------------------------------------------------------
        | Xuất chiếu
        |--------------------------------------------------------------------------
        */

        $xuatChieuIds = XuatChieu::whereIn(
            'maPhong',
            $phongIds
        )->pluck('maXuatChieu');

        /*
        |--------------------------------------------------------------------------
        | Tổng vé bán
        |--------------------------------------------------------------------------
        */

        $tongVeBan = Ve::whereIn(
            'maXuatChieu',
            $xuatChieuIds
        )
        ->where(
            'trangThai',
            'Da_Dat'
        )
        ->count();

        /*
        |--------------------------------------------------------------------------
        | Doanh thu vé
        |--------------------------------------------------------------------------
        */

        $doanhThuVe = Ve::whereIn(
            'maXuatChieu',
            $xuatChieuIds
        )
        ->where(
            'trangThai',
            'Da_Dat'
        )
        ->sum('gia');

        /*
        |--------------------------------------------------------------------------
        | Doanh thu bắp nước
        |--------------------------------------------------------------------------
        */

        $doanhThuFood = HoaDonBapNuoc::query()
            ->join(
                'bap_nuoc',
                'hoa_don_bap_nuoc.maMon',
                '=',
                'bap_nuoc.maMon'
            )
            ->whereIn(
                'bap_nuoc.maRap',
                $rapIds
            )
            ->sum('hoa_don_bap_nuoc.thanhTien');

        /*
        |--------------------------------------------------------------------------
        | Tổng doanh thu
        |--------------------------------------------------------------------------
        */

        $tongDoanhThu =
            $doanhThuVe +
            $doanhThuFood;

        /*
        |--------------------------------------------------------------------------
        | Tổng ghế
        |--------------------------------------------------------------------------
        */

        $tongGhe = Ghe::whereIn(
            'maPhong',
            $phongIds
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Tỷ lệ lấp đầy
        |--------------------------------------------------------------------------
        */

        $tiLeLapDay = 0;

        if ($tongGhe > 0) {

            $tiLeLapDay =
                round(
                    (
                        $tongVeBan /
                        $tongGhe
                    ) * 100,
                    2
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Danh sách rạp
        |--------------------------------------------------------------------------
        */

        $raps = RapChieu::whereIn(
            'maRap',
            $rapIds
        )
        ->get()
        ->map(function ($rap) {

            $phongIds = PhongChieu::where(
                'maRap',
                $rap->maRap
            )->pluck('maPhong');

            $xuatIds = XuatChieu::whereIn(
                'maPhong',
                $phongIds
            )->pluck('maXuatChieu');

            $veBan = Ve::whereIn(
                'maXuatChieu',
                $xuatIds
            )
            ->where(
                'trangThai',
                'Da_Dat'
            )
            ->count();

            $doanhThu = Ve::whereIn(
                'maXuatChieu',
                $xuatIds
            )
            ->where(
                'trangThai',
                'Da_Dat'
            )
            ->sum('gia');

            $tongGheRap = Ghe::whereIn(
                'maPhong',
                $phongIds
            )->count();

            $lapDay = 0;

            if ($tongGheRap > 0) {

                $lapDay = round(
                    (
                        $veBan /
                        $tongGheRap
                    ) * 100,
                    2
                );
            }

            return [
                'maRap' => $rap->maRap,
                'tenRap' => $rap->tenRap,
                'veBan' => $veBan,
                'doanhThu' => $doanhThu,
                'lapDay' => $lapDay
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Top 3 phim bán chạy
        |--------------------------------------------------------------------------
        */

        $topMovies = Ve::query()
            ->join(
                'xuat_chieu',
                've.maXuatChieu',
                '=',
                'xuat_chieu.maXuatChieu'
            )
            ->join(
                'phim',
                'xuat_chieu.maPhim',
                '=',
                'phim.maPhim'
            )
            ->where(
                've.trangThai',
                'Da_Dat'
            )
            ->whereIn(
                'xuat_chieu.maPhong',
                $phongIds
            )
            ->groupBy(
                'phim.maPhim',
                'phim.tieuDe',
                'phim.anhPoster'
            )
            ->selectRaw("
                phim.maPhim,
                phim.tieuDe,
                phim.anhPoster,
                COUNT(ve.maVe) as tongVe
            ")
            ->orderByDesc(
                'tongVe'
            )
            ->limit(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Top combo
        |--------------------------------------------------------------------------
        */

        $topFoods = HoaDonBapNuoc::query()
            ->join(
                'bap_nuoc',
                'hoa_don_bap_nuoc.maMon',
                '=',
                'bap_nuoc.maMon'
            )
            ->whereIn(
                'bap_nuoc.maRap',
                $rapIds
            )
            ->groupBy(
                'bap_nuoc.maMon',
                'bap_nuoc.tenMon',
                'bap_nuoc.hinhAnh'
            )
            ->selectRaw("
                bap_nuoc.maMon,
                bap_nuoc.tenMon,
                bap_nuoc.hinhAnh,
                SUM(hoa_don_bap_nuoc.soLuong) as tongBan
            ")
            ->orderByDesc(
                'tongBan'
            )
            ->limit(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Xuất chiếu vàng
        |--------------------------------------------------------------------------
        */

        $goldenShowtimes = XuatChieu::whereIn(
            'maPhong',
            $phongIds
        )
        ->get()
        ->map(function ($xuatChieu) {

            $tongGhe = Ghe::where(
                'maPhong',
                $xuatChieu->maPhong
            )->count();

            $soVeDaDat = Ve::where(
                'maXuatChieu',
                $xuatChieu->maXuatChieu
            )
            ->where(
                'trangThai',
                'Da_Dat'
            )
            ->count();

            $lapDay = 0;

            if ($tongGhe > 0) {

                $lapDay = round(
                    (
                        $soVeDaDat /
                        $tongGhe
                    ) * 100,
                    2
                );
            }

            return [
                'maXuatChieu' =>
                    $xuatChieu->maXuatChieu,

                'thoiGianBatDau' =>
                    $xuatChieu->thoiGianBatDau,

                'lapDay' =>
                    $lapDay
            ];
        })
        ->sortByDesc('lapDay')
        ->take(3)
        ->values();

        return [

            'tongVeBan' =>
                $tongVeBan,

            'doanhThuVe' =>
                $doanhThuVe,

            'doanhThuFood' =>
                $doanhThuFood,

            'tongDoanhThu' =>
                $tongDoanhThu,

            'tiLeLapDay' =>
                $tiLeLapDay,

            'raps' =>
                $raps,

            'topMovies' =>
                $topMovies,

            'topFoods' =>
                $topFoods,

            'goldenShowtimes' =>
                $goldenShowtimes,
        ];
    }
}
