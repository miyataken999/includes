<?php
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





	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//csv作成
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

		$csv = array();
		$row = array();
		$row[] = mb_convert_encoding("SEQ","SJIS");
		$row[] = mb_convert_encoding("品番","SJIS");
		$row[] = mb_convert_encoding("タイトル","SJIS");
		$row[] = mb_convert_encoding("バーコード","SJIS");
		$row[] = mb_convert_encoding("買取額","SJIS");
		$row[] = mb_convert_encoding("販売額","SJIS");

		$row[] = mb_convert_encoding("付属品","SJIS");
		$row[] = mb_convert_encoding("鑑別","SJIS");
		$row[] = mb_convert_encoding("鑑定機関","SJIS");
		$row[] = mb_convert_encoding("機番（保管用）","SJIS");
		$row[] = mb_convert_encoding("型番","SJIS");

		$csv[] = $row;

		$Eoc_unfinished_id = 3899;

		// $shouhin = shouhin::where("Eoc_unfinished_id",$Eoc_unfinished_id)->where("tentou_label_output_flag",1)->find_many();
		$shouhin = shouhin::where("Eoc_unfinished_id",$Eoc_unfinished_id)->find_many();

		echo "count ".count($shouhin)."\n";
		foreach ($shouhin as $item) {
			$row = "";
			$row = array();
			$val = $item->product_id;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = $item->yahoo_sinaban;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = create_ttl($item);
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = $item->product_id;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = convert_ingo($item->price);
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = convert_ingo($item->sales_price);
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = $item->accessories;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = $item->Discrimination;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = $item->Appraiser;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = $item->serial_number_for_storage;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;

			$val = $item->yahoo_kataban;
			$val = mb_convert_encoding($val,"SJIS");
			$row[] = $val;


			$csv[] = $row;
			$item->tentou_label_output_flag='';
			$item->save();
		}

		// $filepass = '/usr/local/src/google_api/get_buy_card/tmp/'.date("YmdHis").'.csv';
		$filepass = '/usr/local/src/google_api/get_buy_card/tmp/label_output.csv';


		var_dump($csv);

		try {
			file_put_contents($filepass,'');
		} catch (Exception $e) {
			// echo $e->getMessage();
			// exit;
		}


		// ファイルを書き込み用に開きます。
		$f = fopen($filepass, "w");
		// 正常にファイルを開くことができていれば、書き込みます。
		if ( $f ) {
		  // $ary から順番に配列を呼び出して書き込みます。
		  foreach($csv as $line){
		    // fputcsv関数でファイルに書き込みます。
		    fputcsv($f, $line);
		  }
		}
		// ファイルを閉じます。
		fclose($f);
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//csv作成 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■



	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//合計金額をCW送信
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		$tantousya = explode("　",$satei_name)[0];
		$sum_price_total = number_format($sum_price_total);
		$cw_body = "▼テスト送信です▼{$sum_price_max_cate}　{$sum_price_total}　[To:{$satei_to_id}]{$tantousya}";
		$cw_body .= "\nhttps://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit#gid={$sht_id}";

		try {
			cw_file_up($cw_body,'59828282','nishitani',$filepass);
		} catch (Exception $e) {
			print "チャットワークファイル送信に失敗しました。".$e;

			cw_push($cw_body,'59828282','sekkyaku');
		}
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//合計金額をCW送信 END
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
