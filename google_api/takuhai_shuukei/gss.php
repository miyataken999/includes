<?php
include "config.php";

/*---------------------------------------

買取販売GSSへ書き込む

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

include_once __DIR__ . '/../write_gss_takuhai.php';

$write_gss_takuhai = new write_gss_takuhai();
$write_gss_takuhai->spreadsheet_service = $service;
$write_gss_takuhai->count();



// //GSSシートID
// //列違いがあった場合、コピーしてからでないとダメ。どうにかする。
// $spreadsheetId = $url_id;

// //シート名
// $sheet_name = date("ym", strtotime($now_time));
// // $sheet_name = '1905';

// // $range = $sheet_name.'!'.'A1:HE5';
// $range = $sheet_name.'!'.'BD2:CX2';


// $response = $service->spreadsheets_values->get($spreadsheetId, $range);
// $values = $response->getValues();


//0=A,1=B,2=C

// if (count($values) == 0) {
// 	$return = "スプレッドシートのデータが取得できませんでした。\n";
// 	print $return;
// }elseif(
// 	($values[0][0] != '金宅配')
// 	AND ($values[0][9] != '精錬・配送')
// 	AND ($values[0][11] != 'ダイヤ宅配')
// 	AND ($values[0][18] != '宝石宅配')
// 	AND ($values[0][25] != '時計宅配')
// 	AND ($values[0][32] != '服飾宅配')
// 	AND ($values[0][43] != '楽買')
// 	AND ($values[0][44] != '販促費')
// 	AND ($values[0][45] != '新物')
// 	AND ($values[0][46] != '宅処理数')
// 	){

// 	print "BD～CXの形式が違います。【金宅配】<br>";
// 	print "【精錬・配送】<br>";
// 	print "【ダイヤ宅配】<br>";
// 	print "【宝石宅配】<br>";
// 	print "【時計宅配】<br>";
// 	print "【服飾宅配】<br>";
// 	print "【楽買】<br>";
// 	print "【販促費】<br>";
// 	print "【新物】<br>";
// 	print "【宅処理数】<br>";
// 	print "に揃えてください。<br>";
// 	exit();
// }else{
// 	print "<br><br><br><br><br><br>";
// 	print "ok";

// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 	//買取販売GSSへの書き込み
// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		

// 		echo '<br><br>end repo';
// 	    @ob_flush();
// 	    @flush();




// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 	$range = 'BF4:BL34';
// 		// 	$data = '';
// 		// 	$data = array();
// 		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
// 		// 	  'range' => $sheet_name.'!'.$range,
// 		// 	  'values' => $values["kin"]
// 		// 	));
// 		// 	// Additional ranges to update ...
// 		// 	$body = '';
// 		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
// 		// 	  'valueInputOption' => 'RAW',
// 		// 	  'data' => $data
// 		// 	));
// 		// 	try {
// 		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
// 		// 	} catch (Exception $e) {
// 		// 		print "error 1 ".$e->getMessage();
// 		// 		// var_dump($body);
// 		// 	}
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //end 書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 	$range = 'BQ4:BU34';
// 		// 	$data = '';
// 		// 	$data = array();
// 		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
// 		// 	  'range' => $sheet_name.'!'.$range,
// 		// 	  'values' => $values["dia"]
// 		// 	));
// 		// 	// Additional ranges to update ...
// 		// 	$body = '';
// 		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
// 		// 	  'valueInputOption' => 'RAW',
// 		// 	  'data' => $data
// 		// 	));
// 		// 	try {
// 		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
// 		// 	} catch (Exception $e) {
// 		// 		print "error 1 ".$e->getMessage();
// 		// 		// var_dump($body);
// 		// 	}
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //end 書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 	$range = 'BX4:CB34';
// 		// 	$data = '';
// 		// 	$data = array();
// 		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
// 		// 	  'range' => $sheet_name.'!'.$range,
// 		// 	  'values' => $values["jewel"]
// 		// 	));
// 		// 	// Additional ranges to update ...
// 		// 	$body = '';
// 		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
// 		// 	  'valueInputOption' => 'RAW',
// 		// 	  'data' => $data
// 		// 	));
// 		// 	try {
// 		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
// 		// 	} catch (Exception $e) {
// 		// 		print "error 1 ".$e->getMessage();
// 		// 		// var_dump($body);
// 		// 	}
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //end 書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 	$range = 'CE4:CI34';
// 		// 	$data = '';
// 		// 	$data = array();
// 		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
// 		// 	  'range' => $sheet_name.'!'.$range,
// 		// 	  'values' => $values["watch"]
// 		// 	));
// 		// 	// Additional ranges to update ...
// 		// 	$body = '';
// 		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
// 		// 	  'valueInputOption' => 'RAW',
// 		// 	  'data' => $data
// 		// 	));
// 		// 	try {
// 		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
// 		// 	} catch (Exception $e) {
// 		// 		print "error 1 ".$e->getMessage();
// 		// 		// var_dump($body);
// 		// 	}
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //end 書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 	$range = 'CN4:CT34';
// 		// 	$data = '';
// 		// 	$data = array();
// 		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
// 		// 	  'range' => $sheet_name.'!'.$range,
// 		// 	  'values' => $values["brand"]
// 		// 	));
// 		// 	// Additional ranges to update ...
// 		// 	$body = '';
// 		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
// 		// 	  'valueInputOption' => 'RAW',
// 		// 	  'data' => $data
// 		// 	));
// 		// 	try {
// 		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
// 		// 	} catch (Exception $e) {
// 		// 		print "error 1 ".$e->getMessage();
// 		// 		// var_dump($body);
// 		// 	}
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //end 書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// 	$range = 'CV4:CV34';
// 		// 	$data = '';
// 		// 	$data = array();
// 		// 	$data[] = new Google_Service_Sheets_ValueRange(array(
// 		// 	  'range' => $sheet_name.'!'.$range,
// 		// 	  'values' => $values["hansoku"]
// 		// 	));
// 		// 	// Additional ranges to update ...
// 		// 	$body = '';
// 		// 	$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
// 		// 	  'valueInputOption' => 'RAW',
// 		// 	  'data' => $data
// 		// 	));
// 		// 	try {
// 		// 		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
// 		// 	} catch (Exception $e) {
// 		// 		print "error 1 ".$e->getMessage();
// 		// 		// var_dump($body);
// 		// 	}
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 		// //end 書込み
// 		// //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 	//買取販売GSSへの書き込み END
// 	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

// }//end if GSS読み込み





		echo '<br><br>end GSS書込みまで完了しました！';
	    @ob_flush();
	    @flush();










