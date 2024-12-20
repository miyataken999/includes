<?php
//exit();
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'

set_time_limit(0);
ini_set("max_execution_time",0);
// phpinfo();
// exit();
//default 128M
// 危険なので、大量にある場合のみにする。
// ini_set('memory_limit', '512M');

include_once $dirname.'/config.php';

//GSSシートID
//列違いがあった場合、コピーしてからでないとダメ。どうにかする。
$spreadsheetId = $url_id;

//シート名
$sheet_name = $sht_name;

// $range = $sheet_name.'!'.'A1:HE5';
$range = $sheet_name.'!'.'A1:'.$range_column.'500';


$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
if (isset($values[0][16])) {
	$ycbm_id = $values[0][16]; // 1行目Q列
} else {
	$ycbm_id = ""; // 1行目Q列
}

// var_dump($values);
// exit();


//0=A,1=B,2=C

if (count($values) == 0) {
	$return = "スプレッドシートのデータが取得できませんでした。\n";
	print $return;
}elseif(($values[1][$ecc_and_price_column] != "金額") OR ($values[2][$ecc_and_price_column] == "")){
	//金額列が空欄だったら終了
?>
	金額欄が空欄、若しくは取得できませんでした。
	<br>
	ボタンを押し間違えた可能性もあります。
	<br>
	再度お願いいたします。
<?php
}else{

	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//店頭取引データ 作成
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$Eoc_unfinished = "";
		$Eoc_unfinished = Eoc_unfinished::where('url_id',$spreadsheetId)->where('sheet_id',$sht_id)->find_one();
		if($Eoc_unfinished == false){
	        // echo "false\n";
	        $Eoc_unfinished = Eoc_unfinished::create();
			$Eoc_unfinished->url_id = $spreadsheetId;
			$Eoc_unfinished->sheet_id = $sht_id;
			$Eoc_unfinished->sheet_title = $sht_title;
			$Eoc_unfinished->sheet_url = $sht_url;


			// $Eoc_unfinished->created_at = date("Y-m-d H:i:s",strtotime($values[0][5]));
			$Eoc_unfinished->created_at = $now_time;
		}
		$sht_id = $Eoc_unfinished->sheet_id;
		// $sht_id = intval($sht_id);
		//顧客SEQのセルから取得
		$ecc_id = $values[0][$ecc_and_price_column];
		// print "<br>ecc_id　".$ecc_id."<br>";
		//テスト用太郎
		// $ecc_id = 4460;
		// $ecc_id = "";
		$Eoc_unfinished->sheet_name = $sheet_name;
		//全合わずフラグ
		$Eoc_unfinished->zenawazu = 0;




		//査定人を取得、変換
		$satei_by = $values[0][$satei_column];
		$satei_name = $satei_by;
		// print "<br>査定人　".$satei_by."<br>";

		if($satei_by != ''){
			$satei_to_id = users::where('first_name',$satei_by)->find_one()->cw_TO_id;
			$satei_by = users::where('first_name',$satei_by)->find_one()->user_id;
		}else{
			$satei_by = 0;
		}


		$Eoc = Eoc::where('ecc_id',$ecc_id)->find_one();
	
//print($Eoc);
//exit();
	$ecc_new = "";
		if($Eoc == false){
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//顧客データ作成
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$Eoc = Eoc::create();
			$Eoc->created_t = $now_time;
			$Eoc->_registerd_id = $satei_by;

			//メルマガ・DM デフォルト拒否
			$Eoc->mail_check_type = 1;
			$Eoc->dm_check_type = 1;

			// $Eoc->purchase_type = 1;

			$Eoc->save();
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//顧客データ作成
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$ecc_id = 1111://$Eoc->ecc_id;
			$ecc_new = "new";

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//顧客SEQの書き込み
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$input_values = array(
			    array(
			        $ecc_id
			    ),
			);
			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
			  'valueInputOption' => 'RAW'
			);
			$input_range = $sheet_name.'!'.$ecc_id_column.'1';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//顧客SEQの書き込み END
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


		}//end if


		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//日付の書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$week = array( "日", "月", "火", "水", "木", "金", "土" );
		$wa_nowtime = date("Y年m月d日 ").$week[date("w")]."曜日 ".date("H時i分s秒");
		$input_values = array(
		    array(
		        $wa_nowtime
		    ),
		);
		$input_body = new Google_Service_Sheets_ValueRange(array(
		  'values' => $input_values
		));
		$input_params = array(
		  'valueInputOption' => 'RAW'
		);
		$input_range = $sheet_name.'!F1';
		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//日付の書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//結果の部分の書き込み
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$raitenkekka = '成約';
			$input_values = array(
			    array(
			        $raitenkekka
			    ),
			);
			$input_body = new Google_Service_Sheets_ValueRange(array(
			  'values' => $input_values
			));
			$input_params = array(
			  'valueInputOption' => 'RAW'
			);
			$input_range = $sheet_name.'!E1';
			$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//結果の部分の書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$Eoc_unfinished->ecc_id = $ecc_id;
		$Eoc_unfinished->satei_by = $satei_by;

		$Eoc_unfinished->save();

		$Eoc_unfinished_id = $Eoc_unfinished->id;


		$Eoc_unfinished_search = Eoc_unfinished::where('id',$Eoc_unfinished_id)->find_one();

		$satei_name_array = explode("　",$satei_name);
		$remark_stamp = "";
		if($Eoc->remark != ''){
			$remark_stamp .= "\n";
		}
		$remark_stamp .= date("Y")-2000;
		$remark_stamp .= date("md/Hi")." ".$satei_name_array[0];
		if($Eoc_unfinished_search == false){
			$direct_url = "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished->sheet_id}";
		}else{
			$direct_url = "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished_search->sheet_id}";
		}

		$remark_stamp .= "\n{$direct_url}\n";

		if(!strstr($Eoc->remark,$remark_stamp)){
			$Eoc->remark .= "{$remark_stamp}";
		}

		$Eoc->save();



	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//店頭取引データ 作成 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■




	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//店頭詳細データのリセット
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if($Eoc_unfinished_id != ''){
		$Eoc_chigins_unfinished_delete_target = Eoc_chigins_unfinished::where('Eoc_unfinished_id',$Eoc_unfinished_id)->find_many();
		foreach ($Eoc_chigins_unfinished_delete_target as $delete_target) {
			// $delete_target->delete();
			$delete_target->category = '';
			$delete_target->MATERIAL = '';
			$delete_target->jewelry = '';
			$delete_target->details = '';
			$delete_target->item = '';
			$delete_target->weight = '';
			$delete_target->gram = '';
			$delete_target->unit_price = '';
			$delete_target->main_stone = '';
			$delete_target->main_stone_unit_price = '';
			$delete_target->aside_stone = '';
			$delete_target->aside_stone_unit_price = '';
			$delete_target->bikou = '';
			$delete_target->price = null;
			$delete_target->satei_by = null;
			$delete_target->brand = '';
			$delete_target->sub_item = '';
			$delete_target->product_id = null;
			$delete_target->sales_price = null;

			$delete_target->save();
		}
	}
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//店頭詳細データのリセット END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


	// echo "店頭取引データ 完了<br>";
	// echo "顧客データ 完了<br>";
	// echo "日付入力 完了<br>";
	// echo "店頭詳細データ リセット完了<br>";
	// echo "商品データ生成に入ります。<br>";
    @ob_flush();
    // @flush();


	$row_i = 0;
	$skip_i = 0;
	foreach ($values as $row) {
		$skip_i++;
		if($skip_i < 3){
			continue;
		}
		$row_i++;

		//連番通りの行だったら処理開始
		if($row[0] == $row_i){
			// echo '$row[0] start '.$row[0]."<br>";

			//金額列が空欄だったら終了
			if(
				($row[$ecc_and_price_column] == "")
				or
				($row[$ecc_and_price_column] == 0)
			){
				// echo '金額列が空欄 $row[0] '.$row[0]."<br>";
				break;
			}

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//店頭詳細作成
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
				if($gss_type == 'housyoku'){

					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
					//宝飾
					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
						$Eoc_chigins_unfinished = "";
						$Eoc_chigins_unfinished = Eoc_chigins_unfinished::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('row',$row_i)->find_one();
						if($Eoc_chigins_unfinished == false){
					        $Eoc_chigins_unfinished = Eoc_chigins_unfinished::create();
							$Eoc_chigins_unfinished->Eoc_unfinished_id = $Eoc_unfinished_id;
							$Eoc_chigins_unfinished->row = $row_i;
						}

						//左からガラガラ取得してく。

						$Eoc_chigins_unfinished->category = strtoupper($row[1]);
						$Eoc_chigins_unfinished->MATERIAL = $row[2];
						$Eoc_chigins_unfinished->jewelry = $row[3];
						$Eoc_chigins_unfinished->details = $row[4];
						$Eoc_chigins_unfinished->item = $row[5];
						$Eoc_chigins_unfinished->weight = $row[6];
						$Eoc_chigins_unfinished->gram = $row[7];
						$Eoc_chigins_unfinished->unit_price = str_replace(",","",$row[8]);
						$Eoc_chigins_unfinished->main_stone = $row[9];
						$Eoc_chigins_unfinished->main_stone_unit_price = $row[10];
						$Eoc_chigins_unfinished->aside_stone = $row[11];
						$Eoc_chigins_unfinished->aside_stone_unit_price = $row[12];
						// $Eoc_chigins_unfinished->bikou = $row[13];
						$Eoc_chigins_unfinished->brand = $row[13];
						$Eoc_chigins_unfinished->price = str_replace(",","",$row[15]);
						if($row[16] != ''){
							$sales_price = floor($row[16]);
						}else{
							$sales_price = 0;
						}
						$Eoc_chigins_unfinished->sales_price = $sales_price;
						$Eoc_chigins_unfinished->satei_by = $satei_by;

						try {
							$Eoc_chigins_unfinished->save();
						} catch (Exception $e) {
							echo $e->getMessage();
							exit();

						}

					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
					//宝飾 END
					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
				}else{

					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
					//服飾
					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
						$Eoc_chigins_unfinished = "";
						$Eoc_chigins_unfinished = Eoc_chigins_unfinished::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('row',$row_i)->find_one();
						if($Eoc_chigins_unfinished == false){
					        $Eoc_chigins_unfinished = Eoc_chigins_unfinished::create();
							$Eoc_chigins_unfinished->Eoc_unfinished_id = $Eoc_unfinished_id;
							$Eoc_chigins_unfinished->row = $row_i;
						}

						//左からガラガラ取得してく。

						$Eoc_chigins_unfinished->category = strtoupper($row[1]);
						$Eoc_chigins_unfinished->brand = $row[2];
						$Eoc_chigins_unfinished->details = $row[4];
						$Eoc_chigins_unfinished->sub_item = $row[5];
						$Eoc_chigins_unfinished->item = $row[7];
						$Eoc_chigins_unfinished->bikou = $row[9];
						$Eoc_chigins_unfinished->price = str_replace(",","",$row[14]);
						$Eoc_chigins_unfinished->sales_price = floor($row[15]);
						$Eoc_chigins_unfinished->satei_by = $satei_by;

						try {
							$Eoc_chigins_unfinished->save();
						} catch (Exception $e) {
							echo $e->getMessage();
							exit();

						}

					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
					//服飾 END
					//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

				}

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//店頭詳細作成 END
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//商品生成
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//該当する Eoc_unfinished_id がない場合のみ生成。
			$shouhin = shouhin::where('Eoc_chigins_unfinished_id',$Eoc_chigins_unfinished->id)->find_one();
			if($shouhin == false){
		        $shouhin = shouhin::create();
				$shouhin->Eoc_unfinished_id = $Eoc_unfinished_id;
				$shouhin->Eoc_chigins_unfinished_id = $Eoc_chigins_unfinished->id;


				$shouhin->save();

			}


			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			// ST不変ラベル出力
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			if(
				(($Eoc_chigins_unfinished->category == 'D') && ($Eoc_chigins_unfinished->MATERIAL == 'Diamond'))
				OR ($Eoc_chigins_unfinished->category == 'J')
				OR ($Eoc_chigins_unfinished->category == 'B')
				OR ($Eoc_chigins_unfinished->category == 'W')
			){
				// $shouhin->tentou_label_output_flag = '1';
				$shouhin->tentou_label_output_flag = '';
			}else{
				$shouhin->tentou_label_output_flag = '';
			}
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			// ST不変ラベル出力 END
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

			//商品生成プログラム
			include $dirname.'/shouhin_batch.php';

			//あっても無くてもSEQを更新する。
			//これによって、商品側の店頭詳細IDが基準になる
			$Eoc_chigins_unfinished->product_id = $shouhin->product_id;
			try {
				$Eoc_chigins_unfinished->save();
			} catch (Exception $e) {
				echo $e->getMessage();
				exit();

			}

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//商品生成 END
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

			// echo '$row[0] end '.$row[0]."<br>";

		    @ob_flush();
		    // @flush();


		}else{
			//連番が切れた時点で終了
			// echo '連番が切れた $row[0] '.$row[0]."<br>";
			break;
		}
	}//end foreach GSSデータ処理終了



	$row_i--;
	// $url = "https://rifa.life/shop5test/Eoc_unfinished_list.php?q=(id~contains~{$Eoc_unfinished_id})";
	$url = "https://rifa.life/shop5/Eoc_edit.php?editid1={$ecc_id}";




//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//処理が終了したGSSデータを基に、カテゴリーごとに合算して買い備生成
//GSS URL ID（見込み顧客ID） × カテゴリー でキーにして、合算
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	//買い備生成プログラム
	include $dirname.'/kaibi_seiyaku_batch.php';



	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//csv作成
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

		// $csv = array();
		// $row = array();
		// $row[] = mb_convert_encoding("SEQ","SJIS");
		// $row[] = mb_convert_encoding("品番","SJIS");
		// $row[] = mb_convert_encoding("タイトル","SJIS");
		// $row[] = mb_convert_encoding("バーコード","SJIS");
		// $row[] = mb_convert_encoding("買取額","SJIS");
		// $row[] = mb_convert_encoding("販売額","SJIS");
		// $row[] = mb_convert_encoding("機番","SJIS");
		// $row[] = mb_convert_encoding("型番","SJIS");
		//
		//
		// $row[] = mb_convert_encoding("付属品","SJIS");
		// $row[] = mb_convert_encoding("鑑別","SJIS");
		// $row[] = mb_convert_encoding("鑑定機関","SJIS");
		//
		//
		// $csv[] = $row;
		//
		// // $Eoc_unfinished_id = 13267;
		//
		// $shouhin = shouhin::where("Eoc_unfinished_id",$Eoc_unfinished_id)->where("tentou_label_output_flag",1)->find_many();
		// foreach ($shouhin as $item) {
		// 	$row = "";
		// 	$row = array();
		// 	$val = $item->product_id;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = $item->yahoo_sinaban;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = create_ttl($item);
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = $item->product_id;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = convert_ingo($item->price);
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = convert_ingo($item->sales_price);
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		//
		// 	$val = $item->serial_number;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = $item->yahoo_kataban;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		//
		// 	$val = $item->accessories;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = $item->Discrimination;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		// 	$val = $item->Appraiser;
		// 	$val = mb_convert_encoding($val,"SJIS");
		// 	$row[] = $val;
		//
		//
		//
		//
		//
		//
		// 	$csv[] = $row;
		// 	$item->tentou_label_output_flag='';
		// 	$item->save();
		// }
		//
		// // $filepass = '/usr/local/src/google_api/get_buy_card/tmp/'.date("YmdHis").'.csv';
		// $filepass = '/usr/local/src/google_api/get_buy_card/tmp/label_output.csv';
		//
		//
		// // var_dump($csv);
		//
		// try {
		// 	file_put_contents($filepass,'');
		// } catch (Exception $e) {
		// 	// echo $e->getMessage();
		// 	// exit;
		// }
		//
		//
		// // ファイルを書き込み用に開きます。
		// $f = fopen($filepass, "w");
		// // 正常にファイルを開くことができていれば、書き込みます。
		// if ( $f ) {
		//   // $ary から順番に配列を呼び出して書き込みます。
		//   foreach($csv as $line){
		//     // fputcsv関数でファイルに書き込みます。
		//     fputcsv($f, $line);
		//   }
		// }
		// // ファイルを閉じます。
		// fclose($f);
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//csv作成 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//mypage同期処理
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買い備生成プログラム
	include $dirname.'/mypage_sync_data.php';
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//mypage同期処理 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//合計金額をCW送信
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$Eoc_unfinished_search = Eoc_unfinished::where('id',$Eoc_unfinished_id)->find_one();
		$tantousya = explode("　",$satei_name)[0];
		$sum_price_total = number_format($sum_price_total);
		$cw_body = "{$sum_price_max_cate}　{$sum_price_total}　{$tantousya}";
		// $cw_body .= "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished->sheet_id}";
		$mail_body = "{$sum_price_max_cate}　{$sum_price_total}　{$tantousya}";
		if($Eoc_unfinished_search == false){
			$cw_body .= "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished->sheet_id}";
			$mail_body .= "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished->sheet_id}";

		}else{
			$cw_body .= "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished_search->sheet_id}";
			$mail_body .= "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished_search->sheet_id}";
		}
		$cw_body .= "\n店頭取引ページ\n";
		$cw_body .= "\nhttps://rifa.life/shop5/Eoc_unfinished_list.php?masterkey1={$ecc_id}&mastertable=Eoc\n";
		$mail_body .= "\n店頭取引ページ\n";
		$mail_body .= "\nhttps://rifa.life/shop5/Eoc_unfinished_list.php?masterkey1={$ecc_id}&mastertable=Eoc\n";


		// メール送信
		$sendPostData = array(
			"subject" => "店頭成約結果",
			"text" => $mail_body,
		);
		$sendPostData = http_build_query($sendPostData, "", "&");
		$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
		$sendPostContext = stream_context_create($sendPostDataOptions);
		try{
			file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
		}catch(Exception $e){}
		
		// スペースチャット送信
		$sendPostData = array(
			"text" => "店頭成約結果\n" . $cw_body,
		);
		$sendPostData = http_build_query($sendPostData, "", "&");
		$sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
		$sendPostContext = stream_context_create($sendPostDataOptions);
		try{
			file_get_contents('https://rifa.life/refastaProject/pushGoogleChatSpace/AAAAJHp4kGk', false, $sendPostContext);
		}catch(Exception $e){}
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//合計金額をCW送信 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//$ecc_id = 11111;


	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//タイムライン生成
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	try{
		$Eoc_timeline = Eoc_timeline::create();
		$Eoc_timeline->ecc_id = $ecc_id;
		$Eoc_timeline->category = 8;// 来店
		$Eoc_timeline->title = "買取　{$tantousya}";
		if($Eoc_unfinished_search == false){
			$Eoc_timeline->detail = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished->sheet_id}";
		}else{
			$Eoc_timeline->detail = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$Eoc_unfinished_search->sheet_id}";
		}
		$Eoc_timeline->create_by = $satei_by;
//		$Eoc_timeline->create_at = date("Y-m-d H:i:s");
		$Eoc_timeline->save();
	}catch(Exception $e){}
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//タイムライン生成 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■




	/**
	 * 店頭予約データ更新
	 * @return void
	 */
	function update_ycbm($ycbm_id, $ecc_id='', $Eoc_unfinished_id='')
	{
		$ycbm_bookings = ycbm_bookings::where('id',$ycbm_id)->find_one();
		if($ycbm_bookings != false){
			$ycbm_bookings->ecc_id = $ecc_id;
			$ycbm_bookings->Eoc_unfinished_id = $Eoc_unfinished_id;
			$ycbm_bookings->save();
		}
	}

	try {
		if ((isset($ycbm_id)) && (isset($ecc_id)) && (isset($Eoc_unfinished_id))){
			update_ycbm($ycbm_id, $ecc_id, $Eoc_unfinished_id);
		}
	} catch(Exception $e) {
		// なにもしない
	}

?>
<div><?=$row_i?>個</div>
<div>登録・更新完了しました。</div>
<div>▼管理画面</div>
<div><a href='<?=$url?>' id='link_eoc' target='_blank' ><?=$url?></a></div>
<div>▼商品数が足りない場合▼</div>
<div>金額欄が空欄だと反映されないので、各金額欄の確認をしてください。</div>

<script type="text/javascript">
	window.open("https://rifa.life/lounge_API/brother/print_only_form.php?ecc_id=<?=$ecc_id?>&uid=<?=$satei_by?>");
	// location.href='<?=$url?>';
<?php
	if($ecc_new == "new"){
?>
<?php
	}
?>
</script>


<?php

}//end else ⇒スプレッドシートのデータがあった場合の処理 終了
?>
