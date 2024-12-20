<?php
namespace App\Http\Controllers;
//名前空間
use Illuminate\Support\Facades\DB;
class GraphController extends Controller
{
    public function make_graph(){
		if(isset($_GET['request_url'])){
			$_SERVER['REQUEST_URI'] = htmlspecialchars($_GET['request_url'], ENT_QUOTES, 'UTF-8');
		}
		if(isset($_GET['year_first'])){
			$yfirst = htmlspecialchars($_GET['year_first'], ENT_QUOTES, 'UTF-8');
			//ENT_QUOTES ;とかの引用符もエスケープする。
			//UTF-8　セキュリティのため　
		}else{
			$yfirst = "";
		}

		if(isset($_GET['month_first'])){
			$mfirst = htmlspecialchars($_GET['month_first'], ENT_QUOTES, 'UTF-8');
		}else{
			$mfirst = "";
		}

		if(isset($_GET['year_last'])){
			$ylast = htmlspecialchars($_GET['year_last'], ENT_QUOTES, 'UTF-8');
		}else{
			$ylast = "";
		}

		if(isset($_GET['month_last'])){
			$mlast = htmlspecialchars($_GET['month_last'], ENT_QUOTES, 'UTF-8');
		}else{
			$mlast = "";
		}

		if(isset($_GET['month'])){
			$selected_month = htmlspecialchars($_GET['month'], ENT_QUOTES, 'UTF-8');
			//htmlspecialchars　意味をもつ文字からもたない文字　エスケープ
		}else{
			$selected_month = "";
		}
		if(isset($_GET['select_type'])){
			$select_type = htmlspecialchars($_GET['select_type'], ENT_QUOTES, 'UTF-8');
		}else{
			$select_type = "";
		}

		$now = time();
		$s = 60 * 60 * 24 * 30;

		if($select_type == ""){

			$start = date("Y-m-01",strtotime(date('Y-m-01') . '-1 month'));//先月の1日
			//今から30日前の日付から表示
			//タイムスタンプ
			if(isset($_GET["satou_test"])){
				$start = date("Y-m-01",strtotime(date('Y-m-01') . '-4 month'));//先月の1日
			}
			$end = date("Y-m-t");//今月の最終日
			//何年何月何日　時間
			$start_text = date("y年n月j日",strtotime($start));
			$end_text = date("y年n月j日",strtotime($end));
			//strtotime 指定したいとき
			//"{変数}"

			$request_uri = $_SERVER['REQUEST_URI'];
			if(strstr($request_uri,'platinum')){
				$start = date('Y-m-01',strtotime(date('Y-m-01') . '-1 year -3 month'));
				$start_text = date("y年n月j日",strtotime($start));
				$yfirst = date('Y',strtotime($start));
				$mfirst = date('m',strtotime($start));
			}elseif((strstr($request_uri,'silver')) or (strstr($request_uri,'gold/silver'))){
				$start = date('Y-m-01',strtotime(date('Y-m-01') . '-1 year -2 month'));
				$start_text = date("y年n月j日",strtotime($start));
				$yfirst = date('Y',strtotime($start));
				$mfirst = date('m',strtotime($start));
			}elseif(strstr($request_uri,'palladium')){
				$start = date('Y-m-01',strtotime(date('Y-m-01') . '-1 year -1 month'));
				$start_text = date("y年n月j日",strtotime($start));
				$yfirst = date('Y',strtotime($start));
				$mfirst = date('m',strtotime($start));
			}elseif(strstr($request_uri,'gold')){
				$start = date('Y-m-01',strtotime(date('Y-m-01') . '-3 year'));
				$start_text = date("y年n月j日",strtotime($start));
				$yfirst = date('Y',strtotime($start));
				$mfirst = date('m',strtotime($start));
			}elseif(strstr($request_uri,'ingot')){
				$start = date('Y-m-01',strtotime(date('Y-m-01') . '-3 year'));
				$start_text = date("y年n月j日",strtotime($start));
				$yfirst = date('Y',strtotime($start));
				$mfirst = date('m',strtotime($start));
			}




		}else{
			if($select_type == "month"){
				if($selected_month == ""){
					$start = date('Y-m-01');
					$end = date('Y-m-t');
				}else{
					$start = $selected_month ."-01";
					$end = date('Y-m-t',strtotime($selected_month . "-01 00:00:00"));
				}
			}else{
				$start = $yfirst . "-" . $mfirst . "-01";
				$end = date('Y-m-t',strtotime($ylast . "-" . $mlast ."-01 00:00:00"));
			}
			$start_text = date("y年n月j日",strtotime($start));
			$end_text = date("y年n月j日",strtotime($end));
		}

		$start_time = strtotime($start);
		$end_time = strtotime($end);

		$between_time = $end_time - $start_time;
		$judge_time = 60 * 60 * 24 * 180;
		$group_by_month = "";
		if($between_time > $judge_time){
		    $group_by_month = "on";

            $select = "IF(gold_max IS NULL OR gold_max = '', 0, gold_max) AS `gold`
			, IF(platinum_max IS NULL OR platinum_max = '', 0, platinum_max) AS `platinum`
			, IF(silver_max IS NULL OR silver_max = '', 0, silver_max) AS `silver`
			, IF(palladium_max IS NULL OR palladium_max = '', 0, palladium_max) AS `palladium`
			, IF(jp_market_cnh IS NULL OR jp_market_cnh = '', 0, jp_market_cnh) AS `cnh_jyp`
			, IF(jp_market IS NULL OR jp_market = '', 0, jp_market) AS `usd_jyp`
            , REPLACE(start_date,' 00:00:00','') as `resist_data_type_date`";
            $table = "tb_price_group_by_month";
            $where = "start_date BETWEEN ? AND ?";
            $result = DB::table('kinkai.'.$table)
              ->selectRaw($select)
              ->leftjoin("kinkai.tb_market", DB::RAW("REPLACE(REPLACE(kinkai.{$table}.start_date,'-','/'),' 00:00:00','') "), '=', DB::RAW("DATE_FORMAT(kinkai.tb_market.resist_data, '%Y/%m/%d')"));

		}else{
            $select = "resist_data_type_date
			, IF(gold IS NULL OR gold = '', 0, gold) AS `gold`
			, IF(platinum IS NULL OR platinum = '', 0, platinum) AS `platinum`
			, IF(silver IS NULL OR silver = '', 0, silver) AS `silver`
			, IF(palladium IS NULL OR palladium = '', 0, palladium) AS `palladium`
			, IF(jp_market_cnh IS NULL OR jp_market_cnh = '', 0, jp_market_cnh) AS `cnh_jyp`
			, IF(jp_market IS NULL OR jp_market = '', 0, jp_market) AS `usd_jyp`";
            $table = "tb_price";
            $where = "resist_data_type_date BETWEEN ? AND ?";
            $result = DB::table('kinkai.'.$table)
              ->selectRaw($select)
              ->leftjoin('kinkai.tb_market', "kinkai.{$table}.resist_data", '=', DB::RAW("DATE_FORMAT(kinkai.tb_market.resist_data, '%Y/%m/%d')"));

		}
      $result = $result->whereRaw($where, array($start, $end))
        ->orderBy("resist_data_type_date")
        ->get();
		//var_dump($result);
		if(isset($result[count($result) - 1])){
			$end_time = $result[count($result) - 1]->resist_data_type_date;
			$end_text = date("Y年n月j日",strtotime($end_time));
		}
		if(isset($result[0])){
			$start_time = $result[0]->resist_data_type_date;
			$start_text = date("Y年n月j日",strtotime($start_time));
		}
		$display_text = "{$start_text}から{$end_text}";

        $dataset_text = "";



		// json形式にする内容を格納する配列
		$dataset_array = array();
		$dataset_array["gd"] = array();
		$dataset_array["pt"] = array();
		$dataset_array["sv"] = array();
		$dataset_array["pd"] = array();
		$dataset_array["date"] = array();
		$dataset_array["cnh_jyp"] = array();
		$dataset_array["usd_jyp"] = array();


		foreach($result as $item){
			$date = $item->resist_data_type_date;
			$date = str_replace("-", "/" , $date);
			// $dataset_array["gd"][] = array("date"=>$date, "value"=>$item->gold);
			// $dataset_array["pt"][] = array("date"=>$date, "value"=>$item->platinum);
			// $dataset_array["sv"][] = array("date"=>$date, "value"=>$item->silver);
			// $dataset_array["pd"][] = array("date"=>$date, "value"=>$item->palladium);
			$dataset_array["gd"][] = $item->gold;
			$dataset_array["pt"][] = $item->platinum;
			$dataset_array["sv"][] = $item->silver;
			$dataset_array["pd"][] = $item->palladium;
			$dataset_array["date"][] = '"'.$date.'"';
			$dataset_array["cnh_jyp"][] = $item->cnh_jyp;
			$dataset_array["usd_jyp"][] = $item->usd_jyp;
		}
		// $dataset_json["gd"] = json_encode($dataset_array["gd"], JSON_NUMERIC_CHECK);
		// $dataset_json["pt"] = json_encode($dataset_array["pt"], JSON_NUMERIC_CHECK);
		// $dataset_json["sv"] = json_encode($dataset_array["sv"], JSON_NUMERIC_CHECK);
		// $dataset_json["pd"] = json_encode($dataset_array["pd"], JSON_NUMERIC_CHECK);
		// $dataset_json["date"] = json_encode($dataset_array["date"], JSON_NUMERIC_CHECK);

		$dataset_text_res["gd"] = implode(",", $dataset_array["gd"]);
		$dataset_text_res["pt"] = implode(",", $dataset_array["pt"]);
		$dataset_text_res["sv"] = implode(",", $dataset_array["sv"]);
		$dataset_text_res["pd"] = implode(",", $dataset_array["pd"]);
		$dataset_text_res["date"] = implode(",", $dataset_array["date"]);
		$dataset_text_res["cnh_jyp"] = implode(",", $dataset_array["cnh_jyp"]);
		$dataset_text_res["usd_jyp"] = implode(",", $dataset_array["usd_jyp"]);

		// var_dump($dataset_text_res);
		// var_dump($dataset_text_res["date"]);

        foreach($result as $item){
			$date = $item->resist_data_type_date;
			$date = str_replace("-", "/" , $date);
			//str_replace　文字列の置換
			$text = "";
			$text .= "{";
			$text .= "date: '" . $date . "',";
			//$item-> カラム名　データの取得
			$text .= "value: " . $item->palladium;
			$text .= "},";
			//.= は $text = $text .　と同じ .=足し算
			$dataset_text .= $text;
			//$dataset_textに$text追記
        }



			$request_url = str_replace('/refasta_wordpress', '', $_SERVER['REQUEST_URI']);
			$request_link = strstr($request_url, '?', true);



			$chart_link = array();
			$now_year = date('Y');
			//今日の年
			$now_month = date('m');
			//今日の月
			$one_year_ago = date('Y',strtotime(date('Y')) - 60 * 60 * 24 * 365);
			$three_year_ago = date('Y',strtotime(date('Y')) - 60 * 60 * 24 * 365 * 3);
			$five_year_ago = date('Y',strtotime(date('Y')) - 60 * 60 * 24 * 365 * 5);

			$chart_link[$one_year_ago] = "https://kinkaimasu.jp{$request_link}?select_type=between&year_first={$one_year_ago}&month_first={$now_month}&year_last={$now_year}&month_last={$now_month}&month=#graph_search_ttl";
			$chart_link[$three_year_ago] = "https://kinkaimasu.jp{$request_link}?select_type=between&year_first={$three_year_ago}&month_first={$now_month}&year_last={$now_year}&month_last={$now_month}&month=#graph_search_ttl";
			$chart_link[$five_year_ago] = "https://kinkaimasu.jp{$request_link}?select_type=between&year_first={$five_year_ago}&month_first={$now_month}&year_last={$now_year}&month_last={$now_month}&month=#graph_search_ttl";

			//_year_ago←「キー」に対して　＝がバリュー　リンク link_item
			// print('<pre>');
			// var_dump($chart_link);
			// print('</pre>');


		// URLを基準にデフォルト表示を操作する(php)

		// URLを取得

		// URLに「/gold/」が部分一致するか判定する
		$is_gold_show = "";
		$is_platinum_show = "";
		$is_silver_show = "";
		$is_palladium_show = "";
		$is_cnh_jyp_show = "";
		$is_usd_jyp_show = "";

		if(stristr($_SERVER["REQUEST_URI"],"/platinum/")){
			$is_platinum_show = "on";
		}elseif(stristr($_SERVER["REQUEST_URI"],"/silver/")){
			$is_silver_show = "on";
		}elseif(stristr($_SERVER["REQUEST_URI"],"/palladium/")){
			$is_palladium_show = "on";
		}elseif(stristr($_SERVER["REQUEST_URI"],"/gold/")){
			$is_gold_show = "on";
		}elseif(stristr($_SERVER["REQUEST_URI"],"/cnh_jyp/")){
			$is_cnh_jyp_show = "on";
		}elseif(stristr($_SERVER["REQUEST_URI"],"/usd_jyp/")){
			$is_usd_jyp_show = "on";
		}else{
			$is_gold_show = "on";
		}
		// 判定に従ってデフォルトを変更する。


		if(isset($_GET['open_gold'])){
			if($_GET['open_gold'] == "on"){
				$is_gold_show = "on";
			}
		}
		if(isset($_GET['open_silver'])){
			if($_GET['open_silver'] == "on"){
				$is_silver_show = "on";
			}
		}
		if(isset($_GET['open_platinum'])){
			if($_GET['open_platinum'] == "on"){
				$is_platinum_show = "on";
			}
		}
		if(isset($_GET['open_palladium'])){
			if($_GET['open_palladium'] == "on"){
				$is_palladium_show = "on";
			}
		}
		if(isset($_GET['open_cnh_jyp'])){
			if($_GET['open_cnh_jyp'] == "on"){
				$is_cnh_jyp_show = "on";
			}
		}
		if(isset($_GET['open_usd_jyp'])){
			if($_GET['open_usd_jyp'] == "on"){
				$is_usd_jyp_show = "on";
			}
		}

		if(isset($_GET["in_app"])){
			$in_app = true;
			$is_gold_show = "on";
			// return "aaa";
		}else{
			$in_app = false;
		}

		$option_data = array();
		$option_data["dataset_text"] = $dataset_text;
		$option_data["yfirst"] = $yfirst;
		$option_data["mfirst"] = $mfirst;
		$option_data["ylast"] = $ylast;
		$option_data["mlast"] = $mlast;
		$option_data["display_text"] = $display_text;
		$option_data["selected_month"] = $selected_month;
		// $option_data["dataset_json"] = $dataset_json;
		$option_data["dataset_text_res"] = $dataset_text_res;
		//bladeの方で使えるように変数を渡す
		$option_data["select_type"] = $select_type;
		$option_data["group_by_month"] = $group_by_month;
		$option_data["chart_link"] = $chart_link;
		$option_data["is_gold_show"] = $is_gold_show;
		$option_data["is_platinum_show"] = $is_platinum_show;
		$option_data["is_silver_show"] = $is_silver_show;
		$option_data["is_palladium_show"] = $is_palladium_show;
		$option_data["is_cnh_jyp_show"] = $is_cnh_jyp_show;
		$option_data["is_usd_jyp_show"] = $is_usd_jyp_show;

		// アプリからのフラグ
		$option_data["in_app"] = $in_app;

        //var_dump($result);
		if($in_app === true){
			return view("make_graph.in_app_index",$option_data);
		}else{
			return view("make_graph.index",$option_data);
		}

    }
}
