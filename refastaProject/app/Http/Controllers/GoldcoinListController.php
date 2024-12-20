<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Internet\Vw_goldcoin;

class GoldcoinListController extends Controller{

    /**
     * マスターデータを格納する
     */
    public $master_data = array();

    /**
     * ページ内容を作成する
     */
    public function index(Request $request)
    {
        $property_price = Vw_goldcoin::get_property_price(); // 本日の価格取得
        $master_data = Vw_goldcoin::get_master_data();
        $vw_goldcoin = Vw_goldcoin::orderBy("display_list_sort")->get(); // コインデータ取得
        $search_default_coin = array(); // 最終的に表示するコインデータの配列
        $last_updated_time = 0; // 最終更新日
        foreach($vw_goldcoin as $item)
        {
            $coin_array = array();
            $coin_array["coindata"] = $item; // blade側でそのままプロパティにアクセスできるようにしておく
            if($item->related_gold_coin_id == '') continue; // これがなければ計算できないので不要。
            // 更新日を最新のものにする
            if($last_updated_time < strtotime($item->update_at ?? '1970-01-01 00:00:00')){
                $last_updated_time = strtotime($item->update_at ?? '1970-01-01 00:00:00');
            }
            $create_title_result = $item->create_title($coin_array, $master_data); // 商品のタイトル生成
            $coin_array = $create_title_result['coin_array'];
            $gold_property_parents = $create_title_result['gold_property_parents'];
            $coin_array = $item->create_price($coin_array, $gold_property_parents, $property_price); // 金額計算
            $search_default_coin[] = $coin_array;
        }// end foreach
        $column_num = $this->get_column_num($request->header('User-Agent'));// 列数を取得する
        $array = array(
            'column_num' => $column_num,
            'search_default_coin' => $search_default_coin,
            'last_updated_time' => $last_updated_time,
        );
        return view("goldcoin_list.index", $array);
    }


    /**
     * 列数を取得する
     * pc：５列、sp：３列
     */
    public function get_column_num($user_agent)
    {
        if (
            (strpos($user_agent, 'iPhone') !== false) 
            || ((strpos($user_agent, 'Android') !== false) && (strpos($user_agent, 'Mobile') !== false))
            || (strpos($user_agent, 'Windows Phone') !== false)
            || (strpos($user_agent, 'BlackBerry') !== false) 
            || (strpos($user_agent, 'BB10') !== false)
            || (strpos($user_agent, 'Passport') !== false)
        ) 
        {
            $column_num = 3;
        }else{
            $column_num = 5;
        }
        return $column_num;
    }
}