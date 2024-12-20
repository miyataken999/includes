<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MonthlyController extends Controller
{
    public function index(Request $request)
    {

        //金の国内公表買取価格の月次価格推移 DBname :tb_gold_market_year
        $current_year = !empty($_GET['select_year_gold']) ? $_GET['select_year_gold'] : date('Y') - 2;
        $start_year = $current_year;
        $end_year = $current_year + 2;
        $gold_table = DB::table('kinkai.tb_gold_market_year')
            ->whereBetween('date', [$start_year, $end_year])
            ->orderBy('date','ASC')
            ->get();
        //select optionのため
            $options_gold = '';
            for ($i = 2009; $i <= date('Y'); $i += 3) {
                $year = $i;
                $selected = ($year == $current_year) ? 'selected' : '';
                $options_gold .= "<option value=\"$year\" $selected>{$year}～</option>";
            }

        //プラチナ(白金)の国内公表買取価格の月次価格推移 DBname:tb_platinum_market_year    
        $current_year_pt = !empty($_GET['select_year_pt']) ? $_GET['select_year_pt'] : date('Y') - 2;
        $start_year_pt = $current_year_pt;
        $end_year_pt = $current_year_pt + 2;;
        $platinum_table = DB::table('kinkai.tb_platinum_market_year')
            ->whereBetween('date', [$start_year_pt, $end_year_pt])
            ->orderBy('date','ASC')
            ->get();
        //select optionのため
        $options_pt = '';
        for ($i = 2009; $i <= date('Y'); $i += 3) {
            $year_pt = $i;
            $selected = ($year_pt == $current_year_pt) ? 'selected' : '';
            $options_pt .= "<option value=\"$year_pt\" $selected>{$year_pt}～</option>";
        }   
        //bladeに渡す
        $array = [
                'options_gold' => $options_gold,
                'options_pt' => $options_pt,
                'current_year' => $current_year,
                'current_year_pt' => $current_year_pt,
                'gold_table' => $gold_table,
                'platinum_table' => $platinum_table,
            ];
        return view("monthly.index",$array);
    }
}
