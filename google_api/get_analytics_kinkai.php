<?php
ini_set("display_errors",0);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/GSS_analytics_kinkai.php';
//スプレッドシートAPIクライアント読み込み
include_once "/home/nishitani/vendor/autoload.php";




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

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

$client = getClient();
$service = new Google_Service_Sheets($client);


//GSSシートID
$spreadsheetId = '1fM6iFOvQGAlbLq6cmMVeONUuzBSIqJH3YPvdY9QxJSA';
//シート名
$sheet_name = 'ダイヤ関連ページを閲覧したPCユーザー';
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
    $GSS_analytics_kinkai = "";
    $GSS_analytics_kinkai = GSS_analytics_kinkai::where('sheet_id',$spreadsheetId)->where('sheet_name',$sheet_name)->where('row',($row_i+1))->find_one();
    if($GSS_analytics_kinkai == false){
      // echo "false\n";
      $GSS_analytics_kinkai = GSS_analytics_kinkai::create();
    }
    //行ごとの基本情報
    $GSS_analytics_kinkai->sheet_id = $spreadsheetId;
    $GSS_analytics_kinkai->sheet_name = $sheet_name;
    $GSS_analytics_kinkai->row = $row_i+1;

    //カラムごとのデータを取得
    $GSS_analytics_kinkai->date = $row[0];
    $GSS_analytics_kinkai->users = $row[1];
    $GSS_analytics_kinkai->sessions = $row[2];
    $GSS_analytics_kinkai->goal1Completions = $row[3];
    $GSS_analytics_kinkai->goal2Completions = $row[4];
    $GSS_analytics_kinkai->goal3Completions = $row[5];
    $GSS_analytics_kinkai->goal4Completions = $row[6];
    $GSS_analytics_kinkai->goal5Completions = $row[7];
    $GSS_analytics_kinkai->Goal_Completions = $row[8];
    $GSS_analytics_kinkai->Goal_Conversion_Rate = $row[9];
    $GSS_analytics_kinkai->pageviews = $row[10];

    //最終セーブ
    $GSS_analytics_kinkai->save();

    $row_i++;

  }

}






















