<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/store_categories.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/store_sub_categories.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_brand.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinished.php';

class vw_market_decline_alert_test4460 extends Model{

  public static $_table = 'vw_market_decline_alert_test4460';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'id';     // primary key (同様)

  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //大カテの和名変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function daikate(){
    if($this->category_id != ''){
      $res = $this->has_one('store_categories','category_id','category_id')->find_one();
      if($res != false){
      return $res->category_name;
      }else{
        return '';
      }
    }
  }
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //中カテの和名変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function chuukate(){
    if($this->sub_category_id1 != ''){
      $res = $this->has_one('store_sub_categories','id','sub_category_id1')->find_one();
      if($res != false){
        return $res->name;
      }else{
        return '';
      }
    }
  }


  public function create_title(){

    $raku_title = "";
    if($this->sales_price >= 100000){
      $raku_title .= "★";
    }
    if(($this->product_num != "") && ($this->product_num != 631)){
      $mst_brand = mst_brand::where('id', $this->product_num)->find_one();
      if($mst_brand != false){
        $raku_title .= $mst_brand->name;
      }
    }
    if($this->category_id != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->daikate();
    }
    if($this->sub_category_id1 != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->chuukate();
    }
    if($this->line != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->line;
    }
    if($this->item_name != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->item_name;
    }
    if($this->serial_number_for_storage != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->serial_number_for_storage;
    }
    if($this->yahoo_kataban != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->yahoo_kataban;
    }
    if($this->stamp != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->stamp;
    }
    if($this->motif != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->motif;
    }
    // if($this->Setting != ""){
    // 	if($raku_title != ''){$raku_title .= "/";}
    // 	$raku_title .= $this->Setting;
    // }
    if($this->gold_property != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->gold_property;
    }

    if($this->Gram != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Gram.$this->unit;
    }

    if($this->Parent_stone != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Parent_stone.'ct';
    }

    if($this->Aside_stone != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Aside_stone.'ct';
    }





    if($this->Destination_selling != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Destination_selling;
    }// end Destination_selling

    if($this->Finish != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Finish;
    }
    if($this->Discrimination != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Discrimination;
    }
    if($this->accessories != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->accessories;
    }
    if($this->A_storage == 1){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= "A保管";
    }
    if($this->priority == 1){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= "優先";
    }
    if($this->yahoo_color != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->yahoo_color;
    }



    if($this->Eoc_unfinished_id != ""){
      $Eoc_chigins_unfinished = Eoc_chigins_unfinished::where('product_id', $this->product_id)->find_one();
      if($Eoc_chigins_unfinished != false){
        if($Eoc_chigins_unfinished->details != ''){
          if($raku_title != ''){$raku_title .= "/";}
          $raku_title .= $Eoc_chigins_unfinished->details;
        }
      }
    }


    if($this->Remarks != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Remarks;
    }
  
    return $raku_title;
  }//end create_title

}
