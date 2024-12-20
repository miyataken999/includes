<?php
/*---------------------------------------
店頭データ集計、
買取販売GSS書き込みプログラム
---------------------------------------*/
ini_set("display_errors",1);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/vw_Eoc_unfinished_by_day_greatest.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/lounge_report__buy.php';

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
$client = getClient();
$service = new Google_Service_Sheets($client);
//GSSシートID
//列違いがあった場合、コピーしてからでないとダメ。どうにかする。
$spreadsheetId = $url_id;
//シート名
$sheet_name = (date("Y")-2000).date("m");
// $sheet_name = (date("Y")-2000).'03';

// $range = $sheet_name.'!'.'A1:HE5';
$range = $sheet_name.'!'.'AF2:AW2';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

include_once 'write_gss_tentou.php';

$write_gss_tentou = new write_gss_tentou();
$write_gss_tentou->spreadsheet_service = $service;
$write_gss_tentou->count();