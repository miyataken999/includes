<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/store_categories.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/store_sub_categories.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_Appraiser.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/_DIA_DA_CUT.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_Intensity.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_overtone.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_color_fluo.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_side_gem.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_effect.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_shape.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/mst_cutting_style.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/mst_brand.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinished.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/evav62122_audit.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/evav62122_detailed_audit.php';

include_once '/usr/local/src/google_api/sub_cate_alpha.php';


class shouhin extends Model{

  public static $_table = 'shouhin';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'product_id';     // primary key (同様)

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

  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //鑑定機関の略称変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function Appraiser_short(){
    if($this->Appraiser != ''){
      $res = $this->has_one('mst_Appraiser','val','Appraiser')->find_one();
      if($res != false){
        return $res->abbreviation;
      }else{
        return '';
      }
    }
  }

   //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //査定人の和名変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function satei_name(){
    if($this->satei_by != ''){
      $res = $this->has_one('users','user_id','satei_by')->find_one();
      if($res != false){
      return $res->first_name;
      }else{
        return '';
      }
    }
  }

//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //商品ステータスの和名変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function goods_status(){
    if($this->status != ''){
      $res = $this->has_one('_goods_status2','goods_id','status')->find_one();
      if($res != false){
      return $res->goods_status;
      }else{
        return '';
      }
    }
  }
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //蛍光色の変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function mst_cofl(){
    if($this->DA_COLOR_FLUO != ''){
      $res = $this->has_one('mst_color_fluo','id','DA_COLOR_FLUO')->find_one();
      if($res != false){
      return $res->Name;
      }else{
        return '';
      }
    }
  }

  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  //蛍光色の略称変換
  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function cofl_short(){
    if($this->DA_COLOR_FLUO == 1){
			return "B";
		}elseif($this->DA_COLOR_FLUO == 2){
			return "Y";
		}elseif($this->DA_COLOR_FLUO == 3){
			return "G";
		}elseif($this->DA_COLOR_FLUO == 4){
			return 	"R";
		}elseif($this->DA_COLOR_FLUO == 5){
			return "O";
		}elseif($this->DA_COLOR_FLUO == 6){
			return "W";
		}elseif($this->DA_COLOR_FLUO == 7){
			return "YEGR";
		}elseif($this->DA_COLOR_FLUO == 9){
			return "YEBL";
    }else{
      return "";
    }
  }

  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  // カットの略称変換
  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function cut_short(){
    if($this->DA_CUT != ''){
      $res = $this->has_one('_DIA_DA_CUT','name','DA_CUT')->find_one();
      if($res != false){
        return $res->short_name;
      }else{
        return $this->DA_CUT;
      }
    }
  }


  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  // INSIの略称変換
  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function DA_INTENSITY(){
    if($this->DA_CUT != ''){
      $res = $this->has_one('mst_Intensity','ID0','DA_INTENSITY')->find_one();
      if($res != false){
        return $res->Name;
      }else{
        return $this->DA_INTENSITY;
      }
    }
  }


  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  // OVTOの略称変換
  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function DA_OVERTONE(){
    if($this->DA_CUT != ''){
      $res = $this->has_one('mst_overtone','Id','DA_OVERTONE')->find_one();
      if($res != false){
        return $res->Name;
      }else{
        return $this->DA_OVERTONE;
      }
    }
  }


  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  // create_title
  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
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


    $this->title = $raku_title;
    try {
      $this->save();
      // return;
    } catch (Exception $e) {
      return $e->getMessage();
    }

    return $raku_title;
  }//end create_title

  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  // create_user_side_title(査定明細お客様側に見せるタイトル生成)
  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function create_user_side_title(){

    $raku_title = "";

    if(($this->product_num != "") && ($this->product_num != 631)){
      $mst_brand = mst_brand::where('id', $this->product_num)->find_one();
      if($mst_brand != false){
        $raku_title .= $mst_brand->name;
      }
    }


    // ･
    // producing_area




    // ･
    // designer




    if($this->line != ""){
      if($raku_title != ''){$raku_title .= "･";}
      $raku_title .= $this->line;
    }
    if($this->item_name != ""){
      if($raku_title != ''){$raku_title .= "･";}
      $raku_title .= $this->item_name;
    }
    if($this->Sleeve_Length != ""){
      if($raku_title != ''){$raku_title .= "･";}
      $raku_title .= $this->Sleeve_Length;
    }



      // ･
      // collar_neck_line




      // breast


      if($this->motif != ""){
        if($raku_title != ''){$raku_title .= " ";}
        $raku_title .= $this->motif;
      }
      if($this->handle != ""){
        if($raku_title != ''){$raku_title .= "･";}
        $raku_title .= $this->handle;
      }






        // silhouette




        // sleeve


        if($this->length != ""){
          if($raku_title != ''){$raku_title .= " ";}
          $raku_title .= $this->length;
        }



        // number_of_stones





        // hahakai





        // toe





        // heel




        if($this->shape_supplement != ""){
          if($raku_title != ''){$raku_title .= "･";}
          $raku_title .= $this->shape_supplement;
        }


        if($this->side_gem != ""){
          $mst_side_gem = mst_side_gem::where('id', $this->side_gem)->find_one();
          if($mst_side_gem != false){
            if($raku_title != ''){$raku_title .= "･";}
            $raku_title .= $mst_side_gem->name;
          }
        }



        if($this->product_style != ""){
          $raku_title .= $this->product_style;
        }

        if($this->yahoo_sozai != ""){
          if($raku_title != ''){$raku_title .= "･";}
          $raku_title .= $this->yahoo_sozai;
        }





        // cloth




        if($this->amount != ""){
          $raku_title .= $this->amount;
        }

        if($this->effect != ""){
          $mst_effect = mst_effect::where('id', $this->effect)->find_one();
          if($mst_effect != false){
            if($raku_title != ''){$raku_title .= "･";}
            $raku_title .= $mst_effect->name;
          }
        }

        if($this->shape != ""){
          $mst_shape = mst_shape::where('id', $this->shape)->find_one();
          if($mst_shape != false){
            if($raku_title != ''){$raku_title .= "･";}
            $raku_title .= $mst_shape->name;
          }
        }

        if($this->cutting_style != ""){
          $mst_cutting_style = mst_cutting_style::where('id', $this->cutting_style)->find_one();
          if($mst_cutting_style != false){
            if($raku_title != ''){$raku_title .= "･";}
            $raku_title .= $mst_cutting_style->name;
          }
        }



        if($this->Setting != ""){
          if($raku_title != ''){$raku_title .= "･";}
          $raku_title .= $this->Setting;
        }

        if($this->processing != ""){
          if($raku_title != ''){$raku_title .= "･";}
          $raku_title .= $this->processing;
        }



        // ･
        // chain_type



    if($this->gold_property != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->gold_property;
    }
    if($this->Gram != ""){
      if($raku_title != ''){$raku_title .= "-";}
      $raku_title .= $this->Gram.'g';
    }

    if($this->sub_category_id1 != ""){
      // make
      $moji = sub_cate_alpha($this->sub_category_id1);
    }

    if($this->Parent_stone != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $moji.$this->Parent_stone.'ct';
    }

    if($this->Aside_stone != ""){
      if($raku_title != ''){$raku_title .= "/FD:";}
      $raku_title .= $this->Aside_stone.'ct';
    }


    if($this->Appraiser != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Appraiser;
    }

    if($this->Ring_size != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Ring_size;
    }

    if($this->Remarks != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Remarks;
    }

    if($this->yahoo_kataban != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->yahoo_kataban;
    }

    $raku_title = str_replace('・','･',$raku_title);


    return $raku_title;
  }//end create_user_side_title



  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  // マスタ参照し、変換する
  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  /**
      $getName = 'name'; // 取得したい項目名
      $masterTableName = 'mst_brand2'; // マスターテーブル名
      $targetColumnName = 'product_num'; // 参照先の項目名
      $columnName = 'id'; // 参照元の項目名
      // 実行コード例
      $create_title .= $this->change_return($getName, $masterTableName, $targetColumnName, $columnName);
  **/
  public function change_return($getName, $masterTableName, $targetColumnName, $columnName){
    if($this->$columnName != ''){
      $res = $this->has_one($masterTableName, $targetColumnName, $columnName)->find_one();
      if($res != false){
        return $res->$getName;
      }else{
        return $this->$columnName;
      }
    }else{
      return '';
    }
  }//end change_return

  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  // 売却済時のステータス、登録日（者）変更
  // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
  public function set_buying_column($uid=0){
    if($this->status != 33){
      return 'not status baikayakuzumi';
    }

    $now_datetime = date("Y-m-d H:i:s");
    $this->kaitori_by = $uid;
    $this->kaitory_hi = $now_datetime;
    $this->updated_by = $uid;
    $this->updated_at = $now_datetime;

    $this->status = 29;//初期値
    if($this->Destination_selling != ""){
      if($this->Destination_selling == "ECサイト"){
        $this->status = 29;
      }elseif($this->Destination_selling == "Stock"){
        $this->status = 124;
      }elseif($this->Destination_selling == "B市場/服飾"){
        $this->status = 111;
      }elseif($this->Destination_selling == "J市場/宝飾"){
        $this->status = 121;
      }elseif($this->Destination_selling == "A市場/服飾"){
        $this->status = 113;
      }elseif($this->Destination_selling == "M待機"){
        $this->status = 147;
      }elseif($this->Destination_selling == "委託"){
        $this->status = 29;
      }
    }// end Destination_selling

    if($this->Finish != ""){
      $this->status = 110;
    }
    if($this->Discrimination != ""){
      $this->status = 110;
    }

    $this->create_title();

    try {
      $this->save();
      return;
    } catch (Exception $e) {
      return $e->getMessage();
    }

  }// set_buying_column





  public function create_housyoku_title()
  {
    $raku_title = "";
    if(($this->product_num != "") && ($this->product_num != 631)){
      $mst_brand = mst_brand::where('id', $this->product_num)->find_one();
      if($mst_brand != false){
        $brand_array = explode('/', $mst_brand->name);
        $english_brand = $brand_array[1];
        $raku_title .= $english_brand;
      }
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
    if($this->shape_supplement != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->shape_supplement;
    }
    if($this->product_style != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->product_style;
    }
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

    if($this->Appraiser != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= $this->Appraiser;
    }

    if($this->Ring_size != ""){
      if($raku_title != ''){$raku_title .= "/";}
      $raku_title .= str_replace("/","",$this->Ring_size);
    }

    return $raku_title;


  }// end create_housyoku_title




  public function create_ingo($price_column='sales_price'){
    $res = '';
    $hanbai_ingo = '';
    $kaitori_ingo = '';
    $ingo = array(
    	"1" => "R",
    	"2" => "U",
    	"3" => "N",
    	"4" => "J",
    	"5" => "D",
    	"6" => "Z",
    	"7" => "I",
    	"8" => "X",
    	"9" => "A",
    	"0" => "S",
    	"," => "K"
    );


    $hanbai = $this->$price_column;
    $kaitori = $this->price;
    $hanbai = number_format($hanbai);
    $kaitori = number_format($kaitori);
    $array = array();
    if($hanbai!=''){
      $array['hanbai'] = $hanbai;
    }
    if($kaitori!=''){
      $array['kaitori'] = $kaitori;
    }
    foreach($array as $key => $value){
      $value_ingo = "";
      // $value_old = $value;
      if($value!=""){
    		//一文字ずつ配列に格納
        // $msg ="ok";
        $value = str_split($value);
    		$value_len = count($value);
    		if($value_len >= 3){
    			//末３文字が全部ゼロだったら、空にする
    			if(($value[$value_len-1] == "0") && ($value[$value_len-2] == "0") && ($value[$value_len-3] == "0")){
    				$value[$value_len-1] = "";
    				$value[$value_len-2] = "";
    				$value[$value_len-3] = "";
    				if($value[$value_len-4] == ","){
    					$value[$value_len-4] = "";
    				}
    			}
    		}
    		if($value_len == 3){
    			$value_len = 4;
    			$value[3] = $value[2];
    			$value[2] = $value[1];
    			$value[1] = $value[0];
    			$value[0] = ",";
    		}
    		if($value_len == 2){
    			$value_len = 4;
    			$value[3] = $value[1];
    			$value[2] = $value[0];
    			$value[1] = "0";
    			$value[0] = ",";
    		}
    		if($value_len == 1){
    			$value_len = 4;
    			$value[3] = $value[0];
    			$value[2] = "0";
    			$value[1] = "0";
    			$value[0] = ",";
    		}
        // $value_array=array();
        // $value_array[] = $value;
    		//隠語配列に変換、結合
        if($key == "hanbai"){
            for($i=0; $i < $value_len; $i++){
              if($value[$i]!=""){
                $value[$i] = $ingo[$value[$i]];
                $hanbai_ingo .= $value[$i];
            }//end for
          }//if($value[$i]!="")
        }elseif($key=="kaitori"){
          $msg ="kaitori";
            for($i=0; $i < $value_len; $i++){
              if($value[$i]!=""){
                $value[$i] = $ingo[$value[$i]];
                $kaitori_ingo .= $value[$i];
            }//end for
          }//if($value[$i]!="")
        }else{
        }//if($key = "hanbai")
      }//end if($value!="")
    }//end foreach
		$res["hanbai_ingo"] = $hanbai_ingo;
    $res["kaitori_ingo"] = $kaitori_ingo;
    return $res;



  }// end create_ingo





  public function log_save($old_dataArray='', $user_login='', $log_table='', $log_action='', $log_save_table='')
  {
    $logText = '';
    $logText .= "---Keys\n";
    $logText .= "product_id : {$this->product_id}\n";

    $logText .= "---Fields\n";

    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 商品
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    $new_dataArray = $this->as_array();
    $logText = "";
    foreach ($new_dataArray as $data_key => $data_value) {
        if($data_value != $old_dataArray[$data_key]){
            if($old_dataArray[$data_key] == ""){
                $logText .= "{$data_key} [new]:{$data_value}\n";
            }else{
                $logText .= "{$data_key} [old]:{$old_dataArray[$data_key]}\n";
                $logText .= "{$data_key} [new]:{$data_value}\n";
            }
        }
    }
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 商品 END
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 最終
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    if($logText != ""){
        $logFirst = "---Keys\n";
        $logFirst .= "product_id:{$this->product_id}\n";
        $logFirst .= "---Fields\n";
        $logText = $logFirst.$logText;

        if($log_save_table === ''){
          $log_save_table = 'evav62122_audit';
        }else{
          $log_save_table = $log_save_table;
        }

        $auditLog = $log_save_table::create();
        $auditLog->ip = $_SERVER["REMOTE_ADDR"];
        $auditLog->user = $user_login;
        $auditLog->table = $log_table;
        $auditLog->action = $log_action;
        $auditLog->description = $logText;
        $auditLog->datetime = date("Y-m-d H:i:s");

        $auditLog->save();
    }
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 最終 END
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

    try {
      $this->save();
      return true;
    } catch (Exception $e) {
      return $e->getMessage();
    }

  }// end log_save

}// end class
