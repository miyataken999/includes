<?php
/*---------------------------------------

店頭データ集計、
買取販売GSS書き込みプログラム

---------------------------------------*/


ini_set("display_errors",1);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/vw_Eoc_unfinished_by_day_greatest.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/lounge_report__buy_copy.php';

//宅配受付データのビュー
include_once '/usr/local/src/google_api/idiorm_paris/app/vw_Eoc_received_customers_and_purchase_type.php';



$now = date("Y-m")."%";
// print $now;
// exit();




//今月以外も全更新したい場合は、whereを外す
$vw_Eoc_received_customers_and_purchase_type = vw_Eoc_received_customers_and_purchase_type::select("*")->select_expr("COUNT(id)","count")->where_like('time',$now)->group_by('time')->group_by('purchase_type')->group_by('content')->find_many();

foreach ($vw_Eoc_received_customers_and_purchase_type as $ercapt) {
	//いつ
	$time = $ercapt->time;
	//純新か再来か再チャレか
	$content = $ercapt->content;
	//買取種別から、金宅配かブランド宅配か
	$purchase_type = $ercapt->purchase_type;
	print "{$time} {$content} {$purchase_type} <br>";

	// $lounge_report__buy = lounge_report__buy::where('date',$item->create_at_day)->find_one();
	// if($lounge_report__buy === false){
	// 	$lounge_report__buy = lounge_report__buy::create();
	// 	$lounge_report__buy->date = $item->create_at_day;
	// 	try {
	// 		$lounge_report__buy->save();
	// 		print "lounge_report__buy date ".$lounge_report__buy->date." 新規作成\n";
	// 	} catch (Exception $e) {
	// 		print $e->getMessage()."\n";
	// 		exit();
	// 	}
	// }

}








exit();













//今月以外も全更新したい場合は、whereを外す
$vw_Eoc_unfinished_by_day_greatest = vw_Eoc_unfinished_by_day_greatest::where_like('create_at_day',$now)->find_many();
// $vw_Eoc_unfinished_by_day_greatest = vw_Eoc_unfinished_by_day_greatest::find_many();

//各カテゴリーごとの最大値のカテゴリーを集計し、見込み顧客に入れる
foreach ($vw_Eoc_unfinished_by_day_greatest as $greatest) {
	switch ($greatest->greatest) {
		case $greatest->sum_K:
			$max_category = "K";
			break;
		case $greatest->sum_D:
			$max_category = "D";
			break;
		case $greatest->sum_J:
			$max_category = "J";
			break;
		case $greatest->sum_W:
			$max_category = "W";
			break;
		case $greatest->sum_B:
			$max_category = "B";
			break;
		case $greatest->sum_H:
			$max_category = "H";
			break;
		case $greatest->sum_A:
			$max_category = "A";
			break;
		default:
			$max_category = "";
			break;
	}


	$Eoc_unfinished = Eoc_unfinished::where("id",$greatest->id)->find_one();
	if($Eoc_unfinished !== false){
		$Eoc_unfinished->max_category = $max_category;
		try {
			$Eoc_unfinished->save();
			print "Eoc_unfinished id ".$Eoc_unfinished->id." 更新\n";

		} catch (Exception $e) {
			print $e->getMessage()."\n";
			exit();
		}
	}


}



//ここから、本番

// select_expr


$category_Array = array('K','D','J','W','B','H');//合わずのAは省いとく

//総件数の計算
print "---------------------------------------------\n";
print "総件数の計算\n";
$totaladds = vw_Eoc_unfinished_by_day_greatest::select("*")->select_expr("COUNT(id)","count")->where_like('create_at_day',$now)->group_by('create_at_day')->group_by('max_category')->find_many();
foreach ($totaladds as $item) {
	print "create_at_day  ".$item->create_at_day;
	print "<br>\n";
	print "max_category  ".$item->max_category;
	print "<br>\n";
	print "count  ".$item->count;
	print "<br>\n";
	$lounge_report__buy = lounge_report__buy::where('date',$item->create_at_day)->find_one();
	if($lounge_report__buy === false){
		$lounge_report__buy = lounge_report__buy::create();
		$lounge_report__buy->date = $item->create_at_day;
		try {
			$lounge_report__buy->save();
			print "lounge_report__buy date ".$lounge_report__buy->date." 新規作成\n";
		} catch (Exception $e) {
			print $e->getMessage()."\n";
			exit();
		}
	}
	switch ($item->max_category) {
		case 'K':
			$lounge_report__buy->raiten_kin_total_count = $item->count;
			break;
		case 'D':
			$lounge_report__buy->raiten_dia_total_count = $item->count;
			break;
		case 'J':
			$lounge_report__buy->raiten_jewel_total_count = $item->count;
			break;
		case 'W':
			$lounge_report__buy->raiten_watch_total_count = $item->count;
			break;
		case 'B':
			$lounge_report__buy->raiten_brand_total_count = $item->count;
			break;
		default:
			break;
	}

	try {
		$lounge_report__buy->save();
		print "lounge_report__buy date ".$lounge_report__buy->date." 総件数更新\n";
	} catch (Exception $e) {
		print $e->getMessage()."\n";
		exit();
	}


}	//END foreach totaladds


print "---------------------------------------------\n";
print "成約件数の計算\n";
$totaladds_seiyaku = vw_Eoc_unfinished_by_day_greatest::select("*")
	->select_expr("COUNT(id)","count")
	->where_not_equal('ecc_id',4460)
	->where_not_equal('ecc_id',109175)
	->where_gt('greatest',0)
	->where_like('create_at_day',$now)
	->group_by('create_at_day')
	->group_by('max_category')
	->find_many();
foreach ($totaladds_seiyaku as $item) {
	print "create_at_day  ".$item->create_at_day;
	print "<br>\n";
	print "max_category  ".$item->max_category;
	print "<br>\n";
	print "count  ".$item->count;
	print "<br>\n";

	$lounge_report__buy = lounge_report__buy::where('date',$item->create_at_day)->find_one();
	if($lounge_report__buy === false){
		$lounge_report__buy = lounge_report__buy::create();
		$lounge_report__buy->date = $item->create_at_day;
		try {
			$lounge_report__buy->save();
			print "lounge_report__buy date ".$lounge_report__buy->date." 新規作成\n";
		} catch (Exception $e) {
			print $e->getMessage()."\n";
			exit();
		}
	}
	switch ($item->max_category) {
		case 'K':
			$lounge_report__buy->raiten_kin_seiyaku_count = $item->count;
			break;
		case 'D':
			$lounge_report__buy->raiten_dia_seiyaku_count = $item->count;
			break;
		case 'J':
			$lounge_report__buy->raiten_jewel_seiyaku_count = $item->count;
			break;
		case 'W':
			$lounge_report__buy->raiten_watch_seiyaku_count = $item->count;
			break;
		case 'B':
			$lounge_report__buy->raiten_brand_seiyaku_count = $item->count;
			break;
		default:
			break;
	}

	try {
		$lounge_report__buy->save();
		print "lounge_report__buy date ".$lounge_report__buy->date." 成約件数更新\n";
	} catch (Exception $e) {
		print $e->getMessage()."\n";
		exit();
	}

}	//END foreach totaladds_seiyaku


print "---------------------------------------------\n";
print "成約金額の計算\n";
$totaladds_seiyaku = vw_Eoc_unfinished_by_day_greatest::select("*")
	->select_expr("SUM(sum_K)","sum_sum_K")
	->select_expr("SUM(sum_D)","sum_sum_D")
	->select_expr("SUM(sum_J)","sum_sum_J")
	->select_expr("SUM(sum_W)","sum_sum_W")
	->select_expr("SUM(sum_B)","sum_sum_B")
	->select_expr("SUM(sum_H)","sum_sum_H")
	->where_not_equal('ecc_id',4460)
	->where_not_equal('ecc_id',109175)
	->where_like('create_at_day',$now)
	->group_by('create_at_day')
	->find_many();
foreach ($totaladds_seiyaku as $item) {
	print "create_at_day  ".$item->create_at_day;
	print "<br>\n";
	print "sum_sum_K  ".$item->sum_sum_K;
	print "<br>\n";
	print "sum_sum_D  ".$item->sum_sum_D;
	print "<br>\n";
	print "sum_sum_J  ".$item->sum_sum_J;
	print "<br>\n";
	print "sum_sum_W  ".$item->sum_sum_W;
	print "<br>\n";
	print "sum_sum_B  ".$item->sum_sum_B;
	print "<br>\n";
	print "sum_sum_H  ".$item->sum_sum_H;
	print "<br>\n";

	$lounge_report__buy = lounge_report__buy::where('date',$item->create_at_day)->find_one();
	if($lounge_report__buy === false){
		$lounge_report__buy = lounge_report__buy::create();
		$lounge_report__buy->date = $item->create_at_day;
		try {
			$lounge_report__buy->save();
			print "lounge_report__buy date ".$lounge_report__buy->date." 新規作成\n";
		} catch (Exception $e) {
			print $e->getMessage()."\n";
			exit();
		}
	}
	$lounge_report__buy->raiten_kin_seiyaku_price = $item->sum_sum_K;
	$lounge_report__buy->raiten_dia_seiyaku_price = $item->sum_sum_D;
	$lounge_report__buy->raiten_jewel_seiyaku_price = $item->sum_sum_J;
	$lounge_report__buy->raiten_watch_seiyaku_price = $item->sum_sum_W;
	$lounge_report__buy->raiten_brand_seiyaku_price = $item->sum_sum_B;
	$lounge_report__buy->raiten_hansoku_price = $item->sum_sum_H;

	try {
		$lounge_report__buy->save();
		print "lounge_report__buy date ".$lounge_report__buy->date." 買取額更新\n";
	} catch (Exception $e) {
		print $e->getMessage()."\n";
		exit();
	}

}	//END foreach totaladds_seiyaku























/*---------------------------------------

買取販売GSSへ書き込む

---------------------------------------*/


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
  throw new Exception('不正なアクセスです。');
}









$url_id = $_POST["url_id"];

$client = getClient();
$service = new Google_Service_Sheets($client);




//GSSシートID
//列違いがあった場合、コピーしてからでないとダメ。どうにかする。
$spreadsheetId = $url_id;

//シート名
$sheet_name = (date("Y")-2000).date("m");

// $range = $sheet_name.'!'.'A1:HE5';
$range = $sheet_name.'!'.'AF2:AW2';


$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();



//0=A,1=B,2=C

if (count($values) == 0) {
	$return = "スプレッドシートのデータが取得できませんでした。\n";
	print $return;
}elseif(
	($values[0][0] != '金来店・出張')
	AND ($values[0][3] != '精錬・店頭')
	AND ($values[0][5] != 'ダイヤ来店・出張')
	AND ($values[0][8] != '宝石来店・出張')
	AND ($values[0][11] != '時計来店・出張')
	AND ($values[0][14] != '服飾来店・出張')
	AND ($values[0][17] != '販促費')
	){

	print "AF～AWの形式が違います。【金来店・出張】<br>";
	print "【精錬・店頭】<br>";
	print "【ダイヤ来店・出張】<br>";
	print "【宝石来店・出張】<br>";
	print "【時計来店・出張】<br>";
	print "【服飾来店・出張】<br>";
	print "【販促費】<br>";
	print "に揃えてください。<br>";
	exit();
}else{


	print "<br><br><br><br><br><br>";

	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取販売GSSへの書き込み
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$lounge_report__buy = lounge_report__buy::where_like('date',$now)->find_many();
	if($lounge_report__buy !== false){
		$row = 4;
		$values = array();
		$values_2 = array();
		foreach ($lounge_report__buy as $repo) {


			if($repo->raiten_kin_total_count == ''){$repo->raiten_kin_total_count = '';}
			if($repo->raiten_kin_seiyaku_count == ''){$repo->raiten_kin_seiyaku_count = '';}
			if($repo->raiten_kin_seiyaku_price == ''){$repo->raiten_kin_seiyaku_price = '';}

			$values[] = array(
			        $repo->raiten_kin_total_count,
			        $repo->raiten_kin_seiyaku_count,
			        $repo->raiten_kin_seiyaku_price,
			    );



			if($repo->raiten_dia_total_count == ''){$repo->raiten_dia_total_count = '';}
			if($repo->raiten_dia_seiyaku_count == ''){$repo->raiten_dia_seiyaku_count = '';}
			if($repo->raiten_dia_seiyaku_price == ''){$repo->raiten_dia_seiyaku_price = '';}
			if($repo->raiten_jewel_total_count == ''){$repo->raiten_jewel_total_count = '';}
			if($repo->raiten_jewel_seiyaku_count == ''){$repo->raiten_jewel_seiyaku_count = '';}
			if($repo->raiten_jewel_seiyaku_price == ''){$repo->raiten_jewel_seiyaku_price = '';}
			if($repo->raiten_watch_total_count == ''){$repo->raiten_watch_total_count = '';}
			if($repo->raiten_watch_seiyaku_count == ''){$repo->raiten_watch_seiyaku_count = '';}
			if($repo->raiten_watch_seiyaku_price == ''){$repo->raiten_watch_seiyaku_price = '';}
			if($repo->raiten_brand_total_count == ''){$repo->raiten_brand_total_count = '';}
			if($repo->raiten_brand_seiyaku_count == ''){$repo->raiten_brand_seiyaku_count = '';}
			if($repo->raiten_brand_seiyaku_price == ''){$repo->raiten_brand_seiyaku_price = '';}
			if($repo->raiten_hansoku_price == ''){$repo->raiten_hansoku_price = '';}

			$values_2[] = array(
			        $repo->raiten_dia_total_count,
			        $repo->raiten_dia_seiyaku_count,
			        $repo->raiten_dia_seiyaku_price,

			        $repo->raiten_jewel_total_count,
			        $repo->raiten_jewel_seiyaku_count,
			        $repo->raiten_jewel_seiyaku_price,
			        $repo->raiten_watch_total_count,
			        $repo->raiten_watch_seiyaku_count,
			        $repo->raiten_watch_seiyaku_price,
			        $repo->raiten_brand_total_count,
			        $repo->raiten_brand_seiyaku_count,
			        $repo->raiten_brand_seiyaku_price,
			        $repo->raiten_hansoku_price,
			    );



			$row++;//行
		}
	}
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取販売GSSへの書き込み END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

// exit();



}

$data = array();
$data[] = new Google_Service_Sheets_ValueRange(array(
  'range' => $sheet_name.'!AF4:AH34',
  'values' => $values
));
// Additional ranges to update ...
$body = '';
$body = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
  'valueInputOption' => 'RAW',
  'data' => $data
));
try {
	$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
} catch (Exception $e) {
	print "error 1 ".$e->getMessage();
	// var_dump($body);
}

// var_dump($values_2);
// print "<br><br><br><br><br><br>";

$data_2 = array();
$data_2[] = new Google_Service_Sheets_ValueRange(array(
  'range' => $sheet_name.'!AK4:AW34',
  'values' => $values_2
));

$body_2 = '';
$body_2 = new Google_Service_Sheets_BatchUpdateValuesRequest(array(
  'valueInputOption' => 'RAW',
  'data' => $data_2
));
try {
	$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body_2);
} catch (Exception $e) {
	print "error 2 ".$e->getMessage();
	// print "<br>".$repo->raiten_dia_seiyaku_price."<br>";
	// var_dump($body_2);
}





































