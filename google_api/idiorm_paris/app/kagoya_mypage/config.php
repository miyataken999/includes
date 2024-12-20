<?php
include_once '/usr/local/src/google_api/idiorm_paris/libs/idiorm/idiorm.php';
include_once '/usr/local/src/google_api/idiorm_paris/libs/paris/paris.php';

# データベース設定
// ORM::configure('mysql:host=o4042-854.kagoya.net;dbname=internet');
// ORM::configure('username', 'kir023063');
// ORM::configure('password', 'admin');
// ORM::configure('driver_options', [
//     PDO::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES utf8',
//     PDO::ATTR_EMULATE_PREPARES         => false,
//     PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
// ]);


// ORM::configure('mysql:host=o4042-854.kagoya.net;dbname=kinkai');
ORM::configure('mysql:host=35.200.113.217;dbname=mypage');
ORM::configure('username', 'nishitani');
ORM::configure('password', 'nishitani');
ORM::configure('driver_options', [
    PDO::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES utf8',
    PDO::ATTR_EMULATE_PREPARES         => false,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
]);
