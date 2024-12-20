<?php 
//スプレッドシートAPIクライアント読み込み
include_once "/home/apache/vendor/autoload.php";
include_once '/usr/local/src/google_api/idiorm_paris/app/lounge_report__buy_takuhai.php';
include_once 'write_gss.php';

use Carbon\Carbon;
use lounge_report__buy_takuhai;

class write_gss_takuhai extends write_gss 
{
    public function count()
    {
        $this->select_year = date("Y");
        $this->select_month = date("m");
        $this->count_takuhai();
        $date = date("Y-m-01");
        $this->select_year = date('Y', strtotime('-1 month ' . $date));
        $this->select_month = date('m', strtotime('-1 month ' . $date));
        $this->count_takuhai();

    }

    public function count_takuhai()
    {
        $now = $this->select_year . "-" . $this->select_month . "%";


        $lounge_report__buy_takuhai = lounge_report__buy_takuhai::where_like('date',$now)->order_by_asc('date')->find_many();
		if($lounge_report__buy_takuhai !== false){
			$row = 4;
			$values["kin"] = array();
			$values["dia"] = array();
			$values["jewel"] = array();
			$values["jewel_BJ"] = array();
			$values["watch"] = array();
			$values["brand"] = array();
			$values["hansoku"] = array();

			$last_day_num = '';
			foreach ($lounge_report__buy_takuhai as $repo) {
				$day_num = date("d",strtotime($repo->date));
				echo "day_num ".$day_num;
				echo "<br>";

				if($last_day_num == ''){
					$last_day_num = $day_num;
				}else{
					$day_sa = $day_num-$last_day_num;
					if($day_sa > 1){
						$day_sa = $day_sa-1;
						for ($sa_i=0; $sa_i < $day_sa; $sa_i++) {
							$values["kin"][] = array(
							        '',
							        '',
							        '',
							        '',
							        '',
							        '',
							        '',
							    );
							$values["dia"][] = array(
							        '',
							        '',
							        '',
							        '',
							        '',
							    );
							$values["jewel"][] = array(
							        '',
							        '',
							        '',
							        '',
							        '',
							    );
							$values["jewel_BJ"][] = array(
							        '',
							        '',
							        '',
							        '',
							        '',
							    );
							$values["watch"][] = array(
							        '',
							        '',
							        '',
							        '',
							        '',
							    );
							$values["brand"][] = array(
							        '',
							        '',
							        '',
							        '',
							        '',
							        '',
							        '',
							    );
							$values["hansoku"][] = array(
							        '',
							    );
						}//end for sa_i
					}// end if day_sa
					$last_day_num = $day_num;
				}


				if(($day_num != 1) && ($row == 4)){
					$empty_row = $day_num-1;
					for ($empty_i=0; $empty_i < $empty_row; $empty_i++) {
						$values["kin"][] = array(
						        '',
						        '',
						        '',
						        '',
						        '',
						        '',
						        '',
						    );
						$values["dia"][] = array(
						        '',
						        '',
						        '',
						        '',
						        '',
						    );
						$values["jewel"][] = array(
						        '',
						        '',
						        '',
						        '',
						        '',
						    );
						$values["jewel_BJ"][] = array(
						        '',
						        '',
						        '',
						        '',
						        '',
						    );
						$values["watch"][] = array(
						        '',
						        '',
						        '',
						        '',
						        '',
						    );
						$values["brand"][] = array(
						        '',
						        '',
						        '',
						        '',
						        '',
						        '',
						        '',
						    );
						$values["hansoku"][] = array(
						        '',
						    );

					}

				}




				//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
				// 順番あわせる！！！
				//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
				if($repo->kin_seiyaku_count == ''){$repo->kin_seiyaku_count = '';}
				if($repo->kin_awazu_count == ''){$repo->kin_awazu_count = '';}
				if($repo->kin_received_count == ''){$repo->kin_received_count = '';}
				if($repo->kin_received_box_count == ''){$repo->kin_received_box_count = '';}
				if($repo->kin_received_count_sairai == ''){$repo->kin_received_count_sairai = '';}
				if($repo->kin_received_box_count_sairai == ''){$repo->kin_received_box_count_sairai = '';}
				if($repo->kin_price == ''){$repo->kin_price = '';}


				$values["kin"][] = array(
				        $repo->kin_seiyaku_count,
				        $repo->kin_awazu_count,
				        $repo->kin_received_count,
				        $repo->kin_received_box_count,
				        $repo->kin_received_count_sairai,
				        $repo->kin_received_box_count_sairai,
				        $repo->kin_price,
				    );

				if($repo->dia_seiyaku_count == ''){$repo->dia_seiyaku_count = '';}
				if($repo->dia_awazu_count == ''){$repo->dia_awazu_count = '';}
				if($repo->dia_received_count == ''){$repo->dia_received_count = '';}
				if($repo->dia_received_box_count == ''){$repo->dia_received_box_count = '';}
				if($repo->dia_price == ''){$repo->dia_price = '';}

				$values["dia"][] = array(
				        $repo->dia_seiyaku_count,
				        $repo->dia_awazu_count,
				        $repo->dia_received_count,
				        $repo->dia_received_box_count,
				        $repo->dia_price,
				    );





				if($repo->jewel_seiyaku_count == ''){$repo->jewel_seiyaku_count = '';}
				if($repo->jewel_awazu_count == ''){$repo->jewel_awazu_count = '';}
				if($repo->jewel_received_count == ''){$repo->jewel_received_count = '';}
				if($repo->jewel_received_box_count == ''){$repo->jewel_received_box_count = '';}
				if($repo->jewel_price == ''){$repo->jewel_price = '';}
				$values["jewel"][] = array(
				        $repo->jewel_seiyaku_count,
				        $repo->jewel_awazu_count,
				        $repo->jewel_received_count,
				        $repo->jewel_received_box_count,
				        $repo->jewel_price,
				    );

				if($repo->jewel_seiyaku_count_BJ == ''){$repo->jewel_seiyaku_count_BJ = '';}
				if($repo->jewel_awazu_count_BJ == ''){$repo->jewel_awazu_count_BJ = '';}
				if($repo->jewel_received_count_BJ == ''){$repo->jewel_received_count_BJ = '';}
				if($repo->jewel_received_box_count_BJ == ''){$repo->jewel_received_box_count_BJ = '';}
				if($repo->jewel_price_BJ == ''){$repo->jewel_price_BJ = '';}
				$values["jewel_BJ"][] = array(
						$repo->jewel_seiyaku_count_BJ,
						$repo->jewel_awazu_count_BJ,
						$repo->jewel_received_count_BJ,
						$repo->jewel_received_box_count_BJ,
						$repo->jewel_price_BJ,
					);
	

				if($repo->watch_seiyaku_count == ''){$repo->watch_seiyaku_count = '';}
				if($repo->watch_awazu_count == ''){$repo->watch_awazu_count = '';}
				if($repo->watch_received_count == ''){$repo->watch_received_count = '';}
				if($repo->watch_received_box_count == ''){$repo->watch_received_box_count = '';}
				if($repo->watch_price == ''){$repo->watch_price = '';}
				$values["watch"][] = array(
				        $repo->watch_seiyaku_count,
				        $repo->watch_awazu_count,
				        $repo->watch_received_count,
				        $repo->watch_received_box_count,
				        $repo->watch_price,
				    );




				if($repo->brand_seiyaku_count == ''){$repo->brand_seiyaku_count = '';}
				if($repo->brand_awazu_count == ''){$repo->brand_awazu_count = '';}
				if($repo->brand_received_count == ''){$repo->brand_received_count = '';}
				if($repo->brand_received_box_count == ''){$repo->brand_received_box_count = '';}
				if($repo->brand_received_count_sairai == ''){$repo->brand_received_count_sairai = '';}
				if($repo->brand_received_box_count_sairai == ''){$repo->brand_received_box_count_sairai = '';}
				if($repo->brand_price == ''){$repo->brand_price = '';}
				$values["brand"][] = array(
				        $repo->brand_seiyaku_count,
				        $repo->brand_awazu_count,
				        $repo->brand_received_count,
				        $repo->brand_received_box_count,
				        $repo->brand_received_count_sairai,
				        $repo->brand_received_box_count_sairai,
				        $repo->brand_price,
				    );


				if($repo->hansoku_price == ''){$repo->hansoku_price = '';}
				$values["hansoku"][] = array(
				        $repo->hansoku_price,
				    );




				$row++;//行
			}// end foreach
		}






        $this->sheets_batch($values["kin"], "X", "AD", '買取');
        $this->sheets_batch($values["dia"], "AI", "AM", '買取');
        $this->sheets_batch($values["jewel"], "AP", "AT", '買取');
        $this->sheets_batch($values["jewel_BJ"], "BU", "BY", '買取');
        $this->sheets_batch($values["watch"], "AW", "BA", '買取');
        $this->sheets_batch($values["brand"], "BF", "BL", '買取');
        $this->sheets_batch($values["hansoku"], "BN", "BN", '買取');
        
    }




}