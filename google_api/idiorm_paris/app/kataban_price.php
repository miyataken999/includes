<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';

class kataban_price extends Model{

  public static $_table = 'kataban_price';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'yahoo_kataban';     // primary key (同様)


}
