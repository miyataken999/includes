<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Internet\Brand_price;

class VuittonPricelistController extends Controller
{
    public function search_prices(Request $request)
    {
        $brand_price = new Brand_price();
        if(!empty($_GET['category'])){
            $brand_price = $brand_price->where('category', '=', $_GET['category']);
        }
        if(!empty($_GET['line'])){
            $brand_price = $brand_price->where('line', '=', $_GET['line']);
        }
        if(!empty($_GET['freeword'])){
            $brand_price = $brand_price->where(function($brand_price){
                $brand_price->orWhere('category', 'LIKE', '%'.$_GET['freeword'].'%');
                $brand_price->orWhere('line', 'LIKE', '%'.$_GET['freeword'].'%');
                $brand_price->orWhere('item_name', 'LIKE', '%'.$_GET['freeword'].'%');
                $brand_price->orWhere('color', 'LIKE', '%'.$_GET['freeword'].'%');
            });
        }
        $brand_price = $brand_price->get();
        $brand_price_count = count($brand_price);
        for ($i=0; $i<$brand_price_count; $i++) {
            $brand_price[$i]->line_price_n = number_format((int)($brand_price[$i]->list_price) * 0.9); //買取相場N
            $brand_price[$i]->line_price_s = number_format((int)($brand_price[$i]->list_price) * 0.8); //買取相場S
            $brand_price[$i]->line_price_a = number_format((int)($brand_price[$i]->list_price) * 0.7); //買取相場A
            $brand_price[$i]->line_price_b = number_format((int)($brand_price[$i]->list_price) * 0.5); //買取相場B
            $brand_price[$i]->line_price_c = number_format((int)($brand_price[$i]->list_price) * 0.3); //買取相場C
            $brand_price[$i]->line_price = number_format((int)($brand_price[$i]->list_price));
        }
        return response()->json($brand_price, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function get_price_by_kataban(Request $request)
    {
        if (empty($_GET['kataban'])) {
            return '';
        }
        //データを取得する工程
        //brand_priceテーブルのデータを取得
        $brand_price_data = Brand_price::where('kataban', '=', $_GET['kataban'])->first();
        if($brand_price_data === null){
            return '';
        }
        $brand_price_list_price = $brand_price_data->list_price;
        $brand_price_line_price_n = $brand_price_list_price * 0.9; //買取相場N
        $brand_price_line_price_s = $brand_price_list_price * 0.8; //買取相場S
        $brand_price_line_price_a = $brand_price_list_price * 0.7; //買取相場A
        $brand_price_line_price_b = $brand_price_list_price * 0.5; //買取相場B
        $brand_price_line_price_c = $brand_price_list_price * 0.3; //買取相場C
        //配列に格納
        $array = array(
            'brand_price_data' => $brand_price_data,
            'brand_price_list_price' => number_format((int)$brand_price_list_price),
            'brand_price_line_price_n' => number_format((int)$brand_price_line_price_n),
            'brand_price_line_price_s' => number_format((int)$brand_price_line_price_s),
            'brand_price_line_price_a' => number_format((int)$brand_price_line_price_a),
            'brand_price_line_price_b' => number_format((int)$brand_price_line_price_b),
            'brand_price_line_price_c' => number_format((int)$brand_price_line_price_c),
        );
        return response()->json($array, 200, [], JSON_UNESCAPED_UNICODE);
    }
}