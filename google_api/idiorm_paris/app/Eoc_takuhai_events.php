<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_Eoc_received_event_contents.php';


class Eoc_takuhai_events extends Model{

  public static $_table = 'Eoc_takuhai_events';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'id';     // primary key (同様)


  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //イベントの和名変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function content_name(){
  	if($this->content != ''){
	  	$res = $this->has_one('mst_Eoc_received_event_contents','id','content')->find_one();
	  	if($res != false){
			return $res->name;
	  	}else{
	  		return false;
	  	}
  	}
  }


  public function textcontent()
  {
    $res = str_replace("'","",$this->content_by_text);
    $res = str_replace("\"","",$res);
    $res = str_replace("\r\n","",$res);
    $res = str_replace("\r","",$res);
    $res = str_replace("\n","　",$res);
    return $res;
  }



}
