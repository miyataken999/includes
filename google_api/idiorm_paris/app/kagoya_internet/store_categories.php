<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/config.php';

class store_categories extends Model{

  public static $_table = 'store_categories';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'category_id';     // primary key (同様)

}
