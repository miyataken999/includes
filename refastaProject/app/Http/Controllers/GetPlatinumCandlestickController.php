<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class GetPlatinumCandlestickController extends Controller
{
  public function index()
  {
      $current_year = !empty($_GET['select_year_gold']) ? $_GET['select_year_gold'] : date('Y') - 2;
      $start_year = $current_year;
      $end_year = $current_year + 2;
      //プラチナの価格相場表ローソク足用
      $platinumdata = DB::table("kinkai.tb_platinum_market_year")
      ->whereBetween('date', [$start_year, $end_year])
      ->orderBy("date")
      ->get();
      $options_platinum = '';
      for ($i = 2011; $i <= date('Y'); $i += 3) {
          $year = $i;
          $selected = ($year == $current_year) ? 'selected' : '';
          $options_platinum .= "<option value=\"$year\" $selected>{$year}～</option>";
      }
    //プラチナの過去の買取額の最高値と最安値
      $highest_price = DB::table('kinkai.tb_price')
            ->select('kinkai.tb_price.platinum','kinkai.tb_price.resist_data')
            ->orderBy('platinum', 'desc')
            ->first();
      $lowest_price = DB::table('kinkai.tb_price')
             ->select('kinkai.tb_price.platinum', 'kinkai.tb_price.resist_data')
             ->where('kinkai.tb_price.platinum', '<>', 0)
             ->orderBy('platinum', 'asc')
             ->first();
      $array = [
       'options_platinum' => $options_platinum,
       'platinumdata' => $platinumdata,
       'highest_price' => $highest_price,
       'lowest_price' => $lowest_price,
   ];
      return view("metalplatinumtable.index", $array);
  }
}