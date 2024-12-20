<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class GoldPlatinumPrice_fv_updateController extends Controller
{
    public function getJustTimeGoldPlatinumPrice_fv()
    {

        // gold ingot_500over
        $tb_price_gold_ingot_500over = DB::table('kinkai.tb_gold')
            ->select('gold_price')
            ->where('gold_item', 'ingot_500over')
            ->first();

        $price_gold_ingot_500over = $tb_price_gold_ingot_500over->gold_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // gold ingot_100over
        $tb_price_gold_ingot_100over = DB::table('kinkai.tb_gold')
            ->select('gold_price')
            ->where('gold_item', 'ingot_100over')
            ->first();

        $price_gold_ingot_100over = $tb_price_gold_ingot_100over->gold_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // k24
        $tb_price_k24 = DB::table('kinkai.tb_gold')
            ->select('gold_price')
            ->where('gold_item', 'k24')
            ->first();

        $price_k24 = $tb_price_k24->gold_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // k22
        $tb_price_k22 = DB::table('kinkai.tb_gold')
            ->select('gold_price')
            ->where('gold_item', 'k22')
            ->first();

        $price_k22 = $tb_price_k22->gold_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // k18
        $tb_price_k18 = DB::table('kinkai.tb_gold')
            ->select('gold_price')
            ->where('gold_item', 'k18')
            ->first();

        $price_k18 = $tb_price_k18->gold_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // k14
        $tb_price_k14 = DB::table('kinkai.tb_gold')
            ->select('gold_price')
            ->where('gold_item', 'k14')
            ->first();

        $price_k14 = $tb_price_k14->gold_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // k10
        $tb_price_k10 = DB::table('kinkai.tb_gold')
            ->select('gold_price')
            ->where('gold_item', 'k10')
            ->first();

        $price_k10 = $tb_price_k10->gold_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // platinum ingot_500over
        $tb_price_platinum_ingot_500over = DB::table('kinkai.tb_platinum')
            ->select('platinum_price')
            ->where('platinum_item', 'ingot_500over')
            ->first();

        $price_platinum_ingot_500over = $tb_price_platinum_ingot_500over->platinum_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // platinum ingot_100over
        $tb_price_platinum_ingot_100over = DB::table('kinkai.tb_platinum')
            ->select('platinum_price')
            ->where('platinum_item', 'ingot_100over')
            ->first();

        $price_platinum_ingot_100over = $tb_price_platinum_ingot_100over->platinum_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // pt1000
        $tb_price_pt1000 = DB::table('kinkai.tb_platinum')
            ->select('platinum_price')
            ->where('platinum_item', 'pt1000')
            ->first();

        $price_pt1000 = $tb_price_pt1000->platinum_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // pt950
        $tb_price_pt950 = DB::table('kinkai.tb_platinum')
            ->select('platinum_price')
            ->where('platinum_item', 'pt950')
            ->first();

        $price_pt950 = $tb_price_pt950->platinum_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // pt900
        $tb_price_pt900 = DB::table('kinkai.tb_platinum')
            ->select('platinum_price')
            ->where('platinum_item', 'pt900')
            ->first();

        $price_pt900 = $tb_price_pt900->platinum_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // pt850
        $tb_price_pt850 = DB::table('kinkai.tb_platinum')
            ->select('platinum_price')
            ->where('platinum_item', 'pt850')
            ->first();

        $price_pt850 = $tb_price_pt850->platinum_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正

        // pt, pm
        $tb_price_pt_pm = DB::table('kinkai.tb_platinum')
            ->select('platinum_price')
            ->where('platinum_item', 'pt_pm')
            ->first();

        $price_pt_pm = $tb_price_pt_pm->platinum_price; // 仮のプロパティ名やメソッドを指定してオブジェクトから数値に修正


        return response()->json([
            'gold_ingot_500over_realtime_update' => number_format(floor($price_gold_ingot_500over ?? 0), 0, '.', ''),
            'gold_ingot_100over_realtime_update' => number_format(floor($price_gold_ingot_100over ?? 0), 0, '.', ''),
            'gold_k24_realtime_update' => number_format(floor($price_k24 ?? 0), 0, '.', ''),
            'gold_k22_realtime_update' => number_format(floor($price_k22 ?? 0), 0, '.', ''),
            'gold_k18_realtime_update' => number_format(floor($price_k18 ?? 0), 0, '.', ''),
            'gold_k14_realtime_update' => number_format(floor($price_k14 ?? 0), 0, '.', ''),
            'gold_k10_realtime_update' => number_format(floor($price_k10 ?? 0), 0, '.', ''),
            'platinum_ingot_500over_realtime_update' => number_format(floor($price_platinum_ingot_500over ?? 0), 0, '.', ''),
            'platinum_ingot_100over_realtime_update' => number_format(floor($price_platinum_ingot_100over ?? 0), 0, '.', ''),
            'platinum_pt1000_realtime_update' => number_format(floor($price_pt1000 ?? 0), 0, '.', ''),
            'platinum_pt950_realtime_update' => number_format(floor($price_pt950 ?? 0), 0, '.', ''),
            'platinum_pt900_realtime_update' => number_format(floor($price_pt900 ?? 0), 0, '.', ''),
            'platinum_pt850_realtime_update' => number_format(floor($price_pt850 ?? 0), 0, '.', ''),
            'platinum_pt_pm_realtime_update' => number_format(floor($price_pt_pm ?? 0), 0, '.', ''),
        ]);
    }
}