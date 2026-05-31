<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\XuatChieu;
use Carbon\Carbon;

class UpdateShowtimeStatus extends Command
{
    protected $signature =
        'showtime:update-status';

    protected $description =
        'Cập nhật trạng thái suất chiếu';

    public function handle()
    {
        $now = Carbon::now();

        $showtimes =
            XuatChieu::all();

        foreach (
            $showtimes as $showtime
        ) {

            if (
                $now <
                $showtime->thoiGianBatDau
            ) {

                $status = 'sap_chieu';

            } elseif (

                $now >=
                $showtime->thoiGianBatDau

                &&

                $now <
                $showtime->thoiGianKetThuc

            ) {

                $status = 'dang_chieu';

            } else {

                $status = 'da_chieu';
            }

            if (
                $showtime->trangThai
                !==
                $status
            ) {

                $showtime->update([
                    'trangThai' => $status
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
