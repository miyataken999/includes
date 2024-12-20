<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use DB;
use App\Models\tb_price_group_by_month;

use App\Models\tb_price;

use App\Models\tb_gold;
use App\Models\tb_platinum;
use App\Models\tb_palladium;
use App\Models\tb_silver;
use App\Models\tb_combi;

use App\Models\tb_gold_rate_history;
use App\Models\tb_platinum_rate_history;
use App\Models\tb_palladium_rate_history;
use App\Models\tb_silver_rate_history;
use App\Models\tb_combi_rate_history;



// public $gold_arr = array(
//   array('dignity' => '24金', 'detail_dignity' => 'K24・純金<span>インゴット500g以上</span>', )
//   ,array('dignity' => '24金', 'detail_dignity' => 'K24・純金<span>インゴット100g以上</span>', )
//   ,array('dignity' => '24金', 'detail_dignity' => 'K24・999', )
//   ,array('dignity' => '23金', 'detail_dignity' => 'K23・958', )
//   ,array('dignity' => '22金', 'detail_dignity' => 'K22・916', )
//   ,array('dignity' => '21.6金', 'detail_dignity' => 'K21.6・900', )
//   ,array('dignity' => '20金', 'detail_dignity' => 'K20・835', )
//   ,array('dignity' => '18金', 'detail_dignity' => 'K18・750', )
//   ,array('dignity' => '17金', 'detail_dignity' => 'K17・700', )
//   ,array('dignity' => '14金', 'detail_dignity' => 'K14・585', )
//   ,array('dignity' => '12金', 'detail_dignity' => 'K12・500', )
//   ,array('dignity' => '10金', 'detail_dignity' => 'K10・416', )
//   ,array('dignity' => '9金', 'detail_dignity' => 'K9・375', )
//   ,array('dignity' => '8金', 'detail_dignity' => 'K8・333', )
//   ,array('dignity' => '5金', 'detail_dignity' => 'K5・210', )
// )



class PriceListController extends Controller
{
  public function make($gold_type)
  {
    $list_obj = new \stdClass;

    $tb_price_saisin = tb_price::orderBy('resist_data_type_date', 'desc')->first();
    if($tb_price_saisin->gold > 0){
      $list_obj->saisin_gold = number_format($tb_price_saisin->gold);
    }
    if($tb_price_saisin->platinum > 0){
      $list_obj->saisin_platinum = number_format($tb_price_saisin->platinum);
    }
    if($tb_price_saisin->palladium > 0){
      $list_obj->saisin_palladium = number_format($tb_price_saisin->palladium);
    }
    if($tb_price_saisin->silver > 0){
      $list_obj->saisin_silver = number_format($tb_price_saisin->silver);
    }

    if($gold_type == "saisin"){
      return json_encode($list_obj);
    }
    

    
    $before_date = date('Y/m/d', (strtotime(date('Ymd')) - 86400));
    $today_date = date('Y/m/d');
    $tb_price_before = tb_price::orderBy('resist_data_type_date', 'desc')->skip(1)->first();
    // $tb_gold_before = tb_gold_rate_history::whereDate('date', $today_date)->first();
    // $tb_platinum_before = tb_platinum_rate_history::whereDate('date', $today_date)->first();
    $tb_gold_before = tb_gold_rate_history::orderBy('date', "desc")->first();
    $tb_platinum_before = tb_platinum_rate_history::orderBy('date', "desc")->first();
    // $tb_palladium_before = tb_palladium_rate_history::whereDate('date', $before_date)->first();
    // $tb_silver_before = tb_silver_rate_history::whereDate('date', $before_date)->first();
    // $tb_combi_before = tb_combi_rate_history::whereDate('date', $before_date)->first();
    // var_dump($tb_price_before);
    $list_obj->update_date = date("Y年n月j日", strtotime($tb_gold_before->date));




    if(($gold_type == 'gold') or ($gold_type == 'price')){
      $type = "gold";
      $blade_file = "gold";
      $before_gold_price = $tb_price_before->gold;
      $tb_gold = tb_gold::get();
      foreach($tb_gold as $item){
        $gold_item = $item->gold_item;
        $gold_dif_item = $item->gold_item."_dif";
        $gold_ope_item = $item->gold_item."_ope";

        $list_obj->$gold_item = number_format(floor($item->gold_price)) . "円";
        $list_obj->$gold_dif_item = number_format(floor($item->gold_price) - floor(floor($before_gold_price) * $tb_gold_before->$gold_item)) . "円";
        if($list_obj->$gold_dif_item > 0){
          // $list_obj->$gold_ope_item = '<span class="plus_num">+</span>';
          $list_obj->$gold_ope_item = '+';
        }else{
          // $list_obj->$gold_ope_item = '<span class="minus_num">-</span>';
          $list_obj->$gold_ope_item = '';
        }
      }


    }
    
    if(($gold_type == 'platinum') or ($gold_type == 'price')){
      $type = "platinum";
      $blade_file = "platinum";
      $before_platinum_price = $tb_price_before->platinum;
      $tb_platinum = tb_platinum::get();
      foreach($tb_platinum as $item){
        $platinum_item = $item->platinum_item;
        $platinum_dif_item = $item->platinum_item."_dif";
        $platinum_ope_item = $item->platinum_item."_ope";

        $list_obj->$platinum_item = number_format(floor($item->platinum_price)) . "円";
        $list_obj->$platinum_dif_item = number_format(floor($item->platinum_price) - floor(floor($before_platinum_price) * $tb_platinum_before->$platinum_item)) . "円";
        if($list_obj->$platinum_dif_item > 0){
          // $list_obj->$platinum_ope_item = '<span class="plus_num">+</span>';
          $list_obj->$platinum_ope_item = '+';
        }else{
          // $list_obj->$platinum_ope_item = '<span class="minus_num">-</span>';
          $list_obj->$platinum_ope_item = '';
        }
      }
    }
    
    if(($gold_type == 'other') or ($gold_type == 'price')){
      $type = "other";
      $blade_file = "other";
      $tb_silver = tb_silver::get();
      
      foreach($tb_silver as $item){
        $silver_item = 'ag_'.$item->silver_item;
        
        $list_obj->$silver_item = number_format(floor($item->silver_price)) . "円";
      }

      $tb_palladium = tb_palladium::get();
      foreach($tb_palladium as $item){
        $palladium_item = 'pd_'.$item->palladium_item;
        $list_obj->$palladium_item = number_format(floor($item->palladium_price)) . "円";
      }


      $tb_gold_k18 = tb_gold::where('gold_item', 'k18')->first();
      $gold_rate = $tb_gold_k18->gold_price;
      $tb_platinum = tb_platinum::whereRaw('( (`platinum_item` = "pt900") or (`platinum_item` = "pt850") )')->get();
      foreach($tb_platinum as $item){
        $rate_name = $item->platinum_item."_rate";
        $$rate_name = $item->platinum_price;
      }

      $tb_combi = tb_combi::get();
      foreach($tb_combi as $item){
        $pt_rate = "";
        if(strpos($item->combi_item, "p900_") !== false){
          $pt_rate = $pt900_rate;
        }elseif(strpos($item->combi_item, "p850_") !== false){
          $pt_rate = $pt850_rate;
        }
        // var_dump($pt_rate);
        if(strpos($item->combi_item, "_p") !== false){
          $k_rate = 0.1;
          $p_rate = 0.9;
        }elseif(strpos($item->combi_item, "_h") !== false){
          $k_rate = 0.5;
          $p_rate = 0.5;
        }elseif(strpos($item->combi_item, "_k") !== false){
          $k_rate = 0.9;
          $p_rate = 0.1;
        }
        
        
        $combi_item = $item->combi_item;
        $list_obj->$combi_item = number_format(floor(floor(floor(floor($gold_rate) * $k_rate) + floor(floor($pt_rate) * $p_rate)) * $item->item_rate)) . "円";

        // $list_obj->$combi_item = floor(floor($gold_rate * $k_rate) + floor($pt_rate * $p_rate));
        
      }


    }
    // var_dump($list_obj);

    return json_encode($list_obj);

    // return view("price_list.{$blade_file}",[
    //   'list_obj' => $list_obj
    // ]);
  }
}