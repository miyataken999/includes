<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\tb_price_group_by_month;

class Tb_priceController extends Controller
{
    function get_tb_price_data($item)
    {
        if($item === "k18"){
            $tb_gold = DB::table("kinkai.tb_gold")
                ->where("gold_item", "k18")
                ->first();
            if($tb_gold != false){
                return number_format(floor($tb_gold->gold_price))."円";
            }
        }elseif($item === "pt900"){
            $tb_platinum = DB::table("kinkai.tb_platinum")
                ->where("platinum_item", "pt900")
                ->first();
            if($tb_platinum != false){
                return number_format(floor($tb_platinum->platinum_price))."円";
            }
        }elseif($item === "heads"){
            $tb_price = DB::table("kinkai.tb_price")
                ->orderBy("resist_data_type_date", "desc")
                ->first();
            if($tb_price != false){
                if($tb_price->gold > 0){
                    $tb_price->gold = number_format(floor($tb_price->gold))."円"; 
                }
                if($tb_price->platinum > 0){
                    $tb_price->platinum = number_format(floor($tb_price->platinum))."円"; 
                }
                if($tb_price->silver > 0){
                    $tb_price->silver = number_format(floor($tb_price->silver))."円"; 
                }
                if($tb_price->palladium > 0){
                    $tb_price->palladium = number_format(floor($tb_price->palladium))."円"; 
                }
                return json_encode($tb_price);
            }
        }

        return "";

    }

}
