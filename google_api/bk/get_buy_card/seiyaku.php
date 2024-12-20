<?php
$myPath = __FILE__;              //  /home/php/basic/test.php
$dirname = dirname($myPath);     // $dirname => '/home/php/basic'


include_once $dirname.'/config.php';

//GSSシートID
//列違いがあった場合、コピーしてからでないとダメ。どうにかする。
$spreadsheetId = $url_id;

//シート名
$sheet_name = $sht_name;

// $range = $sheet_name.'!'.'A1:HE5';
$range = $sheet_name.'!'.'A1:'.$range_column.'42';


$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

//0=A,1=B,2=C

if (count($values) == 0) {
	$return = "スプレッドシートのデータが取得できませんでした。\n";
	print $return;
}elseif($values[2][$ecc_and_price_column] == ""){
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
		//顧客SEQのセルから取得
		$ecc_id = $values[0][$ecc_and_price_column];
		print "<br>ecc_id　".$ecc_id."<br>";
		//テスト用太郎
		// $ecc_id = 4460;
		// $ecc_id = "";
		$Eoc_unfinished->sheet_name = $sheet_name;
		//全合わずフラグ
		$Eoc_unfinished->zenawazu = 0;




		//査定人を取得、変換
		$satei_by = $values[0][$satei_column];
		$satei_name = $satei_by;
		print "<br>査定人　".$satei_by."<br>";

		if($satei_by != ''){
			$satei_by = users::where('first_name',$satei_by)->find_one()->user_id;
		}else{
			$satei_by = 0;
		}


		$Eoc = Eoc::where('ecc_id',$ecc_id)->find_one();
		$ecc_new = "";
		if($Eoc == false){
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//顧客データ作成
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$Eoc = Eoc::create();
			$Eoc->created_t = $now_time;
			$Eoc->_registerd_id = $satei_by;
			// $Eoc->purchase_type = 1;

			$Eoc->save();
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//顧客データ作成
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			$ecc_id = $Eoc->ecc_id;
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
			$input_range = $sheet_name.'!'.$range_column.'1';
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



		$satei_name_array = explode("　",$satei_name);
		$remark_stamp = "";
		if($Eoc->remark != ''){
			$remark_stamp .= "\n";
		}
		$remark_stamp .= date("Y")-2000;
		$remark_stamp .= date("md/Hi")." ".$satei_name_array[0];
		$direct_url = "https://docs.google.com/spreadsheets/d/{$url_id}/edit#gid={$sht_id}";

		$remark_stamp .= "\n{$direct_url}\n";

		if(!strstr($Eoc->remark,$remark_stamp)){
			$Eoc->remark .= "{$remark_stamp}";
		}

		$Eoc->save();


		$Eoc_unfinished->ecc_id = $ecc_id;
		$Eoc_unfinished->satei_by = $satei_by;

		$Eoc_unfinished->save();

		$Eoc_unfinished_id = $Eoc_unfinished->id;
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//店頭取引データ 作成 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■



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

			//金額列が空欄だったら終了
			if($row[$ecc_and_price_column] == ""){
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

						$Eoc_chigins_unfinished->category = $row[1];
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
						$Eoc_chigins_unfinished->bikou = $row[13];
						$Eoc_chigins_unfinished->price = str_replace(",","",$row[15]);
						$Eoc_chigins_unfinished->satei_by = $satei_by;

						$Eoc_chigins_unfinished->save();
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

						$Eoc_chigins_unfinished->category = $row[1];
						$Eoc_chigins_unfinished->brand = $row[2];
						$Eoc_chigins_unfinished->details = $row[4];
						$Eoc_chigins_unfinished->sub_item = $row[5];
						$Eoc_chigins_unfinished->item = $row[7];
						$Eoc_chigins_unfinished->bikou = $row[9];
						$Eoc_chigins_unfinished->price = str_replace(",","",$row[14]);
						$Eoc_chigins_unfinished->satei_by = $satei_by;

						$Eoc_chigins_unfinished->save();

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

			//商品生成プログラム
			include $dirname.'/shouhin_batch.php';

			//あっても無くてもSEQを更新する。
			//これによって、商品側の店頭詳細IDが基準になる
			$Eoc_chigins_unfinished->product_id = $shouhin->product_id;
			$Eoc_chigins_unfinished->save();

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//商品生成 END
			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■



		}else{
			//連番が切れた時点で終了
			break;
		}
	}//end foreach GSSデータ処理終了

	$row_i--;
	// $url = "https://urlounge.co.jp/shop5test/Eoc_unfinished_list.php?q=(id~contains~{$Eoc_unfinished_id})";
	$url = "https://urlounge.co.jp/shop5/Eoc_edit.php?editid1={$ecc_id}";




//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//処理が終了したGSSデータを基に、カテゴリーごとに合算して買い備生成
//GSS URL ID（見込み顧客ID） × カテゴリー でキーにして、合算
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	//買い備生成プログラム
	include $dirname.'/kaibi_seiyaku_batch.php';

	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//合計金額をCW送信
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$tantousya = explode("　",$satei_name)[0];
		$sum_price_total = number_format($sum_price_total);
		$cw_body = "{$sum_price_max_cate}　{$sum_price_total}　{$tantousya}";
		$cw_body .= "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$sht_id}";

		try {
			cw_push($cw_body,'59828282','sekkyaku');
			print "チャットワーク送信完了。";
		} catch (Exception $e) {
			print "チャットワーク送信に失敗しました。".$e;
		}
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//合計金額をCW送信 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


?>
<div><?=$row_i?>個</div>
<div>登録・更新完了しました。</div>
<div>▼管理画面</div>
<div><a href='<?=$url?>' id='link_eoc' target='_blank' ><?=$url?></a></div>
<div>▼商品数が足りない場合▼</div>
<div>金額欄が空欄だと反映されないので、各金額欄の確認をしてください。</div>

<script type="text/javascript">
	location.href='<?=$url?>';
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


