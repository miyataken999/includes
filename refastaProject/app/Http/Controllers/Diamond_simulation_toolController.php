<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Diamond_simulation_toolController extends Controller
{
    public function make_tool()
    {
        // 金全データ取得
        $gold_price = DB::table("kinkai.tb_gold")->get();
        $gold_k24 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k24'");
        $gold_k22 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k22'");
        $gold_k21 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k21'");
        $gold_k20 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k20'");
        $gold_k18 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k18'");
        $gold_k14 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k14'");
        $gold_k10 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k10'");
        $gold_k9 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k9'");
        $gold_k23 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k23'");
        $gold_k17 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k17'");
        $gold_k12 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k12'");
        $gold_k8 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k8'");
        $gold_k5 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k5'");
        // プラチナ全データ取得
        $platinum_price = DB::table("kinkai.tb_platinum")->get();
        $platinum_pt1000 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt1000'");
        $platinum_pt950 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt950'");
        $platinum_pt900 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt900'");
        $platinum_pt850 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt850'");
        $platinum_pt_pm = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt_pm'");

        // パラジウム全データ取得
         $palladium_price = DB::table("kinkai.tb_palladium")->get();
         $palladium_pd1000 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd1000'");
         $palladium_pd950 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd950'");
         $palladium_pd900 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd900'");
         $palladium_pd500 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd500'");

         // シルバー全データ取得 
         $silver_sv1000 = DB::select("SELECT `silver_price` FROM `kinkai`.`tb_silver` WHERE `silver_item` = 'sv1000'");
         $silver_sv925 = DB::select("SELECT `silver_price` FROM `kinkai`.`tb_silver` WHERE `silver_item` = 'sv925'");
         $silver_sv900 = DB::select("SELECT `silver_price` FROM `kinkai`.`tb_silver` WHERE `silver_item` = 'sv900'");
        // 歯科材のデータ取得、計算
        $calc_price = DB::select("SELECT `gold`, `palladium`, `silver` FROM `kinkai`.`tb_price` ORDER BY `id` DESC LIMIT 1");
        $dental_palladium_rate = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_palladium` WHERE `id` = '2'");

        $calc_1 = floor((round(($calc_price[0]->gold)*0.985)+2)*0.12);
        $calc_2 = floor((round(($calc_price[0]->palladium+1)*0.98)+1)*0.2);
        $calc_3 = floor(round($calc_price[0]->silver*0.9354)*0.45);
        $calc_4 = floor(round(($calc_1+$calc_2+$calc_3)-200) * $dental_palladium_rate[0]->item_rate);
        $calc_4 = floor($calc_4 * 10000) / 10000;
          
        //  コンビ計算用データ取得
        $gold_price_k18 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `id` = '7'");
        $platinum_price_pt900 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `id` = '5'");
        $platinum_price_pt850 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `id` = '6'");

        //  コンビ計算用レート取得
        $c_rate_pt900_p = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '1'");
        $c_rate_pt900_h = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '2'");
        $c_rate_pt900_k = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '3'");
        $c_rate_pt850_p = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '4'");
        $c_rate_pt850_h = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '5'");
        $c_rate_pt850_k = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '6'");

        // コンビの計算

        $pt900_p =  floor(floor($gold_price_k18[0]->gold_price) * 0.1) + floor(floor($platinum_price_pt900[0]->platinum_price) * 0.9);
        $pt900_p =  floor($pt900_p * $c_rate_pt900_p[0]->item_rate);
        $pt900_h =  floor(floor($gold_price_k18[0]->gold_price) / 2) + floor(floor($platinum_price_pt900[0]->platinum_price) / 2);
        $pt900_h =  floor($pt900_h * $c_rate_pt900_h[0]->item_rate);
        $pt900_k =  floor(floor($gold_price_k18[0]->gold_price) * 0.9) + floor(floor($platinum_price_pt900[0]->platinum_price) * 0.1);
        $pt900_k =  floor($pt900_k * $c_rate_pt900_k[0]->item_rate);

        $pt850_p =  floor(floor($gold_price_k18[0]->gold_price) * 0.1) + floor(floor($platinum_price_pt850[0]->platinum_price) * 0.9);
        $pt850_p =  floor($pt850_p * $c_rate_pt850_p[0]->item_rate);
        $pt850_h =  floor(floor($gold_price_k18[0]->gold_price) / 2) + floor(floor($platinum_price_pt850[0]->platinum_price) / 2);
        $pt850_h =  floor($pt850_h * $c_rate_pt850_h[0]->item_rate);
        $pt850_k =  floor(floor($gold_price_k18[0]->gold_price) * 0.9) + floor(floor($platinum_price_pt850[0]->platinum_price) * 0.1);
        $pt850_k =  floor($pt850_k * $c_rate_pt850_k[0]->item_rate);

        // 配列に入れる
        $array = array();
        $array["gold_price"] = $gold_price;
        $array["palladium_price"] = $palladium_price;
        $array["platinum_price"] = $platinum_price;
        $array["pt900_p"] = $pt900_p;
        $array["pt900_h"] = $pt900_h;
        $array["pt900_k"] = $pt900_k;
        $array["pt850_p"] = $pt850_p;
        $array["pt850_h"] = $pt850_h;
        $array["pt850_k"] = $pt850_k;
        $array["calc_4"] = $calc_4;
        $array["gold_k24"] = $gold_k24;
        $array["gold_k22"] = $gold_k22;
        $array["gold_k21"] = $gold_k21;
        $array["gold_k20"] = $gold_k20;
        $array["gold_k18"] = $gold_k18;
        $array["gold_k14"] = $gold_k14;
        $array["gold_k10"] = $gold_k10;
        $array["gold_k9"] = $gold_k9;
        $array["gold_k23"] = $gold_k23;
        $array["gold_k17"] = $gold_k17;
        $array["gold_k12"] = $gold_k12;
        $array["gold_k8"] = $gold_k8;
        $array["gold_k5"] = $gold_k5;
        $array["platinum_pt1000"] = $platinum_pt1000;
        $array["platinum_pt950"] = $platinum_pt950;
        $array["platinum_pt900"] = $platinum_pt900;
        $array["platinum_pt850"] = $platinum_pt850;
        $array["platinum_pt_pm"] = $platinum_pt_pm;
        $array["palladium_pd1000"] = $palladium_pd1000;
        $array["palladium_pd950"] = $palladium_pd950;
        $array["palladium_pd900"] = $palladium_pd900;
        $array["palladium_pd500"] = $palladium_pd500;
        $array["silver_sv1000"] = $silver_sv1000;
        $array["silver_sv925"] = $silver_sv925;
        $array["silver_sv900"] = $silver_sv900;


        return view('diamond_simulation_tool.index', $array);
    }

    public function get_graph()
    {
    $context = stream_context_create([
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false
        ]
    ]);
  
      return file_get_contents("https://kinkaimasu.jp/diatable/graph.html", false, $context);
    }
}


