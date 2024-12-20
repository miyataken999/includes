<?php

namespace App\Notifications;


class PushChatwork
{


    /**
     * Build the message.
     *
     * @return $this
     */
    static function Push($msg,$room='113228487',$token='nishitani')
    {
        //ラウンジ通知ルーム
        $room = "303816082";
        $nishitani = "7556891648b7dc215099992437aa6589";
        $token = $nishitani;
        $msg = str_replace("&", "＆", $msg);
        $msg = str_replace("\"", "”", $msg);
        $msg = str_replace("'", "’", $msg);
        exec('curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$msg.'" "https://api.chatwork.com/v2/rooms/'.$room.'/messages" -O');
    }


    /**
    $msg = "エラーメッセージ";
    $option = array("dir" => __DIR__, "file" => __FILE__, "line" => __LINE__, "class" => __CLASS__, "function" => __FUNCTION__);
    PushChatwork::error_push($msg, $option);
    **/
    static function error_push($msg, $option)
    {
        //ラウンジ通知ルーム
        $room = "303816082";
        $nishitani = "7556891648b7dc215099992437aa6589";
        $token = $nishitani;
        $template = "[info][title]コード詳細[/title]";
        $template .= "dir:{$option["dir"]}\n";
        $template .= "file:{$option["file"]}\n";
        $template .= "line:{$option["line"]}\n";
        $template .= "class:{$option["class"]}\n";
        $template .= "function:{$option["function"]}";
        $template .= "[/info]";
        $msg .= $template;
        $msg = str_replace("&", "＆", $msg);
        $msg = str_replace("\"", "”", $msg);
        $msg = str_replace("'", "’", $msg);
        exec('curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$msg.'" "https://api.chatwork.com/v2/rooms/'.$room.'/messages" -O');
    }
    static function error_message_push($option, $room='150036002',$token='bot')
    {
        //ラウンジ通知ルーム
        $room = "303816082";
        //誰から発行するか
        $nishitani = "7556891648b7dc215099992437aa6589";
        $token = $nishitani;
        $template = "[info][title]エラー発生[/title]";
        $template .= "dir:{$option["dir"]}\n";
        $template .= "error_message:{$option["msg"]}\n";
        $template .= "file:{$option["file"]}\n";
        $template .= "line:{$option["line"]}\n";
        $template .= "class:{$option["class"]}\n";
        $template .= "function:{$option["function"]}";
        $template .= "[/info]";
        exec('curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$template.'" "https://api.chatwork.com/v2/rooms/'.$room.'/messages" -O');
    }

}
