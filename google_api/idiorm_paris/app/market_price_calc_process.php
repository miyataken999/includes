<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';

class market_price_calc_process extends Model{

  public static $_table = 'market_price_calc_process';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'id';     // primary key (同様)
}
