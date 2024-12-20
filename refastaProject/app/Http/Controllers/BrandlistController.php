<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Internet\Mst_brand;

class BrandlistController extends Controller
{
    public function get_brand(Request $request)
    {
        $mst_brand_data_first = Mst_brand::select('update_at')
            ->orderBy('update_at', 'desc')
            ->first();
        $mst_brand_total_count = Mst_brand::select('id')
            ->count();
        $kana_array = array("ア", "イ", "ウ", "エ", "オ", "カ", "キ", "ク", "ケ", "コ", "サ", "シ", "ス", "セ",
            "ソ", "タ", "チ", "ツ", "テ", "ト", "ナ", "ニ", "ヌ", "ネ", "ノ", "ハ", "ヒ", "フ", "へ", "ホ",
            "マ", "ミ", "ム", "メ", "モ", "ヤ", " ", "ユ", " ", "ヨ", "ラ", "リ", "ル", "レ", "ロ", "ワ", " ", "ヲ", " ", "ン");
        $display_kana_brands = array();
        foreach($kana_array as $kana){
            $display_kana_brands[$kana] = array();
        }

        $alphabet_array = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q",
            "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $display_alphabet_brands = array();
        foreach($alphabet_array as $alphabet){
            $display_alphabet_brands[$alphabet] = array();
        }

        if(!empty($_GET['head_word_kana'])) {
            $params = $request->except('page');
            $mst_brand_data = Mst_brand::select('id', 'name', 'bag', 'wallet', 'accessories', 'catch', 'shoes', 'outer', 'tops', 'camisole', 'bottoms', 'underwear', 'Legwear', 'hat', 'other', 'kids', 'cosmetics', 'gold', 'tableware', 'umbrella', 'season', 'kyouka' , 'luxury', 'is_all_gray')
                ->where('seach_j', '=', $_GET['head_word_kana'])
                ->orderBy('name')
                ->paginate(20)
                ->appends($params);
        } else if (!empty($_GET['head_word_alpha'])) {
            $params = $request->except('page');
            $mst_brand_data = Mst_brand::select('id', 'name', 'bag', 'wallet', 'accessories', 'catch', 'shoes', 'outer', 'tops', 'camisole', 'bottoms', 'underwear', 'Legwear', 'hat', 'other', 'kids', 'cosmetics', 'gold', 'tableware', 'umbrella', 'season', 'kyouka' , 'luxury', 'is_all_gray')
                ->where('name', 'LIKE', '%/' . $_GET['head_word_alpha'] . '%')
                ->orderBy('name')
                ->paginate(20)
                ->appends($params);
        } else {
            $mst_brand_data = new Mst_brand();
            if (!empty($_GET['keyword'])) {
                $mst_brand_data = $mst_brand_data->select('id', 'name', 'bag', 'wallet', 'accessories', 'catch', 'shoes', 'outer', 'tops', 'camisole', 'bottoms', 'underwear', 'Legwear', 'hat', 'other', 'kids', 'cosmetics', 'gold', 'tableware', 'umbrella', 'season', 'kyouka' , 'luxury', 'is_all_gray')
                    ->where(function ($mst_brand_data) {
                        $mst_brand_data->orWhere('name', 'LIKE', '%' . $_GET['keyword'] . '%');
                        $mst_brand_data->orWhere('japananese_name', 'LIKE', '%' . $_GET['keyword'] . '%');
                        $mst_brand_data->orWhere('mst_band_title', 'LIKE', '%' . $_GET['keyword'] . '%');
                    });
            }
            $params = $request->except('page');
            $mst_brand_data = $mst_brand_data->select('id', 'name', 'bag', 'wallet', 'accessories', 'catch', 'shoes', 'outer', 'tops', 'camisole', 'bottoms', 'underwear', 'Legwear', 'hat', 'other', 'kids', 'cosmetics', 'gold', 'tableware', 'umbrella', 'season', 'kyouka' , 'luxury', 'is_all_gray')
                ->orderBy('name')
                ->paginate(20)
                ->appends($params);
        }
        $mst_brand_data_count = count($mst_brand_data);
        for ($i=0; $i<$mst_brand_data_count; $i++) {
            $mst_brand_name = explode('/', $mst_brand_data[$i]->name, 2);
            $mst_brand_data[$i]->japananese_name = $mst_brand_name[0];
            $mst_brand_data[$i]->english_name = $mst_brand_name[1] ?? "";
        }

        $links = $mst_brand_data->links("vendor.pagination.custom");
        $links = str_replace("rifa.life/refastaProject/", "brandkaimasu.com/", $links);
        $links = str_replace("/get_brand", "", $links);
        $array = array(
            'mst_brand_data' => $mst_brand_data,
            'mst_brand_data_first' => $mst_brand_data_first,
            'mst_brand_data_count' => $mst_brand_data_count,
            'mst_brand_total_count' => $mst_brand_total_count,
            'links' => $links,
            'kana_array' => $kana_array,
            'alphabet_array' => $alphabet_array,
        );
        return view("brandlist.index", $array);
    }

    //view_all_brandのメソッド
    public function view_all_brand(Request $request)
    {
        $param = "kana";
        if (!empty($_GET['ctrl'])) {
            $param = $_GET['ctrl']=="abc" ? "abc" : "kana";
        }

        // \Cache::forget('mst_brands_html_abc');
        // \Cache::forget('mst_brands_html_kana');
        if ($param === 'abc') {
            $html = \Cache::get('mst_brands_html_abc', null);
        } else {
            $html = \Cache::get('mst_brands_html_kana', null);
        }
        if ($html !== null) {
            return $html;
        }

        $mst_brands = Mst_brand::select('id', 'seach_j', 'name', 'is_all_gray')->orderBy('name')->get();

        $kana_array = array("ア", "イ", "ウ", "エ", "オ", "カ", "キ", "ク", "ケ", "コ", "サ", "シ", "ス", "セ",
            "ソ", "タ", "チ", "ツ", "テ", "ト", "ナ", "ニ", "ヌ", "ネ", "ノ", "ハ", "ヒ", "フ", "へ", "ホ",
            "マ", "ミ", "ム", "メ", "モ", "ヤ", " ", "ユ", " ", "ヨ", "ラ", "リ", "ル", "レ", "ロ", "ワ", " ", "ヲ", " ", "ン");

        $alphabet_array = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q",
            "R", "S", "T", "U", "V", "W", "X", "Y", "Z");

        $display_kana_brands = array();
        foreach($kana_array as $kana){
            $display_kana_brands[$kana] = array();
        }
        foreach($mst_brands as $mst_brand)
        {
            $mst_brand_name = explode('/', $mst_brand->name, 2);
            $name = $mst_brand_name[0];
            $english_name = $mst_brand_name[1] ?? "";
            $display_kana_brands[$mst_brand->seach_j][] = [
                'id' => $mst_brand->id,
                'name' => $name,
                'english_name' => $english_name,
            ];
        }

        $display_alphabet_brands = array();
        foreach($alphabet_array as $alphabet){
            $display_alphabet_brands[$alphabet] = array();
        }
        foreach($mst_brands as $mst_brand)
        {
            $brand_name_array = explode('/', $mst_brand->name, 2);
            if (empty($brand_name_array[1])){
                continue;
            }
            $alpha_kashira = substr($brand_name_array[1], 0, 1);
            $alpha_kashira = strtoupper($alpha_kashira);

            $name = $brand_name_array[0];
            $english_name = $brand_name_array[1] ?? "";
            $display_alphabet_brands[$alpha_kashira][] = [
                'id' => $mst_brand->id,
                'name' => $name,
                'english_name' => $english_name,
            ];
        }

        $array = array(
            'mst_brands' => $mst_brands,
            'display_kana_brands' => $display_kana_brands,
            'display_alphabet_brands' => $display_alphabet_brands,
            'kana_array' => $kana_array,
            'alphabet_array' => $alphabet_array,
            'param' => $param,
        );


        $cache_minutes = 60*24;// 24h
        if ($param === 'abc') {
            $html = view("brandlist.all_brand", $array)->render();
            \Cache::put('mst_brands_html_abc', $html, $cache_minutes);
        } else {
            $html = view("brandlist.all_brand", $array)->render();
            \Cache::put('mst_brands_html_kana', $html, $cache_minutes);
        }
        return $html;
    }
}
