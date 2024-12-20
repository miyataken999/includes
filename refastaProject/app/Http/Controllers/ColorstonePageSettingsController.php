<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Internet\Colorstone_settings;
use App\Models\Internet\Mst_colorstone;
class ColorstonePageSettingsController extends Controller
{
    public function index(Request $request)
    {
        //データを取得する工程
        //colorstone_page_settingsテーブルのデータを取得
        $colorstone_page_settings_url = $_GET['url'];
        $colorstone_page_settings_data = Colorstone_settings::where('url', '=', $colorstone_page_settings_url)->first();
        $colorstone_page_settings_data = json_decode($colorstone_page_settings_data, true);
        //mst_colorstoneテーブルのデータを取得
        $colorstone_id = $colorstone_page_settings_data['colorstone_id'];
        $mst_colorstone_data = Mst_colorstone::where('id', '=', $colorstone_id)->first();
        $mst_colorstone_data = json_decode($mst_colorstone_data, true);
        $page_item_name = $mst_colorstone_data['name'];
        //colorstone_page_settingsテーブルのデータと配列
        $colorstone_page_settings_array = array(
            'url' => $colorstone_page_settings_data['url'],
            'colorstone_id' => $colorstone_id,
            'page_item_name' => $page_item_name,
            'page_item_name_en' => $colorstone_page_settings_data['page_item_name_en'], //ファイル名
            'color_content' => $colorstone_page_settings_data['color_content'], //宝石の色設定
            'transparency_content' => $colorstone_page_settings_data['transparency_content'], //宝石の透明度設定
            'shine_content' => $colorstone_page_settings_data['shine_content'], //宝石の輝き設定
            'process_content' => $colorstone_page_settings_data['process_content'], //処理設定
            'effects_powers_content' => $colorstone_page_settings_data['effects_powers_content'], //石の持つ効果やパワー設定
            'areas_content' => $colorstone_page_settings_data['areas_content'], //宝石の主な産地設定
            'characteristics_content' => $colorstone_page_settings_data['characteristics_content'], //産地別特徴
            'historical_content' => $colorstone_page_settings_data['historical_content'], //歴史的背景
        );
        $array = array(
            'colorstone_page_settings' => $colorstone_page_settings_array,
        );
        return response()->json($array, 200, [], JSON_UNESCAPED_UNICODE);
    }
}