<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule) : void
    {
        // TODO: バッチログも他のログ同様ローテーションしたい。
        $log = config('logging.batchlog');
        $now = Carbon::now();

        // ※ 月や日は0埋め無しでformatしないとバリデーションエラーになるので注意（'Y m d'の代わりに'Y n j'を使う）。

        // 古い履歴データの削除
        foreach (config('database.expire_logs_months') as $info) {
            $schedule->command('partition:drop ' . $now->copy()->subMonthsNoOverflow($info['expire'])->format('Y n'), [$info['model']])->monthlyOn(1, '00:20')->appendOutputTo($log);
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands() : void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
