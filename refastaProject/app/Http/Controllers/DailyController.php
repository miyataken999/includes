<?php

namespace App\Http\Controllers;

use App\Models\tb_gold;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\TbDaily;

class DailyController extends Controller
{
    public function index(Request $request)
{
        $day_array = array(
            'Mon' => '月',
            'Tue' => '火',
            'Wed' => '水',
            'Thu' => '木',
            'Fri' => '金',
            'Sat' => '土',
            'Sun' => '日',
            );
        $array["day_array"] = $day_array; 

        $year = $request->input('select_year') ?? date("Y");
        $month = $request->input('select_month') ?? date("m");
        $from = date("Y/m/01", strtotime($year . '-' . $month . '-01'));
        $to = date("Y/m/t", strtotime($year . '-' . $month . '-01'));
        
        $tb_gold_market_array = $this->getjewellaryprice([
            'year' => $year,
            'month' => $month,
            'from' => $from,
            'to' => $to
        ]);
        $array["tb_gold_market_array"] = $tb_gold_market_array;
        return view("daily.index", $array);
    }
    public function getjewellaryprice(array $data = []) {

        if(!empty($data['year'])){ 
            $year = $data['year']; // called 'year' 
        } else {
            $year = date('Y'); //current year 
        }
        if(!empty($data['month'])) {
            $month = $data['month']; // called 'month' 
        } else {
            $month = date('m'); //current month 
        }
        $query = DB::table('kinkai.tb_price')
        ->leftJoin('kinkai.tb_market', function($join) use ($year, $month) {
            $join->on(DB::raw('DATE_FORMAT(kinkai.tb_price.resist_data, "%Y/%m/%d")'), '=', DB::raw('DATE_FORMAT(kinkai.tb_market.resist_data, "%Y/%m/%d")'));
        })
        ->select(
            DB::raw('DATE_FORMAT(kinkai.tb_price.resist_data, "%Y/%m/%d") as date'),
            'kinkai.tb_market.*',
            'kinkai.tb_price.gold as gold_price', 
            'kinkai.tb_price.silver as silver_price', 
            'kinkai.tb_price.palladium as palladium_price', 
            'kinkai.tb_price.platinum as platinum_price'
        )
        ->whereYear(DB::raw('DATE_FORMAT(kinkai.tb_price.resist_data, "%Y/%m/%d")'), $year)
        ->whereMonth(DB::raw('DATE_FORMAT(kinkai.tb_price.resist_data, "%Y/%m/%d")'), $month);
        
    if (!empty($data)) {
        $query = $query->whereBetween(DB::raw('DATE_FORMAT(kinkai.tb_price.resist_data, "%Y/%m/%d")'), [$data['from'], $data['to']]);
    }
    
    $tb_gold_market_array = $query->orderBy('date')
        ->get()
        ->toArray();
    
        
        $jewellaryprice = [];

        foreach ( $tb_gold_market_array as $index => $data ) {
            
            $diffGoldPrice = 0;
            $diffPlatinumPrice = 0;
            $diffSilverPrice = 0;
            $diffPalladiumPrice = 0;
        
            if(!empty($data->gold_price)){
                $todayGoldPrice = $tb_gold_market_array[$index]->gold_price;
                $yesterdayGoldPrice =  ($index > 0 ) ? $tb_gold_market_array[$index - 1]->gold_price : $tb_gold_market_array[$index]->gold_price;
                $diffGoldPrice = ($todayGoldPrice != 0) ? (int)$todayGoldPrice -  (int)$yesterdayGoldPrice : 0;
            }
            if(!empty($data->platinum_price)) {
                $todayPlatinumPrice = $tb_gold_market_array[$index]->platinum_price;
                $yesterdayPlatinumPrice =  ($index > 0 ) ? $tb_gold_market_array[$index - 1 ]->platinum_price : $tb_gold_market_array[$index]->platinum_price;
                $diffPlatinumPrice = ($todayPlatinumPrice != 0) ?  (int)$todayPlatinumPrice -  (int)$yesterdayPlatinumPrice : 0;  
            }
            if(!empty($data->silver_price)){
                $todaySilverPrice = $tb_gold_market_array[$index]->silver_price;
                $yesterdaySilverPrice =  ($index > 0 ) ? $tb_gold_market_array[$index - 1]->silver_price : $tb_gold_market_array[$index]->silver_price;
                $diffSilverPrice = ($todaySilverPrice != 0) ? (int)$todaySilverPrice -  (int)$yesterdaySilverPrice : 0;
            }
            if(!empty($data->palladium_price)){
                $todayPalladiumPrice = $tb_gold_market_array[$index]->palladium_price;
                $yesterdayPalladiumPrice =  ($index > 0 ) ? $tb_gold_market_array[$index - 1]->palladium_price : $tb_gold_market_array[$index]->palladium_price;
                $diffPalladiumPrice = ($todayPalladiumPrice != 0) ? (int)$todayPalladiumPrice -  (int)$yesterdayPalladiumPrice : 0;
            }
            //Gold
            $data->diff_gold_price = $diffGoldPrice;
            $data->today_gold_price = $todayGoldPrice;
            $data->yes_gold_price = $yesterdayGoldPrice;
            //Silver
            $data->diff_silver_price = $diffSilverPrice;
            $data->today_silver_price = $todaySilverPrice;
            $data->yes_silver_price = $yesterdaySilverPrice;
            //Palladium
            $data->diff_palladium_price = $diffPalladiumPrice;
            $data->today_palladium_price = $todayPalladiumPrice;
            $data->yes_palladium_price = $yesterdayPalladiumPrice;
            //Platinum
            $data->diff_platinum_price = $diffPlatinumPrice;
            $data->today_platinum_price = $todayPlatinumPrice;
            $data->yes_platinum_price = $yesterdayPlatinumPrice;        
            $jewellaryprice[]=$data;
            
        }
        return $tb_gold_market_array;
    }
}


