<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use DB;

class YoutubeListController extends Controller
{
    

    /**
     * @param string title : 動画のタイトル
     * @param string url : 動画のURL
     * @param array option : 何かしらの付属情報はここに入れられるようにしておく。
     */
    public function get_url()
    {
        
        $data_array = array();

        $data_array[] = array(
            "title" => "[2021年版]【買取に悩んでいるあなたへ】リファスタが伝えたい3つのメッセージ",
            "url" => "https://www.youtube.com/embed/1xyIM_DXGdM",
            // "option" => array(),
        );
        return json_encode($data_array);
    }
}