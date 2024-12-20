<?php
include "config.php";



echo $now;



$housyoku_array = array(12,17,18,19,50,20,51,21,22,23,65,66,67,68,69);

//仕入れ
$buy_up_array = array();
//仕入れ日付
//$date_buy_up
// 仕入れ金額総額
//$price_sum_buy_up

$date_array = array();

$Eoc_trader = Eoc_trader::where_like('date',$now)->limit(50)->find_many();
foreach($Eoc_trader as $trade_item){
  if(!in_array($trade_item->date, $date_array)){
    $date_array[] = $trade_item->date;
    $sumprice[$trade_item->date]["J"]["price"] = 0;
    $sumprice[$trade_item->date]["D"]["price"] = 0;
    $sumprice[$trade_item->date]["W"]["price"] = 0;
    $sumprice[$trade_item->date]["B"]["price"] = 0;
  }
  $Eoc_trade_id = $trade_item->id;
  $date_buy_up = $trade_item->date;
  $price_sum_buy_up ="";
  $shouhin = shouhin::where('Eoc_trader_id_for_buy',"$Eoc_trade_id")->find_many();
  if($shouhin == false){
    continue;
  }
  foreach($shouhin as $shouhin_item){
    $shouhin_price = $shouhin_item->price;
    if(($shouhin_item->sub_category_id1 == 415) OR ($shouhin_item->sub_category_id1 == 420)){
      // ルースのダイヤ、メレダイヤ
      $sumprice[$trade_item->date]["D"]["price"] += $shouhin_price;
    }elseif($shouhin_item->category_id == 10){
      // 時計
      $sumprice[$trade_item->date]["W"]["price"] += $shouhin_price;
    }elseif(in_array($shouhin_item->category_id, $housyoku_array)) {
      // 宝飾の大カテ
      $sumprice[$trade_item->date]["J"]["price"] += $shouhin_price;
    }else{
      // それ以外を服飾
      $sumprice[$trade_item->date]["B"]["price"] += $shouhin_price;
    }
  }

}
for ($i=0; $i < count($date_array); $i++) {
  $date = $date_array[$i];

  $Eoc_trader_report = Eoc_trader_report::where('date', $date)->find_one();
  if($Eoc_trader_report == false){
    $Eoc_trader_report = Eoc_trader_report::create();
    $Eoc_trader_report->date = $date;
    $Eoc_trader_report->save();
  }

  $Eoc_trader_report->price_sum_for_jewelry = $sumprice[$date]["J"]["price"];
  $Eoc_trader_report->price_sum_for_dia = $sumprice[$date]["D"]["price"];
  $Eoc_trader_report->price_sum_for_watch = $sumprice[$date]["W"]["price"];
  $Eoc_trader_report->price_sum_for_brand = $sumprice[$date]["B"]["price"];
  $Eoc_trader_report->save();

}




/*---------------------------------------
買取販売GSSへ書き込む
買取販売GSS初期確認
---------------------------------------*/


  //スプレッドシートAPIクライアント読み込み
  include_once "/home/apache/vendor/autoload.php";
  //スプレッドシート関数読み込み
  include_once "/usr/local/src/google_api/function.php";




  define('APPLICATION_NAME', 'Google Sheets API start');
  define('CREDENTIALS_PATH', '/home/apache/.credentials/sheets.googleapis.com-php-quickstart.json');
  define('CLIENT_SECRET_PATH', '/usr/local/src/google_api/client_secret.json');

  define('SCOPES', implode(' ', array(
    Google_Service_Sheets::SPREADSHEETS)
  ));


  // if (php_sapi_name() != 'cgi-fcgi') {
  // if (php_sapi_name() != 'cli') {
  if (php_sapi_name() != 'apache2handler') {
    throw new Exception('不正なアクセスです。');
  }

  $url_id = $_POST["url_id"];
  if($url_id == ''){
  	echo "urlがありません";
  	exit();
  }


  // 開発用のシート
  // $url_id = '1jSvFWN9_s4MDUPQ0PQi68KO7FDYswc9267ofAViqRZM';

  $client = getClient();
  $service = new Google_Service_Sheets($client);

  include_once __DIR__ . '/../write_gss_trader.php';

  $write_gss_trader = new write_gss_trader();
  $write_gss_trader->spreadsheet_service = $service;
  $write_gss_trader->count();
  


//   //GSSシートID
//   //列違いがあった場合、コピーしてからでないとダメ。どうにかする。
//   $spreadsheetId = $url_id;

//   //シート名
//   // if($sheet_name == ''){
//   //   $sheet_name = (date("Y")-2000).date("m");
//   // }
//   // $sheet_name = '1905';

//   // $range = $sheet_name.'!'.'A1:HE5';
//   $range = $sheet_name.'!'.'BD2:CX2';


//   $response = $service->spreadsheets_values->get($spreadsheetId, $range);
//   $values = $response->getValues();


  //0=A,1=B,2=C

//   if (count($values) == 0) {
//   	$return = "スプレッドシートのデータが取得できませんでした。\n";
//   	print $return;
//   }elseif(
//   	($values[0][0] != '金宅配')
//   	AND ($values[0][9] != '精錬・配送')
//   	AND ($values[0][11] != 'ダイヤ宅配')
//   	AND ($values[0][18] != '宝石宅配')
//   	AND ($values[0][25] != '時計宅配')
//   	AND ($values[0][32] != '服飾宅配')
//   	AND ($values[0][43] != '楽買')
//   	AND ($values[0][44] != '販促費')
//   	AND ($values[0][45] != '新物')
//   	AND ($values[0][46] != '宅処理数')
//   	){

//   	print "BD～CXの形式が違います。【金宅配】<br>";
//   	print "【精錬・配送】<br>";
//   	print "【ダイヤ宅配】<br>";
//   	print "【宝石宅配】<br>";
//   	print "【時計宅配】<br>";
//   	print "【服飾宅配】<br>";
//   	print "【楽買】<br>";
//   	print "【販促費】<br>";
//   	print "【新物】<br>";
//   	print "【宅処理数】<br>";
//   	print "に揃えてください。<br>";
//   	exit();
//   }
// end 買取販売GSS初期確認



	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取販売GSSへの書き込み
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■







		echo '<br><br>end repo';

// echo '<pre>';
// var_dump($input_values);
// echo '</pre>';
// exit;


	    @ob_flush();
	    @flush();
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 		$range = 'DK4:DK34';
	// 		$data = '';
	// 		$data = array();
	// 		$data[] = new Google_Service_Sheets_ValueRange(array(
	// 		  'range' => $sheet_name.'!'.$range,
	// 		  'values' => $input_values["J"]
	// 		));
	// 		// Additional ranges to update ...
	// 		$body = '';
	// 		$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
	// 		  'valueInputOption' => 'USER_ENTERED',
	// 		  'data' => $data
	// 		));
	// 		try {
	// 			$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
	// 		} catch (Exception $e) {
	// 			print "error 1 ".$e->getMessage();
	// 			// var_dump($body);
	// 		}
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//end 書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

    // //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 		$range = 'DM4:DM34';
	// 		$data = '';
	// 		$data = array();
	// 		$data[] = new Google_Service_Sheets_ValueRange(array(
	// 		  'range' => $sheet_name.'!'.$range,
	// 		  'values' => $input_values["D"]
	// 		));
	// 		// Additional ranges to update ...
	// 		$body = '';
	// 		$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
	// 		  'valueInputOption' => 'USER_ENTERED',
	// 		  'data' => $data
	// 		));
	// 		try {
	// 			$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
	// 		} catch (Exception $e) {
	// 			print "error 1 ".$e->getMessage();
	// 			// var_dump($body);
	// 		}
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//end 書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

    // //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 		$range = 'DO4:DO34';
	// 		$data = '';
	// 		$data = array();
	// 		$data[] = new Google_Service_Sheets_ValueRange(array(
	// 		  'range' => $sheet_name.'!'.$range,
	// 		  'values' => $input_values["W"]
	// 		));
	// 		// Additional ranges to update ...
	// 		$body = '';
	// 		$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
	// 		  'valueInputOption' => 'USER_ENTERED',
	// 		  'data' => $data
	// 		));
	// 		try {
	// 			$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
	// 		} catch (Exception $e) {
	// 			print "error 1 ".$e->getMessage();
	// 			// var_dump($body);
	// 		}
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//end 書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

    // //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 		$range = 'DQ4:DQ34';
	// 		$data = '';
	// 		$data = array();
	// 		$data[] = new Google_Service_Sheets_ValueRange(array(
	// 		  'range' => $sheet_name.'!'.$range,
	// 		  'values' => $input_values["B"]
	// 		));
	// 		// Additional ranges to update ...
	// 		$body = '';
	// 		$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
	// 		  'valueInputOption' => 'USER_ENTERED',
	// 		  'data' => $data
	// 		));
	// 		try {
	// 			$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
	// 		} catch (Exception $e) {
	// 			print "error 1 ".$e->getMessage();
	// 			// var_dump($body);
	// 		}
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	// 	//end 書込み
	// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

















	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取販売GSSへの書き込み END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■






		echo '<br><br>end GSS書込みまで完了しました！';
	    @ob_flush();
	    @flush();
