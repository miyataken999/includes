<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'


//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_gold.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_platinum.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_silver.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_palladium.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_combi.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_price.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_gold_rate_history.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_platinum_rate_history.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_silver_rate_history.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_palladium_rate_history.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya/tb_combi_rate_history.php';








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



// if ((php_sapi_name() != 'apache2handler') AND ($sshkey != 'ajviaofeioiwuohfehwehehfwoeheohfohwfowh')) {
//   throw new Exception('不正なアクセスです。');
// }



$now_time = date("Y-m-d H:i:s");

$client = getClient();
$service = new Google_Service_Sheets($client);

$url_id = $_POST["url_id"];
$sht_id = $_POST["sht_id"];
$sht_name = $_POST["sht_name"];
$sht_title = $_POST["sht_title"];
$sht_url = $_POST["sht_url"];




