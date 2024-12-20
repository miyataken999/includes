<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>ハイシューカＡＰＩテスト用</title>
	</head>
	<body>
<?php
ini_set('display_errors', 1);
//モデルクラスの読み込み
require_once 'signature.php';

//APIキーの設定
//※※ここだけ変更する
$appKey = "a86b180420f4e80";
$secretKey = "777c4b1008b72087e2fad75be13a93082c65f2bb26d832f";

$Signature = new Signature();
$SignatureString = "";

$Name = "";
$KanaName = "";
$ZipCode = "";
$Prefectures = "";
$Address1 = "";
$Address2 = "";
$Tel = "";
$Mail = "";
$TransPoter = "";
$RequestDate = "";
$RequestTime = "";
$BuggNum = "";
$Payment = "";
$BuggName = "";
$ItemCode = "";
$Memo = "";
$RequestNo = "";

$apiUrl = "";
$param = "";
$Response = "";

$timestamp = date(DATE_ISO8601, time());


//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// テスト用設定値ここから
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
	// $_POST["username"] = "向山　奈穂";
	// $_POST["furikana"] = "ムコウヤマ　ナホ";
	// $_POST["zipcode"] = "1700013";
	// $_POST["prefectures"] = "東京都";
	// $_POST["address1"] = "豊島区東池袋１－２５－１４";
	// $_POST["address2"] = "アルファビルディング２階";
	// $_POST["tel"] = "090-3501-6900";
	// $_POST["email"] = "mukoyama@urlounge.co.jp";
	// $_POST["deliverycompanycode"] = "sagawa";
	// $_POST["collectdate"] = "2018-11-15";
	// $_POST["collecttime"] = "18-21";
	// $_POST["packnum"] = 3;
	// $_POST["voucherflg"] = 2;//2=着払い 8=元払い
	// $_POST["baggage-type"] = "衣類";
	// $_POST["package"] = "0";
	// $_POST["note"] = "壊れ物注意";
	// $_POST["regist"] = "新規";
	// $_POST["list"] = "一覧";
	// $_POST["update"] = "変更";
	// $_POST["stop"] = "停止";
	// $_POST["restart"] = "再開";
	// $_POST["requestno"] = "201811000000366";
	// var_dump($_POST);
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// テスト用設定値ここまで
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//


//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 新規ここから
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
	if (array_key_exists("regist", $_POST)){
		//エンドポイントの設定
		$apiUrl = "https://hi-shuka.jp/api/1/orders/regist";

		//集荷依頼者の基本情報
		$requester["username"] = $_POST["username"];
		$requester["furikana"] = $_POST["furikana"];
		$requester["zipcode"] = $_POST["zipcode"];
		$requester["prefectures"] = $_POST["prefectures"];
		$requester["address1"] = $_POST["address1"];
		$requester["address2"] = $_POST["address2"];
		$requester["tel"] = $_POST["tel"];
		$requester["email"] = $_POST["email"];
		$param["requester"] = $requester;

		$Name = $_POST["username"];
		$KanaName = $_POST["furikana"];
		$ZipCode = $_POST["zipcode"];
		$Prefectures = $_POST["prefectures"];
		$Address1 = $_POST["address1"];
		$Address2 = $_POST["address2"];
		$Tel = $_POST["tel"];
		$Mail = $_POST["email"];

		// 集荷依頼情報
		$pickup["deliverycompanycode"] = $_POST["deliverycompanycode"];
		$pickup["collectdate"] = $_POST["collectdate"];
		$pickup["collecttime"] = $_POST["collecttime"];
		$pickup["packnum"] = $_POST["packnum"];
		$pickup["voucherflg"] = $_POST["voucherflg"];
		$pickup["baggage-type"] = $_POST["baggage-type"];
		$pickup["package"] = $_POST["package"];
		$pickup["note"] = $_POST["note"];
		$param["pickup"] = $pickup;

		$param["timestamp"] = $timestamp;

		$TransPoter = $_POST["deliverycompanycode"];
		$RequestDate = $_POST["collectdate"];
		$RequestTime = $_POST["collecttime"];
		$BuggNum = $_POST["packnum"];
		$Payment = $_POST["voucherflg"];
		$BuggName = $_POST["baggage-type"];
		$ItemCode = $_POST["package"];
		$Memo = $_POST["note"];

		$SignatureString = $Signature->CreateSignature($apiUrl. "&POST", $param, $secretKey);

	mb_internal_encoding("utf-8");

	function encode($val){
		$json = "{";
		$json_in = '';
		foreach ($val as $key => $value) {
			if(($key == "requester") OR ($key == "pickup")){
				$json_in .= "\"{$key}\":{";
				foreach ($value as $key2 => $value2) {
					if($json_in != "\"{$key}\":{"){
						$json_in .= ",";
					}
					$json_in .= "\"{$key2}\":\"{$value2}\"";
				}
				$json_in .= "}";
				if($key == "requester"){
					$json_in .= ",";
				}

				if($key == "pickup"){
					$json_in = str_replace("\"pickup\":{,","\"pickup\":{",$json_in);
					$json .= $json_in;
				}
			}else{
				if($json != "{"){
					$json .= ",";
				}
				$json .= "\"{$key}\":\"{$value}\"";
			}
		}
		$json .= "}";
		return $json;
	}

		$options = array(
				'http' => array(
						"method"  => "POST",
						"content" => json_encode($param),
						"header"  => "Content-Type: application/json; charset=utf8\r\n".
						"X-FCSH-Application-Key: ".$appKey."\r\n".
						"X-FCSH-Timestamp: ".$timestamp."\r\n" .
						"X-FCSH-Signature: ".$SignatureString."\r\n"
				)
		);

		$context = stream_context_create($options);
		$Response = file_get_contents($apiUrl,false,$context);
	}//end if
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 新規ここまで
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//



//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 変更ここから
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
	if (array_key_exists("update", $_POST))
		{
		$apiUrl = "https://hi-shuka.jp/api/1/orders/update";

		// 集荷依頼者の基本情報
		$param["requestno"] = $_POST["requestno"];
		$requester["username"] = $_POST["username"];
		$requester["username"] = $_POST["username"];
		$requester["furikana"] = $_POST["furikana"];
		$requester["zipcode"] = $_POST["zipcode"];
		$requester["prefectures"] = $_POST["prefectures"];
		$requester["address1"] = $_POST["address1"];
		$requester["address2"] = $_POST["address2"];
		$requester["tel"] = $_POST["tel"];
		$requester["email"] = $_POST["email"];
		$param["requester"] = $requester;

		$RequestNo = $_POST["requestno"];
		$Name = $_POST["username"];
		$KanaName = $_POST["furikana"];
		$ZipCode = $_POST["zipcode"];
		$Prefectures = $_POST["prefectures"];
		$Address1 = $_POST["address1"];
		$Address2 = $_POST["address2"];
		$Tel = $_POST["tel"];
		$Mail = $_POST["email"];

		// 集荷依頼情報
		$pickup["deliverycompanycode"] = $_POST["deliverycompanycode"];
		$pickup["collectdate"] = $_POST["collectdate"];
		$pickup["collecttime"] = $_POST["collecttime"];
		$pickup["packnum"] = $_POST["packnum"];
		$pickup["voucherflg"] = $_POST["voucherflg"];
		$pickup["baggage-type"] = $_POST["baggage-type"];
		$pickup["package"] = $_POST["package"];
		$pickup["note"] = $_POST["note"];
		$param["pickup"] = $pickup;

		$param["timestamp"] = $timestamp;
		$param["timestamp"] = "2018-11-25T16:40:37.277325";

		$TransPoter = $_POST["deliverycompanycode"];
		$RequestDate = $_POST["collectdate"];
		$RequestTime = $_POST["collecttime"];
		$BuggNum = $_POST["packnum"];
		$Payment = $_POST["voucherflg"];
		$BuggName = $_POST["baggage-type"];
		$ItemCode = $_POST["package"];
		$Memo = $_POST["note"];

		$SignatureString = $Signature->CreateSignature($apiUrl. "&POST", $param, $secretKey);

		$options = array(
				'http' => array(
						"method"  => "POST",
						"content" => json_encode($param),
						"header"  => "Content-Type: application/json; charset=utf8\r\n".
						"X-FCSH-Application-Key: ".$appKey."\r\n".
						"X-FCSH-Timestamp: ".$timestamp."\r\n" .
						"X-FCSH-Signature: ".$SignatureString."\r\n"
				)
		);
		var_dump($param);
		print $RequestNo;
		$context = stream_context_create($options);

		$Response = file_get_contents($apiUrl, false, $context);

	}
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 変更ここまで
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//



//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 集荷依頼の停止ここから
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//

	if (array_key_exists("stop", $_POST))
	{
		$apiUrl = "https://hi-shuka.jp/api/1/orders/pending";

		$RequestNo = $_POST["requestno"];

		$param["requestno"] = $_POST["requestno"];
		$param["timestamp"] = $timestamp;

		$SignatureString = $Signature->CreateSignature($apiUrl. "&POST", $param, $secretKey);


		$options = array(
				'http' => array(
						'method'  => "POST",
						'content' => json_encode($param),
						'header'  => "Content-Type: application/json; charset=utf8\r\n".
						"X-FCSH-Application-Key: ".$appKey."\r\n".
						"X-FCSH-Timestamp: ".$timestamp."\r\n" .
						"X-FCSH-Signature: ".$SignatureString."\r\n"
				)
		);


		$context = stream_context_create($options);
		// var_dump($context);

		$Response = file_get_contents($apiUrl, false, $context);
	}
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 集荷依頼の停止ここまで
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//



//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 集荷依頼の再開ここから
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
	if (array_key_exists("restart", $_POST))
	{
		$apiUrl = "https://hi-shuka.jp/api/1/orders/restart";

		$RequestNo = $_POST["requestno"];

		$param["requestno"] = $_POST["requestno"];
		$param["timestamp"] = $timestamp;

		$SignatureString = $Signature->CreateSignature($apiUrl. "&POST", $param, $secretKey);

		$options = array(
				'http' => array(
						'method'  => "POST",
						'content' => json_encode($param),
						'header'  => "Content-Type: application/json; charset=utf8\r\n".
						"X-FCSH-Application-Key: ".$appKey."\r\n".
						"X-FCSH-Timestamp: ".$timestamp."\r\n" .
						"X-FCSH-Signature: ".$SignatureString."\r\n"
				)
		);
		$context = stream_context_create($options);

		$Response = file_get_contents($apiUrl, false, $context);
	}
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 集荷依頼の再開ここまで
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//




//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 一覧ここから
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
	if (array_key_exists("list", $_POST)){
		//エンドポイント
		$apiUrl = "https://hi-shuka.jp/api/1/orders";

		// 検索条件
		$param = "?";
		$param .= "start-date=2018-11-01";
		$param .= "&end-date=2018-12-31";
		$param .= "&type=";
		$param .= "&transporter=sagawa";
		$param .= "&page=1";
		$param .= "&per=10";

		$apiUrl = $apiUrl . $param;
		$SignatureString = $Signature->CreateSignature($apiUrl. "&GET", "", $secretKey);

		$options = array(
				"http" => array(
						"method"  => "GET",
						'header'  => "Content-Type: application/json; charset=utf8\r\n".
						"X-FCSH-Application-Key: ".$appKey."\r\n".
						"X-FCSH-Timestamp: ".$timestamp."\r\n" .
						"X-FCSH-Signature: ".$SignatureString."\r\n"
				)
		);

		$context = stream_context_create($options);
		$Response = file_get_contents($apiUrl, false, $context);
	}//end if
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//
// 一覧ここまで
//■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■//

//住所
	if (array_key_exists("address", $_POST))
	{
		$apiUrl = "https://hi-shuka.jp/api/1/orders/address";

		$param = $_POST["zipcode"];

		$ZipCode = $_POST["zipcode"];

		$apiUrl = $apiUrl . "?zipcode=" . $param;

		$options = array(
				"http" => array(
						"method"  => "GET",
						'header'  => "Content-Type: application/json; charset=utf8\r\n" .
						"X-FCSH-Application-Key: ".$appKey."\r\n"
				)
		);
		$context = stream_context_create($options);

		$Response = file_get_contents($apiUrl, false, $context);
	}

	//入力チェック
	if (array_key_exists("check", $_POST))
	{
		$apiUrl = "https://hi-shuka.jp/api/1/orders/requestcheck";

		// 集荷依頼者の基本情報
		$requester["username"] = $_POST["username"];
		$requester["furikana"] = $_POST["furikana"];
		$requester["zipcode"] = $_POST["zipcode"];
		$requester["prefectures"] = $_POST["prefectures"];
		$requester["address1"] = $_POST["address1"];
		$requester["address2"] = $_POST["address2"];
		$requester["tel"] = $_POST["tel"];
		$requester["email"] = $_POST["email"];
		$param["requester"] = $requester;

		$Name = $_POST["username"];
		$KanaName = $_POST["furikana"];
		$ZipCode = $_POST["zipcode"];
		$Prefectures = $_POST["prefectures"];
		$Address1 = $_POST["address1"];
		$Address2 = $_POST["address2"];
		$Tel = $_POST["tel"];
		$Mail = $_POST["email"];

		$params['requester'] = array(
				'username'    => $Name,
				'furikana'    => $KanaName,
				'zipcode'     => $ZipCode,
				'prefectures'  => $Prefectures,
				'address1'    => $Address1,
				'address2'    => $Address2,
				'tel'         => $Tel,
				'email'       => $Mail
		);

		// 集荷依頼情報
		$pickup["deliverycompanycode"] = $_POST["deliverycompanycode"];
		$pickup["collectdate"] = $_POST["collectdate"];
		$pickup["collecttime"] = $_POST["collecttime"];
		$pickup["packnum"] = $_POST["packnum"];
		$pickup["voucherflg"] = $_POST["voucherflg"];
		$pickup["baggage-type"] = $_POST["baggage-type"];
		$pickup["package"] = $_POST["package"];
		$pickup["note"] = $_POST["note"];
		$param["pickup"] = $pickup;

		$TransPoter = $_POST["deliverycompanycode"];
		$RequestDate = $_POST["collectdate"];
		$RequestTime = $_POST["collecttime"];
		$BuggNum = $_POST["packnum"];
		$Payment = $_POST["voucherflg"];
		$BuggName = $_POST["baggage-type"];
		$ItemCode = $_POST["package"];
		$Memo = $_POST["note"];

		$params['pickup'] = array(
				'deliverycompanycode'       => $TransPoter,//対象運送会社はヤマトのみ
				'collectdate'               => $RequestDate,
				'collecttime'               => $RequestTime,
				'packnum'                   => $BuggNum,
				'voucherflg'                => $Payment, //着払いのみ
				'baggage-type'              => $BuggName,
				'package'                   => $ItemCode,
				'note'                      => $Memo //ドライバーさんへの伝達事項
		);

		$param["timestamp"] = $timestamp;
		$params["timestamp"] = $timestamp;

		$SignatureString = $Signature->CreateSignature($apiUrl . "&POST", $param, $secretKey);
		$SignatureString2 = $Signature->CreateSignature($apiUrl . "&POST", $params, $secretKey);

		//シグネチャー作成
		$paramData = $Signature->ArraySort($params);//配列をキー順でソート
		$parameterString = json_encode($paramData);
		$signatureBase   = array($apiUrl, "POST", $parameterString);
		$signatureBase   = implode('&', $signatureBase);
		$signatureBase   = urlencode($signatureBase);
		$hashString      = hash_hmac('sha256',$signatureBase , $secretKey);//シグネチャー

		$options = array(
				'http' => array(
						"method"  => "POST",
						"content" => json_encode($param),
						"header"  => "Content-Type: application/json; charset=utf8\r\n".
						"X-FCSH-Application-Key: ".$appKey."\r\n".
						"X-FCSH-Timestamp: ".$timestamp."\r\n" .
						"X-FCSH-Signature: ".$hashString."\r\n"
				)
		);
		$context = stream_context_create($options);

		$Response = file_get_contents($apiUrl, false, $context);
	}

	// $Response = "{\"errors\":[{\"message\":\"テスト\"}]}";





// include_once $_SERVER["DOCUMENT_ROOT"]."/include/hi_shuka/template.php";
// var_dump($_POST);
?>
<pre>
<?//php echo print_r($Response, true); ?>
<?//php echo print_r(json_decode($Response), true); ?>
</pre>

<div>



<!-- <a href="/include/speed/list.php">一覧画面へ</a> -->

</div>
	</body>
</html>
