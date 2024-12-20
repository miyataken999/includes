<?php
include "config.php";










echo $now;



// 完了変更日
// kanryou_henbi
// 伝票種別
// slip_type
// 請求金額
// billing_amount
// 精錬上がり
// seiren_price


for ($i=1; $i <= $now_month_last; $i++) {
  $now_day = sprintf('%02d', $i);
  $wheredate = "{$now_month}-{$now_day}";
  $gold_check = gold_check::select("id")
                ->select("slip_type")
                ->where_like('kanryou_henbi',$wheredate.'%')
                ->find_many();

  $gold_check_id_array["kaitori"] = array();
  $gold_check_id_array["seiren"] = array();
  // 対応するidを取得
  foreach ($gold_check as $item) {
    if($item->slip_type == 1){
      $gold_check_id_array["kaitori"][] = $item->id;
    }elseif($item->slip_type == 2){
      $gold_check_id_array["seiren"][] = $item->id;
    }
  }//end foreach

  //なきゃcontinue
  if((count($gold_check_id_array["kaitori"]) == 0) && (count($gold_check_id_array["seiren"]) == 0)){
    continue;
  }

echo "<pre>";
// var_dump($gold_check_id_array);
echo "</pre>";


  //taxの取得
  $mst_tax = mst_tax::whereLte('start_time', $wheredate)->order_by_desc('start_time')->limit(1)->find_one();
  if($mst_tax->rate != ''){
    $rate = $mst_tax->rate;
  }else{
    $rate = 0.8;
  }

  //集計
  $sum_price["kaitori"] = 0;
  $sum_price["seiren"] = 0;
  $sum_dia_price["kaitori"] = 0;
  $sum_dia_price["seiren"] = 0;
  foreach ($gold_check_id_array as $key => $value) {
    foreach ($value as $gold_check_id) {
      $gold_check_item = gold_check_item::where("gold_check_id", $gold_check_id)->find_many();
      $gold_check_parent = gold_check::where("id", $gold_check_id)->find_one();
      foreach ($gold_check_item as $item) {
        if($item->name == 45){
          $sum_dia_price[$key] += $item->price;
        }else{
          if($key != "seiren"){
            $sum_price[$key] += $item->price;
          }
        }
      }//end foreach gold check item
      if($key == "seiren"){
        if($gold_check_parent->slip_type == 2){
          $sum_price["seiren"] += $gold_check_parent->billing_amount;
        }
      }
    }//end foreach gold check id find many
    if($key == "kaitori"){
      $slip_type = 1;
    }else{
      $slip_type = 2;
    }


    $gold_check_report = gold_check_report::where('date', $wheredate)
                        ->where('slip_type', $slip_type)
                        ->find_one();
    if($gold_check_report == false){
      $gold_check_report = gold_check_report::create();
      $gold_check_report->date = $wheredate;
      $gold_check_report->slip_type = $slip_type;
    }// end if false


    if($slip_type == 1){
      $gold_check_report->billing_amount = floor($sum_price[$key] / $rate);
      $gold_check_report->dia_price = floor($sum_dia_price[$key] / $rate);
    }elseif($slip_type == 2){
      $gold_check_report->seiren_price = floor($sum_price[$key] / $rate);
      $gold_check_report->dia_price = floor($sum_dia_price[$key] / $rate);
    }
    try {
      $gold_check_report->save();
    } catch (\Exception $e) {
      echo $e->getMessage();
      exit;
    }
  }//end foreach id array
}//end for now month last





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

  include_once __DIR__ . '/../write_gss_jigane_haraidashi.php';

  $write_gss_jigane_haraidashi = new write_gss_jigane_haraidashi();
  $write_gss_jigane_haraidashi->spreadsheet_service = $service;
  $write_gss_jigane_haraidashi->count();
  


//   //GSSシートID
//   //列違いがあった場合、コピーしてからでないとダメ。どうにかする。
//   $spreadsheetId = $url_id;

//   //シート名
//   $sheet_name = date("ym", strtotime($now_time));
//   // $sheet_name = '1905';

//   // $range = $sheet_name.'!'.'A1:HE5';
//   $range = $sheet_name.'!'.'BD2:CX2';


//   $response = $service->spreadsheets_values->get($spreadsheetId, $range);
//   $values = $response->getValues();


//   //0=A,1=B,2=C

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
// // end 買取販売GSS初期確認



	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取販売GSSへの書き込み
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■



// echo '<pre>';
// var_dump($input_values);
// echo '</pre>';
// exit;


	    @ob_flush();
	    @flush();
		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// //書込み
		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// 	$range = 'DW4:DX34';
		// 	$data = '';
		// 	$data = array();
		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
		// 	  'range' => $sheet_name.'!'.$range,
		// 	  'values' => $input_values
		// 	));
		// 	// Additional ranges to update ...
		// 	$body = '';
		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
		// 	  'valueInputOption' => 'USER_ENTERED',
		// 	  'data' => $data
		// 	));
		// 	try {
		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
		// 	} catch (Exception $e) {
		// 		print "error 1 ".$e->getMessage();
		// 		// var_dump($body);
		// 	}
		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// //end 書込み
		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

    // //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// //書込み
		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// 	$range = 'DZ4:DZ34';
		// 	$data = '';
		// 	$data = array();
		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
		// 	  'range' => $sheet_name.'!'.$range,
		// 	  'values' => $input_values_dia
		// 	));
		// 	// Additional ranges to update ...
		// 	$body = '';
		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
		// 	  'valueInputOption' => 'USER_ENTERED',
		// 	  'data' => $data
		// 	));
		// 	try {
		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
		// 	} catch (Exception $e) {
		// 		print "error 1 ".$e->getMessage();
		// 		// var_dump($body);
		// 	}
		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		// //end 書込み
		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取販売GSSへの書き込み END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■






		echo '<br><br>end GSS書込みまで完了しました！';
	    @ob_flush();
	    @flush();
