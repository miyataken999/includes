<?php
include "config.php";

// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 到着件数
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
$receiveds = vw_takuhaishuukei__received::where_like('received_time',$now)->find_many();
echo '<br><br>end vw_takuhaishuukei__received   SELECT';

$save_array = [];
$save_array['received_count'] = [];
$save_array['received_box_count'] = [];
foreach ($receiveds as $received) {
	$received_time = date("Y-m-d",strtotime($received->received_time));

	if(!isset($save_array['received_count'][$received_time])) {
		$save_array['received_count'][$received_time] = [];
	}
	if(!isset($save_array['received_box_count'][$received_time])) {
		$save_array['received_box_count'][$received_time] = [];
	}
	if (!empty(($received->max_chigin_type))) {
		switch($received->max_chigin_type){
			case 1:
				$name = 'kin';
				break;
			case 2:
				$name = 'dia';
				break;
			case 4:
				$name = 'jewel';
				break;
			case 8:
				$name = 'watch';
				break;
			case 3:
				$name = 'brand';
				break;
			default:
				$name = 'kin';
		}
	} else {
		switch($received->category){
			case 'K':
				$name = 'kin';
				break;
			case 'D':
				$name = 'dia';
				break;
			case 'J':
				$name = 'jewel';
				break;
			case 'W':
				$name = 'watch';
				break;
			case 'B':
				$name = 'brand';
				break;
			default:
				$name = 'kin';
		}
	}
	if (!empty($received->chigin_id_1)) {
		$count_column_name = "{$name}_received_count";
		$box_count_column_name = "{$name}_received_box_count";
		$total_count_column_name = "total_received_count";
		$total_box_count_column_name = "total_received_box_count";
	} else {
		$count_column_name = "{$name}_received_count_sairai";
		$box_count_column_name = "{$name}_received_box_count_sairai";
		$total_count_column_name = "total_received_count_sairai";
		$total_box_count_column_name = "total_received_box_count_sairai";
	}
	if (($received->is_NJ_or_BJ === 'BJ') && ($name === 'jewel')) {
		$count_column_name = $count_column_name . '_BJ';
		$box_count_column_name = $box_count_column_name . '_BJ';
	}

	// 計算結果を格納する配列の定義
	if (!isset($save_array['received_count'][$received_time][$count_column_name])) {
		$save_array['received_count'][$received_time][$count_column_name] = 0;
	}
	if (!isset($save_array['received_box_count'][$received_time][$box_count_column_name])) {
		$save_array['received_box_count'][$received_time][$box_count_column_name] = 0;
	}
	// total
	if (!isset($save_array['received_count'][$received_time][$total_count_column_name])) {
		$save_array['received_count'][$received_time][$total_count_column_name] = 0;
	}
	if (!isset($save_array['received_box_count'][$received_time][$total_box_count_column_name])) {
		$save_array['received_box_count'][$received_time][$total_box_count_column_name] = 0;
	}

	// 件数はひとつずつ加算
	$save_array['received_count'][$received_time][$count_column_name]++;
	$save_array['received_count'][$received_time][$total_count_column_name]++;
	// 箱数加算
	$save_array['received_box_count'][$received_time][$box_count_column_name] += $received->hakosuu;
	$save_array['received_box_count'][$received_time][$total_box_count_column_name] += $received->hakosuu;
}
// 件数の配列をDB保存
foreach ($save_array['received_count'] as $received_time => $count_column_names) {
	$lounge_report__buy_takuhai = create_lounge_report__buy_takuhai($received_time);
	foreach ($count_column_names as $count_column_name => $value) {
		$lounge_report__buy_takuhai->$count_column_name = $value;
		try {
			$lounge_report__buy_takuhai->save();
			// echo '<br><br>end new_received '.$received_time.' cate '.$count_column_name;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}
// 箱数の配列をDB保存
foreach ($save_array['received_box_count'] as $received_time => $count_column_names) {
	$lounge_report__buy_takuhai = create_lounge_report__buy_takuhai($received_time);
	foreach ($count_column_names as $count_column_name => $value) {
		$lounge_report__buy_takuhai->$count_column_name = $value;
		try {
			$lounge_report__buy_takuhai->save();
			// echo '<br><br>end new_received '.$received_time.' cate '.$count_column_name;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}



// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// end 到着件数
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

var_dump($save_array);



echo '<br><br>end 到着件数';

function create_lounge_report__buy_takuhai($received_time)
{
	//該当の日付があるかないか判断
	$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$received_time)->find_one();
	if($lounge_report__buy_takuhai == false){
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
		$lounge_report__buy_takuhai->date = $received_time;
		try {
			$lounge_report__buy_takuhai->save();
			print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
		} catch (Exception $e) {
			print $e->getMessage()."\n";
			exit();
		}
	}
	return $lounge_report__buy_takuhai;
}
