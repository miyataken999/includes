<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class GoldPrice_updateController extends Controller
{
    public function getJustTimeGoldPrice()
    {
        $tb_price = DB::table('kinkai.tb_price')
            ->select('kinkai.tb_price.gold', 'kinkai.tb_price.resist_data', 'kinkai.tb_price.platinum')
            ->orderBy('resist_data', 'desc')
            ->first();
        
            return response()->json([
                'gold_realtime_update' => number_format($tb_price->gold ?? 0),
                'platinum_realtime_update' => number_format($tb_price->platinum ?? 0),
                'resist_data_realtime_update' => date($tb_price->resist_data),
            ]);
        }
}
