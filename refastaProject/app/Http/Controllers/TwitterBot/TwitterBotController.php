<?php

namespace App\Http\Controllers\TwitterBot;

use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\TwitterOAuth3\TwitterOAuth;



class TwitterBotController
{
    public function sendTweet()
    {
        //日曜日は実行しない
        if(date("w") == "0"){
            return;
        }

        $today_price = array();
        $yesterday_price = array();

        $market_stmt = DB::select("SELECT * FROM `kinkai`.`tb_market` WHERE `resist_data`='".date("Y/m/d")."' LIMIT 1");

        $ny_market = '';
        $jp_market = '';
        foreach ($market_stmt as $item) {
            $ny_market = $item->ny_market;
            $jp_market = $item->jp_market;
        }

        $stmt = DB::select("SELECT * FROM `kinkai`.`tb_price` ORDER BY `resist_data` DESC LIMIT 2");
        foreach($stmt as $item){
            if($item->resist_data_type_date == date("Y-m-d")){
                $today_date = $item->resist_data_type_date;
                $today_date = date('n/j',strtotime($today_date));
                $today_price["gold"] = $item->gold;
                $today_price["platinum"] = $item->platinum;
                $today_price["silver"] = $item->silver;
                $today_price["palladium"] = $item->palladium;
            }elseif($item->resist_data_type_date == date("Y-m-d",strtotime('-1 day'))){
                $yesterday_price["gold"] = $item->gold;
                $yesterday_price["platinum"] = $item->platinum;
                $yesterday_price["silver"] = $item->silver;
                $yesterday_price["palladium"] = $item->palladium;
            }
        }

        $price_display = array();
        $price_display_text = array();
        foreach($today_price as $key => $today_price_item){
            $display_cls = "";
            $price_difference = $today_price_item - $yesterday_price[$key];
            if($price_difference!=0){
                if($price_difference>0){
                $display_cls = "up_cls";
                $price_difference = "↑ ".number_format($price_difference)."円";
                }elseif($price_difference<0){
                $display_cls = "down_cls";
                $price_difference = "↓ ".number_format($price_difference)."円";
                }
                $price_display[$key] = number_format($today_price_item)."円<span class =".$display_cls.">(".$price_difference.")</span>";
                $price_display_text[$key] = number_format($today_price_item)."円(".$price_difference.")";
            }else{
                $display_cls = "even_cls";
                $price_difference = "±".$price_difference."円";
                $price_display[$key] = number_format($today_price_item)."円<span class =".$display_cls.">(".$price_difference.")</span>";
                $price_display_text[$key] = number_format($today_price_item)."円(".$price_difference.")";
            }
        }

        $gold_col_name_array = array('k24','k23','k22','k21','k20','k18','k17','k14','k12','k10','k9','k8','k5');
        $platinum_col_name_array = array('pt1000','pt950','pt900','pt850','pt_pm','pm900','pm850');
        $silver_col_name_array = array('sv1000','sv925','sv900');
        $palladium_col_name_array = array('au12pd20','pd1000','pd950','pd900','pd500');
        $gold_type_price = array();
        $platinum_type_price = array();
        $silver_type_price = array();
        $palladium_type_price = array();

        $date = date("Y-m-d");

        $write_file_name = "writing_template.html";
        $write_file_path = "twitterbot/{$write_file_name}";
        ob_start();
        include_once("template.html");
        $str = ob_get_contents();
        ob_end_clean();
        $str = str_replace("★日付★",$today_date,$str);
        $str = str_replace("★金相場★",$price_display["gold"],$str);
        $str = str_replace("★プラチナ相場★",$price_display["platinum"],$str);
        $str = str_replace("★銀相場★",$price_display["silver"],$str);
        $str = str_replace("★パラジウム相場★",$price_display["palladium"],$str);

        $str = str_replace("★NY為替終値★",$ny_market,$str);
        $str = str_replace("★東京為替始値★",$jp_market,$str);


        $tb_gold_stmt = DB::select("SELECT * FROM `kinkai`.`tb_gold_rate_history` ORDER BY `date` DESC LIMIT 1");
        foreach ($tb_gold_stmt as $tb_gold_item){
            foreach($gold_col_name_array as $gold_col_name){
                $gold_type_price = floor($tb_gold_item->$gold_col_name*$today_price["gold"]);
                $gold_type_price = number_format($gold_type_price)."円";
                $replace_name = "★".$gold_col_name."★";
                $str = str_replace($replace_name,$gold_type_price,$str);
            }
        }
        
        $tb_platinum_stmt = DB::select("SELECT * FROM `kinkai`.`tb_platinum_rate_history` ORDER BY `date` DESC LIMIT 1");
        foreach ($tb_platinum_stmt as $tb_platinum_item){
            foreach($platinum_col_name_array as $platinum_col_name){
                $platinum_type_price = floor($tb_platinum_item->$platinum_col_name*$today_price["platinum"]);
                $platinum_type_price = number_format($platinum_type_price)."円";
                $replace_name = "★".$platinum_col_name."★";
                $str = str_replace($replace_name,$platinum_type_price,$str);
            }
        }

        $tb_silver_stmt = DB::select("SELECT * FROM `kinkai`.`tb_silver_rate_history` ORDER BY `date` DESC LIMIT 1");
        foreach ($tb_silver_stmt as $tb_silver_item){
            foreach($silver_col_name_array as $silver_col_name){
                $silver_type_price = floor($tb_silver_item->$silver_col_name*$today_price["silver"]);
                $silver_type_price = number_format($silver_type_price)."円";
                $replace_name = "★".$silver_col_name."★";
                $str = str_replace($replace_name,$silver_type_price,$str);
            }
        }

        $tb_palladium_stmt = DB::select("SELECT * FROM `kinkai`.`tb_palladium_rate_history` ORDER BY `date` DESC LIMIT 1");
        foreach ($tb_palladium_stmt as $tb_palladium_item){
            foreach($palladium_col_name_array as $palladium_col_name){
                if($palladium_col_name == 'au12pd20'){
                    $palladium_type_price = floor((floor(((($today_price["gold"]+1)*0.985)+1)*0.12)+floor(((($today_price["palladium"]+1)*0.98)+1)*0.2)+floor(floor($today_price["silver"]*0.935)*0.45) )-200)*$tb_palladium_item->$palladium_col_name;
                }else{
                    $palladium_type_price = floor($tb_palladium_item->$palladium_col_name*$today_price["palladium"]);
                }
                $palladium_type_price = number_format($palladium_type_price)."円";
                $replace_name = "★".$palladium_col_name."★";
                $str = str_replace($replace_name,$palladium_type_price,$str);
            }
        }

        Storage::put($write_file_path, $str);

        $storage_path = storage_path("app/".$write_file_path);
        $storage_path = str_replace($write_file_name, "", $storage_path);
        system("cd {$storage_path} && wkhtmltoimage --width 320 --height 900 {$write_file_name} dist.jpg");

        // TODO ツイッターアAPIの接続、処理
        define('TWITTER_CONSUMER_KEY', config('services.twitter_bot.consumer_key'));
        define('TWITTER_CONSUMER_SECRET', config('services.twitter_bot.consumer_secret'));
        $access_token = config('services.twitter_bot.access_token');
        $access_token_secret = config('services.twitter_bot.access_token_secret');
        $connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $access_token, $access_token_secret);
        // $content = $connection->get("account/verify_credentials");
        // 画像をアップロードして、そのメディアIDを変数に格納
         $media1 = $connection->upload('media/upload', ['media' => $storage_path.'dist.jpg']);
        // var_dump($media1);

        $tweet_text = "💰本日の貴金属買取価格💰";
        $tweet_text .= "\n";
        $tweet_text .= "#金 {$price_display_text['gold']}\n";
        $tweet_text .= "#プラチナ {$price_display_text['platinum']}\n";
        $tweet_text .= "#銀 {$price_display_text['silver']}\n";
        $tweet_text .= "\n";
        $tweet_text .= "\n";

        $tweet_text .= "#宅配買取 は全国対応。";
        $tweet_text .= "\n";
        $tweet_text .= "安心の高額買取なら #リファスタ へ";
        $tweet_text .= "\n";
        $tweet_text .= "https://kinkaimasu.jp/";
        $tweet_text .= "\n";
        $tweet_text .= "\n";
        $tweet_text .= "#池袋 #金買取 #ダイヤ買取 #売りたい #大掃除 #引越し #遺品整理 #貴金属買取 #断捨離 #終活";
        // var_dump($tweet_text);

        // ツイートするためのパラメータをセット
         $parameters = [
             'text' => $tweet_text,
             'media' => ['media_ids' => [$media1->media_id_string]],
         ];
         // ツイートを実行
        try {
          $connection->setApiVersion(2);
          $result = $connection->post('tweets', $parameters, true);
        } catch (\Exception $e) {
          echo $e->getMessage();
        }
        return response()->json([
            // 'twittertest' => $content,
            'storage_path' => $storage_path
        ]);  
    }
}
