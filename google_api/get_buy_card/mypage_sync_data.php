<?php
/*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
mypage同期処理
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/

//mypagedataの更新処理
// 店頭取引データ、商品データ同期処理
// $Eoc_unfinished_id;
$base_uri = "https://rifa.life";
$endpoint = "/evaProject/api/mypage/func/transfer_data/sync_data_for_tentou";
// POSTデータ
$data = array(
	"Eoc_unfinished_id" => $Eoc_unfinished_id
);
// $data = http_build_query($data, "", "&");
$data = json_encode($data);
// header
$header = array(
		"Content-Type: application/json",
		// 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
		"Content-Length: " . strlen($data),
);
$context = array(
		"http" => array(
				"method"  => "POST",
				"header"  => implode("\r\n",$header),
				"content" => $data,
		)
);

file_get_contents($base_uri.$endpoint, false, stream_context_create($context));

// /*■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// END mypage同期処理
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■*/
