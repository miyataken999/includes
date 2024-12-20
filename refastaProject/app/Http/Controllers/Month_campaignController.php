<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\Routing\Loader\YamlFileLoader;

class Month_campaignController extends Controller
{
    public function index() {
        if((isset($_GET["year"])) && (isset($_GET["month"]))){
          // yearとmonth　$_GETにあったら＄year＄monthに変換して代入
            $year = htmlspecialchars($_GET["year"], ENT_QUOTES);
            // htmlspecialchars（）　ENT_QUOTESで” ’ものもエスケープ（機能させない）
            $month = htmlspecialchars($_GET["month"], ENT_QUOTES);
          }else{
            // でなければ＄year＄monthにdate("")のフォーマットで代入するアクセスしたじかん
            $year = date("Y");
            $month = date("m");
          }
          // $between_startに月初の値を代入　$between_endに月の終わりを代入　０１は桁数を合わせてる
          // date("Y-m-tのtは月の日にち
        $between_start = date("Y-m-01 00:00:00",strtotime("{$year}-{$month}-01 00:00:00"));
        $between_end   = date("Y-m-t 00:00:00",strtotime("{$year}-{$month}-01 00:00:00"));

        // $campaignにテーブル名internet.mst_buy_campaignのデータを取得する
        // mousikomi_startDateの$between_startから$between_endの間のをfirst一つ代入　なければNULLが来る（（getは全部持ってくる）
        $campaign = DB::table("internet.mst_buy_campaign")->whereRaw("mousikomi_startDate BETWEEN ? AND ?", array($between_start, $between_end))->first();

        if($campaign == false){
        // データがなかった場合の処理を書く。
          return false;
        }
        // $campaign_contentに　internet.mst_buy_campaign_contentのpage_idが61のものを絞って代入
        $campaign_content = DB::table("internet.mst_buy_campaign_content")->whereRaw("page_id = ? ", array(61))->get();

        // $arrayに$campaign、$campaign_contentを代入し＄arrayをmonth_campaign.indexでbladeに渡す
        $array = array();
        $array["campaign"] = $campaign;//$array["キー"] = $campaign;　で$campaignをキーに代入
        $array["campaign_content"] = $campaign_content;
        return view ('month_campaign.index', $array); //view ('ブレイドファイル', 渡す変数（連想配列でなければだめ）);


    }

}