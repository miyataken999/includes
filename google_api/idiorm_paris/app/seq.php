<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';

class seq extends Model{

  public static $_table = 'seq';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'product_id';     // primary key (同様)


}
