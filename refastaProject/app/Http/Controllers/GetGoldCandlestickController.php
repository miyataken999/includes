<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
class GetGoldCandlestickController extends Controller
{
  public function index()
  {
      $current_year = !empty($_GET['select_year_gold']) ? $_GET['select_year_gold'] : date('Y') - 2;
      $start_year = $current_year;
      $end_year = $current_year + 2;
      //金の価格相場表ローソク足用
      $golddata = DB::table("kinkai.tb_gold_market_year")
      ->whereBetween('date', [$start_year, $end_year])
      ->orderBy("date")
      ->get();
      $options_gold = '';
      for ($i = 2009; $i <= date('Y'); $i += 3) {
          $year = $i;
          $selected = ($year == $current_year) ? 'selected' : '';
          $options_gold .= "<option value=\"$year\" $selected>{$year}～</option>";
      }
    //金の過去の買取額の最高値と最安値
      $highest_price = DB::table('kinkai.tb_price')
            ->select('kinkai.tb_price.gold','kinkai.tb_price.resist_data')
            ->orderBy('gold', 'desc')
            ->first();
      $lowest_price = DB::table('kinkai.tb_price')
            ->select('kinkai.tb_price.gold', 'kinkai.tb_price.resist_data')
            ->where('kinkai.tb_price.gold', '<>', 0)
            ->orderBy('gold', 'asc')
            ->first();
      $array = [
       'options_gold' => $options_gold,
       'golddata' => $golddata,
       'highest_price' => $highest_price,
       'lowest_price' => $lowest_price,
   ];
      return view("metaltable.index", $array);
  }
}