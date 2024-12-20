<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'


//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/shouhin.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/_chigin_type.php';

// include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_unfinishedtest.php';
// include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinishedtest.php';
// include_once '/usr/local/src/google_api/idiorm_paris/app/shouhintest.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins.php';
// include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chiginstest.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/users.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/store_categories.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/store_sub_categories.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_brand.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/seq.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/market_product_performance_original.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_timeline.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/ycbm_bookings.php';



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
  // throw new Exception('不正なアクセスです。');
}



$now_time = date("Y-m-d H:i:s");

//create key
$client = getClient();
$service = new Google_Service_Sheets($client);

$url_id = $_POST["url_id"];
$sht_id = $_POST["sht_id"];
$sht_name = $_POST["sht_name"];
$sht_title = $_POST["sht_title"];
$sht_url = $_POST["sht_url"];

