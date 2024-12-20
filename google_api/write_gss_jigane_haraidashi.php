<?php 
//スプレッドシートAPIクライアント読み込み
include_once "/home/apache/vendor/autoload.php";
include_once '/usr/local/src/google_api/idiorm_paris/app/gold_check_report.php';
include_once 'write_gss.php';

use Carbon\Carbon;
use gold_check_report;

class write_gss_jigane_haraidashi extends write_gss 
{
    public function count()
    {
        $this->select_year = date("Y");
        $this->select_month = date("m");
        $this->count_jigane();
        $date = date("Y-m-01");
        $this->select_year = date('Y', strtotime('-1 month ' . $date));
        $this->select_month = date('m', strtotime('-1 month ' . $date));
        $this->count_jigane();

    }

    public function count_jigane()
    {
        $now_month = $this->select_year . "-" . $this->select_month;
        $now = $now_month."%";


        $result_repo = gold_check_report::select('date')
                                          ->select_expr("SUM(billing_amount)","sum_billing_amount")
                                          ->select_expr("SUM(seiren_price)","sum_seiren_price")
                                          ->select_expr("SUM(dia_price)","sum_dia_price")
                                          ->where_like('date', $now)
                                          ->group_by('date')
                                          ->order_by_asc('date')->find_many();
    
    
    
        $row_date=0;
        foreach ($result_repo as $repo) {
          $row_date++;//行
          if($row_date != date("j",strtotime($repo->date.' 00:00:00'))){
            while($row_date < date("j",strtotime($repo->date.' 00:00:00'))){
              $input_values[] = array(
                    '',
                    '',
                        );
              $input_values_dia[] = array(
                    '',
                        );
              $row_date++;//行
              // continue;
            }
          }
    
        //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
        // 順番あわせる！！！
        //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
          if(($repo->sum_billing_amount == '') or ($repo->sum_billing_amount == '0')){$repo->sum_billing_amount = '';}
          if(($repo->sum_seiren_price == '') or ($repo->sum_seiren_price == '0')){$repo->sum_seiren_price = '';}
          if(($repo->sum_dia_price == '') or ($repo->sum_dia_price == '0')){$repo->sum_dia_price = '';}
    
    
                $input_values[] = array(
                $repo->sum_billing_amount,
                $repo->sum_seiren_price,
                    );
          $input_values_dia[] = array(
                $repo->sum_dia_price,
                    );
            }// end foreach
            echo '<br><br>end repo';

        $this->sheets_batch($input_values, "D", "E", '販売');
        $this->sheets_batch($input_values_dia, "G", "G", '販売');
        
    }




}