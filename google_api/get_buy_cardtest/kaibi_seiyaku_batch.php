<?php
$now_time = date("Y-m-d H:i:s");
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取備考作成
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

		//GSS上のカテゴリーを配列に格納
		$category_Array = array('K','D','J','W','B','H');//合わずのAは省いとく
		$cateName_Array = array('金','ダ','宝','時','服','販');//合わずのAは省いとく
		$sum_price_max = 0;
		$sum_price_total = 0;

		for ($category_i=0; $category_i < count($category_Array); $category_i++) {
			$cate = $category_Array[$category_i];
			$cate_name = $cateName_Array[$category_i];

			//見込み顧客IDとカテゴリーをキーにして、まず合算
			$Eoc_chigins_unfinished_by_category = Eoc_chigins_unfinished::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('category',$cate)->find_many();
			// $sum_price = $Eoc_chigins_unfinished_by_category->sum('price');
			$sum_price = Eoc_chigins_unfinished::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('category',$cate)->sum('price');
			if($sum_price == ''){
				$sum_price = 0;
			}
			if($sum_price_max <= $sum_price){
				$sum_price_max = $sum_price;
				$sum_price_max_cate = $cate_name;
			}
			$sum_price_total = $sum_price_total+$sum_price;


			$Eoc_chigins = Eoc_chigins::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('category',$cate)->find_one();
			if($Eoc_chigins == false){
				if($sum_price > 0){
			        $Eoc_chigins = Eoc_chigins::create();
					$Eoc_chigins->Eoc_unfinished_id = $Eoc_unfinished_id;
					$Eoc_chigins->category = $cate;
					$Eoc_chigins->ching_created_at = $now_time;
				}else{
					continue;
				}
			}

			$Eoc_chigins->create_by = $satei_by;
			$Eoc_chigins->satei_by = $satei_by;
			$Eoc_chigins->chigin_ecc_id = $ecc_id;
			$Eoc_chigins->ching_price = $sum_price;

			switch ($cate) {
				case 'K':
					$Eoc_chigins->chigin_type = 1;
					$Eoc_chigins->haraidasi = 3;
					break;
				case 'D':
					$Eoc_chigins->chigin_type = 2;
					$Eoc_chigins->haraidasi = 9;
					break;
				case 'J':
					$Eoc_chigins->chigin_type = 4;
					$Eoc_chigins->haraidasi = 4;
					break;
				case 'W':
					$Eoc_chigins->chigin_type = 8;
					$Eoc_chigins->haraidasi = 4;
					break;
				case 'B':
					$Eoc_chigins->chigin_type = 3;
					$Eoc_chigins->haraidasi = 4;
					break;
				case 'H':
					$Eoc_chigins->chigin_type = 6;
					$Eoc_chigins->haraidasi = 10;
					break;
				default:
					break;
			}



			//カテゴリーごとの情報から備考の作成
			$remark = "";
			foreach ($Eoc_chigins_unfinished_by_category as $item) {
				if($remark != ""){$remark .= "\n";}

				if($item->MATERIAL != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->MATERIAL;
				}
				if($item->jewelry != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->jewelry;
				}
				if($item->brand != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->brand;
				}
				if($item->details != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->details;
				}
				if($item->item != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->item;
				}
				if($item->weight != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->weight.' '.$item->gram;
				}
				if($item->main_stone != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->main_stone;
				}
				if($item->main_stone_unit_price != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->main_stone_unit_price;
				}
				if($item->aside_stonel != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->aside_stonel;
				}
				if($item->aside_stone_unit_price != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->aside_stone_unit_price;
				}
				if($item->bikou != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->bikou;
				}
				if($item->price != ''){
					if($remark != ""){$remark .= " ";}
					$remark .= $item->price;
				}

			}

			$Eoc_chigins->remark = $remark;
			$Eoc_chigins->save();

		}//END for 買い備合算カテゴリー


	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取備考作成 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
