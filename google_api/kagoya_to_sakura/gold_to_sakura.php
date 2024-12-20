<?php
ini_set("display_errors",0);


$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	金価格取得プログラム

■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_gold.php';
// include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_gold_copy.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_platinum.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_silver.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_palladium.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_combi.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_price.php';




/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
本日の価格取得
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
$tb_price = tb_price::order_by_desc("resist_data_type_date")->limit(1)->find_one();

$kir["tb_gold"] = tb_gold::find_many();
$kir["tb_platinum"] = tb_platinum::find_many();
$kir["tb_silver"] = tb_silver::find_many();
$kir["tb_palladium"] = tb_palladium::find_many();

$for_combi = array();




ORM::reset_db();



include_once '/usr/local/src/google_api/idiorm_paris/app/tb_gold_property_price.php';


$category = "gold";
foreach ($kir["tb_gold"] as $item) {
	$tb_gold_property_price = tb_gold_property_price::where("category",$category)->where("reference_id",$item->id)->find_one();
	if($tb_gold_property_price == false){
		$tb_gold_property_price = tb_gold_property_price::create();

		$tb_gold_property_price->category = $category;
		$tb_gold_property_price->reference_id = $item->id;
		$tb_gold_property_price->save();
	}

	$tb_gold_property_price->item = $item->gold_item;
	$tb_gold_property_price->price = floor($item->gold_price);
	$tb_gold_property_price->rate = $item->item_rate;

	$tb_gold_property_price->save();
	//コンビ計算用に保存しておく
	$for_combi[$tb_gold_property_price->item] = $tb_gold_property_price->price;
}


$category = "platinum";
foreach ($kir["tb_platinum"] as $item) {
	$tb_gold_property_price = tb_gold_property_price::where("category",$category)->where("reference_id",$item->id)->find_one();
	if($tb_gold_property_price == false){
		$tb_gold_property_price = tb_gold_property_price::create();

		$tb_gold_property_price->category = $category;
		$tb_gold_property_price->reference_id = $item->id;
		$tb_gold_property_price->save();
	}

	$tb_gold_property_price->item = $item->platinum_item;
	$tb_gold_property_price->price = floor($item->platinum_price);
	$tb_gold_property_price->rate = $item->item_rate;

	$tb_gold_property_price->save();
	//コンビ計算用に保存しておく
	$for_combi[$tb_gold_property_price->item] = $tb_gold_property_price->price;
}


$category = "silver";
foreach ($kir["tb_silver"] as $item) {
	$tb_gold_property_price = tb_gold_property_price::where("category",$category)->where("reference_id",$item->id)->find_one();
	if($tb_gold_property_price == false){
		$tb_gold_property_price = tb_gold_property_price::create();

		$tb_gold_property_price->category = $category;
		$tb_gold_property_price->reference_id = $item->id;
		$tb_gold_property_price->save();
	}

	$tb_gold_property_price->item = $item->silver_item;
	$tb_gold_property_price->price = floor($item->silver_price);
	$tb_gold_property_price->rate = $item->item_rate;

	$tb_gold_property_price->save();
	//コンビ計算用に保存しておく
	$for_combi[$tb_gold_property_price->item] = $tb_gold_property_price->price;
}



$category = "palladium";
foreach ($kir["tb_palladium"] as $item) {
	$tb_gold_property_price = tb_gold_property_price::where("category",$category)->where("reference_id",$item->id)->find_one();
	if($tb_gold_property_price == false){
		$tb_gold_property_price = tb_gold_property_price::create();

		$tb_gold_property_price->category = $category;
		$tb_gold_property_price->reference_id = $item->id;
		$tb_gold_property_price->save();
	}

	$tb_gold_property_price->item = $item->palladium_item;
	$tb_gold_property_price->price = floor($item->palladium_price);
	$tb_gold_property_price->rate = $item->item_rate;

	$tb_gold_property_price->save();
	//コンビ計算用に保存しておく
	$for_combi[$tb_gold_property_price->item] = $tb_gold_property_price->price;
}






/*

コンビの計算

*/


$k18 = floor($for_combi["k18"]);
$k18_9 = floor($k18 * 0.9);
$k18_5 = floor($k18 * 0.5);
$k18_1 = floor($k18 * 0.1);

$pt900 = floor($for_combi["pt900"]);
$pt900_9 = floor($pt900 * 0.9);
$pt900_5 = floor($pt900 * 0.5);
$pt900_1 = floor($pt900 * 0.1);

$pt850 = floor($for_combi["pt850"]);
$pt850_9 = floor($pt850 * 0.9);
$pt850_5 = floor($pt850 * 0.5);
$pt850_1 = floor($pt850 * 0.1);

$pt950 = floor($for_combi["pt950"]);
$pt950_9 = floor($pt950 * 0.9);
$pt950_5 = floor($pt950 * 0.5);
$pt950_1 = floor($pt950 * 0.1);

$combi_price[34]["item"] = "p900_k18_p";
$combi_price[34]["price"] = $pt900_k18_p = floor( ($pt900_9 + $k18_1) * 0.928);
$combi_price[35]["item"] = "pt900_k18_h";
$combi_price[35]["price"] = $pt900_k18_h = floor( ($pt900_5 + $k18_5) * 0.948);
$combi_price[36]["item"] = "pt900_k18_k";
$combi_price[36]["price"] = $pt900_k18_k = floor( ($pt900_1 + $k18_9) * 0.978);

$combi_price[37]["item"] = "pt850_k18_p";
$combi_price[37]["price"] = $pt850_k18_p = floor( ($pt850_9 + $k18_1) * 0.903);
$combi_price[38]["item"] = "pt850_k18_h";
$combi_price[38]["price"] = $pt850_k18_h = floor( ($pt850_5 + $k18_5) * 0.948);
$combi_price[39]["item"] = "pt850_k18_k";
$combi_price[39]["price"] = $pt850_k18_k = floor( ($pt850_1 + $k18_9) * 0.948);

$combi_price[40]["item"] = "pt950_k18_p";
$combi_price[40]["price"] = $pt950_k18_p = floor( ($pt950_9 + $k18_1) * 0.978);
$combi_price[41]["item"] = "pt950_k18_h";
$combi_price[41]["price"] = $pt950_k18_h = floor( ($pt950_5 + $k18_5) * 0.978);
$combi_price[42]["item"] = "pt950_k18_k";
$combi_price[42]["price"] = $pt950_k18_k = floor( ($pt950_1 + $k18_9) * 0.978);



$category = "combi";
for ($combi_i=34; $combi_i <= 42; $combi_i++) {
	$kir_combi_i = 1;
	$tb_gold_property_price = tb_gold_property_price::where("id",$combi_i)->find_one();
	if($tb_gold_property_price == false){
		$tb_gold_property_price = tb_gold_property_price::create();
		$tb_gold_property_price->save();
	}

	$tb_gold_property_price->price = $combi_price[$combi_i]["price"];
	$tb_gold_property_price->item = $combi_price[$combi_i]["item"];

	if($combi_price[$combi_i]["item"] == "p900_k18_p"){
		$tb_gold_property_price->rate = 0.928;
	}elseif($combi_price[$combi_i]["item"] == "pt900_k18_h"){
		$tb_gold_property_price->rate = 0.948;
	}elseif($combi_price[$combi_i]["item"] == "pt900_k18_k"){
		$tb_gold_property_price->rate = 0.978;
	}elseif($combi_price[$combi_i]["item"] == "pt850_k18_p"){
		$tb_gold_property_price->rate = 0.903;
	}elseif($combi_price[$combi_i]["item"] == "pt850_k18_h"){
		$tb_gold_property_price->rate = 0.948;
	}elseif($combi_price[$combi_i]["item"] == "pt850_k18_k"){
		$tb_gold_property_price->rate = 0.948;
	}else{
		$tb_gold_property_price->rate = 0.978;
	}



	$tb_gold_property_price->save();

	$kir_combi_i++;
}



/*

Au12/Pd20歯科材30g

*/
$au = floor((($tb_price->gold)+1)*0.985)+1;
$pd = floor((($tb_price->palladium)+1)*0.98)+1;
$ag = floor(($tb_price->silver)*0.935);
$au_pd_ag = floor((floor($au*0.12)+floor($pd*0.2)+floor($ag*0.45))-200);
$tb_gold_property_price = tb_gold_property_price::where("id",28)->find_one();
$tb_gold_property_price->price = floor($au_pd_ag*$tb_gold_property_price->rate);
$tb_gold_property_price->save();
