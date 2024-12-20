<?php
/*---------------------------------------

買取販売GSS
https://docs.google.com/spreadsheets/d/1tqki3x3r6xnhYl37XWT8SLXRTTLHlys6h-0w4gUH4JQ/edit#gid=1991358933
データ取得プログラム

---------------------------------------*/


ini_set("display_errors",0);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/GSS_buyAndSell.php';
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
//列違いがあった場合、コピーしてからでないとダメ。どうにかする。
$spreadsheetId_for_db = '1pw0cYyDjy7BrLzT-FnKgDQegpC9yu1lJua9JR8RTnz4';//コピーGSS
$spreadsheetId = '1tqki3x3r6xnhYl37XWT8SLXRTTLHlys6h-0w4gUH4JQ';//本番GSS

//シート名


$year = date("Y");
$short_year = $year-2000;
// $month = date("m",strtotime("-1 month"));
$month = date("m");

$sheet_name = "{$short_year}{$month}";
$year_month = "{$year}-{$month}-";


// $sheet_name = '1809';
// $year_month = '2018-09-';
// $range = $sheet_name.'!'.'A1:HE5';
$range = $sheet_name.'!'.'A1:HE34';


try {
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
} catch (Exception $e) {
  /*
  GSSデータの取得に失敗したら、CWを送信して強制終了する。
  */
  $cw_body = "[info][title]▼買取販売GSSデータ取得エラー▼[/title]";
  $cw_body .= "spreadsheetId　{$spreadsheetId}\n";
  $cw_body .= "range　{$range}";
  $cw_body .= "[code]".$e->getMessage()."[/code]";
  $cw_body .= "[/info]";

  $to_ids = '2107601';//西谷
  $room = '59878168';//開発チャット
  $token = 'bot';//botから。
  cw_task($cw_body,$to_ids,$room,$token);
  // cw_task($cw_body);//テスト用
  exit();
}
$values = $response->getValues();


//まずゼロから入れる
$columnList = range('A', 'Z');
//2桁のアルファベット生成。今のGSS上、H●まで。
foreach (range('A', 'H') as $columnItem) {
    foreach (range('A', 'Z') as $columnItem_2) {
        $columnList[] = $columnItem.$columnItem_2;
    }
}

// var_dump($columnList);
// exit();

if (count($values) == 0) {
  print "NooooData\n";
} else {


$row_i = 0;
  foreach ($values as $row) {
    //0=A,1=B,2=C
    // printf("%s, %s\n", $row[0], $row[1]);
    if($row_i < 2){
      $row_i++;
      continue;
    }


    if($row_i == 2){
      for ($iii=0; $iii < count($columnList); $iii++) {
        // include '/usr/local/src/google_api/column.php';

        // $columnname[$iii] = str_replace("\n","",$row[$iii]);
        // $columnname[$iii] = str_replace("\r","",$columnname[$iii]);

        $columnname[$iii] = $columnList[$iii];

        // $GSS_buyAndSell = "";
        // $GSS_buyAndSell = GSS_buyAndSell::create();
        // $GSS_buyAndSell->column = $columnname[$iii];
        // $GSS_buyAndSell->row = $row_i+1;
        // $GSS_buyAndSell->data = $row[$iii];
        // $GSS_buyAndSell->save();
      }
    }else{
      echo "row_i".$row_i."\n";
      //行ごとのデータの存在確認
      $GSS_buyAndSell = "";
      $GSS_buyAndSell = GSS_buyAndSell::where('sheet_id',$spreadsheetId_for_db)->where('sheet_name',$sheet_name)->where('row',($row_i+1))->find_one();
      if($GSS_buyAndSell == false){
        // echo "false\n";
        $GSS_buyAndSell = GSS_buyAndSell::create();
      }
      //行ごとの基本情報
      $GSS_buyAndSell->sheet_id = $spreadsheetId_for_db;
      $GSS_buyAndSell->sheet_name = $sheet_name;
      $GSS_buyAndSell->row = $row_i+1;
      $date = $year_month.$row[0];
      $GSS_buyAndSell->date = $date;

      //カラムごとのデータを取得
      for ($iii=0; $iii < count($columnList); $iii++) {
        if(isset($row[$iii])){
          $data = str_replace('¥','',$row[$iii]);
          if(strstr($data,'%')){
            $data = str_replace('%','',$data);
            $data = $data/100;
          }
          $data = str_replace(',','',$data);
          $columnname_data = $columnname[$iii];
          $GSS_buyAndSell->$columnname_data = $data;
        }
      }
      //最終セーブ
      $GSS_buyAndSell->save();
    }
    $row_i++;

  }



















}






















