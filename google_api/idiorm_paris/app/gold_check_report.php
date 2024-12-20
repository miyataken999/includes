<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';

class gold_check_report extends Model{

  public static $_table = 'gold_check_report';    // table name (異なる場合オーバライドできる)
  public static $_id_column = array('date', 'slip_type');     // primary key (同様)


}
