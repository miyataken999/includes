<?php


set_time_limit(0);
ini_set("display_errors",1);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/vw_takuhaishuukei__seiyaku_awazu_kensuu.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/rs_takuhaishuukei__seiyaku_awazu_kensuu.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/vw_takuhaishuukei__shouhin_no_price.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/lounge_report__buy_takuhai.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/shouhin.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_takuhai.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/vw_takuhaishuukei__received.php';


$now_date = date("Y-m");
// $now_date = '2022-01';
$now = $now_date."%";

$now_time = $now_date . "-01 00:00:00";
