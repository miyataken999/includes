<?php

$jikkou = 'force';

include_once "/var/www/html/lounge_API/wkhtml/drive_file_up.php";

$client = getClient();
$service = new Google_Service_Drive($client);
$sheetservice = new Google_Service_Sheets($client);

$drive_file_up = new drive_file_up;
$drive_file_up->SCOPES = SCOPES;
$drive_file_up->content_name = "public_folder";
$drive_file_up->upload_file_name = '';
$drive_file_up->content = $content;
$drive_file_up->service = $service;


$room = '150036002';
$bot = "ed472827b8dda0ba81ba586c8b0e4bd9";
$nishitani = $bot;
$tamiya = $bot;
$token = $nishitani;

try {
  $drive_file_up->folderMake('', 'on');//dayフォルダのフラグon

  $msg = 'フォルダ作成成功';
  $c = system('curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$msg.'" "https://api.chatwork.com/v2/rooms/'.$room.'/messages" -O',$c);

} catch (Exception $e) {
  $msg = 'フォルダー作成に失敗しました。';
  $msg .= "\n".$e->getMessage();
  $c = system('curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$msg.'" "https://api.chatwork.com/v2/rooms/'.$room.'/messages" -O',$c);
}
