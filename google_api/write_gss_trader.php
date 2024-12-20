<?php 
//スプレッドシートAPIクライアント読み込み
include_once "/home/apache/vendor/autoload.php";
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_trader_report.php';
include_once 'write_gss.php';

use Carbon\Carbon;
use Eoc_trader_report;

class write_gss_trader extends write_gss 
{
    public function count()
    {
        $this->select_year = date("Y");
        $this->select_month = date("m");
        $this->count_trader();
        $date = date("Y-m-01");
        $this->select_year = date('Y', strtotime('-1 month ' . $date));
        $this->select_month = date('m', strtotime('-1 month ' . $date));
        $this->count_trader();

    }

    public function count_trader()
    {
        $now_month = $this->select_year . "-" . $this->select_month;
        $now_time = $now_month . "-01 00:00:00";
        $now = $now_month."%";
        $now_date = date("Y-m-d", strtotime($now_time));
        $now_month_last = date("t", strtotime($now_date));

        for ($row_date=1; $row_date <= $now_month_last; $row_date++) {
            $repo = Eoc_trader_report::where_like('date', $now_month.'-'.sprintf("%02d", $row_date))->find_one();
            if($repo == false){
              $input_values["J"][] = array(
                    '',
                        );
              $input_values["D"][] = array(
                    '',
                        );
              $input_values["W"][] = array(
                    '',
                        );
              $input_values["B"][] = array(
                    '',
                        );
              continue;
            }
      
            //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
                  // 順番あわせる！！！
                  //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
            if(($repo->price_sum_for_jewelry == '') or ($repo->price_sum_for_jewelry == '0')){$repo->price_sum_for_jewelry = '';}
            if(($repo->price_sum_for_dia == '') or ($repo->price_sum_for_dia == '0')){$repo->price_sum_for_dia = '';}
            if(($repo->price_sum_for_watch == '') or ($repo->price_sum_for_watch == '0')){$repo->price_sum_for_watch = '';}
            if(($repo->price_sum_for_brand == '') or ($repo->price_sum_for_brand == '0')){$repo->price_sum_for_brand = '';}
      
            $input_values["J"][] = array(
                  $repo->price_sum_for_jewelry,
                      );
            $input_values["D"][] = array(
                  $repo->price_sum_for_dia,
                      );
            $input_values["W"][] = array(
                  $repo->price_sum_for_watch,
                      );
            $input_values["B"][] = array(
                  $repo->price_sum_for_brand,
                      );
          }//end for row_date
      
      
        $this->sheets_batch($input_values["J"], "BQ", "BQ", '買取');
        $this->sheets_batch($input_values["D"], "BR", "BR", '買取');
        $this->sheets_batch($input_values["W"], "BS", "BS", '買取');
        $this->sheets_batch($input_values["B"], "BT", "BT", '買取');
        
    }




}