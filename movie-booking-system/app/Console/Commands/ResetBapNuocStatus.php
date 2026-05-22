<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BapNuoc;

class ResetBapNuocStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature =
        'bapnuoc:reset-status';

    /**
     * The console command description.
     */
    protected $description =
        'Reset trạng thái bắp nước hết bán trong ngày';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = BapNuoc::where(
            'trangThai',
            'HET_BAN_TRONG_NGAY'
        )->update([
            'trangThai' => 'DANG_BAN'
        ]);

        $this->info(
            "Đã reset {$count} món."
        );

        return Command::SUCCESS;
    }
}
