<?php
namespace{
  include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_mypage/config.php';
}

namespace mypage{
  use Model;
  class service_users extends Model{

    public static $_table = 'service_users';    // table name (異なる場合オーバライドできる)
    public static $_id_column = 'id';     // primary key (同様)


  }
}