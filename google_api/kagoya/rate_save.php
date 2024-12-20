<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	金価格取得プログラム

■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
ini_set("display_errors",0);
include_once $dirname.'/config.php';




/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
本日の価格取得
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
$property_price = array();



$gold = array();
$platinum = array();
$silver = array();
$palladium = array();

$cls_tb_gold = tb_gold::order_by_asc("id")->find_many();
$cls_tb_platinum = tb_platinum::order_by_asc("id")->find_many();
$cls_tb_silver = tb_silver::order_by_asc("id")->find_many();
$cls_tb_palladium = tb_palladium::order_by_asc("id")->find_many();
$cls_tb_combi = tb_combi::order_by_asc("id")->find_many();

$now_date = date("Y-m-d 00:00:00");

$category_name = "gold";
$price_name = "gold_item";
foreach ($cls_tb_gold as $item) {
	$rate_name = $item->$price_name;
	$tb_gold_rate_history = tb_gold_rate_history::where('date',$now_date)->find_one();
	if($tb_gold_rate_history == false){
		$tb_gold_rate_history = tb_gold_rate_history::create();
		$tb_gold_rate_history->date = $now_date;
	}
	$tb_gold_rate_history->$rate_name = $item->item_rate;
	$tb_gold_rate_history->save();
}


$category_name = "platinum";
$price_name = "platinum_item";
foreach ($cls_tb_platinum as $item) {
	$rate_name = $item->$price_name;
	$tb_platinum_rate_history = tb_platinum_rate_history::where('date',$now_date)->find_one();
	if($tb_platinum_rate_history == false){
		$tb_platinum_rate_history = tb_platinum_rate_history::create();
		$tb_platinum_rate_history->date = $now_date;
	}
	$tb_platinum_rate_history->$rate_name = $item->item_rate;
	$tb_platinum_rate_history->save();
}


$category_name = "silver";
$price_name = "silver_item";
foreach ($cls_tb_silver as $item) {
	$rate_name = $item->$price_name;
	$tb_silver_rate_history = tb_silver_rate_history::where('date',$now_date)->find_one();
	if($tb_silver_rate_history == false){
		$tb_silver_rate_history = tb_silver_rate_history::create();
		$tb_silver_rate_history->date = $now_date;
	}
	$tb_silver_rate_history->$rate_name = $item->item_rate;
	$tb_silver_rate_history->save();
}


$category_name = "palladium";
$price_name = "palladium_item";
foreach ($cls_tb_palladium as $item) {
	$rate_name = $item->$price_name;
	$tb_palladium_rate_history = tb_palladium_rate_history::where('date',$now_date)->find_one();
	if($tb_palladium_rate_history == false){
		$tb_palladium_rate_history = tb_palladium_rate_history::create();
		$tb_palladium_rate_history->date = $now_date;
	}
	$tb_palladium_rate_history->$rate_name = $item->item_rate;
	$tb_palladium_rate_history->save();
}


$category_name = "combi";
$price_name = "combi_item";
foreach ($cls_tb_combi as $item) {
	$rate_name = $item->$price_name;
	$tb_combi_rate_history = tb_combi_rate_history::where('date',$now_date)->find_one();
	if($tb_combi_rate_history == false){
		$tb_combi_rate_history = tb_combi_rate_history::create();
		$tb_combi_rate_history->date = $now_date;
	}
	$tb_combi_rate_history->$rate_name = $item->item_rate;
	$tb_combi_rate_history->save();
}

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
END 本日の価格取得
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/




include_once '/usr/local/src/google_api/kagoya_to_sakura/gold_to_sakura.php';
