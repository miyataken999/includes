<?php

namespace App\Http\Controllers;

use App\Models\Api\Request_api;
use App\Notifications\PushChatwork;

use Illuminate\Http\Request;
use SoapClient;
use SimpleXMLElement;
use DB;
use App\Models\Kinkai\tb_rapa_history;
use App\Models\Kinkai\tb_rapa_history_log;
use App\Models\Kinkai\tb_rapa_pricelist;
use App\Models\Kinkai\tb_diamond_history;
use Symfony\Component\VarDumper\VarDumper;

class RapaApiController
{
	public function dummy(){
		return;
	}
	//TODO 為替が更新された際にkinkai/admininfo/up_date.phpでdiamond_price()を呼び出す。
	//当日の全rapaデータ取得（既存に無い条件は除く）->計算->tb_rapa_pricelistに格納
	public function diamond_price(){
		$today = date('Y-m-d');
		$history_date = tb_rapa_history::orderBy('date', 'desc')->first();
		if ($history_date == NULL) {
			return;
		}
		$date = $history_date->date;
		$history_table = tb_rapa_history::where('date', $date)->get();
		if ($history_table == NULL) {
			return;
		}
		$masterrate_table = \DB::table("kinkai.tb_masterrate")->first();
		if ($history_table == NULL) {
			return;
		}
		$rate_change = $masterrate_table->masterrate_exchange;
		$tax = $masterrate_table->masterrate_tax;

		foreach ($history_table as $history_val) {
			if ($history_val->carat > 0.20 && $history_val->carat < 0.24) {
				$history_val->carat = "0.20";
			}
			$rate_val = \DB::table("kinkai.tb_multiplication_rate")
			->where('color', $history_val->color)
			->where('ct',(float)$history_val->carat)
			->where('clarity',$history_val->clarity)
			->first();

			if ($rate_val == false) {
				continue;
			}

			$today_rapaprice = tb_rapa_pricelist::where('regist_date',$today)
			->where('carat', $history_val->carat)
			->where('clarity', $history_val->clarity)
			->where('color', $history_val->color)
			->first();
						// 重複データがある場合↓
			if ($today_rapaprice !== NULL) {
				// 値段のみ更新
				$today_rapaprice->Excellent = floor($history_val->price * $rate_change * $tax * $rate_val->Excellent / 1000) * 1000;
				$today_rapaprice->VeryGood = floor($history_val->price * $rate_change * $tax * $rate_val->Verygood / 1000) * 1000;
				$today_rapaprice->Good = floor($history_val->price * $rate_change * $tax * $rate_val->Good / 1000) * 1000;
				$today_rapaprice->save();
			}else {
		// 重複データが無い場合↓
		// 新規データとして保存
				$price_table = new tb_rapa_pricelist;
				$price_table->Excellent = floor($history_val->price * $rate_change * $tax * $rate_val->Excellent / 1000) * 1000;
				$price_table->VeryGood = floor($history_val->price * $rate_change * $tax * $rate_val->Verygood / 1000) * 1000;
				$price_table->Good = floor($history_val->price * $rate_change * $tax * $rate_val->Good / 1000) * 1000;
				$price_table->color = $history_val->color;
				$price_table->clarity = $history_val->clarity;
				$price_table->carat = $history_val->carat;
				$price_table->regist_date = $today;
				try{
					$price_table->save();
				}catch(\Exception $e){
					$msg = $e->getMessage();
					$option = array("dir" => __DIR__, "file" => __FILE__, "line" => __LINE__, "class" => __CLASS__, "function" => __FUNCTION__);
					PushChatwork::error_message_push($msg, $option, '150036002',"takami");
					return;
				}
			}
		}
	}

	//tb_rapa_historyのidとtb_rapa_history_logを紐付け
	public function relation_id($log_id, $history_id){
		$history_log = tb_rapa_history_log::where('id',$log_id)->first();
		if ($history_log == NULL) {
			return;
		}
		$history_log->tb_rapa_history_id = $history_id;
		try{
			$history_log->save();
		}catch(\Exception $e){
			$msg = $e->getMessage();
			$option = array("dir" => __DIR__, "file" => __FILE__, "line" => __LINE__, "class" => __CLASS__, "function" => __FUNCTION__);
			PushChatwork::error_message_push($msg, $option, '150036002',"takami");
			return;
		}
	}

	// rapaAPIからのデータ通信のlogを保存(loginは他の関数で対応)
	public function req_res($client){
		$date = date('Y-m-d H:i:s', strtotime('now'));

		$res_req = new tb_rapa_history_log;
		$res_req->request_header = $client->__getLastRequestHeaders();
		$res_req->request_body = $client->__getLastRequest();
		$request_xml = $client->__getLastRequest();
		$request_soap = simplexml_load_string($request_xml);
		$request = $request_soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children('ns1','getprice');
		$request_json = json_encode($request);
		$res_req->request_body_json = $request_json;

		$last_res =  $client->__getLastResponseHeaders();
		preg_match_all('/[0-9]{3}/', $last_res, $matches);
		$res_req->status_code = $matches[0][0];
		$res_req->response_header = $client->__getLastResponseHeaders();
		$res_req->response_body = $client->__getLastResponse();
		$response_xml=$client->__getLastResponse();
		$response_soap = simplexml_load_string($response_xml);
		$response = $response_soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children();
		$response_json = json_encode($response);
		$res_req->response_body_json = $response_json;
		$res_req->date = $date;
		$res_req->save();
		$log_id = $res_req->id;
		return $log_id;
	}

		// 取得したRAPAデータをテーブルに追加する関数
	public function rapa_table($result){
		// 重複データの確認
		$date = str_replace('T00:00:00', '', $result->Date);
		$today_history = tb_rapa_history::where('date',$date)
		->where('carat', $result->low_size)
		->where('clarity', $result->clarity)
		->where('color', $result->color)
		->first();

		// 重複データがある場合↓
		if ($today_history !== NULL) {
			// 値段のみ更新
			$today_history->price = $result->price;
			$today_history->save();
			$history_id = $today_history->id;
			return $history_id;
			// 重複データがない場合↓
		}else {
			$tb_rapa_history = new tb_rapa_history;
			$tb_rapa_history->carat = $result->low_size;
			$tb_rapa_history->color = $result->color;
			$tb_rapa_history->clarity = $result->clarity;
			$tb_rapa_history->price = $result->price;
			$tb_rapa_history->date = $date;
			try{
				$tb_rapa_history->save();
				// 追加したデータのid取得（tb_rapa_history_logテーブルとの紐付けのため）
				$history_id = $tb_rapa_history->id;
				return $history_id;
			}catch(\Exception $e){
				$msg = $e->getMessage();
				$option = array("dir" => __DIR__, "file" => __FILE__, "line" => __LINE__, "class" => __CLASS__, "function" => __FUNCTION__);
				PushChatwork::error_message_push($msg, $option, '150036002',"takami");
				return;
			}
		}
	}

	// login成功時時のlogテーブル保存
	public function login_history_true($client){
		$date = date('Y-m-d H:i:s', strtotime('now'));

		$login_history = new tb_rapa_history_log;
		$login_history->request_header = $client->__getLastRequestHeaders();
		$login_history->request_body = $client->__getLastRequest();
		$request_xml = $client->__getLastRequest();
		$request_soap = simplexml_load_string($request_xml);
		$request = $request_soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children('ns1','login');
		$request_json = json_encode($request);
		$login_history->request_body_json = $request_json;

		$last_res =  $client->__getLastResponseHeaders();
		preg_match_all('/[0-9]{3}/', $last_res, $matches);
		$login_history->status_code = $matches[0][0];
		$login_history->response_header = $client->__getLastResponseHeaders();
		$login_history->response_body = $client->__getLastResponse();
		$response_xml = $client->__getLastResponse();
		$response_soap = simplexml_load_string($response_xml);
		$response = $response_soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children();
		$response_json = json_encode($response);
		$login_history->response_body_json = $response_json;
		$login_history->date = $date;
		try{
			$login_history->save();
		}catch(\Exception $e){
			$msg = $e->getMessage();
			$option = array("dir" => __DIR__, "file" => __FILE__, "line" => __LINE__, "class" => __CLASS__, "function" => __FUNCTION__);
			PushChatwork::error_message_push($msg, $option, '150036002',"takami");
		}
	}

	// login失敗時のlogテーブル保存
	public function login_history_false($client,$i){

		$login_history = new tb_rapa_history_log;
		$login_history->request_header = $client->__getLastRequestHeaders();
		$login_history->request_body = $client->__getLastRequest();
		$request_xml = $client->__getLastRequest();
		$request_soap = simplexml_load_string($request_xml);
		$request = $request_soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children('ns1','login');
		$request_json = json_encode($request);
		$login_history->request_body_json = $request_json;
		$last_res = $client->__getLastResponseHeaders();
		preg_match_all('/[0-9]{3}/', $last_res, $matches);
		$login_history->status_code = $matches[0][0];
		$login_history->response_header = $client->__getLastResponseHeaders();
		$login_history->response_body = $client->__getLastResponse();

		$response_xml = $client->__getLastResponse();
		$response_soap = simplexml_load_string($response_xml);
		$response = $response_soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children('soap', 'fault')->children();
		$response_json = json_encode($response);
		$login_history->response_body_json = $response_json;
		$date = date('Y-m-d H:i:s', strtotime('now'));
		$login_history->date = $date;
		$login_history->save();

		if($i == 2)
		{
			$err_msg = "[info][title]RAPAPORT API　ログインエラー発生[/title]";
			$err_msg .= "ERROR MESSAGE :";
			$err_msg .= $response->faultstring;
			$err_msg .= "\n";
			$err_msg .= "STATUS CODE :{$login_history->status_code}[/info]";
			PushChatwork::push($err_msg, $room = "150036002", $token = "takami");
		}
	}

	public function get_rapa_price()
	{
		$rap_soapUrl = "https://technet.rapaport.com/webservices/prices/rapaportprices.asmx?wsdl";
		$client = new \SoapClient(
			$rap_soapUrl,
			array("trace" => 1, "exceptions" => 0, "cache_wsdl" => 0)
		);
		$params = array('Username' => '81370', 'Password' => 'blocrhodia');

		//login　を三回試す
		for ($i=0; $i < 3; $i++) {
			$login_result = $client->__soapCall("Login", array($params), NULL, NULL, $output_headers);
			//login 失敗した場合
			if (is_soap_fault($login_result) == TRUE)
			{
				if($i == 2){
					$this->login_history_false($client,$i);
					return;
				}
				$this->login_history_false($client,$i);
			//login 成功した場合
			} elseif (is_soap_fault($login_result) == FALSE) {
				$this->login_history_true($client);
				break;
			}
		}
		// ログイン成功後、チケット取得して・rapaapiのデータに接続
		$ticket = $output_headers["AuthenticationTicketHeader"]->Ticket;
		$ns = "http://technet.rapaport.com/";
		$headerBody = array("Ticket" => $ticket);
		$header = new \SoapHeader($ns, 'AuthenticationTicketHeader', $headerBody);
		$client->__setSoapHeaders($header);
		$today = date("Y-m-d");

		// ダイヤモンド各条件（獲得可能全範囲）
		$size_array = ["0.01", "0.04", "0.08", "0.15","0.18", "0.23", "0.30", "0.40","0.50","0.70", "0.90", "1.00", "1.50" , "2.00", "3.00", "4.00", "5.00", "10.00"];
		$color_array = ["D","E","F","G","H","I","J","K","L","M"];
		$clarity_array = ["if","vvs1","vvs2","vs1","vs2","si1","si2","si3","i1","i2","i3"];

		// 全範囲のrapaデータ獲得
		for ($k=0; $k < count($color_array); $k++) {
			$paramsA["color"] = $color_array[$k];
			for ($j=0; $j < count($clarity_array); $j++) {
				$paramsA["clarity"] = $clarity_array[$j];
				for ($i=0; $i < count($size_array); $i++) {
					$paramsA["shape"] = "round";
					$paramsA["size"] = $size_array[$i];
					$params = array("SearchParams" => $paramsA);
					$results = $client->__soapCall("GetPrice", array($paramsA), NULL, NULL, $output_headers);
					$log_id = $this->req_res($client);
					$results->GetPriceResult->size = $size_array[$i];
					$rapa_data = $results->GetPriceResult;
					$has_value = $rapa_data->price;
					if ($has_value !== '0') {
						$history_id = $this->rapa_table($rapa_data);
						$this->relation_id($log_id, $history_id);
					}
				}
			}
		}
		return;
	}

	public function getDiamond(){
		//今日の日にちを配列に追加[今日、昨日]
		$date = array();
		$date['today'] = date('Y-m-d');
		$date['yesterday'] = date('Y-m-d', strtotime($date['today'])-60*60*24);

		//ダイヤモンドテーブルから項目dateから今日の値を獲得
		$today_history = tb_diamond_history::where('date',$date['today'])->first();

		$carat_array = array("0.30","0.50","1.00","3.00");
		$sum_excellent = 0;
		$sum_verygood = 0;
		$sum_good = 0;
		$i = 0;
		$j=0;
		$total = 0;
		$ave_price_array = array();

		for ($i=0; $i < count($carat_array); $i++) {
			$carat = $carat_array[$i];
			$sum_total_ex = tb_rapa_pricelist::where('carat', $carat)->get();
			foreach ($sum_total_ex as $sum_ex) {
				$sum_excellent += $sum_ex->Excellent;
				$sum_verygood += $sum_ex->VeryGood;
				$sum_good += $sum_ex->Good;
				$j++;
			}
			$total = $sum_excellent + $sum_verygood + $sum_good;
			$ave_price_array[$i] = (floor(($total / ($j * 3)) / 1000) * 1000);
			$j = 0;
			$total = 0;
			$sum_excellent = 0;
			$sum_verygood = 0;
			$sum_good = 0;
		}

		// 今日の値がある場合　（更新）
		if($today_history !== NULL){
			$tb_diamond_history = tb_diamond_history::orderBy('date', 'desc')->first();
			$tb_diamond_history->ct03_ave_price = $ave_price_array[0];
			$tb_diamond_history->ct05_ave_price = $ave_price_array[1];
			$tb_diamond_history->ct1_ave_price = $ave_price_array[2];
			$tb_diamond_history->ct3_ave_price = $ave_price_array[3];
			$tb_diamond_history->date = $date['today'];
			$tb_diamond_history->save();
		}
		// 今日の値がない場合　（新規追加）
		else
		{
			$tb_diamond_history =new tb_diamond_history;
			$tb_diamond_history->ct03_ave_price = $ave_price_array[0];
			$tb_diamond_history->ct05_ave_price = $ave_price_array[1];
			$tb_diamond_history->ct1_ave_price = $ave_price_array[2];
			$tb_diamond_history->ct3_ave_price = $ave_price_array[3];
			$tb_diamond_history->date = $date['today'];
			$tb_diamond_history->save();
		}
	}
}
