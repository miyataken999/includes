宝飾
<br>
<?php

ini_set("display_errors",1);

//DBライブラリ読み込み
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins_unfinished.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/Eoc_chigins.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/users.php';

//スプレッドシートAPIクライアント読み込み
include_once "/home/apache/vendor/autoload.php";
//スプレッドシート関数読み込み
include_once "/usr/local/src/google_api/function.php";




define('APPLICATION_NAME', 'Google Sheets API start');
define('CREDENTIALS_PATH', '/home/apache/.credentials/sheets.googleapis.com-php-quickstart.json');
define('CLIENT_SECRET_PATH', '/usr/local/src/google_api/client_secret.json');

define('SCOPES', implode(' ', array(
  Google_Service_Sheets::SPREADSHEETS)
));


// if (php_sapi_name() != 'cgi-fcgi') {
// if (php_sapi_name() != 'cli') {
if (php_sapi_name() != 'apache2handler') {
  throw new Exception('不正なアクセスです。');
}









$url_id = $_POST["url_id"];
$sht_id = $_POST["sht_id"];
$sht_name = $_POST["sht_name"];
$sht_title = $_POST["sht_title"];
$sht_url = $_POST["sht_url"];









$client = getClient();
$service = new Google_Service_Sheets($client);




//GSSシートID
//列違いがあった場合、コピーしてからでないとダメ。どうにかする。
$spreadsheetId = $url_id;

//シート名
$sheet_name = $sht_name;

// $range = $sheet_name.'!'.'A1:HE5';
$range = $sheet_name.'!'.'A1:P42';


$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();




//0=A,1=B,2=C

if (count($values) == 0) {
	$return = "スプレッドシートのデータが取得できませんでした。\n";
	print $return;
}elseif($values[2][15] == ""){
	//金額列が空欄だったら終了
?>
	金額欄が空欄、若しくは取得できませんでした。
	<br>
	ボタンを押し間違えた可能性もあります。
	<br>
	再度お願いいたします。
<?php
}else{

	$Eoc_unfinished = "";
	$Eoc_unfinished = Eoc_unfinished::where('url_id',$spreadsheetId)->where('sheet_id',$sht_id)->where('sheet_name',$sheet_name)->find_one();
	if($Eoc_unfinished == false){
        // echo "false\n";
        $Eoc_unfinished = Eoc_unfinished::create();
		$Eoc_unfinished->url_id = $spreadsheetId;
		$Eoc_unfinished->sheet_id = $sht_id;
		$Eoc_unfinished->sheet_name = $sheet_name;
		$Eoc_unfinished->sheet_title = $sht_title;
		$Eoc_unfinished->sheet_url = $sht_url;


		// $Eoc_unfinished->created_at = date("Y-m-d H:i:s",strtotime($values[0][5]));
		$Eoc_unfinished->created_at = date("Y-m-d H:i:s");
	}
	//顧客SEQのセルから取得
	$ecc_id = $values[0][15];
	print "<br>ecc_id　".$ecc_id."<br>";
	//テスト用太郎
	// $ecc_id = 4460;
	// $ecc_id = "";





	//査定人を取得、変換
	$satei_by = $values[0][13];
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
		$Eoc->created_t = date("Y-m-d H:i:s");
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
		$input_range = $sheet_name.'!P1';
		$input_result = $service->spreadsheets_values->update($spreadsheetId, $input_range,$input_body, $input_params);
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		//顧客SEQの書き込み END
		//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	}//end if

	$satei_name_array = explode("　",$satei_name);
	$remark_stamp = "";
	if($Eoc->remark != ''){
		$remark_stamp .= "\n";
	}
	$remark_stamp .= date("Y")-2000;
	$remark_stamp .= date("md/Hi")." ".$satei_name_array[0];
	$direct_url = "https://docs.google.com/spreadsheets/d/{$url_id}/edit#gid={$sht_id}";

	$remark_stamp .= "\n{$direct_url}";

	if(!strstr($Eoc->remark,$remark_stamp)){
		$Eoc->remark .= "{$remark_stamp}";
	}

	$Eoc->save();



	$Eoc_unfinished->ecc_id = $ecc_id;

	$Eoc_unfinished->satei_by = $satei_by;
	$Eoc_unfinished->save();



	$Eoc_unfinished_id = $Eoc_unfinished->id;

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
			if($row[15] == ""){
				break;
			}

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//見込み伝票作成
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
			$Eoc_chigins_unfinished->unit_price = $row[8];
			$Eoc_chigins_unfinished->main_stone = $row[9];
			$Eoc_chigins_unfinished->main_stone_unit_price = $row[10];
			$Eoc_chigins_unfinished->aside_stone = $row[11];
			$Eoc_chigins_unfinished->aside_stone_unit_price = $row[12];
			$Eoc_chigins_unfinished->bikou = $row[13];
			$Eoc_chigins_unfinished->price = str_replace(",","",$row[15]);
			$Eoc_chigins_unfinished->satei_by = $satei_by;

			$Eoc_chigins_unfinished->save();

			//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			//見込み伝票作成 END
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


	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	//買取備考作成
	//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


		//GSS上のカテゴリーを配列に格納
		$category_Array = array('K','D','J','W','B','H');//合わずのAは省いとく

		for ($category_i=0; $category_i < count($category_Array); $category_i++) {
			$cate = $category_Array[$category_i];

			//見込み顧客IDとカテゴリーをキーにして、まず合算
			$Eoc_chigins_unfinished_by_category = Eoc_chigins_unfinished::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('category',$cate)->find_many();
			// $sum_price = $Eoc_chigins_unfinished_by_category->sum('price');
			$sum_price = Eoc_chigins_unfinished::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('category',$cate)->sum('price');



			$Eoc_chigins = Eoc_chigins::where('Eoc_unfinished_id',$Eoc_unfinished_id)->where('category',$cate)->find_one();
			if($Eoc_chigins == false){
				if($sum_price > 0){
			        $Eoc_chigins = Eoc_chigins::create();
					$Eoc_chigins->Eoc_unfinished_id = $Eoc_unfinished_id;
					$Eoc_chigins->category = $cate;
					$Eoc_chigins->ching_created_at = date("Y-m-d H:i:s");
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


