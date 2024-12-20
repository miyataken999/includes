<?php
namespace App\Http\Controllers;
//名前空間

class CandleGraphController_nishitani extends Controller
{
    public function make_candle()
    {
        
        // ---------------------------------------------------------------------------
        // 入力データを指定の変数に格納
        // ---------------------------------------------------------------------------
        
        // URLパラメータの部分だけを変数に格納
        // $param = $request_uri;
        // parse_strで分解処理し、第二引数で配列の変数名を指定
        // parse_str($param, $str);
        if (isset($_GET['year_first'])) {
            $yfirst = htmlspecialchars($_GET['year_first'], ENT_QUOTES, 'UTF-8');
        } else {
            $yfirst = "";
        }

        if (isset($_GET['year_last'])) {
            $ylast = htmlspecialchars($_GET['year_last'], ENT_QUOTES, 'UTF-8');
        } else {
            $ylast = "";
        }

        if (isset($_GET['year_select'])) {
            $year_select = htmlspecialchars($_GET['year_select'], ENT_QUOTES, 'UTF-8');
        } else {
            $year_select = "";
        }
        
        // ---------------------------------------------------------------------------
        // 入力内容による期間データの加工
        // ---------------------------------------------------------------------------



        // ---------------------------------------------------------------------------
        // DBからデータ取得 20210719 satou add
        // ---------------------------------------------------------------------------
        
        if((isset($_GET["year_select"])) and ($_GET["year_select"] != "" )){
            $request_uri = $_SERVER['REQUEST_URI'];
            if($_GET["year_select"] == 2009){
                $year_select = 2009;
            }elseif($_GET["year_select"] == 2012){
                $year_select = 2012;
            }elseif($_GET["year_select"] == 2015){
                $year_select = 2015;
            }elseif($_GET["year_select"] == 2018){
                $year_select = 2018;
            }else{
                $year_select = date("Y") - 2;
            }
        }else{
            $year_select = date("Y") - 2;
        }
        
        $start_year = $year_select;
        $end_year = $year_select + 2;
        
        // ---------------------------------------------------------------------------
        // DBからデータ取得 
        // ---------------------------------------------------------------------------
        //DBからgold platinumから月初の値、月末の値、最安値、最高値をひっぱってくる
        $whereraw = "
            (DATE_FORMAT(date, '%Y') BETWEEN ? AND ?)
            AND (high > 0)
            AND (low > 0)
            AND (open > 0)
            AND (close > 0)
        ";

        // gold
        $golddata = \DB::table("kinkai.tb_gold_market_year")
        ->whereRaw($whereraw, array($start_year, $end_year))
        ->orderBy("date")
        ->get();
        // platinum
        $platinumdata = \DB::table("kinkai.tb_platinum_market_year")
        ->whereRaw($whereraw, array($start_year, $end_year))
        ->orderBy("date")
        ->get();
        
        // $request_uri = $_SERVER['REQUEST_URI'];
        // $request_url = str_replace('/refasta_wordpress', '', $_SERVER['REQUEST_URI']);
        // $request_link = strstr($request_url, '?', true);
        
        // ---------------------------------------------------------------------------
        // 表示用テキストの加工
        // ---------------------------------------------------------------------------
        if (isset($golddata[count($golddata) - 1])) {
            $end_time = $golddata[count($golddata) - 1]->date;
            $end_text = date("y年n月j日", strtotime($end_time));
        }
        if (isset($golddata[0])) {
            $start_time = $golddata[0]->date;
            $start_text = date("y年n月j日", strtotime($start_time));
        }
        $display_text = "{$start_text}から{$end_text}";

        
        // $dataset_array = array();
		// $dataset_array["high"] = array();
		// $dataset_array["low"] = array();
		// $dataset_array["open"] = array();
		// $dataset_array["close"] = array();
		// $dataset_array["date"] = array();

        // foreach($golddata as $item){
		// 	$date = $item->date;
		// 	$date = str_replace("-", "/" , $date);
        //     $dataset_array["high"][] = $item->high;
        //     $dataset_array["low"][] = $item->low;
        //     $dataset_array["open"][] = $item->open;
        //     $dataset_array["close"][] = $item->close;
		// }

        // $dataset_text_res["high"] = implode(",", $dataset_array["high"]);
		// $dataset_text_res["low"] = implode(",", $dataset_array["low"]);
		// $dataset_text_res["open"] = implode(",", $dataset_array["open"]);
		// $dataset_text_res["close"] = implode(",", $dataset_array["close"]);
		// $dataset_text_res["date"] = implode(",", $dataset_array["date"]);



        // URLに「/gold/」が部分一致するか判定する
        $is_gold_show = "";
        $is_platinum_show = "";
        if (stristr($_SERVER["REQUEST_URI"], "/platinum/")) {
            $is_platinum_show = "on";
        } elseif (stristr($_SERVER["REQUEST_URI"], "/gold/")) {
            $is_gold_show = "on";
        }
        // 判定に従ってデフォルトを変更する。
        if (isset($_GET['open_gold'])) {
            if ($_GET['open_gold'] == "on") {
                $is_gold_show = "on";
            }
        }
        if (isset($_GET['open_platinum'])) {
            if ($_GET['open_platinum'] == "on") {
                $is_platinum_show = "on";
            }
        }
        
 

        



        $option_data = array();
        // $option_data["dataset_text"] = $dataset_text;
        $option_data["yfirst"] = $yfirst;
        $option_data["ylast"] = $ylast;
        $option_data["display_text"] = $display_text;
        // $option_data["dataset_json"] = $dataset_json;
        // $option_data["dataset_text_res"] = $dataset_text_res;
        //bladeの方で使えるように変数を渡す
        $option_data["is_gold_show"] = $is_gold_show;
        $option_data["is_platinum_show"] = $is_platinum_show;
        $option_data["year_select"] = $year_select;

        $option_data["golddata"] = $golddata;
        $option_data["platinumdata"] = $platinumdata;
                
        return view("make_candle_graph.index_nishitani", $option_data);
    }
}
