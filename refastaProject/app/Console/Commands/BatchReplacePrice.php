<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class BatchReplacePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:batch-replace-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'リファスタ管理画の面価格1日前編集へ自動反映をつける処理';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y/m/d');
    
        $this->processTbPrice($today);
        $this->processTbMarket($today);
    }
     public function processTbPrice($today)
        {
            $todayTbPrice = DB::table('kinkai.tb_price')
                            ->where('resist_data', $today)
                            ->first();

            if($todayTbPrice === null) {
                // 今日のデータがない場合、昨日のデータを取得
                $latestDateWithData = DB::table('kinkai.tb_price')
                                ->select('*')
                                ->orderBy('resist_data', 'desc')
                                ->first();

                $latestRecordDate = new DateTime($latestDateWithData->resist_data);
                $currentDate = new DateTime($today);
                $interval = $latestRecordDate->diff($currentDate);

                if ($latestDateWithData) {
                    $todayTbPrice = [
                        "gold" => $latestDateWithData->gold,
                        "platinum" => $latestDateWithData->platinum,
                        "silver" => $latestDateWithData->silver,
                        "palladium" => $latestDateWithData->palladium,
                        "y_gold" => $latestDateWithData->y_gold,
                        "y_platinum" => $latestDateWithData->y_platinum,
                        "resist_data" => $today,
                        "resist_data_type_date" => Carbon::now()->format('Y-m-d'), 
                        "y_silver" => $latestDateWithData->y_silver,
                        "y_palladium" => $latestDateWithData->y_palladium,
                    ];
                    
                    for ($i = 0; $i < $interval->days; $i++) {
                        $latestDateWithData = DB::table('kinkai.tb_price')
                        ->select('*')
                        ->orderBy('resist_data', 'desc')
                        ->first();

                        $insertDate = new DateTime($latestDateWithData->resist_data);
                        $todayTbPrice['resist_data'] = $insertDate->modify("+1 day")->format("Y/m/d");
                        $todayTbPrice['resist_data_type_date'] = $insertDate->modify("+1 day")->format("Y-m-d");
                        DB::table('kinkai.tb_price')
                            ->insert($todayTbPrice);
                            
                    }  
                    
                }
            }
        }
        public function processTbMarket($today)
        {
            $todayTbMarket = DB::table('kinkai.tb_market')
                            ->where('resist_data', $today)
                            ->first();

            if($todayTbMarket === null) {
                // 今日のデータがない場合、昨日のデータを取得
                $latestDateWithData = DB::table('kinkai.tb_market')
                                ->select('*')
                                ->orderBy('resist_data', 'desc')
                                ->first();

                $latestRecordDate = new DateTime($latestDateWithData->resist_data);
                $currentDate = new DateTime($today);
                $interval = $latestRecordDate->diff($currentDate);

                if ($latestDateWithData) {
                    $todayTbMarket = [
                        "ny_gold" => $latestDateWithData->ny_gold,
                        "ny_platinum" => $latestDateWithData->ny_platinum,
                        "ny_silver" => $latestDateWithData->ny_silver,
                        "ny_market" => $latestDateWithData->ny_market,
                        "jp_market" => $latestDateWithData->jp_market,
                        "resist_data" => $today,
                        "jp_market_cnh" => $latestDateWithData->jp_market_cnh,
                        "ny_palladium" => $latestDateWithData->ny_palladium,
                    ];
                    
                    for ($i = 0; $i < $interval->days; $i++) {
                        $latestDateWithData = DB::table('kinkai.tb_market')
                        ->select('*')
                        ->orderBy('resist_data', 'desc')
                        ->first();

                        $insertDate = new DateTime($latestDateWithData->resist_data);
                        $todayTbMarket['resist_data'] = $insertDate->modify("+1 day")->format("Y/m/d");
                        DB::table('kinkai.tb_market')
                            ->insert($todayTbMarket);
                            
                    }  
                    
                }
            }
        }
}
