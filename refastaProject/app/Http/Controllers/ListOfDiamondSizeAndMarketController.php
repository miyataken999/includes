<?php
namespace App\Http\Controllers;

use App\Models\Kinkai\Tb_diamond03;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Kinkai\Dia_perpiece_price;

class ListOfDiamondSizeAndMarketController extends Controller
{
    public function search_diamond_price_by_select(Request $request)
    {
        $dia_perpiece_price_array = Dia_perpiece_price::get();//dia_perpiece_priceテーブルのレコードを取得する
        $count = $dia_perpiece_price_array->count();

        // 最大金額の計算
        $max_items = Tb_diamond03::select('Excellent', 'ct')->where('clarity', 'VVS1')->where('color', 'D')->get();
        $MaxPrice = [];
        foreach ($max_items as $max_item) {
            $price = $max_item->Excellent;
            $MaxPrice[(string)$max_item->ct] = $price;
        }

        // 最安金額の計算
        $min_items = Tb_diamond03::select('Good', 'ct')->where('clarity', 'SI2')->where('color', 'J')->get();
        $MinPrice = [];
        foreach ($min_items as $item) {
            $price = $item->Good;
            $MinPrice[(string)$item->ct] = $price;
        }

        for ($i = 0; $i < $count; $i++) {
            // 最高
            $max_1ct_price = $MaxPrice[(string)$dia_perpiece_price_array[$i]->carat_base];
            $max_price = $max_1ct_price * ($dia_perpiece_price_array[$i]->carat + 0.009) * 1.1;
            $max_price = floor($max_price / 1000) * 1000;
            $dia_perpiece_price_array[$i]->max_price = $max_price;
            // 最安
            $ct_price = $MinPrice[(string)$dia_perpiece_price_array[$i]->carat_base];
            $min_price = $ct_price * ($dia_perpiece_price_array[$i]->carat);
            $min_price = floor($min_price / 1000) * 1000;
            $dia_perpiece_price_array[$i]->min_price = $min_price;
            // 平均
            $dia_perpiece_price_array[$i]->avg_price = floor(($max_price + $min_price) / 2);
        }
        $array = array(
            'dia_perpiece_price_array' => $dia_perpiece_price_array,
        );
        return view("list_of_diamond_size_and_market.index", $array);
    }
}