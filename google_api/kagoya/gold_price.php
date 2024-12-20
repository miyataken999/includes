<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	金価格取得プログラム

■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
ini_set("display_errors",1);
include_once $dirname.'/config.php';




/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
本日の価格取得
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
$property_price = array();


$cls_tb_price = tb_price::order_by_desc("resist_data_type_date")->find_many();

$gold = array();
$platinum = array();
$silver = array();
$palladium = array();



$kakaku_array = array();
foreach ($cls_tb_price as $item) {
	$kakaku = '';
	$kakaku = array();

	$kakaku[] = $item->resist_data_type_date;
	$kakaku[] = $item->gold;
	$kakaku[] = $item->platinum;
	$kakaku[] = $item->silver;
	$kakaku[] = $item->palladium;

	$kakaku_array[] = $kakaku;
}

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
END 本日の価格取得
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/

//GSSシートID
// 開発用2のシートID固定
// 最終、原本の「国内公表価格」に
$spreadsheetId = "1cvtF1f0xp0ujFn2UQvrurq4x648KlVOCLgZWS9LcYic";
$sheet_name = "価格";

//0=A,1=B,2=C

$last_date = date("Y年m月d日 H時i分s秒");
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$input_values = array(
		    array(
		    	$last_date
		    ),
		);
		$input_body = new Google_Service_Sheets_ValueRange(array(
		  'values' => $input_values
		));
		$input_params = array(
		  'valueInputOption' => 'RAW'
		);
		$input_range = $sheet_name.'!B1';
		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);


		$input_values = $kakaku_array;
		$input_body = new Google_Service_Sheets_ValueRange(array(
		  'values' => $input_values
		));
		$input_params = array(
		  'valueInputOption' => 'RAW'
		);
		$input_range = $sheet_name.'!A3:E10000';
		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

















$cls_tb_gold = tb_gold_rate_history::order_by_desc("date")->find_many();
$cls_tb_platinum = tb_platinum_rate_history::order_by_desc("date")->find_many();
$cls_tb_silver = tb_silver_rate_history::order_by_desc("date")->find_many();
$cls_tb_palladium = tb_palladium_rate_history::order_by_desc("date")->find_many();
$cls_tb_combi = tb_combi_rate_history::order_by_desc("date")->find_many();



$rate_gold = array();
$rate_platinum = array();
$rate_silver = array();
$rate_palladium = array();
$rate_combi = array();

$price_name = "gold_item";
foreach ($cls_tb_gold as $item) {
	$rate_gold[] = array(
			date("Y-m-d",strtotime($item->date)),
			$item->ingot_500over,
			$item->ingot_100over,
			$item->k24,
			$item->k23,
			$item->k22,
			$item->k21,
			$item->k20,
			$item->k18,
			$item->k17,
			$item->k14,
			$item->k12,
			$item->k10,
			$item->k9,
			$item->k8,
			$item->k5,
		);
}
$price_name = "platinum_item";
foreach ($cls_tb_platinum as $item) {
	$rate_platinum[] = array(
			$item->ingot_500over,
			$item->ingot_100over,
			$item->pt1000,
			$item->pt950,
			$item->pt900,
			$item->pt850,
			$item->pt_pm,
		);


}
$price_name = "silver_item";
foreach ($cls_tb_silver as $item) {
	$rate_silver[] = array(
			$item->ingot,
			$item->sv1000,
			$item->sv925,
			$item->sv900,
		);
}
$price_name = "palladium_item";
foreach ($cls_tb_palladium as $item) {
	$rate_palladium[] = array(
			$item->ingot,
			$item->au12pd20,
			$item->ingot_100over,
			$item->pd1000,
			$item->pd950,
			$item->pd900,
			$item->pd500,
		);
}
$price_name = "combi_item";
foreach ($cls_tb_combi as $item) {
	$rate_combi[] = array(
			$item->p900_k18_p,
			$item->p900_k18_h,
			$item->p900_k18_k,
			$item->p850_k18_p,
			$item->p850_k18_h,
			$item->p850_k18_k,
		);
}


$sheet_name = "料率";


		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// 日付 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$input_values = array(
		    array(
		    	$last_date
		    ),
		);
		$input_body = new Google_Service_Sheets_ValueRange(array(
		  'values' => $input_values
		));
		$input_params = array(
		  'valueInputOption' => 'RAW'
		);
		$input_range = $sheet_name.'!B1';
		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// 日付 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// 金 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_gold;

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
			  'valueInputOption' => 'RAW'
			);
			$input_range = $sheet_name.'!A4:P10000';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// 金 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// プラチナ 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_platinum;

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
			  'valueInputOption' => 'RAW'
			);
			$input_range = $sheet_name.'!Q4:W10000';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// プラチナ 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// シルバー 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_silver;

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
			  'valueInputOption' => 'RAW'
			);
			$input_range = $sheet_name.'!X4:AA10000';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// シルバー 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// パラジウム 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_palladium;

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
			  'valueInputOption' => 'RAW'
			);
			$input_range = $sheet_name.'!AB4:AH10000';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// パラジウム 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// コンビ 書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = $rate_combi;

			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
			  'valueInputOption' => 'RAW'
			);
			$input_range = $sheet_name.'!AI4:AN10000';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);

		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// コンビ 書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■




include_once '/usr/local/src/google_api/kagoya_to_sakura/gold_to_sakura.php';

//相場下落更新処理
include '/usr/local/src/alert/update_price_auto.php';

include '/usr/local/src/google_api/cw_market_change/index.php';

?>



