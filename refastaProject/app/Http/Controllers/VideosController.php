<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use DB;

class VideosController extends Controller
{

    /**
     * 再利用ブロック用コンテンツ
     */
    public function make_block_contents()
    {
        $videos = DB::table("internet.videos")
            ->orderBy("open_at", "desc")
            ->orderBy("video_id", "desc")
            ->take(3)
            ->get();
        $array = array();
        $array["videos"] = $videos;
        return view("videos.reusable_block.index", $array);
    }
}
