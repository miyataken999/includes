<?php
set_time_limit(0);
ini_set("display_errors",1);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/shouhin.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_trader.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_trader_report.php';


$now_month = date("Y-m");
// $now_month = '2022-01';

// $now_time = date("Y-m-d H:i:s");
$now_time = $now_month . "-01 00:00:00";
// $now_time = '2020-04-1 10:00:00';

$now = $now_month."%";

$now_date = date("Y-m-d", strtotime($now_time));
// $now_date = '2020-04-01';


$sheet_name = date("ym", strtotime($now_date));
// $sheet_name = '2004';


$now_month_last = date("t", strtotime($now_date));
