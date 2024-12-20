<?php


set_time_limit(0);
ini_set("display_errors",1);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/gold_check.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/gold_check_item.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/gold_check_report.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_tax.php';


$now_month = date("Y-m");
// $now_month = '2022-01';
// $now_time = date("Y-m-d H:i:s");
$now_time = $now_month . "-01 00:00:00";
// $now_month = '2019-05';
$now = $now_month."%";

$now_month_last = date("t", strtotime($now_time));
