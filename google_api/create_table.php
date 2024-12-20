<?php

//ストッパー
exit();


define('DB_HOST', '34.84.18.177');
define('DB_NAME', 'internet');
define('DB_USER', 'testssh');
define('DB_PASSWORD', 'testssh');

// 文字化け対策
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");

// PHPのエラーを表示するように設定
error_reporting(E_ALL & ~E_NOTICE);

// データベースの接続
try {
     $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD, $options);
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     echo $e->getMessage();
     exit;
}


$query = 'ALTER TABLE `GSS_buyAndSell`';
//まずゼロから入れる
// $columnList = range('A', 'Z');
$columnList = array();
//2桁のアルファベット生成。今のGSS上、H●まで。
foreach (range('C', 'H') as $columnItem) {
    foreach (range('A', 'Z') as $columnItem_2) {
        $columnList[] = $columnItem.$columnItem_2;
    }
}

$coltext = '';
for ($i=0; $i < count($columnList); $i++) {
	$name = $columnList[$i];
	if($coltext != ''){$coltext .= ',';}
	// $coltext .= 'ADD COLUMN `'.$name.'`  text(0) NULL';
	// $coltext .= 'DROP COLUMN `'.$name.'`';

}
$query .= $coltext;

$stmt = $dbh->query($query);
// while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
// 	print($result['data']."\n");
// }








?>
