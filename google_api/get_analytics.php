<?php
ini_set("display_errors",0);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/GSS_analytics.php';
//スプレッドシートAPIクライアント読み込み
include_once "/home/nishitani/vendor/autoload.php";
//スプレッドシート関数読み込み
include_once "/usr/local/src/google_api/function.php";




define('APPLICATION_NAME', 'Google Sheets API start');
define('CREDENTIALS_PATH', '~/.credentials/sheets.googleapis.com-php-quickstart.json');
define('CLIENT_SECRET_PATH', '/usr/local/src/google_api/client_secret.json');

define('SCOPES', implode(' ', array(
  Google_Service_Sheets::SPREADSHEETS_READONLY)
));


// if (php_sapi_name() != 'cgi-fcgi') {
if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}


$client = getClient();
$service = new Google_Service_Sheets($client);


//GSSシートID
$spreadsheetId = '1PWuub35rsjJAT28sRNPKGpNetwshP4MWmO5lMniiTv0';  //総合（キンカイ＋ブラカイ）アナリティクスデータ
//シート名
$sheet_name = '総合';
//サイト名
$site_name = 'all';
// $range = $sheet_name.'!'.'A1:HE5';
$range = $sheet_name.'!'.'A16:K500';


$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();




//まずゼロから入れる
// $columnList = range('A', 'D');
//2桁のアルファベット生成。今のGSS上、H●まで。
// foreach (range('A', 'H') as $columnItem) {
//     foreach (range('A', 'Z') as $columnItem_2) {
//         $columnList[] = $columnItem.$columnItem_2;
//     }
// }

// var_dump($columnList);
// exit();

if (count($values) == 0) {
  print "NooooData\n";
} else {
  $row_i = 0;
  foreach ($values as $row) {
    if($row[0] == ''){
      break;
    }
    //0=A,1=B,2=C
    // printf("%s, %s\n", $row[0], $row[1]);


    echo "row_i".$row_i."\n";
    //行ごとのデータの存在確認
    $GSS_analytics = "";
    $GSS_analytics = GSS_analytics::where('sheet_id',$spreadsheetId)->where('sheet_name',$sheet_name)->where('row',($row_i+1))->find_one();
    if($GSS_analytics == false){
      // echo "false\n";
      $GSS_analytics = GSS_analytics::create();
    }
    //行ごとの基本情報
    $GSS_analytics->sheet_id = $spreadsheetId;
    $GSS_analytics->sheet_name = $sheet_name;
    $GSS_analytics->row = $row_i+1;
    $GSS_analytics->site_name = $site_name;

    //カラムごとのデータを取得
    $column_ii = 0;
    $GSS_analytics->date = $row[$column_ii];  $column_ii++;
    $GSS_analytics->users = $row[$column_ii];  $column_ii++;
    $GSS_analytics->sessions = $row[$column_ii];  $column_ii++;
    $GSS_analytics->pageviews = $row[$column_ii];  $column_ii++;
    $GSS_analytics->goal1Completions = $row[$column_ii];  $column_ii++;
    $GSS_analytics->goal2Completions = $row[$column_ii];  $column_ii++;
    $GSS_analytics->goal3Completions = $row[$column_ii];  $column_ii++;
    $GSS_analytics->goal4Completions = $row[$column_ii];  $column_ii++;
    $GSS_analytics->goal5Completions = $row[$column_ii];  $column_ii++;
    $GSS_analytics->Goal_Completions = $row[$column_ii];  $column_ii++;
    $GSS_analytics->Goal_Conversion_Rate = $row[$column_ii];  $column_ii++;


    //最終セーブ
    $GSS_analytics->save();

    $row_i++;

  }

}






















