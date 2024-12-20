<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $PriceReportController = new \App\Http\Controllers\Tasks\PriceReportController;
            // グラフ用地金価格の月毎の集計
            $PriceReportController->set_month();
            // admininfoの月次、日次の自動集計処理
            $DbMarketYearController = new \App\Http\Controllers\DbMarketYearController;
            $DbMarketYearController->index();
        })->cron('00 11 * * * *');

        $schedule->call(function () {
            $Controller = new \App\Http\Controllers\TwitterBot\TwitterBotController;
            // tweet
            $Controller->sendTweet();
        })->cron('00 12 * * * *');

        $schedule->call(function () {
            if (date('w') == 6) { // 金曜日のみの更新なので、土曜日のみ取得する
                $RapaApiController = new \App\Http\Controllers\RapaApiController;
                $RapaApiController->get_rapa_price();
            }
        })->cron('00 3 * * * *');

        $schedule->call(function () {
            \App\Http\Controllers\Tasks\TaskController::put_kiyaku_html();
        })->cron('00 4 * * * *');
        //リファスタ管理画の面価格1日前編集へ自動反映をつける処理
        $schedule->command('command:batch-replace-price')->cron('00 09 * * 0,6');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

}
