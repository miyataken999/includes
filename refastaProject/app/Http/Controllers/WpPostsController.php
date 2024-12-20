<?php

namespace App\Http\Controllers;

use App\Models\Api\Request_api;

use Illuminate\Http\Request;
use DB;

class WpPostsController extends Controller
{
    public $base_url = "https://rifa.life/refasta_wordpress";

    /**
     * 投稿データの取得API
     * @param int id : 投稿POSTの id
     */
    public function get_wp_posts($id=0)
    {
        // $id = "23135";

        $endpoint = "/wp-json/wp/v2/posts/{$id}";
        if(isset($_GET["block_id"])){
            $endpoint .= "?block_id=" . intval($_GET["block_id"]);
        }
        


        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Content-Type: application/json",
                )
        );

        $context = stream_context_create($opts);

        // 上で設定した HTTP ヘッダを使用してファイルをオープンします

        $res = file_get_contents($this->base_url . $endpoint, false, $context);


        // $data = json_decode(json_encode($res), true);
        // $data = json_encode($res);
        $data = json_decode($res, false);
        // var_dump($data);
        // exit;
        // rendered

        $array = array(
            "data" => $data->content->rendered,
        );
        
        return view("get_wp_posts.index", $array);
    }
}
