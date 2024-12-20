<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Ingot_price_listController extends Controller
{
    public function make_list()
    {
        // データ取得
        $gold_market_price_500over = DB::table("kinkai.tb_gold")->where("id",1)->first();
        $gold_market_price_100over = DB::table("kinkai.tb_gold")->where("id", 2)->first();
        $gold_market_price_k24 = DB::table("kinkai.tb_gold")->where("id", 3)->first();
        $platinum_market_price_500over = DB::table("kinkai.tb_platinum")->where("id", 1)->first();
        $platinum_market_price_100over = DB::table("kinkai.tb_platinum")->where("id", 2)->first();
        $platinum_market_price_pt1000 = DB::table("kinkai.tb_platinum")->where("id", 3)->first();
        $silver_market_price = DB::table("kinkai.tb_silver")->where("id",4)->first();
        $silver_market_price_sv1000 = DB::table("kinkai.tb_silver")->where("id", 2)->first();
        $palladium_market_price = DB::table("kinkai.tb_palladium")->where("id", 1)->first();
        $palladium_market_price_100over = DB::table("kinkai.tb_palladium")->where("id", 3)->first();
        $palladium_market_price_pd1000 = DB::table("kinkai.tb_palladium")->where("id", 4)->first();

        // 最新インゴット相場取得
        $ingot_price = DB::table("kinkai.tb_price")->orderBy('id','desc')->first();

        //指定日の曜日を取得する
        $date = date('w', strtotime(date($ingot_price->resist_data_type_date)));
        $week = ['日','月','火','水','木','金','土',];
        
        // 前日料金取得
        $date_price = DB::table("kinkai.tb_price")->orderBy('id','desc')->offset(1)->limit(1)->first();
        $gold_rate_history = DB::table("kinkai.tb_gold_rate_history")->orderBy('date','desc')->offset(1)->limit(1)->first();
        $platinum_rate_history = DB::table("kinkai.tb_platinum_rate_history")->orderBy('date','desc')->offset(1)->limit(1)->first();
        $silver_rate_history = DB::table("kinkai.tb_silver_rate_history")->orderBy('date','desc')->offset(1)->limit(1)->first();
        $palladium_rate_history = DB::table("kinkai.tb_palladium_rate_history")->orderBy('date','desc')->offset(1)->limit(1)->first();


        // 料率計算
        // ゴールド
        $gold_rate_500over_yesterday= floor($gold_market_price_500over->gold_price) - floor($gold_rate_history->ingot_500over * $date_price->gold);
        $gold_rate_100over_yesterday= floor($gold_market_price_100over->gold_price) - floor($gold_rate_history->ingot_100over * $date_price->gold);
        $gold_rate_k24_yesterday= floor($gold_market_price_k24->gold_price) - floor($gold_rate_history->k24 * $date_price->gold);
        // プラチナ
        $platinum_rate_500over_yesterday= floor($platinum_market_price_500over->platinum_price) - floor($platinum_rate_history->ingot_500over * $date_price->platinum);
        $platinum_rate_100over_yesterday= floor($platinum_market_price_100over->platinum_price) - floor($platinum_rate_history->ingot_100over * $date_price->platinum);
        $platinum_rate_pt1000_yesterday= floor($platinum_market_price_pt1000->platinum_price) - floor($platinum_rate_history->pt1000 * $date_price->platinum);
        // シルバー
        $silver_rate_yesterday= floor($silver_market_price->silver_price) - floor($silver_rate_history->ingot * $date_price->silver);
        $silver_rate_sv1000_yesterday= floor($silver_market_price_sv1000->silver_price) - floor($silver_rate_history->sv1000 * $date_price->silver);
        // パラジウム
        $palladium_rate_ingot_yesterday= floor($palladium_market_price->palladium_price) - floor($palladium_rate_history->ingot * $date_price->palladium);
        $palladium_rate_ingot_100over_yesterday= floor($palladium_market_price_100over->palladium_price) - floor($palladium_rate_history->ingot_100over * $date_price->palladium);
        $palladium_rate_pd1000_yesterday= floor($palladium_market_price_pd1000->palladium_price) - floor($palladium_rate_history->pd1000 * $date_price->palladium);
 
        // マーケット情報取得
        $market = DB::table("kinkai.tb_market")->orderBy('resist_data','desc')->first();
        $market_yesterday = DB::table("kinkai.tb_market")->orderBy('resist_data','desc')->offset(1)->limit(1)->first();
        // マーケット情報計算
        $market_price_ny_gold_comparison= $market->ny_gold - $market_yesterday->ny_gold;
        $market_price_ny_platinum_comparison= $market->ny_platinum - $market_yesterday->ny_platinum;
        $market_price_ny_silver_comparison= $market->ny_silver - $market_yesterday->ny_silver;
        if ($market_price_ny_silver_comparison == "") {
            $market_price_ny_silver_comparison  = 0;
        }

        // 配列に入れる
        $array = array();
        $array["date_price"] = $date_price;
        $array["gold_rate_history"] = $gold_rate_history;
        $array["gold_rate_500over_yesterday"] = $gold_rate_500over_yesterday;
        $array["gold_rate_100over_yesterday"] = $gold_rate_100over_yesterday;
        $array["gold_rate_k24_yesterday"] = $gold_rate_k24_yesterday;
        $array["platinum_rate_500over_yesterday"] = $platinum_rate_500over_yesterday;
        $array["platinum_rate_100over_yesterday"] = $platinum_rate_100over_yesterday;
        $array["platinum_rate_pt1000_yesterday"] = $platinum_rate_pt1000_yesterday;
        $array["silver_rate_yesterday"] = $silver_rate_yesterday;
        $array["silver_rate_sv1000_yesterday"] = $silver_rate_sv1000_yesterday;
        $array["palladium_rate_ingot_100over_yesterday"] = $palladium_rate_ingot_100over_yesterday;
        $array["palladium_rate_ingot_yesterday"] = $palladium_rate_ingot_yesterday;
        $array["palladium_rate_pd1000_yesterday"] = $palladium_rate_pd1000_yesterday;
        $array["market_yesterday"] = $market_yesterday;
        $array["market_price_ny_gold_comparison"] = $market_price_ny_gold_comparison;
        $array["market_price_ny_platinum_comparison"] = $market_price_ny_platinum_comparison;
        $array["market_price_ny_silver_comparison"] = $market_price_ny_silver_comparison;
        $array["silver_rate_yesterday"] = $silver_rate_yesterday;
        $array["silver_rate_sv1000_yesterday"] = $silver_rate_sv1000_yesterday;
        $array["gold_market_price_500over"] = $gold_market_price_500over;
        $array["gold_market_price_100over"] = $gold_market_price_100over;
        $array["gold_market_price_k24"] = $gold_market_price_k24;
        $array["platinum_market_price_500over"] = $platinum_market_price_500over;
        $array["platinum_market_price_100over"] = $platinum_market_price_100over;
        $array["platinum_market_price_pt1000"] = $platinum_market_price_pt1000;
        $array["silver_market_price"] = $silver_market_price;
        $array["silver_market_price_sv1000"] = $silver_market_price_sv1000;
        $array["palladium_market_price"] = $palladium_market_price;
        $array["palladium_market_price_100over"] = $palladium_market_price_100over;
        $array["palladium_market_price_pd1000"] = $palladium_market_price_pd1000;
        $array["market"] = $market;
        $array["palladium_rate_history"] = $palladium_rate_history;
        $array["gold_rate_history"] = $gold_rate_history;
        $array["platinum_rate_history"] = $platinum_rate_history;
        $array["ingot_price"] = $ingot_price;
        $array["date"] = $date;
        $array["week"] = $week;

        return view('ingot_price_list.index', $array);
    }

}
