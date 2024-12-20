<?php

//最新の顧客SEQに変更
$shouhin->ecc_id = $ecc_id;
//「形状」を「形状名」へ。
$shouhin->product_style = $Eoc_chigins_unfinished->item;



//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//ブランド
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
if($Eoc_chigins_unfinished->brand != ''){

	$mst_brand = mst_brand::where_like('name','%'.$Eoc_chigins_unfinished->brand.'%')->limit(1)->find_one();
	if($mst_brand == false){
		$shouhin->product_num = $Eoc_chigins_unfinished->brand;
	}else{
		$shouhin->product_num = $mst_brand->id;
	}

}else{
	$shouhin->product_num = null;
}
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//ブランド END
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//大カテ中カテ性別の分岐
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if($Eoc_chigins_unfinished->category == 'D'){
		if($Eoc_chigins_unfinished->MATERIAL == 'Diamond Melee'){
			$shouhin->category_id = 17;
			$shouhin->sub_category_id1 = 420;
			$shouhin->yahoo_sex = 3;
		}else{
			$shouhin->category_id = 17;
			$shouhin->sub_category_id1 = 415;
			$shouhin->yahoo_sex = 3;
		}
	}elseif($Eoc_chigins_unfinished->category == 'H'){
		$shouhin->category_id = 12;
		$shouhin->sub_category_id1 = 840;
		$shouhin->yahoo_sex = 3;
	}elseif($Eoc_chigins_unfinished->category == 'A'){
			// $shouhin->category_id = 12;
			// $shouhin->sub_category_id1 = 838;
			$shouhin->yahoo_sex = 3;


		if($Eoc_chigins_unfinished->MATERIAL == 'Diamond Melee'){
			$shouhin->category_id = 17;
			$shouhin->sub_category_id1 = 420;
		}else{
			//大カテ
			if($Eoc_chigins_unfinished->item != ''){
				$store_categories = store_categories::where_like('category_name','%'.$Eoc_chigins_unfinished->item.'%')->limit(1)->find_one();
				if($store_categories == false){
					// $daikate = $Eoc_chigins_unfinished->item;
					//ぴったり来なかったなら、その他(12)
					$daikate = 12;
				}else{
					//引っかかたら大カテ挿入
					$daikate = $store_categories->category_id;
				}
			}else{
				//デフォルトではその他(12)
				$daikate = 12;
			}
			$shouhin->category_id = $daikate;


			//中カテ 宝飾
			if($Eoc_chigins_unfinished->jewelry != ''){
				if($store_categories != false){
					$store_sub_categories = store_sub_categories::where('category_id',$store_categories->category_id)->where_like('name','%'.$Eoc_chigins_unfinished->jewelry.'%')->limit(1)->find_one();
					if($store_sub_categories == false){
						// $tyukate = $Eoc_chigins_unfinished->jewelry;
						// イコール、その他(12)だったら。
						$tyukate = 838;
					}else{
						$tyukate = $store_sub_categories->id;
					}
				}else{
					// イコール、その他(12)だったら。
					$tyukate = 838;
				}
			}elseif($Eoc_chigins_unfinished->sub_item != ''){
			//中カテ 服飾
				if($store_categories != false){
					$store_sub_categories = store_sub_categories::where('category_id',$store_categories->category_id)->where_like('name','%'.$Eoc_chigins_unfinished->sub_item.'%')->limit(1)->find_one();
					if($store_sub_categories == false){
						// $tyukate = $Eoc_chigins_unfinished->sub_item;
						// イコール、その他(12)だったら。
						$tyukate = 838;
					}else{
						$tyukate = $store_sub_categories->id;
					}
				}else{
					// イコール、その他(12)だったら。
					$tyukate = 838;
				}
			}else{
				// 中カテになる列が空欄だったら。
				if($daikate == 12){
					$tyukate = 838;
				}else{
					$tyukate = '';
				}
			}
			$shouhin->sub_category_id1 = $tyukate;

		}// end else Diamond Melee


	}else{

		if($Eoc_chigins_unfinished->category == 'K'){
			// $shouhin->category_id = 12;
			// $shouhin->sub_category_id1 = 841;

			/*
			Kでも大カテ中カテが入ってれば入れる！
			*/

			//大カテ
			if($Eoc_chigins_unfinished->item != ''){
				$store_categories = store_categories::where_like('category_name','%'.$Eoc_chigins_unfinished->item.'%')->limit(1)->find_one();
				if($store_categories == false){
					$daikate = 12;
				}else{
					$daikate = $store_categories->category_id;
				}
			}else{
				$daikate = 12;
			}
			$shouhin->category_id = $daikate;


			//中カテ 宝飾
			if($Eoc_chigins_unfinished->jewelry != ''){
				if($store_categories != false){
					$store_sub_categories = store_sub_categories::where('category_id',$store_categories->category_id)->where_like('name','%'.$Eoc_chigins_unfinished->jewelry.'%')->limit(1)->find_one();
					if($store_sub_categories == false){
						$tyukate = 841;
					}else{
						$tyukate = $store_sub_categories->id;
					}
				}else{
					$tyukate = 841;
				}
			}else{
				$tyukate = 841;
			}
			$shouhin->sub_category_id1 = $tyukate;



			//中カテ 服飾
			if($Eoc_chigins_unfinished->sub_item != ''){
				if($store_categories != false){
					$store_sub_categories = store_sub_categories::where('category_id',$store_categories->category_id)->where_like('name','%'.$Eoc_chigins_unfinished->sub_item.'%')->limit(1)->find_one();
					if($store_sub_categories == false){
						$tyukate = 841;
					}else{
						$tyukate = $store_sub_categories->id;
					}
				}else{
					$tyukate = 841;
				}
			}

			$shouhin->sub_category_id1 = $tyukate;


		}else{
			//大カテ
			if($Eoc_chigins_unfinished->item != ''){
				$store_categories = store_categories::where_like('category_name','%'.$Eoc_chigins_unfinished->item.'%')->limit(1)->find_one();
				if($store_categories == false){
					$daikate = $Eoc_chigins_unfinished->item;
				}else{
					$daikate = $store_categories->category_id;
				}
			}else{
				$daikate = '';
			}
			$shouhin->category_id = $daikate;


			//中カテ 宝飾
			if($Eoc_chigins_unfinished->jewelry != ''){
				if($store_categories != false){
					$store_sub_categories = store_sub_categories::where('category_id',$store_categories->category_id)->where_like('name','%'.$Eoc_chigins_unfinished->jewelry.'%')->limit(1)->find_one();
					if($store_sub_categories == false){
						$tyukate = $Eoc_chigins_unfinished->jewelry;
					}else{
						$tyukate = $store_sub_categories->id;
					}
				}else{
					$tyukate = $Eoc_chigins_unfinished->jewelry;
				}
			}else{
				$tyukate = null;
			}
			$shouhin->sub_category_id1 = $tyukate;



			//中カテ 服飾
			if($Eoc_chigins_unfinished->sub_item != ''){
				if($store_categories != false){
					$store_sub_categories = store_sub_categories::where('category_id',$store_categories->category_id)->where_like('name','%'.$Eoc_chigins_unfinished->sub_item.'%')->limit(1)->find_one();
					if($store_sub_categories == false){
						$tyukate = $Eoc_chigins_unfinished->sub_item;
					}else{
						$tyukate = $store_sub_categories->id;
					}
				}else{
					$tyukate = $Eoc_chigins_unfinished->sub_item;
				}
			}

			$shouhin->sub_category_id1 = $tyukate;



		}



		$shouhin->yahoo_sex = 2;

		// if($Eoc->option5 == '男性'){
		// 	$shouhin->yahoo_sex = 1;
		// }elseif($Eoc->option5 == '女性'){
		// 	$shouhin->yahoo_sex = 2;
		// }else{
		// 	$shouhin->yahoo_sex = 1;
		// }

	}
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//大カテ中カテ性別の分岐 END
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■



//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//ステータス分岐
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if( ($Eoc_chigins_unfinished->category == 'J') OR ($Eoc_chigins_unfinished->category == 'B') OR ($Eoc_chigins_unfinished->category == 'W') ){
		$shouhin->status = 32;
	}elseif($Eoc_chigins_unfinished->category == 'K'){
		$shouhin->status = 119;
	}elseif($Eoc_chigins_unfinished->category == 'D'){
		if($Eoc_chigins_unfinished->MATERIAL == 'Diamond Melee'){
			$shouhin->status = 119;
		}else{
			$shouhin->status = 127;
			$product_num = 631;
			$shouhin->product_num = $product_num;
			$brand = mst_brand::where("id",$product_num)->find_one()->name;
			$shouhin->yahoo_junle = $brand;

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//品番生成 ※大カテ中カテ性別が固定のため、可能。
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$shouhin->save(); //seqテーブルに表示させるため、いったん追加処理。
			$seq = seq::where("product_id",$shouhin->product_id)->find_one();

			//イニシャルが空欄だったら X
			if($seq->initial_name == ""){
				$ini = "X";
			}else{
				$ini= $seq->initial_name;
			}

			//seqテーブルの値から品番生成
			$yahoo_seq=$seq->sub_cate.$ini.$seq->c.$seq->d.$seq->sex_ini;
			$shouhin->yahoo_sinaban = $yahoo_seq;
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//品番生成 END
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		}

	}elseif($Eoc_chigins_unfinished->category == 'H'){
		$shouhin->status = 119;
	}elseif($Eoc_chigins_unfinished->category == 'A'){
		$shouhin->AWAZU_DATE = $now_time;
		$shouhin->status = 34;
	}else{
		$shouhin->status = 32;
	}

	//全合わずの場合、ステータスだけ強制的に合わずにする。
	if($zenawazu_flag == 1){
		$shouhin->AWAZU_DATE = $now_time;
		$shouhin->status = 34;
	}
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//ステータス分岐 END
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//品名・詳細と備考
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$komento = '';

	if($Eoc_chigins_unfinished->brand !=''){
		if($komento != ''){
			$komento .= ' ';
		}
		$komento .= $Eoc_chigins_unfinished->brand;
	}
	//金性
	if($Eoc_chigins_unfinished->MATERIAL !=''){
		if($komento != ''){
			$komento .= ' ';
		}
		$komento .= $Eoc_chigins_unfinished->MATERIAL;
	}
	//宝石
	if($Eoc_chigins_unfinished->jewelry !=''){
		if($komento != ''){
			$komento .= ' ';
		}
		$komento .= $Eoc_chigins_unfinished->jewelry;
	}
	//品名詳細
	if($Eoc_chigins_unfinished->details !=''){
		if($komento != ''){
			$komento .= ' ';
		}
		$komento .= $Eoc_chigins_unfinished->details;
	}
	//形状
	if($Eoc_chigins_unfinished->item !=''){
		if($komento != ''){
			$komento .= ' ';
		}
		$komento .= $Eoc_chigins_unfinished->item;
	}
	//備考
	if($Eoc_chigins_unfinished->bikou !=''){
		if($komento != ''){
			$komento .= ' ';
		}
		$komento .= $Eoc_chigins_unfinished->bikou;
	}


	// if($Eoc_chigins_unfinished->category == 'D'){
	// 	$shouhin->description = $komento;
	// }else{
	// 	$shouhin->comment = $komento;
	// }
	$shouhin->comment = $komento;


	$shouhin->Remarks = $Eoc_unfinished->sheet_name."-".$Eoc_chigins_unfinished->row;

	// $shouhin->label_output_flag = 0;
if(isset($Eoc_chigins_unfinished->category)){
	$shouhin->purchase_category = $Eoc_chigins_unfinished->category;
}

// 服飾コメントの内容
// 現在⇒品名+半角スペース+備考
// (ブランド名)
// ※ブランドをコメントに入れる前提での形式です。
// 宝飾コメントの内容
// 現在⇒詳細+半角スペース+備考
// (金性)+半角スペース+(宝石)

//+半角スペース+(品名詳細)+半角スペース+(形状)+半角スペース+(備考)
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//品名・詳細と備考 END
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//店頭詳細のデータから生成。
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$shouhin->gold_property = $Eoc_chigins_unfinished->MATERIAL;
	$shouhin->Gram = $Eoc_chigins_unfinished->weight;
	$shouhin->unit = $Eoc_chigins_unfinished->gram;
	$shouhin->price_per_gram = $Eoc_chigins_unfinished->unit_price;
	$shouhin->Parent_stone = $Eoc_chigins_unfinished->main_stone;
	$shouhin->price_per_parent_stone = 1000*($Eoc_chigins_unfinished->main_stone_unit_price);
	$shouhin->Aside_stone = $Eoc_chigins_unfinished->aside_stone;
	$shouhin->price_per_aside_stone = 1000*($Eoc_chigins_unfinished->aside_stone_unit_price);
	$shouhin->price = $Eoc_chigins_unfinished->price;
	$shouhin->sales_price = $Eoc_chigins_unfinished->sales_price;
	// print "<br>";
	// print "shouhin->price ".$shouhin->price;
	// print "<br>";
	// print "Eoc_chigins_unfinished->price ".$Eoc_chigins_unfinished->price;
	// print "<br>";
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//店頭詳細のデータから生成。 END
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■




//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//日付系
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$status = $shouhin->status;
	if($status == 119){
		$shouhin->nyuukin_price = $shouhin->price;
		$shouhin->kanryou_henbi = $now_time;

		$shouhin->REG_AUTHOR = $satei_by;
		$shouhin->DT_UP_DATE = $now_time;

		$shouhin->AWAZU_NIN = null;
		$shouhin->AWAZU_DATE = null;
	}elseif($status == 34){
		$shouhin->nyuukin_price = null;
		$shouhin->kanryou_henbi = null;

		$shouhin->REG_AUTHOR = null;
		$shouhin->DT_UP_DATE = null;

		$shouhin->AWAZU_NIN = $satei_by;
		$shouhin->AWAZU_DATE = $now_time;
	}else{
		$shouhin->nyuukin_price = null;
		$shouhin->kanryou_henbi = null;

		$shouhin->REG_AUTHOR = $satei_by;
		$shouhin->DT_UP_DATE = $now_time;

		$shouhin->AWAZU_NIN = null;
		$shouhin->AWAZU_DATE = null;
	}

	//全共通
	$shouhin->satei_by = $satei_by;
	$shouhin->satei_hi = $now_time;
	//更新日、更新者
	$shouhin->updated_by = $satei_by;
	$shouhin->updated_at = $now_time;


	$shouhin->logo = '無';
	$shouhin->parts = '無';
	$shouhin->guarantee = '無';




//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//日付系 END
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//直接完了フラグ(G待機じゃなくそのままG完了にする処理)
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
include_once "/usr/local/src/google_api/idiorm_paris/app/mst_gold_property.php";
if($shouhin->status===119){
	$gold_property=$shouhin->gold_property;
	$mst_gold_property=mst_gold_property::where('val',$gold_property)->find_one();
	if($mst_gold_property!==false){
		$direct_completion=$mst_gold_property->direct_completion;
		if($direct_completion===1){
			$shouhin->status=142;
		}
	}


	if(
			($shouhin->category_id == 12)
			and(
				($shouhin->sub_category_id1 == 840)
				or ($shouhin->sub_category_id1 == 838)
				or ($shouhin->sub_category_id1 == 839)
				or ($shouhin->sub_category_id1 == 847)
			)
		){
		$shouhin->status=142;
	}
}
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//直接完了フラグ END
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■



// if($add_label == 'on'){
// 	if($shouhin->status != 34){
// 		$shouhin->tentou_label_output_flag = 1;
// 	}
// }else{
// 	$shouhin->tentou_label_output_flag = 0;
// }
$market_product_performance_original = market_product_performance_original::where('product_id',"$shouhin->product_id")->find_one();
if($market_product_performance_original === false){
	$market_product_create = market_product_performance_original::create();
	$market_product_create->product_id = $shouhin->product_id;
	$market_product_create->created_at = date('Y/m/d H:i:s');
	$market_product_create->front_hidden_flg = 'True';
	//保存っ
	try {
		$market_product_create->save();
	} catch (Exception $e) {
		print $shouhin->product_id;
		print "<br>";
		print $e->getMessage();
		exit();
	}
}






//保存っ
try {
	$shouhin->save();
} catch (Exception $e) {
	print $shouhin->product_id;
	print "<br>";
	print $e->getMessage();
	exit();
}

?>
