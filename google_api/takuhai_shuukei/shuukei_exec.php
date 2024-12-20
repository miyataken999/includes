<?php
ini_set("display_errors",1);

// phpinfo();
$room = 68289449;
$token='nishitani';
set_time_limit(0);

/*---------------------------------------

宅配データ集計、
買取販売GSS書き込みプログラム

---------------------------------------*/


include "config.php";
include "/usr/local/src/google_api/function.php";

echo "集計スタート<br>";
// @ob_flush();
// @flush();

$mail_body = "宅配集計　スタート";
//メール送信
$sendPostData = array(
    "subject" => "宅配集計　スタート",
    "text" => $mail_body,
);
$sendPostData = http_build_query($sendPostData, "", "&");
$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
$sendPostContext = stream_context_create($sendPostDataOptions);
try{
    file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
}catch(Exception $e){}


// exit();


//今月以外も全更新したい場合は、whereを外す
// limit 5種別×31日=155＋余剰分 200
try {
	// $rs_takuhaishuukei__seiyaku_awazu_kensuu = rs_takuhaishuukei__seiyaku_awazu_kensuu::where_like('date',$now)->order_by_desc('date')->limit(200)->find_many();
	$rs_takuhaishuukei__seiyaku_awazu_kensuu = rs_takuhaishuukei__seiyaku_awazu_kensuu::where_like('date',$now)->limit(200)->find_many();
} catch (Exception $e) {
	echo $e->getMessage();
	$msg = $e->getMessage();

	$sendPostData = array(
		"subject" => "宅配集計　エラー",
		"text" => $msg,
	);
	$sendPostData = http_build_query($sendPostData, "", "&");
	$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
	$sendPostContext = stream_context_create($sendPostDataOptions);
	try{
		file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
	}catch(Exception $e){}
	
	exit();
}


echo "集計ビュー取得！<br>";
// @ob_flush();
// @flush();

$msg = "宅配集計データ取得！これからEVAを更新します。";
$sendPostData = array(
	"subject" => $msg,
	"text" => $msg,
);
$sendPostData = http_build_query($sendPostData, "", "&");
$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
$sendPostContext = stream_context_create($sendPostDataOptions);
try{
	file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
}catch(Exception $e){}


	// exit();

// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 成約、合わずの件数
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//ゼロ更新用のフラグ配列
$zero_flag = array();
foreach ($rs_takuhaishuukei__seiyaku_awazu_kensuu as $item) {
	//該当の日付があるかないか判断
	$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$item->date)->find_one();
	if($lounge_report__buy_takuhai == false){
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
		$lounge_report__buy_takuhai->date = $item->date;
		try {
			$lounge_report__buy_takuhai->save();
			print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			// @ob_flush();
			// @flush();
		} catch (Exception $e) {
			print "エラー発生！！<br>";
			print $e->getMessage()."\n";
			exit();
		}
	}else{
		print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." すでにあり\n";
		// @ob_flush();
		// @flush();
	}
	// end date生成

	//最大カテゴリーから該当項目名を算出
	switch ($item->max_chigin_type) {
		case '1':
			$name = "kin";
			break;
		case '2':
			$name = "dia";
			break;
		case '3':
			$name = "brand";
			break;
		case '4':
			$name = "jewel";
			break;
		case '6':
			$name = "";
			break;
		case '8':
			$name = "watch";
			break;
		default:
			$name = "";
			break;
	}
	// end 最大カテゴリー

	if ($name === 'jewel') {
		// 成約のNJ
		$zero_flag = save_BJ_NJ($name, $is_BJ=false, $item_column_name='seiyaku', $item, $zero_flag, $lounge_report__buy_takuhai);
		// 成約のBJ
		$zero_flag = save_BJ_NJ($name, $is_BJ=true, $item_column_name='seiyaku', $item, $zero_flag, $lounge_report__buy_takuhai);
		// 合わずのNJ
		$zero_flag = save_BJ_NJ($name, $is_BJ=false, $item_column_name='zenawazu', $item, $zero_flag, $lounge_report__buy_takuhai);
		// 合わずのBJ
		$zero_flag = save_BJ_NJ($name, $is_BJ=true, $item_column_name='zenawazu', $item, $zero_flag, $lounge_report__buy_takuhai);
		continue;
	}
	if($item->seiyaku > 0){
		//成約時の項目名を生成
		if($name != ''){
			$zero_flag["seiyaku"][$name][$lounge_report__buy_takuhai->date] = 1;
			$column_name = "{$name}_seiyaku_count";
			$lounge_report__buy_takuhai->$column_name = $item->seiyaku;
			$lounge_report__buy_takuhai->save();
		}else{
			$column_name = '';
		}

	}
	if($item->zenawazu > 0){
		//全合わず時の項目名を生成
		if($name != ''){
			$zero_flag["awazu"][$name][$lounge_report__buy_takuhai->date] = 1;
			$column_name = "{$name}_awazu_count";
			$lounge_report__buy_takuhai->$column_name = $item->zenawazu;
			$lounge_report__buy_takuhai->save();
		}else{
			$column_name = '';
		}

	}
}// end rs_takuhaishuukei__seiyaku_awazu_kensuu

/**
 * NJとBJのカラムの計算処理
 *
 * @param [type] $name
 * @param [type] $is_BJ
 * @param [type] $item_column_name
 * @param [type] $item
 * @param array $zero_flag
 * @param [type] $lounge_report__buy_takuhai
 * @return array $zero_flag
 */
function save_BJ_NJ($name, $is_BJ, $item_column_name, $item, $zero_flag, $lounge_report__buy_takuhai)
{
	// seiyaku or zenawazu
	if ($item_column_name === 'zenawazu') {
		$array_key_name = 'awazu';
	} else {
		$array_key_name = 'seiyaku';
	}
	if ($is_BJ === true) {
		$item_column_name .= '_BJ';// seiyaku_BJ, zenawazu_BJ
	} else {
		$item_column_name .= '_NJ';// seiyaku_NJ, zenawazu_NJ
	}

	$column_name = "{$name}_{$array_key_name}_count";
	if ($is_BJ === true) {
		$column_name .= '_BJ';
	}
	if ($item->$item_column_name > 0) {
		$zero_flag[$array_key_name][$name][$lounge_report__buy_takuhai->date] = 1;
		$lounge_report__buy_takuhai->$column_name = $item->$item_column_name;
	} else {
		$lounge_report__buy_takuhai->$column_name = 0;
	}
	$lounge_report__buy_takuhai->save();
	return $zero_flag;
}

$cate_array = array();
$cate_array[] = "kin";
$cate_array[] = "dia";
$cate_array[] = "brand";
$cate_array[] = "jewel";
$cate_array[] = "watch";

$now_date_last = date("t",strtotime($now_date.'-01 00:00:00'));


$day = 1;
while ($day <= $now_date_last) {
	if($day < 10){
		$str_day = '0'.$day;
	}else{
		$str_day = $day;
	}


	for ($i=0; $i < count($cate_array); $i++) {
		$cate = $cate_array[$i];
		$ymd = $now_date."-{$str_day}";

		echo $ymd;
	    // @ob_flush();
	    // @flush();

		// if(!isset($zero_flag["seiyaku"][$cate][$ymd])){
		if(($zero_flag["seiyaku"][$cate][$ymd] != 1) && (!isset($zero_flag["seiyaku"][$cate][$ymd]))){
			//該当の日付があるかないか判断
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ymd)->find_one();
			if($lounge_report__buy_takuhai != false){
				$column_name = "{$cate}_seiyaku_count";
				$lounge_report__buy_takuhai->$column_name = '0';
				$lounge_report__buy_takuhai->save();
			}
		}

		// if(!isset($zero_flag["awazu"][$cate][$ymd])){
		if(($zero_flag["awazu"][$cate][$ymd] != 1) && (!isset($zero_flag["awazu"][$cate][$ymd]))){
			//該当の日付があるかないか判断
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ymd)->find_one();
			if($lounge_report__buy_takuhai != false){
				$column_name = "{$cate}_awazu_count";
				$lounge_report__buy_takuhai->$column_name = '0';
				$lounge_report__buy_takuhai->save();
			}
		}


	}//end category for
	$day++;
}// end day while









// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// end 成約、合わずの件数
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■



$msg = "宅配集計終了しました。この後、「【書き込み】宅配集計」を押してGSSへ反映させてください。";

$sendPostData = array(
	"subject" => $msg,
	"text" => $msg,
);
$sendPostData = http_build_query($sendPostData, "", "&");
$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
$sendPostContext = stream_context_create($sendPostDataOptions);
try{
	file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
}catch(Exception $e){}
