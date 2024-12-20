<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';

class users extends Model{

  public static $_table = 'users';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'user_id';     // primary key (同様)


}
