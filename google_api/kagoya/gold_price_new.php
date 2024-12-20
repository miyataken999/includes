<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'

$url_id = $_POST["url_id"];
$sht_id = $_POST["sht_id"];
// sht_name
// sht_title
// sht_url



echo $url_id;
// echo "<br><br>";
// echo $sht_id;
// exit();
/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	金価格取得プログラム

■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
ini_set("display_errors",1);
include_once $dirname.'/config.php';




/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
本日の価格取得
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
$property_price = array();


$cls_tb_price = tb_price::order_by_desc("resist_data_type_date")->limit(2)->find_many();

$resist_date = array();
$insert_date = array();
$gold = array();
$platinum = array();
$silver = array();
$palladium = array();

$kakaku_array = array();
foreach ($cls_tb_price as $item) {
	$kakaku = '';
	$kakaku = array();

	$insert_date[] = array($item->resist_data_type_date);
	$gold[] = $item->gold;
	$platinum[] = $item->platinum;
	$silver[] = $item->silver;
	$palladium[] = $item->palladium;
}
$kakaku_array[] = $gold;
$kakaku_array[] = $platinum;
$kakaku_array[] = $silver;
$kakaku_array[] = $palladium;

// var_dump($resist_date);
// echo "<br>";
// var_dump($kakaku_array);

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
END 本日の価格取得
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/

//GSSシートID
// 開発用2のシートID固定
// 最終、原本の「国内公表価格」に
$spreadsheetId = $url_id;
$sheet_name = "価格マスタ";

//0=A,1=B,2=C

$last_date = date("Y年m月d日 H時i分s秒");
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		//書き込み
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$input_values = array(
		    array(
		    	$last_date
		    ),
		);
		$input_body = new Google_Service_Sheets_ValueRange(array(
		  'values' => $insert_date
		));
		$input_params = array(
		  'valueInputOption' => 'RAW'
		);
		$input_range = $sheet_name.'!B1:B2';
		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		$input_values = $kakaku_array;
		$input_body = new Google_Service_Sheets_ValueRange(array(
		  'values' => $input_values
		));
		$input_params = array(
			'valueInputOption' => 'USER_ENTERED',
	    'responseValueRenderOption' => 'FORMATTED_VALUE',
		);
		$input_range = $sheet_name.'!B5:C8';
		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		//書き込み END
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// exit();

$cls_tb_gold = tb_gold_rate_history::order_by_desc("date")->limit(2)->find_many();
$cls_tb_platinum = tb_platinum_rate_history::order_by_desc("date")->limit(2)->find_many();
$cls_tb_silver = tb_silver_rate_history::order_by_desc("date")->limit(2)->find_many();
$cls_tb_palladium = tb_palladium_rate_history::order_by_desc("date")->limit(2)->find_many();
$cls_tb_combi = tb_combi_rate_history::order_by_desc("date")->limit(2)->find_many();

$gold_rate_category = array("ingot_500over","ingot_100over","k24","k22","k21","k20","k18","k14","k10","k9","k23","k17","k12","k8","k5");
$platinum_rate_category = array("ingot_500over","ingot_100over","pt1000","pt950","pt900","pt850","pt_pm");
$silver_rate_category = array("sv1000","sv925","ingot","sv900");
$palladium_rate_category = array("ingot","au12pd20","ingot_100over","pd1000","pd950","pd900","pd500");
$combi_rate_category = array("p900_k18_p","p900_k18_h","p900_k18_k","p850_k18_p","p850_k18_h","p850_k18_k","p950_k18_p","p950_k18_h","p950_k18_k");

//
$rate_gold = array();
$rate_platinum = array();
$rate_silver = array();
$rate_palladium = array();
$rate_combi = array();
//
// $price_name = "gold_item";
$count = 0;
foreach ($cls_tb_gold as $item) {
	foreach($gold_rate_category as $gold_cate){
		if($count == 0){
			$rate_gold[0][] = array($item->$gold_cate);
		}else{
			$rate_gold[1][] = array($item->$gold_cate);
		}

	}
	$count++;
}
$count = 0;
foreach ($cls_tb_platinum as $item) {
	foreach($platinum_rate_category as $platinum_cate){
		if($count == 0){
			$rate_platinum[0][] = array($item->$platinum_cate);
		}else{
			$rate_platinum[1][] = array($item->$platinum_cate);
		}
	}
	$count++;
}

// var_dump($rate_platinum);
$count = 0;
foreach ($cls_tb_silver as $item) {
	foreach($silver_rate_category as $silver_cate){
		if($count == 0){
			$rate_silver[0][] = array($item->$silver_cate);
		}else{
			$rate_silver[1][] = array($item->$silver_cate);
		}
	}
	$count++;
}
$count = 0;
foreach ($cls_tb_palladium as $item) {
	foreach($palladium_rate_category as $palladium_cate){
		if($count == 0){
			$rate_palladium[0][] = array($item->$palladium_cate);
		}else{
			$rate_palladium[1][] = array($item->$palladium_cate);
		}
	}
	$count++;
}
$count = 0;
foreach ($cls_tb_combi as $item) {
	foreach($combi_rate_category as $combi_cate){
		if($count == 0){
			$rate_combi[0][] = array($item->$combi_cate);
		}else{
			$rate_combi[1][] = array($item->$combi_cate);
		}
	}
	$count++;
}

// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 日付 書き込み
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		$input_values = array(
// 		    array(
// 		    	$last_date
// 		    ),
// 		);
// 		$input_body = new Google_Service_Sheets_ValueRange(array(
// 		  'values' => $input_values
// 		));
// 		$input_params = array(
// 		  'valueInputOption' => 'RAW'
// 		);
// 		$input_range = $sheet_name.'!B1';
// 		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 日付 書き込み END
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//
//
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 金 書き込み
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_gold[0];

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!E5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 金 書き込み END
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 金 書き込み
// 		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_gold[1];

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!O5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// 金 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// プラチナ 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_platinum[0];

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!G5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// プラチナ 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// プラチナ 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_platinum[1];

			$input_body = new Google_Service_Sheets_ValueRange(array(
				'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!Q5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// プラチナ 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// シルバー 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_silver[0];

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!I5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// シルバー 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// シルバー 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_silver[1];

			$input_body = new Google_Service_Sheets_ValueRange(array(
				'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!S5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// シルバー 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// パラジウム 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_palladium[0];

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!K5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// パラジウム 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// パラジウム 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_palladium[1];

			$input_body = new Google_Service_Sheets_ValueRange(array(
				'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!U5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// パラジウム 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// コンビ 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_combi[0];

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!M5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// コンビ 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// コンビ 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_combi[1];

			$input_body = new Google_Service_Sheets_ValueRange(array(
				'values' => $input_values
			));
			$input_params = array(
				'valueInputOption' => 'USER_ENTERED',
				'responseValueRenderOption' => 'FORMATTED_VALUE',
			);
			$input_range = $sheet_name.'!W5';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// コンビ 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//
//
//
//
include_once '/usr/local/src/google_api/kagoya_to_sakura/gold_to_sakura.php';

// //相場下落更新処理
// include '/usr/local/src/alert/update_price_auto.php';

// include '/usr/local/src/google_api/cw_market_change/index.php';

// $file_get_contents = file_get_contents("https://rifa.life/lounge_API/evaeva/alert/update_price_auto.php?update_type=lower&token=gk;ljowqksg.na;e.rgk2389p5j3fga./;932ap;ijf:@er09yu@hsr8eyht;fp23rjh:apof3kq4th");

// echo $file_get_contents;


?>
