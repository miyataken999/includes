<?php
// include_once '/usr/local/src/function/function.php';

// $tamiya_mes = "[toall][info][title]".date('Y-m-d H:i:s')."：相場変動アラート[/title]";
$tamiya_mes = "メール送信をお願い致します。\n";
$tamiya_mes .= "相場下落確認画面\n";
$tamiya_mes .= "\n";
// $tamiya_mes .= "[hr]\n";
$tamiya_mes .= "商品情報URL:http://rifa.life/lounge_API/evaeva/alert/market_decline_alert.php";
// $tamiya_mes .= "[/info]\n";
// $token = 'bot';
// $room = 59827824;

//メール送信
$mail_body = $tamiya_mes;
$sendPostData = array(
    "subject" => date('Y-m-d H:i:s')."：相場変動アラート",
    "text" => $mail_body,
);
$sendPostData = http_build_query($sendPostData, "", "&");
$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
$sendPostContext = stream_context_create($sendPostDataOptions);
try{
    file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
}catch(Exception $e){}
