<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use App\Models\Urlounge\analysisMail_send_logs;
use App\Models\Urlounge\analysisMail_mailgun_webhook_logs;

/**
 * @link 進捗シート https://docs.google.com/spreadsheets/d/1xgdxpcc99UYeYm03PTuqbohIrjsuE_-Il2a4juCS45A/edit#gid=0
 */


class MailgunApiController 
{
	public function receive_event(Request $request)
	{
		return "aaaaaa";
	}

	public function receive_post_event(Request $request)
	{

		$request_data = $request->all(); 
		$request_header_data = $request->header();
		$method = $request->method();
		$event_data = $request_data["event-data"];


		$message_id = $event_data["message"]["headers"]["message-id"] ?? '';
	
		
		$analysisMail_mailgun_webhook_logs = new analysisMail_mailgun_webhook_logs;


		$analysisMail_mailgun_webhook_logs->mailgun_id = $event_data["id"] ?? '';
		$analysisMail_mailgun_webhook_logs->mailgun_message_id = $message_id;
		$analysisMail_mailgun_webhook_logs->event_type = $event_data["event"] ?? '';
		$analysisMail_mailgun_webhook_logs->method = $method;
		$analysisMail_mailgun_webhook_logs->request_header = json_encode($request_header_data);
		$analysisMail_mailgun_webhook_logs->request_body = json_encode($request_data);

		try{
			$analysisMail_mailgun_webhook_logs->save();
		}catch(\Exception $e){
			echo $e->getMessage();
		}

		echo "OK!!!<br>";

		$event = $event_data["event"] ?? '';
		$severity = $event_data["severity"] ?? '';

		if($message_id != ""){
			$this->convert_from_mailgun_log_to_send_log($message_id);
		}
		echo "convert finish<br>";

		return "ENDDD!!!";
	}



	public function convert_from_mailgun_log_to_send_log($mailgun_message_id='')
	{
		$limit = htmlspecialchars($_GET["limit"] ?? 200000, ENT_QUOTES, 'UTF-8');

		if($mailgun_message_id != ""){
			$mailgun_logs = analysisMail_mailgun_webhook_logs::where("mailgun_message_id", "$mailgun_message_id")
												->orderBy("send_log_id","desc")
												->get();
		}else{
			$mailgun_logs = analysisMail_mailgun_webhook_logs::orderBy("send_log_id","desc")->take($limit)->get();
		}
		
		foreach($mailgun_logs as $mailgun_data){
			$request_data = json_decode($mailgun_data->request_data, true);

			$event_data = $request_data["event-data"];

			$event = $mailgun_data->event_type;
			$send_log_id = $mailgun_data->send_log_id;

			$severity = $event_data["severity"] ?? '';

			$mailgun_webhook_last_status = $event;
			if($severity != ''){
				$mailgun_webhook_last_status = $event."_".$severity;
			}

			if($mailgun_webhook_last_status == ""){
				continue;
			}

			$mailgun_message_id = $mailgun_data->mailgun_message_id;

			$analysisMail_send_logs = analysisMail_send_logs::where("mailgun_message_id", $mailgun_message_id)->first();
			if($analysisMail_send_logs == null){
				continue;
			}

			$analysisMail_send_logs->send_result = "success";
			if($mailgun_data->event_type == "delivered"){
					$analysisMail_send_logs->mailgun_webhook_last_status = $mailgun_webhook_last_status;
			}elseif($mailgun_data->event_type == "clicked"){
				// $analysisMail_send_logs->is_clicked = 1;
				$analysisMail_send_logs->send_result = "success";
			}elseif($mailgun_data->event_type == "opened"){
				// $analysisMail_send_logs->is_opened = 1;
			}elseif($mailgun_data->event_type == "failed"){
				if($severity == "permanent"){
					//永続的な送信失敗
					//メルマガ拒否の可能性あり
				}elseif($severity == "temporary"){
					//一時的な送信失敗
					//メールサーバのエラーの可能性の為、配信失敗で値を保存
					$analysisMail_send_logs->send_result = "error";
				}
				
				$analysisMail_send_logs->mailgun_webhook_last_status = $mailgun_webhook_last_status;

			}elseif($mailgun_data->event_type == "complained"){
				//迷惑メール報告を受けた。
				//todo どういう処理にするかは相談
				$analysisMail_send_logs->mailgun_webhook_last_status = $mailgun_webhook_last_status;
			}


			try{
				$analysisMail_send_logs->save();
			}catch(\Exception $e){
				echo $e->getMessage();
			}
		}
		// return "finish";
		return redirect("https://rifa.life/evaProject/analysisMail/dashboard/index");
	}

}

