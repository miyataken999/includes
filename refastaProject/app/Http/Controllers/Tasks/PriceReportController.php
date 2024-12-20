<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use DB;
use App\Models\tb_price_group_by_month;

class PriceReportController
{
    public function set_month()
    {
        $selectraw = "
        date_format(resist_data_type_date, '%Y-%m') as date
        , MIN(IFNULL(0,gold)) as min_gold
        , AVG(gold) as avg_gold
        , MAX(gold) as max_gold

        , MIN(IFNULL(0,platinum)) as min_platinum
        , AVG(platinum) as avg_platinum
        , MAX(platinum) as max_platinum

        , MIN(IFNULL(0,silver)) as min_silver
        , AVG(silver) as avg_silver
        , MAX(silver) as max_silver

        , MIN(IFNULL(0,palladium)) as min_palladium
        , AVG(palladium) as avg_palladium
        , MAX(palladium) as max_palladium
        ";

        $data = DB::table("kinkai.tb_price")
            ->selectRaw($selectraw)
            ->groupBy(DB::raw("date_format(resist_data_type_date, '%Y-%m')"))
            ->get();
        //セレクト文　groupBy→項目をまとめ


        foreach($data as $item){
            $target_date = $item->date."-01 00:00:00";
            $target = tb_price_group_by_month::where("start_date",$target_date)->first();
            //first 一個だけとる
            if($target == false){
                $target = new tb_price_group_by_month;
            }
            $target->start_date = $target_date;
            $target->gold_min = $item->min_gold;
            $target->gold_avg = $item->avg_gold;
            $target->gold_max = $item->max_gold;

            $target->platinum_min = $item->min_platinum;
            $target->platinum_avg = $item->avg_platinum;
            $target->platinum_max = $item->max_platinum;

            $target->silver_min = $item->min_silver;
            $target->silver_avg = $item->avg_silver;
            $target->silver_max = $item->max_silver;

            $target->palladium_min = $item->min_palladium;
            $target->palladium_avg = $item->avg_palladium;
            $target->palladium_max = $item->max_palladium;
            $target->save();




        }//endforeach



    }

}