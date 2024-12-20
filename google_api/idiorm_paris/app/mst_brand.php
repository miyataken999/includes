<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';

class mst_brand extends Model{

  public static $_table = 'mst_brand';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'id';     // primary key (同様)

  public static function change_name($product_num, $lang='en')
  {
    if(($product_num != "") && ($product_num != 631)){
      $mst_brand = self::where('id', $product_num)->find_one();
      if($mst_brand != false){
        $brand_array = explode('/', $mst_brand->name);
        $ja_brand = $brand_array[0];
        $english_brand = $brand_array[1];

        if($lang == 'en'){
          return $english_brand;
        }elseif($lang == 'ja'){
          return $ja_brand;
        }
      }
    }
    return '';
  }
}
