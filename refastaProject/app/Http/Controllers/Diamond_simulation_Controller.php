<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;
use App\Models\diamondSimulation;
use App\Models\tb_gold;

class Diamond_simulation_Controller extends Controller
{
    public function index(Request $request)
    {
        return view('diamond_simulation.tools');
    }
    public function make_tool(Request $request)
    {
        // 金全データ取得
        $gold_price = tb_gold::get();
        $gold_k24 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k24'");
        $gold_k22 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k22'");
        $gold_k21 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k21'");
        $gold_k20 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k20'");
        $gold_k18 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k18'");
        $gold_k14 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k14'");
        $gold_k10 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k10'");
        $gold_k9 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k9'");
        $gold_k23 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k23'");
        $gold_k17 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k17'");
        $gold_k12 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k12'");
        $gold_k8 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k8'");
        $gold_k5 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `gold_item` = 'k5'");
        // プラチナ全データ取得
        $platinum_price = DB::table("kinkai.tb_platinum")->get();
        $platinum_pt1000 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt1000'");
        $platinum_pt950 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt950'");
        $platinum_pt900 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt900'");
        $platinum_pt850 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt850'");
        $platinum_pt_pm = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `platinum_item` = 'pt_pm'");

        // パラジウム全データ取得
         $palladium_price = DB::table("kinkai.tb_palladium")->get();
         $palladium_pd1000 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd1000'");
         $palladium_pd950 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd950'");
         $palladium_pd900 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd900'");
         $palladium_pd500 = DB::select("SELECT `palladium_price` FROM `kinkai`.`tb_palladium` WHERE `palladium_item` = 'pd500'");

         // シルバー全データ取得 
         $silver_sv1000 = DB::select("SELECT `silver_price` FROM `kinkai`.`tb_silver` WHERE `silver_item` = 'sv1000'");
         $silver_sv925 = DB::select("SELECT `silver_price` FROM `kinkai`.`tb_silver` WHERE `silver_item` = 'sv925'");
         $silver_sv900 = DB::select("SELECT `silver_price` FROM `kinkai`.`tb_silver` WHERE `silver_item` = 'sv900'");
        // 歯科材のデータ取得、計算
        $calc_price = DB::select("SELECT `gold`, `palladium`, `silver` FROM `kinkai`.`tb_price` ORDER BY `id` DESC LIMIT 1");
        $dental_palladium_rate = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_palladium` WHERE `id` = '2'");

        $calc_1 = floor((round(($calc_price[0]->gold)*0.985)+2)*0.12);
        $calc_2 = floor((round(($calc_price[0]->palladium+1)*0.98)+1)*0.2);
        $calc_3 = floor(round($calc_price[0]->silver*0.9354)*0.45);
        $calc_4 = floor(round(($calc_1+$calc_2+$calc_3)-200) * $dental_palladium_rate[0]->item_rate);
        $calc_4 = floor($calc_4 * 10000) / 10000;
          
        //  コンビ計算用データ取得
        $gold_price_k18 = DB::select("SELECT `gold_price` FROM `kinkai`.`tb_gold` WHERE `id` = '7'");
        $platinum_price_pt900 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `id` = '5'");
        $platinum_price_pt850 = DB::select("SELECT `platinum_price` FROM `kinkai`.`tb_platinum` WHERE `id` = '6'");

        //  コンビ計算用レート取得
        $c_rate_pt900_p = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '1'");
        $c_rate_pt900_h = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '2'");
        $c_rate_pt900_k = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '3'");
        $c_rate_pt850_p = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '4'");
        $c_rate_pt850_h = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '5'");
        $c_rate_pt850_k = DB::select("SELECT `item_rate` FROM `kinkai`.`tb_combi` WHERE `id` = '6'");

        // コンビの計算

        $pt900_p =  floor(floor($gold_price_k18[0]->gold_price) * 0.1) + floor(floor($platinum_price_pt900[0]->platinum_price) * 0.9);
        $pt900_p =  floor($pt900_p * $c_rate_pt900_p[0]->item_rate);
        $pt900_h =  floor(floor($gold_price_k18[0]->gold_price) / 2) + floor(floor($platinum_price_pt900[0]->platinum_price) / 2);
        $pt900_h =  floor($pt900_h * $c_rate_pt900_h[0]->item_rate);
        $pt900_k =  floor(floor($gold_price_k18[0]->gold_price) * 0.9) + floor(floor($platinum_price_pt900[0]->platinum_price) * 0.1);
        $pt900_k =  floor($pt900_k * $c_rate_pt900_k[0]->item_rate);

        $pt850_p =  floor(floor($gold_price_k18[0]->gold_price) * 0.1) + floor(floor($platinum_price_pt850[0]->platinum_price) * 0.9);
        $pt850_p =  floor($pt850_p * $c_rate_pt850_p[0]->item_rate);
        $pt850_h =  floor(floor($gold_price_k18[0]->gold_price) / 2) + floor(floor($platinum_price_pt850[0]->platinum_price) / 2);
        $pt850_h =  floor($pt850_h * $c_rate_pt850_h[0]->item_rate);
        $pt850_k =  floor(floor($gold_price_k18[0]->gold_price) * 0.9) + floor(floor($platinum_price_pt850[0]->platinum_price) * 0.1);
        $pt850_k =  floor($pt850_k * $c_rate_pt850_k[0]->item_rate);

        // 配列に入れる
        $array = array();
        $diamondInfo = $this->get_DiamondInfo($request);
        if (!is_array($diamondInfo)) {
            $diamondInfo = []; 
        }
        $resultInfo = $this->get_result($request);
        if (!is_array($resultInfo)) {
            $resultInfo = []; 
        }
        $get_4c = $this->get_4c();
        $array = array_merge($diamondInfo, $resultInfo,$get_4c);

        
        $array["gold_price"] = $gold_price;
        $array["palladium_price"] = $palladium_price;
        $array["platinum_price"] = $platinum_price;
        $array["pt900_p"] = $pt900_p;
        $array["pt900_h"] = $pt900_h;
        $array["pt900_k"] = $pt900_k;
        $array["pt850_p"] = $pt850_p;
        $array["pt850_h"] = $pt850_h;
        $array["pt850_k"] = $pt850_k;
        $array["calc_4"] = $calc_4;
        $array["gold_k24"] = $gold_k24;
        $array["gold_k22"] = $gold_k22;
        $array["gold_k21"] = $gold_k21;
        $array["gold_k20"] = $gold_k20;
        $array["gold_k18"] = $gold_k18;
        $array["gold_k14"] = $gold_k14;
        $array["gold_k10"] = $gold_k10;
        $array["gold_k9"] = $gold_k9;
        $array["gold_k23"] = $gold_k23;
        $array["gold_k17"] = $gold_k17;
        $array["gold_k12"] = $gold_k12;
        $array["gold_k8"] = $gold_k8;
        $array["gold_k5"] = $gold_k5;
        $array["platinum_pt1000"] = $platinum_pt1000;
        $array["platinum_pt950"] = $platinum_pt950;
        $array["platinum_pt900"] = $platinum_pt900;
        $array["platinum_pt850"] = $platinum_pt850;
        $array["platinum_pt_pm"] = $platinum_pt_pm;
        $array["palladium_pd1000"] = $palladium_pd1000;
        $array["palladium_pd950"] = $palladium_pd950;
        $array["palladium_pd900"] = $palladium_pd900;
        $array["palladium_pd500"] = $palladium_pd500;
        $array["silver_sv1000"] = $silver_sv1000;
        $array["silver_sv925"] = $silver_sv925;
        $array["silver_sv900"] = $silver_sv900;

        return view('diamond_simulation.index', $array);
    }

    public function get_DiamondInfo(Request $request){
        // Requestデータ
        $carat = $request->input('input_ct');
        $inputcarat = $request->input('input_ct');
        $color = $request->input('sel_color');
        $cut = strtoupper($request->input('sel_cut')); 
        $shape = strtoupper($request->input('sel_shape'));
        $clarity = $request->input('sel_clarity');
        $fluor = $request->input('sel_fluor');
        $labo = $request->input('sel_labo');
        $selectedyear = $request->input('sel_year');
        $jiganevalue = $request->input('jigane_name');
        $jiganegram = $request->input('jigane_gram');

        // チェックボックスがチェックされている場合の処理
        $isChecked = filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN);
        $inputcarat_min = 0;
        $inputcarat_max = 0;
        if ($isChecked === true) { 
            if ($inputcarat == "0.199") {
                $carat = "0.2"; 
                $inputcarat_min = 0.001;
                $inputcarat_max = 0.199;
            } elseif ($inputcarat == "0.299") {
                $carat = "0.2"; 
                $inputcarat_min = 0.2;
                $inputcarat_max = 0.299;
            } elseif ($inputcarat == "0.399") {
                $carat = "0.3"; 
                $inputcarat_min = 0.3;
                $inputcarat_max = 0.399;
            } elseif ($inputcarat == "0.499") {
                $carat = "0.4"; 
                $inputcarat_min = 0.4;
                $inputcarat_max = 0.499;
            }elseif ($inputcarat == "0.599") {
                $carat = "0.5"; 
                $inputcarat_min = 0.5;
                $inputcarat_max = 0.599;
            }elseif ($inputcarat == "0.699") {
                $carat = "0.6"; 
                $inputcarat_min = 0.6;
                $inputcarat_max = 0.699;
            }elseif ($inputcarat == "0.799") {
                $carat = "0.7"; 
                $inputcarat_min = 0.7;
                $inputcarat_max = 0.799;
            }elseif ($inputcarat == "0.899") {
                $carat = "0.8"; 
                $inputcarat_min = 0.8;
                $inputcarat_max = 0.899;
            }elseif ($inputcarat == "0.999") {
                $carat = "0.9"; 
                $inputcarat_min = 0.9;
                $inputcarat_max = 0.999;
            }elseif ($inputcarat == "1.499") {
                $carat = "1"; 
                $inputcarat_min = 1;
                $inputcarat_max = 1.499;
            }elseif ($inputcarat == "1.999") {
                $carat = "1.5"; 
                $inputcarat_min = 1.5;
                $inputcarat_max = 1.999;
            }elseif ($inputcarat == "2.999") {
                $carat = "2"; 
                $inputcarat_min = 2;
                $inputcarat_max = 2.999;
            }elseif ($inputcarat == "3.999") {
                $carat = "3"; 
                $inputcarat_min = 3;
                $inputcarat_max = 3.999;
            }elseif ($inputcarat == "4.999") {
                $carat = "4"; 
                $inputcarat_min = 4;
                $inputcarat_max = 4.999;
            }elseif ($inputcarat == "5.999") {
                $carat = "5"; 
                $inputcarat_min = 5;
                $inputcarat_max = 5.999;
            }elseif ($inputcarat == "6.999") {
                $carat = "6"; 
                $inputcarat_min = 6;
                $inputcarat_max = 6.999;
            }elseif ($inputcarat == "7.999") {
                $carat = "7"; 
                $inputcarat_min = 7;
                $inputcarat_max = 7.999;
            }elseif ($inputcarat == "8.999") {
                $carat = "8"; 
                $inputcarat_min = 8;
                $inputcarat_max = 8.999;
            }elseif ($inputcarat == "9.999") {
                $carat = "9"; 
                $inputcarat_min = 9;
                $inputcarat_max = 9.999;
            }elseif ($inputcarat == "10.999") {
                $carat = "10"; 
                $inputcarat_min = 10;
                $inputcarat_max = 10.999;
            }
        }

        // カラット数の判断
        if ($carat >= 0.20 && $carat <= 0.299) { 
            $carat = "0.2"; 
        }elseif ($carat >= 0.30 && $carat <= 0.399) { 
            $carat = "0.3"; 
        }elseif ($carat >= 0.40 && $carat <= 0.499) { 
            $carat = "0.4"; 
        }elseif ($carat >= 0.50 && $carat <= 0.599) { 
            $carat = "0.5"; 
        }elseif ($carat >= 0.60 && $carat <= 0.699) { 
            $carat = "0.6"; 
        }elseif ($carat >= 0.70 && $carat <= 0.799) { 
            $carat = "0.7"; 
        }elseif ($carat >= 0.80 && $carat <= 0.899) { 
            $carat = "0.8"; 
        }elseif ($carat >= 0.90 && $carat <= 0.999) { 
            $carat = "0.9"; 
        }elseif ($carat >= 1 && $carat <= 1.499) { 
            $carat = "1"; 
        }elseif ($carat >= 1.5 && $carat <= 1.999) { 
            $carat = "1.5"; 
        }elseif ($carat >= 2 && $carat <= 2.999) { 
            $carat = "2"; 
        }elseif ($carat >= 3 && $carat <= 3.999) { 
            $carat = "3"; 
        }elseif ($carat >= 4 && $carat <= 4.999) { 
            $carat = "4"; 
        }elseif ($carat >= 5 && $carat <= 5.999) { 
            $carat = "5"; 
        }elseif ($carat >= 6 && $carat <= 6.999) { 
            $carat = "6"; 
        }elseif ($carat >= 7 && $carat <= 7.999) { 
            $carat = "7"; 
        }elseif ($carat >= 8 && $carat <= 8.999) { 
            $carat = "8"; 
        }elseif ($carat >= 9 && $carat <= 9.999) { 
            $carat = "9"; 
        }elseif ($carat >= 10 && $carat <= 10.999) { 
            $carat = "10"; 
        }

        // クラリティのグルプ化
        $groupsClarity = diamondSimulation::GROUPS_CLARITY_COEFFICIENTS;
        $clarityPrice = '';
        foreach ($groupsClarity as $clarityKey => $clarityValues) {
            if (array_key_exists($clarity, $clarityValues)) { 
                $clarityPrice = $clarityValues[$clarity]; 
                break;
            }
        }
        foreach ($groupsClarity as $clarityKey => $clarityValues) {
            if (in_array($clarity, array_keys($clarityValues))) { 
                $clarity = $clarityKey; 
                break;
            }
        }

        // カラーのグルプ化
        $groupsColor = [
            'fancy'=>['PINK','YELLOW','BLUE','RED','GREEN','PURPLE','ORANGE','VIOLET','GRAY','BLACK','CAMPAGNE','COGNAC','CHAMELEON','WHITE','BY COLOR','GRAYNISH YELLOW','BROWN','OTHER'],
        ];
        foreach ($groupsColor as $ColorKey => $ColorValues) {
            if (in_array($color, $ColorValues)) {
                $color = $ColorKey;
                break;
            }
        }
        // DBからrapaの4c価格の取得
        $tb_diamond03 = DB::table("kinkai.tb_diamond03")
                        ->where('ct',$carat)
                        ->where('color', 'LIKE', $color)
                        ->where('clarity', 'LIKE', $clarity)
                        ->first();

        // レコードが見つからない場合は、エラーを返す
        if (!$tb_diamond03) {
            return response()->json(['error' => '一致するダイヤモンド情報が見つかりませんでした。'], 404);
        }

        // カットのグルプ化
        $groupsCut = diamondSimulation::GROUPS_CUT_COEFFICIENTS;
        $Price4c = null;
        $cutPrice = '';
        foreach ($groupsCut as $cutKey => $cutValues) {
            if (array_key_exists($cut, $cutValues)) { 
                $cutPrice = $cutValues[$cut]; 
                break;
            }
        }
        foreach ($groupsCut as $cutKey => $cutValues) {
            if (in_array($cut, array_keys($cutValues))) { 
                $cutColumnName = $cutKey; 
                $Price4c = $tb_diamond03->$cutColumnName; 
                break;
            }
        }
        // シェイプ
        $shapePrice = 1;
        foreach ($groupsCut as $cutKey => $cutValues) {
            if (array_key_exists($shape, $cutValues)) { 
                $shapePrice = $cutValues[$shape]; 
                break;
            }
        }
        // 蛍光の係数
        $groupsFluor = diamondSimulation::FLUORESCENCE_COEFFICIENTS;
        $fluorPrice = 1;
        foreach ($groupsFluor as $fluorKey => $fluorValues) {
            if ($fluor === $fluorKey) { 
                $fluorPrice = $fluorValues;
                break;
            }
        }

        $groupslabo = diamondSimulation::APPRAISER_COEFFICIENTS;
        $laboPrice = 1;
        foreach ($groupslabo as $laboKey => $laboValues) {
            if ($labo === $laboKey) { 
                $laboPrice = $laboValues;
                break;
            }
        }
        $groupsYear = diamondSimulation::ISSUE_YEAR_COEFFICIENTS;
        $yearPrice = 1;
        foreach ($groupsYear as $yearKey => $yearValues) {
            if ($selectedyear === $yearKey) { 
                $yearPrice = $yearValues;
                break;
            }
        }
        // Rapaの4c価格と係数の値計算
        $Price4c = floatval($Price4c);
        $clarityPrice = floatval($clarityPrice);
        $cutPrice = floatval($cutPrice);
        $shapePrice = floatval($shapePrice);
        $fluorPrice = floatval($fluorPrice);
        $laboPrice = floatval($laboPrice);
        $yearPrice = floatval($yearPrice);
        $inputcarat = floatval($inputcarat);
        $inputcarat_min = floatval($inputcarat_min);
        $inputcarat_max = floatval($inputcarat_max);

        //確認のため
        // var_dump('カラット数(ct)::',$carat); echo "<br>";
        // var_dump('カラット数inputcarat(ct)::',$inputcarat); echo "<br>";
        // var_dump('カラー(色)::',$color); echo "<br>";
        // var_dump('クラリティ(内包物)::',$clarity); echo "<br>";
        // var_dump('clarity::', $clarity); echo "<br>";
        // var_dump('カットの総合評価::',$Price4c); echo "<br>";
        // var_dump('係数clarityPrice::', $clarityPrice); echo "<br>";
        // var_dump('係数cutPrice::', $cutPrice); echo "<br>";
        // var_dump('シェイプ（Shape）::', $shapePrice); echo "<br>";
        // var_dump('fluorPrice::', $fluorPrice); echo "<br>";
        // var_dump('laboPrice::', $laboPrice); echo "<br>";
        // var_dump('yearPrice::', $yearPrice); echo "<br>";
        // var_dump('jiganevalue::', $jiganevalue); echo "<br>";
        // var_dump('jiganegram::', $jiganegram); echo "<br>";
        
        $calculate_4c_Coefficient_price_min = ($Price4c * $inputcarat_min) * $clarityPrice * $cutPrice * $shapePrice * $fluorPrice * $laboPrice * $yearPrice + ($jiganevalue * $jiganegram);
        $calculate_4c_Coefficient_price_max = ($Price4c * $inputcarat_max) * $clarityPrice * $cutPrice * $shapePrice * $fluorPrice * $laboPrice * $yearPrice + ($jiganevalue * $jiganegram);
        $calculate_4c_Coefficient_price = ($Price4c * $inputcarat) * $clarityPrice * $cutPrice * $shapePrice * $fluorPrice * $laboPrice * $yearPrice + ($jiganevalue * $jiganegram);

        $array["Price4c"] = $Price4c;
        $array["calculate_4c_Coefficient_price"] = $calculate_4c_Coefficient_price;
        $array["calculate_4c_Coefficient_price_min"] = $calculate_4c_Coefficient_price_min;
        $array["calculate_4c_Coefficient_price_max"] = $calculate_4c_Coefficient_price_max;

        return $array;
    }
    public function get_4c(){
        $colors_option = DB::table("internet._DIA_DA_COLOR")->where('name', 'NOT LIKE', 'N')->orderBy('sort')->pluck('name');
        $claritys_option = DB::table("internet._DIA_DA_CLARITY")->orderBy('sort')->pluck('name');
        $cuts_option = DB::table("internet._DIA_DA_CUT")->orderBy('sort')->pluck('name');
        $fluor_option = DB::table("internet._DIA_DA_FLUO")->orderBy('sort')->pluck('name');
        $labo_option = DB::table("internet._DIA_DA_APPRAISER")->orderBy('sort')->pluck('wa_name');

        $claritys_option_formatted = [];
        foreach ($claritys_option as $clarity) {
            // "-" を削除して形式を変換
            $formatted_clarity = str_replace('-', '', $clarity);
            $claritys_option_formatted[] = $formatted_clarity;
        }

        $array["colors_option"] = $colors_option;
        $array["claritys_option"] = $claritys_option_formatted;
        $array["cuts_option"] = $cuts_option;
        $array["fluor_option"] = $fluor_option;
        $array["labo_option"] = $labo_option;

        return $array;
    }
    public function get_result(Request $request){
        // 検索キー
        $ct = $request->input('input_ct');
        $color = $request->input('sel_color');
        $cut = $request->input('sel_cut');
        $clarity = $request->input('sel_clarity');
        $sel_insi = $request->input('sel_insi');
        $sel_ovto = $request->input('sel_ovto');
        $sel_fluor = $request->input('sel_fluor');
        $sel_shape = $request->input('sel_shape');
        // $sel_cofl = $request->input('sel_cofl');

        $PMT_EV001 = DB::table("internet.PMT_EV001")
                    ->join('internet.mst_color_fluo', 'internet.PMT_EV001.DA_COLOR_FLUO', '=', 'internet.mst_color_fluo.sort')
                    ->orderBy("DA_THESPECIFICATIONSSETTINGSUN", "desc")
                    ->whereRaw("STR_TO_DATE(DA_THESPECIFICATIONSSETTINGSUN, '%Y-%m-%d') IS NOT NULL")
                    ->orderByRaw("STR_TO_DATE(DA_THESPECIFICATIONSSETTINGSUN, '%Y-%m-%d') desc")
                    ->select('internet.PMT_EV001.*', 'internet.mst_color_fluo.name as DA_COLOR_FLUO');

        $ctRanges = [
            ['min' => 0, 'max' => 0.299],
            ['min' => 0.30, 'max' => 0.399],
            ['min' => 0.40, 'max' => 0.499],
            ['min' => 0.50, 'max' => 0.599],
            ['min' => 0.60, 'max' => 0.699],
            ['min' => 0.70, 'max' => 0.799],
            ['min' => 0.80, 'max' => 0.899],
            ['min' => 0.90, 'max' => 0.999],
            ['min' => 1, 'max' => 1.999], 
            ['min' => 2, 'max' => 2.999],
            ['min' => 3, 'max' => 3.999],
            ['min' => 4, 'max' => 4.999],
            ['min' => 5, 'max' => 5.999],
            ['min' => 6, 'max' => 6.999],
            ['min' => 7, 'max' => 7.999],
            ['min' => 8, 'max' => 8.999],
            ['min' => 9, 'max' => 9.999],
            ['min' => 10, 'max' => 10.999],
        ];

        /////////////////////////////////////////////////////////////
        // 4Cでの条件で絞り込みを行う
        if (empty($sel_shape)&& $ct && $color && $cut && $clarity) {
            foreach ($ctRanges as $range) {
                if ($ct >= $range['min'] && $ct <= $range['max']) {
                    $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                    break;
                }
            }
            if ($cut) {
                $PMT_EV001->where('DA_CUT', 'LIKE', $cut);
            }
            if ($color) {
                $PMT_EV001->where('DA_COLOR', 'LIKE', $color);
            }
            if ($clarity) {
                $clarity_with_minus = str_replace(['VS1', 'VVS1', 'VS2', 'VVS2', 'SI1', 'SI2', 'SI3', 'I1', 'I2', 'I3'], 
                                                ['VS-1', 'VVS-1', 'VS-2', 'VVS-2', 'SI-1', 'SI-2', 'SI-3', 'I-1', 'I-2', 'I-3'], 
                                                $clarity);
                $PMT_EV001->where('DA_CLARITY', 'LIKE' , $clarity_with_minus);
            }
        }
        else if($sel_shape === 'ROUND' && $ct && $color && $cut && $clarity) {
            foreach ($ctRanges as $range) {
                if ($ct >= $range['min'] && $ct <= $range['max']) {
                    $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                    break;
                }
            }
            if ($cut) {
                $PMT_EV001->where('DA_CUT', 'LIKE', $cut);
            }
            if ($color) {
                $PMT_EV001->where('DA_COLOR', 'LIKE', $color);
            }
            if ($clarity) {
                $clarity_with_minus = str_replace(['VS1', 'VVS1', 'VS2', 'VVS2', 'SI1', 'SI2', 'SI3', 'I1', 'I2', 'I3'], 
                                                ['VS-1', 'VVS-1', 'VS-2', 'VVS-2', 'SI-1', 'SI-2', 'SI-3', 'I-1', 'I-2', 'I-3'], 
                                                $clarity);
                $PMT_EV001->where('DA_CLARITY', 'LIKE' , $clarity_with_minus);
            }
        }
        else if ($sel_shape !== 'ROUND' && !empty($sel_shape) && $ct && $color && $sel_shape && $clarity) {
            foreach ($ctRanges as $range) {
                if ($ct >= $range['min'] && $ct <= $range['max']) {
                    $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                    break;
                }
            }
            if ($sel_shape) {
                $PMT_EV001->where('DA_CUT', 'LIKE', $sel_shape);
            }
            if ($color) {
                $PMT_EV001->where('DA_COLOR', 'LIKE', $color);
            }
            if ($clarity) {
                $clarity_with_minus = str_replace(['VS1', 'VVS1', 'VS2', 'VVS2', 'SI1', 'SI2', 'SI3', 'I1', 'I2', 'I3'], 
                                                ['VS-1', 'VVS-1', 'VS-2', 'VVS-2', 'SI-1', 'SI-2', 'SI-3', 'I-1', 'I-2', 'I-3'], 
                                                $clarity);
                $PMT_EV001->where('DA_CLARITY', 'LIKE' , $clarity_with_minus);
            }
        }
        // カラット、カラー、シェイプで絞り込みを行う
        else if ($ct && $color && $sel_shape){    
            foreach ($ctRanges as $range) {
                if ($ct >= $range['min'] && $ct <= $range['max']) {
                    $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                    break;
                }
            }
            $PMT_EV001->where('DA_COLOR', 'LIKE', $color)
                        ->where('DA_CUT', 'LIKE', $sel_shape);
        }
        //　シェイプ、カラーで絞り込みを行う
        else if ($sel_shape && $color) { 
            $PMT_EV001->where('DA_CUT', 'LIKE', $sel_shape)
                        ->where('DA_COLOR', 'LIKE', $color);
        }
        //　カラット、カラーで絞り込みを行う
        else if($ct && $color) {  
            foreach ($ctRanges as $range) {
                if ($ct >= $range['min'] && $ct <= $range['max']) {
                    $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                    break;
                }
            }
            $PMT_EV001->where('DA_COLOR', 'LIKE', $color);
        } 
        //　カラット、シェイプで絞り込みを行う
        else if ($ct && $sel_shape) { 
            foreach ($ctRanges as $range) {
                if ($ct >= $range['min'] && $ct <= $range['max']) {
                    $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                    break;
                }
            }
            $PMT_EV001->where('DA_CUT', 'LIKE', $sel_shape);

        } 
        // カラット、カラー、インシル、オーバルで絞り込みを行う
        else if ($ct && $color && $sel_insi && $sel_ovto ) { 
            foreach ($ctRanges as $range) {
                if ($ct >= $range['min'] && $ct <= $range['max']) {
                    $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                    break;
                }
            }
            $PMT_EV001->where('DA_COLOR', 'LIKE', $color)
                        ->where('INSI_waname', 'LIKE', $sel_insi)
                        ->where('OVTO_waname', 'LIKE', $sel_ovto);

        } 
        // カラー、インシル、オーバルで絞り込みを行う
        else if ($color && $sel_insi && $sel_ovto ) { 
            $PMT_EV001->where('DA_COLOR', 'LIKE', $color)
                        ->where('INSI_waname', 'LIKE', $sel_insi)
                        ->where('OVTO_waname', 'LIKE', $sel_ovto);

        } 
        // 一つだけの条件で絞り込みを行う
        else {   
            if ($ct) {  
                foreach ($ctRanges as $range) {
                    if ($ct >= $range['min'] && $ct <= $range['max']) {
                        $PMT_EV001->whereBetween('DA_WEIGHT', [$range['min'], $range['max']]);
                        break;
                    }
                }
            } 
            elseif ($color) {
                $PMT_EV001->where('DA_COLOR', 'LIKE', $color);
            } 
            elseif ($sel_shape) {
                $PMT_EV001->where('DA_CUT', 'LIKE' , $sel_shape);
            }
            elseif ($cut) {
                $PMT_EV001->where('DA_CUT', 'LIKE', $cut);
            } 
            elseif ($clarity) {
                $clarity_with_minus = str_replace(['VS1', 'VVS1', 'VS2', 'VVS2', 'SI1', 'SI2', 'SI3', 'I1', 'I2', 'I3'], 
                                                ['VS-1', 'VVS-1', 'VS-2', 'VVS-2', 'SI-1', 'SI-2', 'SI-3', 'I-1', 'I-2', 'I-3'], 
                                                $clarity);
                $PMT_EV001->where('DA_CLARITY', 'LIKE' , $clarity_with_minus);
            }
            elseif ($sel_fluor) {
                $PMT_EV001->where('DA_FLUO', 'LIKE' , $sel_fluor);
            }
            elseif ($sel_insi) {
                $PMT_EV001->where('INSI_waname', 'LIKE' , $sel_insi);
            }
            elseif ($sel_ovto) {
                $PMT_EV001->where('OVTO_waname', 'LIKE' , $sel_ovto);
            }
        }
        
        /////////////////////////////////////////////////////////////

        $colorCodes = [
            'D' => '#ffffff',
            'E' => '#fffef5',
            'F' => '#fefde8',
            'G' => '#fefce2',
            'H' => '#fefbda',
            'I' => '#fefbd5',
            'J' => '#fef9c3',
            'K' => '#fdf8b8',
            'L' => '#fdf7ad',
            'M' => '#fcf59c',
            'N' => '#fbf38c',
            'YELLOW' => '#fef690',
            'PINK' => '#ffd8e9',
            'BLUE' => '#b2ceea',
            'RED' => '#f6a2a8',
            'GREEN' => '#c6f4d6',
            'PURPLE' => '#e5c4e8',
            'ORANGE' => '#ffdeb3',
            'BROWN' => '#cdbdb1',
            'VIOLET' => '#b0a0c9',
            'GRAY' => '#dddddd',
            'BLACK' => '#888888',
            'CAMPAGNE' => '#dfc3ae',
            'COGNAC' => '#dda695',
            'CHAMELEON' => '#bbcfa9',
            'WHITE' => '#ffffff',
            'OTHER' => '#f5f5f5',
        ];
        

        $PMT_EV001 = $PMT_EV001->paginate(50);
        $array["PMT_EV001"] = $PMT_EV001;
        $array["colorCodes"] = $colorCodes;

        return $array;

    }
    public function get_graph()
    {
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ]);
        return file_get_contents("https://kinkaimasu.jp/diatable/graph_new.html", false, $context);
    }
}

