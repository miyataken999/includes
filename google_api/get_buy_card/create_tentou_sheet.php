<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	店頭買取カード作成時プログラム

■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
ini_set("display_errors",0);

//個別設定値

include_once $dirname.'/config.php';

$sht_url = $_POST["sht_url"];
$date = (date("Y")-2000) . date("md");
$cw_body = "[info][title]:*{$date}:*[/title]{$sht_url}[/info]";


$sendPostData = array(
    "subject" => "店頭買取カード作成 {$date}",
    "text" => $sht_url,
);
$sendPostData = http_build_query($sendPostData, "", "&");
$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
$sendPostContext = stream_context_create($sendPostDataOptions);
try{
    file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
}catch(Exception $e){}
