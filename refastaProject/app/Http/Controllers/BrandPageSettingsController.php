<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Internet\Brand_page_settings;
use App\Models\Internet\Brand_page_purchase_results;
use App\Models\Internet\Mst_brand;


class BrandPageSettingsController extends Controller
{
    public function index(Request $request)
    {
        //データを取得する工程
        //brand_page_settingsテーブルのデータを取得
        $brand_page_settings_url = $_GET['url'];
        $brand_page_settings_brand_id = $_GET['brand_id'];
        $brand_page_settings_data = Brand_page_settings::where('url', '=', $brand_page_settings_url)->where('brand_id', '=', $brand_page_settings_brand_id)->first();
        $brand_page_settings_data = json_decode($brand_page_settings_data, true);
        //mst_brandテーブルのデータを取得
        $brand_id = $brand_page_settings_data['brand_id'];
        $mst_brand_data = Mst_brand::where('id', '=', $brand_id)->first();
        $mst_brand_data = json_decode($mst_brand_data, true);
        //brand_page_purchase_resultsテーブルのデータを取得
        $brand_page_settings_id = $brand_page_settings_data['id'];
        $brand_page_purchase_results_data = Brand_page_purchase_results::where('brand_page_setting_id', '=', $brand_page_settings_id)->get();
        $brand_page_purchase_results_data_array = json_decode($brand_page_purchase_results_data, true);
        //brand_page_settingsテーブルのデータと配列
        $brand_page_settings_array = array(
            'url' => $brand_page_settings_data['url'],
            'brand_id' => $brand_id,
            'page_item_name' => $brand_page_settings_data['page_item_name'], //ページのキーワード
            'page_item_name_en' => $brand_page_settings_data['page_item_name_en'], //ファイル名
            'top_text' => $brand_page_settings_data['top_text'], //PCのh1設定
            'top_text_sp' => $brand_page_settings_data['top_text_sp'], //SPのh1
            'h1_size' => $brand_page_settings_data['h1_size'], //PC・ h1の文字調整（※単位は　rem）
            'content_second_word' => $brand_page_settings_data['content_second_word'], //.content_second_condition の右側のテキスト
        );
        //mst_brandテーブルのデータと配列
        $mst_brand_array = array(
            'name' => $mst_brand_data['name'],//name
            'eng_name' => $mst_brand_data['eng_name'],//eng_name
            'japananese_name' => $mst_brand_data['japananese_name'],//japananese_name
            'english_name' => $mst_brand_data['english_name'],//english_name
        );
        //brand_page_purchase_resultsテーブルのデータと配列
        $brand_page_purchase_results_array = array();
        //実積情報
        foreach($brand_page_purchase_results_data_array as $brand_page_purchase_results_data) {
            $brand_page_purchase_results_array[]= array(
                'title' => $brand_page_purchase_results_data['title'],
                'min_price' => $brand_page_purchase_results_data['min_price'],
                'max_price' => $brand_page_purchase_results_data['max_price'],
                'image_url' => $brand_page_purchase_results_data['image_url'],
                'kataban' => $brand_page_purchase_results_data['kataban'],
                'list_price' => $brand_page_purchase_results_data['list_price'],
                'camp_price' => $brand_page_purchase_results_data['camp_price'],
            );
        }
        $array = array(
            'brand_page_settings' => $brand_page_settings_array,
            'mst_brand' => $mst_brand_array,
            'brand_page_purchase_results' => $brand_page_purchase_results_array,
        );
        return response()->json($array, 200, [], JSON_UNESCAPED_UNICODE);
    }
}