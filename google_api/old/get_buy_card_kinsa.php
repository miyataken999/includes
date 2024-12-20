<?php

ini_set("display_errors",0);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/users.php';

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
// if (php_sapi_name() != 'apache2handler') {
//   throw new Exception('不正なアクセスです。');
// }




$client = getClient();
$service = new Google_Service_Sheets($client);


//GSSシートID
//列違いがあった場合、コピーしてからでないとダメ。どうにかする。
$spreadsheetId = '1_lR0RCcpabgXhatH4wOj_ei0hOfl2I948Llj929uiHc';
// $spreadsheetId = '1GiYQPDZfhY8gq_uP4BTom6iWrgbwqpTuHs3nBJtX9Ao';


// $range = 'test!A1:O42';


// $response = $service->spreadsheets_values->get($spreadsheetId, $range);
// $values = $response->getValues();
// var_dump($values);

// exit();


$input_values = array(
    array(
        100
    ),
);
$input_body = new Google_Service_Sheets_ValueRange(array(
  'values' => $input_values
));
$input_params = array(
  'valueInputOption' => 'RAW'
);
$input_range = 'test!A1';
$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);


