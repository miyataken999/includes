<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class pushGoogleChatSpaceController extends Controller
{
    /**
     * @see webhookのURLリスト https://docs.google.com/spreadsheets/d/1iWalshX4BjxVNSexeRNldnZJm-mcTxFvuqnRSq-_Eu8/edit#gid=0
     */
    public function send_chat(Request $request)
    {
        $space_id = $request->route('space_id');
        $text = $request->input('text');

        // スペースIDからkeyとtokenを決める
        if($space_id=="AAAA6DggJtw") {
            // hibanaテストルーム（スレッドじゃないバージョン）
            $key = "AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI";
            $token = "0xyma34l4cL-CZiW9T0LqBVTuqiVEWGofqcvW0XGONQ%3D";
        } else if($space_id=="AAAAkWzs5Ag") {
            // hibanaテストルーム（スレッドバージョン）
            $key = "AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI";
            $token = "hx_yc0jaJ6tutBGqU4lyHF5hiPna-1MoseeNZVKiidY%3D";
        } else if($space_id == 'AAAAJHp4kGk') {
            // [01]接客席スペース
            $key = "AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI";
            $token = "lHcKWVGJvCuVC3_ncIHCrk74kw_4jsWnYnpB-Mab30o%3D";
        } else if($space_id == 'AAAAFVrKvXE') {
            // [NPC]庶務部トピ(D0005)
            $key = "AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI";
            $token = "VJe2nVzOfdOcOCLS3ODv8PjPv-q37RnzJZLylMRP-Jg%3D";
        } else if($space_id == 'AAAA8sgQ7I8') {
            // 西ポス
            $key = "AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI";
            $token = "KIqJ52cs92nhWODrDgxqGs-AoOPZPar7VT7WanZbAN8";
        } else if($space_id == 'AAAAcclcL3M') {
            // Refasta Application Lounge
            $key = "AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI";
            $token = "dQEF0ceG1-zdWOqxNqKGyuPhz8CFR0gZKrSHb30IBeA";
        } else if($space_id == 'AAAAPM4--yA') {
            // survey lounge
            $key = "AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI";
            $token = "YEWShPSzU5jckrcxTPb-WeDpC6tweZ8lI9AxhaShw4M";
        } else {
            // スペースIDが一致しなければ
            return "URLが正しくありません";
        }

        // urlを生成する
        $url = "https://chat.googleapis.com/v1/spaces/$space_id/messages?key=$key&token=$token";

        // 送信データ
        $data = array(
            "text" => $text,
        );

        // JSON形式に変換
        $data = json_encode($data);

        // ストリームコンテキストのオプションを作成
        $options = array(
            // HTTPコンテキストオプションをセット
            'http' => array(
                'method'=> 'POST',
                'header'=> 'Content-Type: application/json',
                'content' => $data
            )
        );

        // ストリームコンテキストの作成
        $context = stream_context_create($options);
        $contents = file_get_contents($url, false, $context);

        try {
            echo $contents;
        } catch (\Exception $e) {
            $e->getMessage();
            session()->flash('flash_message', '更新が失敗しました');
        }
    }
}