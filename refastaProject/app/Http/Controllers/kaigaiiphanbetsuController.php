<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Notifications\PushChatwork;

/** 
 *  @link 管理画面 https://ipinfo.io/account/home
 *  @link  https://ipinfo.io/
 *  @example response
 *        city: "Tokyo"
 *        country: "JP"
 *        hostname: "pw126035199242.25.panda-world.ne.jp"
 *        ip: "126.35.199.242"
 *        loc: "35.6895,139.6917"
 *        org: "AS17676 Softbank BB Corp."
 *        postal: "151-0052"
 *        readme: "https://ipinfo.io/missingauth"
 *        region: "Tokyo"
 *        timezone: "Asia/Tokyo"
 **/
class kaigaiiphanbetsuController extends Controller
{
    public function index(Request $request, $ip)
    {
        if(strstr($ip, ",")){
            $array = explode("," , $ip);
            // $ip = $array[count($array) - 1];
            $ip = $array[0];
            $ip = trim($ip);
        }


        // nishitani@urlounge.co.jpのアカウント
        // $url = "https://ipinfo.io/{$ip}?token=2b3171b91b8fc3";

        // nishitani@hi-ba-na.jpのアカウント
        $url = "https://ipinfo.io/{$ip}?token=9ee6ce99e71b3e";
        
        // エラー時は基本的にはJPにし、通すようにしておく。
        // シャットアウトしてしまうと、全てのユーザーが送信出来なくなる。
        try {
            $options['ssl']['verify_peer']=false;
            $options['ssl']['verify_peer_name']=false;
            $res = file_get_contents($url, false, stream_context_create($options));
            $json = json_decode($res);
            $country = $json->country ?? "JP";
        } catch (\Throwable $th) {
            // var_dump($th);
            // exit;
            $country = "JP";
        }

        // var_dump($country);
        if($country != "JP"){
            $msg = "[info][title]海外IPアクセス[/title]{$ip}\n国コード「{$country}」\n▼詳細▼\n[code]{$res}[/code][/info]";
            PushChatwork::Push($msg, $room= "257711106", $token='bot');
        }


        return $country;
        // return view("kaigaiiphanbetsu.index", array(
        //     "country" => $country,
        // ));
    }

}
