<?php 
//スプレッドシートAPIクライアント読み込み
include_once "/home/apache/vendor/autoload.php";
include_once '/usr/local/src/google_api/idiorm_paris/app/lounge_report__buy.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/vw_Eoc_unfinished_by_day_greatest.php';

include_once 'write_gss.php';

use Carbon\Carbon;
use lounge_report__buy;
use vw_Eoc_unfinished_by_day_greatest;

class write_gss_tentou extends write_gss 
{
    public function count()
    {
        $this->select_year = date("Y");
        $this->select_month = date("m");
        $this->save_max_category();
        $this->save_count_tentou();
        $this->write_tentou_count();
        $date = date("Y-m-01");
        $this->select_year = date('Y', strtotime('-1 month ' . $date));
        $this->select_month = date('m', strtotime('-1 month ' . $date));
        $this->save_max_category();
        $this->save_count_tentou();
        $this->write_tentou_count();

    }

    public function save_max_category()
    {
        $now = "{$this->select_year}-{$this->select_month}"."%";
        //今月以外も全更新したい場合は、whereを外す
        $vw_Eoc_unfinished_by_day_greatest = vw_Eoc_unfinished_by_day_greatest::where_like('create_at_day',$now)->find_many();
        // $vw_Eoc_unfinished_by_day_greatest = vw_Eoc_unfinished_by_day_greatest::find_many();

        //各カテゴリーごとの最大値のカテゴリーを集計し、見込み顧客に入れる
        foreach ($vw_Eoc_unfinished_by_day_greatest as $greatest) {
            if($greatest->greatest == '-1'){
                $max_greatest = $greatest->sum_K;
                $max_category = "K";
                if($max_greatest < $greatest->sum_D){
                    $max_greatest = $greatest->sum_D;
                    $max_category = "D";
                }
                if($max_greatest < $greatest->sum_J){
                    $max_greatest = $greatest->sum_J;
                    $max_category = "J";
                }
                if($max_greatest < $greatest->sum_W){
                    $max_greatest = $greatest->sum_W;
                    $max_category = "W";
                }
                if($max_greatest < $greatest->sum_B){
                    $max_greatest = $greatest->sum_B;
                    $max_category = "B";
                }
                if($max_greatest < $greatest->sum_H){
                    $max_greatest = $greatest->sum_H;
                    $max_category = "H";
                }
                if($max_greatest < $greatest->sum_A){
                    $max_greatest = $greatest->sum_A;
                    $max_category = "A";
                }
            }else{
                switch ($greatest->greatest) {
                    case $greatest->sum_K:
                        $max_category = "K";
                        break;
                    case $greatest->sum_D:
                        $max_category = "D";
                        break;
                    case $greatest->sum_J:
                        $max_category = "J";
                        break;
                    case $greatest->sum_W:
                        $max_category = "W";
                        break;
                    case $greatest->sum_B:
                        $max_category = "B";
                        break;
                    case $greatest->sum_H:
                        $max_category = "H";
                        break;
                    case $greatest->sum_A:
                        $max_category = "A";
                        break;
                    default:
                        $max_category = "";
                        break;
                }
            }





            $Eoc_unfinished = Eoc_unfinished::where("id",$greatest->id)->find_one();
            if($Eoc_unfinished !== false){
                // if($Eoc_unfinished->zenawazu == 1){
                // 	$Eoc_unfinished->max_category = '';
                // }else{
                // 	$Eoc_unfinished->max_category = $max_category;
                // }
                $Eoc_unfinished->max_category = $max_category;

                try {
                    $Eoc_unfinished->save();
                    print "Eoc_unfinished id ".$Eoc_unfinished->id." 更新\n";

                } catch (Exception $e) {
                    print $e->getMessage()."\n";
                    exit();
                }
            }


        }

    }


    public function save_count_tentou()
    {
        $now = "{$this->select_year}-{$this->select_month}"."%";
        $this->reset_raiten($now);
        
        $category_Array = array('K','D','J','W','B','H');//合わずのAは省いとく

        //総件数の計算
        print "---------------------------------------------\n";
        print "総件数の計算\n";
        $totaladds = vw_Eoc_unfinished_by_day_greatest::select("*")
            // ->select_expr("COUNT(id)","count")
            ->where_like('create_at_day',$now)
            // ->group_by('create_at_day')
            // ->group_by('max_category')
            ->find_many();
        $total_count = [
            'K' => [],
            'D' => [],
            'J' => [],
            'J_BJ' => [],
            'B' => [],
            'W' => [],
        ];
        foreach ($totaladds as $item) {
            if (empty($item->max_category)) {
                continue;
            }
            // カテゴリー
            if (($item->max_category === 'J') && ($item->is_NJ_or_BJ === 'BJ')) {
                $save_category = 'J_BJ';
            } else {
                $save_category = $item->max_category;
            }
            // 初期定義
            if (!isset($total_count[$save_category][$item->create_at_day])) {
                $total_count[$save_category][$item->create_at_day] = 0;
            }
            // 加算
            $total_count[$save_category][$item->create_at_day]++;
        }	//END foreach totaladds
        // 総件数の保存処理
        foreach ($total_count as $save_category => $create_at_days) {
            foreach ($create_at_days as $create_at_day => $count) {
                // レコード初期作成
                $lounge_report__buy = lounge_report__buy::where('date',$create_at_day)->find_one();
                if($lounge_report__buy === false){
                    $lounge_report__buy = lounge_report__buy::create();
                    $lounge_report__buy->date = $create_at_day;
                    try {
                        $lounge_report__buy->save();
                        print "lounge_report__buy date ".$lounge_report__buy->date." 新規作成\n";
                    } catch (Exception $e) {
                        print $e->getMessage()."\n";
                        exit();
                    }
                }
                switch ($save_category) {
                    case 'K':
                        $lounge_report__buy->raiten_kin_total_count = $count;
                        break;
                    case 'D':
                        $lounge_report__buy->raiten_dia_total_count = $count;
                        break;
                    case 'J':
                        $lounge_report__buy->raiten_jewel_total_count = $count;
                        break;
                    case 'J_BJ':
                        $lounge_report__buy->raiten_jewel_total_count_BJ = $count;
                        break;
                    case 'W':
                        $lounge_report__buy->raiten_watch_total_count = $count;
                        break;
                    case 'B':
                        $lounge_report__buy->raiten_brand_total_count = $count;
                        break;
                    default:
                        break;
                }
            
                try {
                    $lounge_report__buy->save();
                    print "lounge_report__buy date ".$lounge_report__buy->date." 総件数更新\n";
                } catch (Exception $e) {
                    print $e->getMessage()."\n";
                    exit();
                }
            }
        }
        
        print "---------------------------------------------\n";
        print "成約件数の計算\n";
        $totaladds_seiyaku = vw_Eoc_unfinished_by_day_greatest::where_not_equal('ecc_id',4460)
            ->where_not_equal('ecc_id',109175)
            ->where_not_equal('zenawazu','1')
            ->where_gt('greatest',0)
            ->where_like('create_at_day',$now)
            // ->group_by('create_at_day')
            // ->group_by('max_category')
            ->find_many();

        $total_seiyaku_count = [
            'K' => [],
            'D' => [],
            'J' => [],
            'J_BJ' => [],
            'B' => [],
            'W' => [],
        ];
        foreach ($totaladds_seiyaku as $item) {
            if (empty($item->max_category)) {
                continue;
            }
            // カテゴリー
            if (($item->max_category === 'J') && ($item->is_NJ_or_BJ === 'BJ')) {
                $save_category = 'J_BJ';
            } else {
                $save_category = $item->max_category;
            }
            // 初期定義
            if (!isset($total_seiyaku_count[$save_category][$item->create_at_day])) {
                $total_seiyaku_count[$save_category][$item->create_at_day] = 0;
            }
            // 加算
            $total_seiyaku_count[$save_category][$item->create_at_day]++;
        }	//END foreach totaladds
    
        // 成約件数の保存処理
        foreach ($total_seiyaku_count as $save_category => $create_at_days) {
            foreach ($create_at_days as $create_at_day => $count) {
                // レコード初期作成
                $lounge_report__buy = lounge_report__buy::where('date',$create_at_day)->find_one();
                if($lounge_report__buy === false){
                    $lounge_report__buy = lounge_report__buy::create();
                    $lounge_report__buy->date = $create_at_day;
                    try {
                        $lounge_report__buy->save();
                        print "lounge_report__buy date ".$lounge_report__buy->date." 新規作成\n";
                    } catch (Exception $e) {
                        print $e->getMessage()."\n";
                        exit();
                    }
                }
                switch ($save_category) {
                    case 'K':
                        $lounge_report__buy->raiten_kin_seiyaku_count = $count;
                        break;
                    case 'D':
                        $lounge_report__buy->raiten_dia_seiyaku_count = $count;
                        break;
                    case 'J':
                        $lounge_report__buy->raiten_jewel_seiyaku_count = $count;
                        break;
                    case 'J_BJ':
                        $lounge_report__buy->raiten_jewel_seiyaku_count_BJ = $count;
                        break;
                    case 'W':
                        $lounge_report__buy->raiten_watch_seiyaku_count = $count;
                        break;
                    case 'B':
                        $lounge_report__buy->raiten_brand_seiyaku_count = $count;
                        break;
                    default:
                        break;
                }
        
                try {
                    $lounge_report__buy->save();
                    print "lounge_report__buy date ".$lounge_report__buy->date." 成約件数更新\n";
                } catch (Exception $e) {
                    print $e->getMessage()."\n";
                    exit();
                }
            }
        }






        
        print "---------------------------------------------\n";
        print "成約金額の計算\n";
        $totaladds_seiyaku = vw_Eoc_unfinished_by_day_greatest::select("*")
            // ->select_expr("SUM(sum_K)","sum_sum_K")
            // ->select_expr("SUM(sum_D)","sum_sum_D")
            // ->select_expr("SUM(sum_J)","sum_sum_J")
            // ->select_expr("SUM(sum_W)","sum_sum_W")
            // ->select_expr("SUM(sum_B)","sum_sum_B")
            // ->select_expr("SUM(sum_H)","sum_sum_H")
            ->where_gt('greatest',0)
            ->where_not_equal('ecc_id',4460)
            ->where_not_equal('ecc_id',109175)
            ->where_like('create_at_day',$now)
            // ->group_by('create_at_day')
            ->find_many();

        $total_seiyaku_price = [
            'K' => [],
            'D' => [],
            'J' => [],
            'J_BJ' => [],
            'B' => [],
            'W' => [],
            'H' => [],
        ];
        foreach ($totaladds_seiyaku as $item) {
            if (empty($item->max_category)) {
                continue;
            }
            // カテゴリー
            if (($item->max_category === 'J') && ($item->is_NJ_or_BJ === 'BJ')) {
                $save_category = 'J_BJ';
            } else {
                $save_category = $item->max_category;
            }
            // 初期定義
            if (!isset($total_seiyaku_price[$save_category][$item->create_at_day])) {
                $total_seiyaku_price[$save_category][$item->create_at_day] = 0;
            }
            // 加算
            switch ($save_category) {
                case 'K':
                    $total_seiyaku_price[$save_category][$item->create_at_day] += $item->sum_K;
                    break;
                case 'D':
                    $total_seiyaku_price[$save_category][$item->create_at_day] += $item->sum_D;
                    break;
                case 'J':
                    $total_seiyaku_price[$save_category][$item->create_at_day] += $item->sum_J;
                    break;
                case 'J_BJ':
                    $total_seiyaku_price[$save_category][$item->create_at_day] += $item->sum_J;
                    break;
                case 'W':
                    $total_seiyaku_price[$save_category][$item->create_at_day] += $item->sum_W;
                    break;
                case 'B':
                    $total_seiyaku_price[$save_category][$item->create_at_day] += $item->sum_B;
                    break;
                case 'H':
                    $total_seiyaku_price[$save_category][$item->create_at_day] += $item->sum_H;
                    break;
                default:
                    break;
            }
    

        }	//END foreach totaladds
        // 成約金額の保存処理
        foreach ($total_seiyaku_price as $save_category => $create_at_days) {
            foreach ($create_at_days as $create_at_day => $price) {
                // レコード初期作成
                $lounge_report__buy = lounge_report__buy::where('date',$create_at_day)->find_one();
                if($lounge_report__buy === false){
                    $lounge_report__buy = lounge_report__buy::create();
                    $lounge_report__buy->date = $create_at_day;
                    try {
                        $lounge_report__buy->save();
                        print "lounge_report__buy date ".$lounge_report__buy->date." 新規作成\n";
                    } catch (Exception $e) {
                        print $e->getMessage()."\n";
                        exit();
                    }
                }

                switch ($save_category) {
                    case 'K':
                        $lounge_report__buy->raiten_kin_seiyaku_price = $price;
                        break;
                    case 'D':
                        $lounge_report__buy->raiten_dia_seiyaku_price = $price;
                        break;
                    case 'J':
                        $lounge_report__buy->raiten_jewel_seiyaku_price = $price;
                        break;
                    case 'J_BJ':
                        $lounge_report__buy->raiten_jewel_seiyaku_price_BJ = $price;
                        break;
                    case 'W':
                        $lounge_report__buy->raiten_watch_seiyaku_price = $price;
                        break;
                    case 'B':
                        $lounge_report__buy->raiten_brand_seiyaku_price = $price;
                        break;
                    case 'H':
                        $lounge_report__buy->raiten_hansoku_price = $price;
                        break;
                    default:
                        break;
                }
        
                try {
                    $lounge_report__buy->save();
                    print "lounge_report__buy date ".$lounge_report__buy->date." 買取額更新\n";
                } catch (Exception $e) {
                    print $e->getMessage()."\n";
                    exit();
                }
            }
        }	//END foreach totaladds_seiyaku
    }





    public function write_tentou_count()
    {
        $now = $this->select_year . "-" . $this->select_month . "%";

        $lounge_report__buy = lounge_report__buy::where_like('date',$now)->order_by_asc('date')->find_many();
        if($lounge_report__buy !== false){
            $row = 4;
            $values = array();
            $values_2 = array();
            $values_3 = array();
    
            $last_day_num = '';
            foreach ($lounge_report__buy as $repo) {
                $day_num = date("d",strtotime($repo->date));
                if($last_day_num == ''){
                    $last_day_num = $day_num;
                }else{
                    $day_sa = $day_num-$last_day_num;
                    if($day_sa > 1){
                        $day_sa = $day_sa-1;
                        for ($sa_i=0; $sa_i < $day_sa; $sa_i++) {
                            $values[] = array(
                                    '',
                                    '',
                                    '',
                                );
    
    
                            $values_2[] = array(
                                    '',
                                    '',
                                    '',
    
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                );
                            $values_3[] = array(
                                    '',
                                    '',
                                    '',
                                );

                        }//end for sa_i
                    }// end if day_sa
                    $last_day_num = $day_num;
                }
                if(($day_num != 1) && ($row == 4)){
                    $empty_row = $day_num-1;
                    for ($empty_i=0; $empty_i < $empty_row; $empty_i++) {
                        $values[] = array(
                                '',
                                '',
                                '',
                            );
    
    
                        $values_2[] = array(
                                '',
                                '',
                                '',
    
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                            );
                        $values_3[] = array(
                                '',
                                '',
                                '',
                            );
    
                    }
    
                }
    
                if($repo->raiten_kin_total_count == ''){$repo->raiten_kin_total_count = '';}
                if($repo->raiten_kin_seiyaku_count == ''){$repo->raiten_kin_seiyaku_count = '';}
                if($repo->raiten_kin_seiyaku_price == ''){$repo->raiten_kin_seiyaku_price = '';}
    
                $values[] = array(
                        $repo->raiten_kin_total_count,
                        $repo->raiten_kin_seiyaku_count,
                        $repo->raiten_kin_seiyaku_price,
                    );
    
    
    
                if($repo->raiten_dia_total_count == ''){$repo->raiten_dia_total_count = '';}
                if($repo->raiten_dia_seiyaku_count == ''){$repo->raiten_dia_seiyaku_count = '';}
                if($repo->raiten_dia_seiyaku_price == ''){$repo->raiten_dia_seiyaku_price = '';}
                if($repo->raiten_jewel_total_count == ''){$repo->raiten_jewel_total_count = '';}
                if($repo->raiten_jewel_seiyaku_count == ''){$repo->raiten_jewel_seiyaku_count = '';}
                if($repo->raiten_jewel_seiyaku_price == ''){$repo->raiten_jewel_seiyaku_price = '';}
                if($repo->raiten_watch_total_count == ''){$repo->raiten_watch_total_count = '';}
                if($repo->raiten_watch_seiyaku_count == ''){$repo->raiten_watch_seiyaku_count = '';}
                if($repo->raiten_watch_seiyaku_price == ''){$repo->raiten_watch_seiyaku_price = '';}
                if($repo->raiten_brand_total_count == ''){$repo->raiten_brand_total_count = '';}
                if($repo->raiten_brand_seiyaku_count == ''){$repo->raiten_brand_seiyaku_count = '';}
                if($repo->raiten_brand_seiyaku_price == ''){$repo->raiten_brand_seiyaku_price = '';}
                if($repo->raiten_hansoku_price == ''){$repo->raiten_hansoku_price = '';}
    
                $values_2[] = array(
                        $repo->raiten_dia_total_count,
                        $repo->raiten_dia_seiyaku_count,
                        $repo->raiten_dia_seiyaku_price,
    
                        $repo->raiten_jewel_total_count,
                        $repo->raiten_jewel_seiyaku_count,
                        $repo->raiten_jewel_seiyaku_price,
                        $repo->raiten_watch_total_count,
                        $repo->raiten_watch_seiyaku_count,
                        $repo->raiten_watch_seiyaku_price,
                        $repo->raiten_brand_total_count,
                        $repo->raiten_brand_seiyaku_count,
                        $repo->raiten_brand_seiyaku_price,
                        $repo->raiten_hansoku_price,
                    );

                if($repo->raiten_jewel_total_count_BJ == ''){$repo->raiten_jewel_total_count_BJ = '';}
                if($repo->raiten_jewel_seiyaku_count_BJ == ''){$repo->raiten_jewel_seiyaku_count_BJ = '';}
                if($repo->raiten_jewel_seiyaku_price_BJ == ''){$repo->raiten_jewel_seiyaku_price_BJ = '';}
    
                $values_3[] = array(
                    $repo->raiten_jewel_total_count_BJ,
                    $repo->raiten_jewel_seiyaku_count_BJ,
                    $repo->raiten_jewel_seiyaku_price_BJ,
                );

    
                $row++;//行
            }
        }
        $this->write_array = $values;
        $this->write_array_2 = $values_2;
        $this->write_array_3 = $values_3;
        $this->count_start();
    }


    public function count_start()
    {
        // GSS書き込み
        // 金来店・出張
        $this->sheets_batch($this->write_array, "D", "F", '買取');

        // 精錬・店頭なし

        // ダイヤ来店から店頭販促費
        $this->sheets_batch($this->write_array_2, "I", "U", '買取');

        // 来店BJ
        $this->sheets_batch($this->write_array_3, "BZ", "CB", '買取');

        return "finish";
    }

    public function reset_raiten($now)
    {
        $lounge_report__buys = lounge_report__buy::where_like('date',$now)->find_many();
        foreach ($lounge_report__buys as $lounge_report__buy) {
            $lounge_report__buy->raiten_kin_total_count = 0;
            $lounge_report__buy->raiten_kin_seiyaku_count = 0;
            $lounge_report__buy->raiten_kin_seiyaku_price = 0;
            $lounge_report__buy->raiten_dia_total_count = 0;
            $lounge_report__buy->raiten_dia_seiyaku_count = 0;
            $lounge_report__buy->raiten_dia_seiyaku_price = 0;
            $lounge_report__buy->raiten_jewel_total_count = 0;
            $lounge_report__buy->raiten_jewel_seiyaku_count = 0;
            $lounge_report__buy->raiten_jewel_seiyaku_price = 0;
            $lounge_report__buy->raiten_watch_total_count = 0;
            $lounge_report__buy->raiten_watch_seiyaku_count = 0;
            $lounge_report__buy->raiten_watch_seiyaku_price = 0;
            $lounge_report__buy->raiten_brand_total_count = 0;
            $lounge_report__buy->raiten_brand_seiyaku_count = 0;
            $lounge_report__buy->raiten_brand_seiyaku_price = 0;
            $lounge_report__buy->raiten_hansoku_price = 0;
            $lounge_report__buy->raiten_jewel_total_count_BJ = 0;
            $lounge_report__buy->raiten_jewel_seiyaku_count_BJ = 0;
            $lounge_report__buy->raiten_jewel_seiyaku_price_BJ = 0;
            $lounge_report__buy->save();
        }
    }

}