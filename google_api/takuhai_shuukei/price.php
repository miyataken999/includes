<?php
include "config.php";

// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// 買取金額
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	$Eoc_chigins["kin"] = Eoc_chigins::select("ching_created_at")
					->select_expr("SUM(ching_price)","sum_price")
					->where_like('ching_created_at',$now)
					->where_null('Eoc_unfinished_id')
					->where_not_null('chigin_ecc_id')
					->where('chigin_type',1)// 貴金属
					->group_by_expr("DATE_FORMAT(ching_created_at,'%Y-%m-%d')")
					->find_many();
	$Eoc_chigins["dia"] = Eoc_chigins::select("ching_created_at")
					->select_expr("SUM(ching_price)","sum_price")
					->where_like('ching_created_at',$now)
					->where_null('Eoc_unfinished_id')
					->where_not_null('chigin_ecc_id')
					->where('chigin_type',2)// ダイヤ
					->group_by_expr("DATE_FORMAT(ching_created_at,'%Y-%m-%d')")
					->find_many();
	$Eoc_chigins["jewel"] = Eoc_chigins::select("ching_created_at")
					->select_expr("SUM(ching_price)","sum_price")
					->where_like('ching_created_at',$now)
					->where_null('Eoc_unfinished_id')
					->where_not_null('chigin_ecc_id')
					->where('chigin_type',4)// 宝飾品
					->group_by_expr("DATE_FORMAT(ching_created_at,'%Y-%m-%d')")
					->find_many();
	$Eoc_chigins["watch"] = Eoc_chigins::select("ching_created_at")
					->select_expr("SUM(ching_price)","sum_price")
					->where_like('ching_created_at',$now)
					->where_null('Eoc_unfinished_id')
					->where_not_null('chigin_ecc_id')
					->where('chigin_type',8)// 時計
					->group_by_expr("DATE_FORMAT(ching_created_at,'%Y-%m-%d')")
					->find_many();
	$Eoc_chigins["brand"] = Eoc_chigins::select("ching_created_at")
					->select_expr("SUM(ching_price)","sum_price")
					->where_like('ching_created_at',$now)
					->where_null('Eoc_unfinished_id')
					->where_not_null('chigin_ecc_id')
					->where('chigin_type',3)// ブランド
					->group_by_expr("DATE_FORMAT(ching_created_at,'%Y-%m-%d')")
					->find_many();
	$Eoc_chigins["hansoku"] = Eoc_chigins::select("ching_created_at")
					->select_expr("SUM(ching_price)","sum_price")
					->where_like('ching_created_at',$now)
					->where_null('Eoc_unfinished_id')
					->where_not_null('chigin_ecc_id')
					->where('chigin_type',6)// 販促費
					->group_by_expr("DATE_FORMAT(ching_created_at,'%Y-%m-%d')")
					->find_many();
	$Eoc_chigins["total"] = Eoc_chigins::select("ching_created_at")
					->select_expr("SUM(ching_price)","sum_price")
					->where_like('ching_created_at',$now)
					->where_null('Eoc_unfinished_id')
					->where_not_null('chigin_ecc_id')
					->group_by_expr("DATE_FORMAT(ching_created_at,'%Y-%m-%d')")
					->find_many();





	foreach ($Eoc_chigins["kin"] as $item) {
		$ching_created_at = date("Y-m-d",strtotime($item->ching_created_at));
		$name = "kin";
		//該当の日付があるかないか判断
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ching_created_at)->find_one();
		if($lounge_report__buy_takuhai == false){
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
			$lounge_report__buy_takuhai->date = $ching_created_at;
			try {
				$lounge_report__buy_takuhai->save();
				print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			} catch (Exception $e) {
				print $e->getMessage()."\n";
				exit();
			}
		}
		// end date生成
		$column_name = "{$name}_price";
		$lounge_report__buy_takuhai->$column_name = $item->sum_price;
		$lounge_report__buy_takuhai->save();
	}// end foreach

	foreach ($Eoc_chigins["dia"] as $item) {
		$ching_created_at = date("Y-m-d",strtotime($item->ching_created_at));
		$name = "dia";
		//該当の日付があるかないか判断
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ching_created_at)->find_one();
		if($lounge_report__buy_takuhai == false){
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
			$lounge_report__buy_takuhai->date = $ching_created_at;
			try {
				$lounge_report__buy_takuhai->save();
				print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			} catch (Exception $e) {
				print $e->getMessage()."\n";
				exit();
			}
		}
		// end date生成
		$column_name = "{$name}_price";
		$lounge_report__buy_takuhai->$column_name = $item->sum_price;
		$lounge_report__buy_takuhai->save();
	}// end foreach
	foreach ($Eoc_chigins["jewel"] as $item) {
		$ching_created_at = date("Y-m-d",strtotime($item->ching_created_at));
		$name = "jewel";
		//該当の日付があるかないか判断
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ching_created_at)->find_one();
		if($lounge_report__buy_takuhai == false){
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
			$lounge_report__buy_takuhai->date = $ching_created_at;
			try {
				$lounge_report__buy_takuhai->save();
				print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			} catch (Exception $e) {
				print $e->getMessage()."\n";
				exit();
			}
		}
		// end date生成
		$BJ_sumprice = 0;
		$NJ_sumprice = 0;
		$Eoc_chigins["jewel_not_group"] = Eoc_chigins::where_like('ching_created_at',$ching_created_at . '%')
			->where_null('Eoc_unfinished_id')
			->where_not_null('chigin_ecc_id')
			->where('chigin_type',4)// 宝飾品
			->find_many();
		foreach($Eoc_chigins["jewel_not_group"] as $Eoc_chigin) {
			$vw_takuhaishuukei__shouhin_no_price = vw_takuhaishuukei__shouhin_no_price::select('is_NJ_or_BJ')
				->where('Eoc_takuhai_id', $Eoc_chigin->Eoc_takuhai_id)
				->where('max_chigin_type', 4)// 宅配取引の一番高い買取備考によって、種別が決まるのでここでわける
				->find_one();
			if ($vw_takuhaishuukei__shouhin_no_price == false) {
				continue;
			}
			$shouhins = shouhin::select('price')
				->where('purchase_category', 'J')
				->where('Eoc_takuhai_id', $Eoc_chigin->Eoc_takuhai_id)
				->where_not_equal('status', '34')
				->find_many();
			foreach ($shouhins as $shouhin) {
				if(($shouhin->price != "")and($shouhin->price != NULL)and($shouhin->price != 0)){
					$price = $shouhin->price;
				}else{
					$price = 0;
				}
				if ($vw_takuhaishuukei__shouhin_no_price->is_NJ_or_BJ === 'BJ') {
					$BJ_sumprice += $price;
				} else {
					$NJ_sumprice += $price;
				}
			}
		}
		$lounge_report__buy_takuhai->jewel_price = $NJ_sumprice;
		$lounge_report__buy_takuhai->jewel_price_BJ = $BJ_sumprice;
		$lounge_report__buy_takuhai->save();
	}// end foreach

	foreach ($Eoc_chigins["watch"] as $item) {
		$ching_created_at = date("Y-m-d",strtotime($item->ching_created_at));
		$name = "watch";
		//該当の日付があるかないか判断
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ching_created_at)->find_one();
		if($lounge_report__buy_takuhai == false){
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
			$lounge_report__buy_takuhai->date = $ching_created_at;
			try {
				$lounge_report__buy_takuhai->save();
				print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			} catch (Exception $e) {
				print $e->getMessage()."\n";
				exit();
			}
		}
		// end date生成
		$column_name = "{$name}_price";
		$lounge_report__buy_takuhai->$column_name = $item->sum_price;
		$lounge_report__buy_takuhai->save();
	}// end foreach

	foreach ($Eoc_chigins["brand"] as $item) {
		$ching_created_at = date("Y-m-d",strtotime($item->ching_created_at));
		$name = "brand";
		//該当の日付があるかないか判断
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ching_created_at)->find_one();
		if($lounge_report__buy_takuhai == false){
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
			$lounge_report__buy_takuhai->date = $ching_created_at;
			try {
				$lounge_report__buy_takuhai->save();
				print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			} catch (Exception $e) {
				print $e->getMessage()."\n";
				exit();
			}
		}
		// end date生成
		$column_name = "{$name}_price";
		$lounge_report__buy_takuhai->$column_name = $item->sum_price;
		$lounge_report__buy_takuhai->save();
	}// end foreach

	foreach ($Eoc_chigins["hansoku"] as $item) {
		$ching_created_at = date("Y-m-d",strtotime($item->ching_created_at));
		$name = "hansoku";
		//該当の日付があるかないか判断
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ching_created_at)->find_one();
		if($lounge_report__buy_takuhai == false){
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
			$lounge_report__buy_takuhai->date = $ching_created_at;
			try {
				$lounge_report__buy_takuhai->save();
				print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			} catch (Exception $e) {
				print $e->getMessage()."\n";
				exit();
			}
		}
		// end date生成
		$column_name = "{$name}_price";
		$lounge_report__buy_takuhai->$column_name = $item->sum_price;
		$lounge_report__buy_takuhai->save();
	}// end foreach

	foreach ($Eoc_chigins["total"] as $item) {
		$ching_created_at = date("Y-m-d",strtotime($item->ching_created_at));
		$name = "total";
		//該当の日付があるかないか判断
		$lounge_report__buy_takuhai = lounge_report__buy_takuhai::where("date",$ching_created_at)->find_one();
		if($lounge_report__buy_takuhai == false){
			$lounge_report__buy_takuhai = lounge_report__buy_takuhai::create();
			$lounge_report__buy_takuhai->date = $ching_created_at;
			try {
				$lounge_report__buy_takuhai->save();
				print "lounge_report__buy_takuhai date ".$lounge_report__buy_takuhai->date." 新規作成\n";
			} catch (Exception $e) {
				print $e->getMessage()."\n";
				exit();
			}
		}
		// end date生成
		$column_name = "{$name}_price";
		$lounge_report__buy_takuhai->$column_name = $item->sum_price;
		$lounge_report__buy_takuhai->save();
	}// end foreach

// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// end 買取金額
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

		echo '<br><br>end 買取金額';
	    @ob_flush();
	    @flush();



