宝飾
<br>
<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'

/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	宝飾シート
	成約用プログラム

■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
ini_set("display_errors",0);

//個別設定値

$range_column = 'P'; //読み込みレンジの右端の列
$ecc_and_price_column = 15; //顧客SEQと金額の列
$satei_column = 13; //査定人の列
$gss_type = 'housyoku'; //店頭詳細、商品生成の部分の振り分けフラグ

include $dirname.'/awazu.php';