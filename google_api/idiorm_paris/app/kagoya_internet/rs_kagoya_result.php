<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/config.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/kagoya_result_img.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/products_detail.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/mst_saisun_item.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/box_for_result.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/store_categories.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/store_sub_categories.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/mst_dia.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/_DIA_DA_APPRAISER.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/mst_Intensity.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/mst_overtone.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/mst_color_fluo.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/kagoya_internet/_DIA_DA_CUSTOMER.php';


class rs_kagoya_result extends Model{

  public static $_table = 'rs_kagoya_result';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'product_id';     // primary key (同様)

  public function img_url($width=400){
    $return = "";

    $box_res = box_for_result::where('box_id',"$this->box_id")->find_one();
      // $box_res = $this->has_one('box_for_result','box_id','box_id')->find_one();
      if($box_res==false){
        return $return;
      }
    //has_one(対象のクラス、対象のクラス側の特定するカラム、こっち側のクラスの外部キー)
    // return 1;

  $image_urlounge_img_pass = $box_res->zip;
  $image_pieces = explode(".", $image_urlounge_img_pass);
  $image_urlounge_box_name = $box_res->box_name;

  $product_id = $this->product_id;
  if(($image_pieces[1] != "") && ($image_urlounge_box_name != "") && ($product_id != "")){
    $ur_url = "/shop/services/image/{$image_urlounge_box_name}/{$image_pieces[1]}/{$product_id}_1.jpg";
    $ur_width = $width;
    $ur_resize_url = "/inc_ver01/resize/resize_img.php?url={$ur_url}&width={$ur_width}";
    $img_box[0] = "<div class='res_img' data='{$product_id}'><img src='{$ur_resize_url}' alt='' width='{$ur_width}px' ></div>";
    $return = "https://kinkaimasu.jp/".$ur_resize_url;
  //   for ($i=1; $i < 10; $i++) {
  //     //画像圧縮処理
  //     $img_i = $i+1;
  //     $ur_url = "/shop/services/image/{$image_urlounge_box_name}/{$image_pieces[1]}/{$product_id}_{$img_i}.jpg";
  //     $ur_width = $width;
  //     $ur_resize_url = "/inc_ver01/resize/resize_img.php?url={$ur_url}&width={$ur_width}";
  //     // $img_box = "<a href='https://kinkaimasu.jp/result_detail/?id={$product_id}'><div class='res_img' data='{$product_id}'><img src='{$ur_resize_url}' alt='' width='150px' height='150px' ></div></a>";
  //     $img_box[$i] = "<div class='res_img' data='{$product_id}'><img src='{$ur_resize_url}' alt='' width='{$ur_width}px' ></div>";
  //   }
  }else{

  //   $img_box[0] = "<div class='res_img no_img'><span>No image</span><span>画像データはありません。</span></div>";
  }

  // return $img_box;
  return $return;

  }


  public function zenhanMstSel($zenhan,$fielddata,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri){
    //いったん全部パス
    // return $zenhan;


    if($fielddata != ""){
      // $fielddata = ORM::select("SELECT {$mst_sel_name} FROM {$mst_name} WHERE {$mst_key} = {$fielddata}")[0]->name;
      $fielddata = ORM::for_table($mst_name)->select($mst_sel_name)->where($mst_key,$fielddata)->find_one()->$mst_sel_name;
      if($zenhan != ""){$zenhan .= $kugiri;}
      $zenhan .= $fielddata;
      // $zenhan .= "【{$fieldName} {$fielddata}】";
    }
    return $zenhan;
  }

  public function zenhan($zenhan,$fielddata,$fieldName,$kugiri){
    if($fielddata != ""){
      if($zenhan != ""){$zenhan .= $kugiri;}
      $zenhan .= $fielddata;
      // $zenhan .= "【{$fieldName} {$fielddata}】";
    }
    return $zenhan;
  }

  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //タイトル生成
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function title(){
  //  	$title = "";
  // if($this->category_id != ""){
  // 	if($title != ''){$title .= "/";}
  //   	$title .= $this->has_one('store_categories','category_id','category_id')->find_one()->category_name;
  // }
  // if($this->sub_category_id1 != ""){
  // 	if($title != ''){$title .= "/";}
  //   	$title .= $this->has_one('store_sub_categories','id','sub_category_id1')->find_one()->name;
  // }

  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //基本タイトル生成
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

    //前半ステータス
    $zenhan = "";


    $fieldName = 'producing_area';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'designer';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'line';	$kugiri = "･";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'item_name';	$kugiri = "･";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'Sleeve_Length';	$kugiri = "･";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'collar_neck_line';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'breast';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'motif';	$kugiri = " ";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'handle';	$kugiri = "･";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'silhouette';	$kugiri = " ";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'sleeve';	$kugiri = " ";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'length';	$kugiri = " ";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'number_of_stones';	$kugiri = " ";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'hahakai';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'toe';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'heel';	$kugiri = " ";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'shape_supplement';	$kugiri = "･";
    if($this->$fieldName == ''){
      $fieldName = 'sub_category_id1';
      $mst_name = "store_sub_categories";	$mst_key = 'id';	$mst_sel_name = 'name';
      $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);
    }else{
      $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);
    }

    $fieldName = 'side_gem';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);


    $fieldName = 'product_style';	$kugiri = "";
    if($this->$fieldName == ''){
      $fieldName = 'category_id';
      $mst_name = "store_categories";	$mst_key = 'category_id';	$mst_sel_name = 'category_name';
      $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);
    }else{
      $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);
    }

    // $fieldName = 'yahoo_sozai';	$kugiri = "･";
    // $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'cloth';	$kugiri = " ";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $fieldName = 'amount';	$kugiri = "";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'effect';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    // $fieldName = 'shape';	$kugiri = "･";
    // $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    // $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    // $fieldName = 'cutting_style';	$kugiri = "･";
    // $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    // $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    // $fieldName = 'Setting';	$kugiri = "･";
    // $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'processing';	$kugiri = "･";
    $zenhan = $this->zenhan($zenhan,$this->$fieldName,$fieldName,$kugiri);

    $fieldName = 'chain_type';	$kugiri = "･";
    $mst_name = "mst_{$fieldName}";	$mst_key = 'id';	$mst_sel_name = 'name';
    $zenhan = $this->zenhanMstSel($zenhan,$this->$fieldName,$fieldName,$mst_name,$mst_key,$mst_sel_name,$kugiri);

    $gold_property = $this->gold_property;
    if($gold_property != ""){$gold_property = "/".$gold_property;}

    $Gram = $this->Gram;
    if($Gram != ""){$Gram = "-".$Gram."g";}

    $sub_cate = $this->sub_category_id1;
    $moji = "";
    if($sub_cate != ""){
      //つくる！！！
      if(function_exists("sub_cate_alpha")){
        $moji = sub_cate_alpha($sub_cate);
      }
    }

    $Parent_stone = $this->Parent_stone;
    if($Parent_stone != ""){
      $Parent_stone = "/".$moji.$Parent_stone."ct";
    }

    $Aside_stone = $this->Aside_stone;
    if($Aside_stone != ""){$Aside_stone = "/FD:".$Aside_stone."ct";}

    $Appraiser = $this->Appraiser;
    if($Appraiser != ""){$Appraiser = "/".$Appraiser;}

    $Ring_size = $this->Ring_size;
    if($Ring_size != ""){$Ring_size = "/".$Ring_size;}

    $Remarks = $this->Remarks;
    if($Remarks != ""){$Remarks = "/".$Remarks;}

    $yahoo_kataban = $this->yahoo_kataban;
    if($yahoo_kataban != ""){$yahoo_kataban = "/".$yahoo_kataban;}


    $mark = $zenhan . $gold_property . $Gram . $Parent_stone . $Aside_stone . $Appraiser . $Ring_size . $Remarks . $yahoo_kataban;
    //タイトル部分を書き換え
    $mark = str_replace('・','･',$mark);
    // $this->title = $mark;
    $title = $mark;


    $title = str_replace('【×】','',$title);


    //ラスト
    //楽天タイトル形成
      // $display_value_product_num_1_val = DB::select("SELECT name FROM mst_brand WHERE id = {$this->product_num}")[0]->name;
      // $display_value_product_num_1_val = str_replace("'","’",$display_value_product_num_1_val);
      // $value_product_num_1_val = $this->product_num;

      // if($value_product_num_1_val == 631){
      //     $brand_eimei = "";
      // }else if (strstr($display_value_product_num_1_val,'/')) {
      //     $product_num_split = explode("/",$display_value_product_num_1_val);
      //     $brand_eimei = "/".$product_num_split[1];
      // }else{
      //     $brand_eimei = "";
      // }
      // $this->yahoo_junle = $display_value_product_num_1_val;


      // $color_val = $this->yahoo_color;
      // $kataban_val = $this->yahoo_kataban;
      // if($color_val != ""){
      // 	$color_val = "/".$color_val;
      // }
      // if($kataban_val != ""){
      // 	$kataban_val = "/".$kataban_val;
      // }

      // if($this->product_num == 631){
      //   $_title = 'ノーブランド(ノンブランド) '.$this->title.$kataban_val;
      // }else{
      //   $_title = $this->yahoo_junle." ".$this->title.$kataban_val;
      // }

      // if($_title != ""){
      // 	$raku_title = $_title;
      // }

  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //基本タイトル生成　END
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  return $title;
  }

}
