<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DbMarketYearController {

    public function index(){
        //$current_year = !empty($_GET['kinkai.tb_price']) ? $_GET['kinkai.tb_price'] : date('Y')- 0;
        $current_month = Carbon::now()->startOfMonth()->format('Y/m/d');
        $today = Carbon::now()->format('Y/m/d');

        $this->runMonthProcess($current_month, 'gold');
        $this->runMonthProcess($current_month, 'platinum');
        $this->runDayProcess($today, 'gold');
        $this->runDayProcess($today, 'platinum');
    }

     // 月次集計
    public function setMonthProcessConfig($type){
         switch($type){
             case 'gold':
                 $tb_price_column_name = 'gold';
                 $year_table_name = 'tb_gold_market_year';
                 break;
             case 'platinum':
                 $tb_price_column_name = 'platinum';
                 $year_table_name = 'tb_platinum_market_year';
                 break;
             default:
                 return false;
         }
         return [
             'tb_price_column_name' => $tb_price_column_name,
             'year_table_name' => $year_table_name,
         ];
    }
    public function runMonthProcess($current_month, $type){
        // ----------- 金相場月次管理 --------------//
        // 金データ[tb_price]から取得
        $config = $this->setMonthProcessConfig($type);
        if ($config === false){
            return false;
        }
        // ' . $config['tb_price_column_name'] . '
        // ----------- 金相場月次管理 --------------//
        // 金データ[tb_price]から取得
        $monthlyGold = DB::table('kinkai.tb_price')
            ->selectRaw('DATE_FORMAT(resist_data, "%Y/%m/01") as date')
            ->selectRaw('MAX(' . $config['tb_price_column_name'] . ') as high, MIN(' . $config['tb_price_column_name'] . ') as low')
            ->selectRaw('(SELECT ' . $config['tb_price_column_name'] . ' FROM kinkai.tb_price AS sub1 WHERE DATE_FORMAT(sub1.resist_data, "%Y/%m/01") = date ORDER BY sub1.resist_data ASC LIMIT 1) as open')
            ->selectRaw('(SELECT ' . $config['tb_price_column_name'] . ' FROM kinkai.tb_price AS sub2 WHERE DATE_FORMAT(sub2.resist_data, "%Y/%m/01") = date ORDER BY sub2.resist_data DESC LIMIT 1) as close')
            ->whereRaw('DATE_FORMAT(resist_data, "%Y/%m/01") = ?', [$current_month])
            ->groupBy('date')
            ->first();
           //DB:tb_gold_market_yeaデータベーステーブルに挿入
            if($monthlyGold){
                DB::table('kinkai.' . $config['year_table_name'])
                ->where('date', $current_month)
                ->update([
                    'date' => $monthlyGold->date, 
                    'open' => (int)$monthlyGold->open, 
                    'high' => (int)$monthlyGold->high,
                    'low' => (int)$monthlyGold->low,
                    'close' => (int)$monthlyGold->close, 
                ]);
            }
    }
    // 日別集計
    public function setDayProcessConfig($type){
        switch($type){
            case 'gold':
                $tb_price_column_name = 'gold';
                $day_table_name = 'tb_gold_market';
                break;
            case 'platinum':
                $tb_price_column_name = 'platinum';
                $day_table_name = 'tb_platinum_market';
                break;
            default:
                return false;
        }
        return [
            'tb_price_column_name' => $tb_price_column_name,
            'day_table_name' => $day_table_name,
        ];
    }
    public function runDayProcess($today, $type){
        $config = $this->setDayProcessConfig($type);
        if ($config === false){
            return false;
        }
        $dailyGold = DB::table('kinkai.tb_price')
        ->select('resist_data as date', $config['tb_price_column_name'])
        ->where('resist_data', $today)
        ->first();

        if(!empty($dailyGold)) {
                $dailyGoldDate = $dailyGold->date;
                $retriveTbGoldMarketData = DB::table('kinkai.' .  $config['day_table_name'])
                ->where('date', $dailyGoldDate)
                ->first();

            if ($retriveTbGoldMarketData) {
                //データベーステーブル[DB:tb_gold_market]に更新
                DB::table('kinkai.' .  $config['day_table_name'])
                    ->where('date', $dailyGold->date)
                    ->update([
                        'date' => $dailyGold->date,
                        'price' => (int)$dailyGold->{$config['tb_price_column_name']},
                ]);
            } else {
                //データベーステーブル[DB:tb_gold_market]に挿入
                DB::table('kinkai.' . $config['day_table_name'])
                    ->insert([
                    'date' => $dailyGold->date,
                    'price' => (int)$dailyGold->{$config['tb_price_column_name']},
                ]);
            }
        }
    } 
}    